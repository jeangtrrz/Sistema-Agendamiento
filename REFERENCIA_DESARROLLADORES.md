# 🔧 REFERENCIA RÁPIDA PARA DESARROLLADORES

## Sistema de Agendamiento - Internet Cordillera v1.0

---

## 📌 CONSTANTES PRINCIPALES

```php
// config/config.php

// Base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'internet_agenda');

// Aplicación
define('APP_URL', 'http://localhost/sistema_agendamiento/');
define('APP_TIMEZONE', 'America/Santiago');
define('APP_DEBUG', true); // Cambiar a false en producción

// Horarios operacionales
define('HORA_INICIO', '09:00');
define('HORA_FIN', '18:00');
define('DURACION_CITA_MINUTOS', 60);

// Arrays configurables
$GLOBALS['TIPOS_CITA'] = ['instalacion', 'retiro', 'soporte'];
$GLOBALS['PERFILES'] = ['tecnico', 'administrador'];
$GLOBALS['ESTADOS_CITA'] = ['pendiente', 'completada', 'cancelada'];
$GLOBALS['COLORES_CITA'] = [
    'instalacion' => '#4CAF50', // Verde
    'retiro' => '#FF9800',      // Naranja
    'soporte' => '#2196F3'      // Azul
];
```

---

## 🔐 FUNCIONES DE SEGURIDAD

```php
// config/session.php

// Verificar autenticación
if (!isAuthenticated()) {
    require_auth();
}

// Verificar rol
if (!isAdmin()) {
    die("Acceso denegado");
}

// Funciones disponibles:
isAuthenticated()  // ¿Hay sesión?
isAdmin()          // ¿Es administrador?
isTechnician()     // ¿Es técnico?
hasProfile($role)  // ¿Tiene perfil específico?
requireAuth()      // Redirigir si no autenticado
requireAdmin()     // Redirigir si no admin
requireTechnician()// Redirigir si no técnico
getCurrentUser()   // Obtener datos del usuario
```

---

## 📊 MÉTODOS DEL MODELO USUARIO

```php
// models/Usuario.php

$usuario = new Usuario();

// LECTURA
$usuario->getByEmail('email@example.com');  // Usuario por email
$usuario->getById($id);                     // Usuario por ID
$usuario->getAll();                         // Todos los usuarios
$usuario->getTechnicians();                 // Solo técnicos activos

// CREACIÓN
$usuario->create([
    'nombre' => 'Juan',
    'email' => 'juan@example.com',
    'password' => '123456',
    'perfil' => 'tecnico',
    'estado' => 'activo'
]);

// ACTUALIZACIÓN
$usuario->update($id, ['nombre' => 'Juan Actualizado']);

// ELIMINACIÓN
$usuario->delete($id);

// AUTENTICACIÓN
$usuario->verifyPassword('123456', $password_hash);

// ESTADÍSTICAS
$usuario->getStats();  // Contar por perfil y estado
$usuario->countByProfile('tecnico');
```

---

## 📅 MÉTODOS DEL MODELO CITA

```php
// models/Cita.php

$cita = new Cita();

// LECTURA
$cita->getById($id);                    // Cita específica
$cita->getAll($filtros);                // Lista filtrada
$cita->getWeekCitas($technician_id);   // Citas de la semana
$cita->getCitasByTechnician($tech_id);  // Citas de un técnico

// CREACIÓN
$cita->create([
    'cliente_nombre' => 'Cliente Nuevo',
    'cliente_telefono' => '+56912345678',
    'cliente_email' => 'cliente@example.com',
    'cliente_direccion' => 'Calle 123',
    'tipo_cita' => 'instalacion',
    'fecha_cita' => '2026-05-15',
    'hora_inicio' => '10:00',
    'tecnico_id' => 1,
    'descripcion' => 'Detalles',
    'created_by' => 1
]);

// ACTUALIZACIÓN
$cita->update($id, ['estado' => 'completada']);

// ELIMINACIÓN
$cita->delete($id);

// OPERACIONES ESPECIALES
$cita->markAsCompleted($id, 'Observaciones');
$cita->isTimeSlotAvailable($fecha, $hora, $tecnico_id);

// ESTADÍSTICAS
$cita->getStats($filtros);  // Por estado y tipo
```

---

## 🎮 MÉTODOS DEL CONTROLADOR CitaController

```php
// controllers/CitaController.php

$ctrl = new CitaController();

// VALIDAR DATOS
$ctrl->validateCitaData($data, $partial = false);
// Valida: email, fecha, hora, día, horarios, tipo, etc.

// CRUD CON PERMISOS
$ctrl->create($data);         // Solo admin
$ctrl->update($id, $data);    // Solo admin
$ctrl->delete($id);           // Solo admin
$ctrl->markCompleted($id, $obs); // Técnico/admin

// LECTURA
$ctrl->getById($id);
$ctrl->getAll($filtros);
$ctrl->getWeekCitas($tech_id);
```

---

## 🔗 ENDPOINTS DE API

### Autenticación (auth.api.php)

```javascript
// Login
fetch('controllers/auth.api.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        action: 'login',
        email: 'email@example.com',
        password: '123456'
    })
})

// Logout
fetch('controllers/auth.api.php', {
    method: 'POST',
    body: JSON.stringify({action: 'logout'})
})

// Cambiar contraseña
fetch('controllers/auth.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'changePassword',
        current_password: '123456',
        new_password: 'newpass123'
    })
})
```

### Citas (citas.api.php)

```javascript
// Obtener citas
fetch('controllers/citas.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'getCitas',
        filtros: {tipo_cita: 'instalacion'}
    })
})

// Crear cita (admin)
fetch('controllers/citas.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'createCita',
        cliente_nombre: 'Juan',
        cliente_telefono: '+56912345678',
        cliente_email: 'juan@example.com',
        cliente_direccion: 'Calle 123',
        tipo_cita: 'instalacion',
        fecha_cita: '2026-05-15',
        hora_inicio: '10:00',
        tecnico_id: 1,
        descripcion: 'Detalles'
    })
})

// Marcar como completada (técnico)
fetch('controllers/citas.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'markCompleted',
        id: 1,
        observaciones: 'Completada exitosamente'
    })
})

// Calendario
fetch('controllers/citas.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'getWeekCitas',
        technician_id: 1
    })
})
```

### Usuarios (usuarios.api.php)

```javascript
// Listar usuarios (admin)
fetch('controllers/usuarios.api.php', {
    method: 'POST',
    body: JSON.stringify({action: 'getUsuarios'})
})

// Crear usuario (admin)
fetch('controllers/usuarios.api.php', {
    method: 'POST',
    body: JSON.stringify({
        action: 'createUsuario',
        nombre: 'Nuevo Usuario',
        email: 'nuevo@example.com',
        password: '123456',
        perfil: 'tecnico'
    })
})

// Obtener técnicos
fetch('controllers/usuarios.api.php', {
    method: 'POST',
    body: JSON.stringify({action: 'getTechnicians'})
})
```

---

## 🎨 CLASES CSS PRINCIPALES

```css
/* Contenedores */
.container        /* Ancho máximo, centrado */
.navbar           /* Navegación superior */
.sidebar          /* Panel lateral (si se usa) */
.card             /* Caja con sombra */
.modal            /* Diálogo modal */

/* Estados de cita */
.badge            /* Etiqueta */
.badge-pending    /* Estado: pendiente (gris) */
.badge-completed  /* Estado: completada (verde) */
.badge-cancelled  /* Estado: cancelada (rojo) */

/* Tipos de cita */
.tipo-instalacion /* Verde */
.tipo-retiro      /* Naranja */
.tipo-soporte     /* Azul */

/* Componentes */
.btn              /* Botón */
.btn-primary      /* Botón principal */
.btn-danger       /* Botón peligroso */
.form-group       /* Grupo de formulario */
.table            /* Tabla */
.alert            /* Alerta */
.alert-success    /* Alerta éxito */
.alert-error      /* Alerta error */
```

---

## 📱 FUNCIONES JAVASCRIPT PRINCIPALES

```javascript
// main.js

// Utilidades
Utils.showAlert(message, type='success');
Utils.ajax(url, method, data);
Utils.formatDate(date);
Utils.getDayName(date);
Utils.isValidEmail(email);
Utils.isValidPhone(phone);
Utils.showLoading();
Utils.hideLoading();

// Modal
Modal.open(id);
Modal.close(id);
Modal.closeAll();
Modal.init();

// citas.js
Citas.loadCitas(filtros);
Citas.displayCitas(data);
Citas.createCita();
Citas.editCita(id);
Citas.deleteCita(id);
Citas.markCompleted(id, observaciones);
Citas.getTipoLabel(tipo);
Citas.getEstadoLabel(estado);
```

---

## 🗂️ ESTRUCTURA DE RESPUESTAS JSON

### Respuesta exitosa
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nombre": "Juan",
        "email": "juan@example.com"
    },
    "message": "Operación exitosa"
}
```

### Respuesta con error
```json
{
    "success": false,
    "error": "Descripción del error",
    "message": "Algo salió mal"
}
```

### Lista de citas
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "cliente_nombre": "Juan",
            "tipo_cita": "instalacion",
            "fecha_cita": "2026-05-15",
            "hora_inicio": "10:00",
            "tecnico_nombre": "Freddy",
            "estado": "pendiente"
        }
    ]
}
```

---

## 🔧 CONFIGURACIÓN INICIAL POST-INSTALACIÓN

```
1. Cambiar contraseñas iniciales
   Login: jean@internetcordillera.cl / 123456
   Perfil → Cambiar Contraseña

2. Crear usuarios reales
   Usuarios → Nueva Usuario
   Ingresar: nombre, email, password, perfil, estado

3. Probar flujo completo
   - Admin: Crear cita
   - Técnico: Marcar como completada
   - Admin: Ver en reportes

4. Hacer backup de BD
   cPanel → phpMyAdmin → Backup
```

---

## 🐛 DEBUGGING

### Archivo de errores
```bash
logs/error.log  # Ver errores PHP
```

### Activar modo debug
```php
// config/config.php
define('APP_DEBUG', true); // Ver errores en consola
```

### Console del navegador (F12)
```javascript
// Ver requests/responses
Network tab
Console → Ver errores JS
```

---

## 📋 TABLA DE CAMBIOS COMUNES

| Necesidad | Archivo | Cambio |
|-----------|---------|---------|
| Cambiar horario | config.php | HORA_INICIO, HORA_FIN |
| Agregar tipo cita | config.php | TIPOS_CITA array |
| Color de tipo | config.php | COLORES_CITA array |
| Email de contacto | config.php | Agregar constante |
| Verificación BD | verificar.php | Abrir en navegador |
| Ver credenciales | index.php | Pie de página |
| Editar formulario | citas.php | Modal form fields |

---

## ⚙️ VARIABLES DE SESIÓN

```php
$_SESSION['user_id']      // ID del usuario
$_SESSION['user_name']    // Nombre del usuario
$_SESSION['user_email']   // Email del usuario
$_SESSION['user_perfil']  // Perfil (tecnico/administrador)
$_SESSION['user_estado']  // Estado (activo/inactivo)
$_SESSION['login_time']   // Timestamp de login
```

---

## 🎯 PERMISOS POR ACCIÓN

| Acción | Admin | Técnico |
|--------|-------|---------|
| Ver dashboard | ✓ | ✓ |
| Ver citas | ✓ | ✓ (solo asignadas) |
| Crear cita | ✓ | ✗ |
| Editar cita | ✓ | ✗ |
| Eliminar cita | ✓ | ✗ |
| Marcar completada | ✓ | ✓ (si asignada) |
| Ver usuarios | ✓ | ✗ |
| Crear usuario | ✓ | ✗ |
| Editar usuario | ✓ | ✗ |
| Eliminar usuario | ✓ | ✗ |
| Ver reportes | ✓ | ✓ |
| Cambiar contraseña | ✓ | ✓ |

---

## 🚀 PRÓXIMAS MEJORAS

1. **PDF/Excel Export**
   - Librería: tcpdf o phpoffice/phpspreadsheet
   - Ubicación: reportes.php

2. **Notificaciones Email**
   - Librería: PHPMailer
   - Eventos: Nueva cita, completada

3. **SMS**
   - Servicio: Twilio
   - Evento: Confirmación cita

4. **Google Calendar**
   - API: Google Calendar API
   - Sincronización bidireccional

5. **App Móvil**
   - Framework: React Native
   - APIs: Usar endpoints existentes

---

## 📞 ERRORES COMUNES

| Error | Causa | Solución |
|-------|-------|----------|
| "No se pudo conectar a BD" | Credenciales incorrectas | Revisar config.php |
| "Acceso denegado" | Rol insuficiente | Verificar $_SESSION |
| "Slot no disponible" | Conflicto de horario | Cambiar hora en cita |
| "Fecha inválida" | Fecha pasada o domingo | Seleccionar fecha válida |
| "Email ya existe" | Email duplicado | Usar email único |
| "Error de validación" | Campo requerido vacío | Llenar todos los campos |

---

## 💾 BACKUP Y RESTAURACIÓN

```bash
# Exportar BD
mysqldump -u internet_agenda_user -p internet_agenda > backup.sql

# Importar BD
mysql -u internet_agenda_user -p internet_agenda < backup.sql

# En cPanel:
phpMyAdmin → Exportar/Importar
```

---

## 🔒 SEGURIDAD CHECKLIST

- [ ] Cambiar contraseñas iniciales
- [ ] config.php en .gitignore
- [ ] HTTPS habilitado
- [ ] Backups regulares
- [ ] Revisar logs periódicamente
- [ ] Actualizar PHP a versión nueva
- [ ] BD user con permisos mínimos
- [ ] .htaccess activo

---

**Referencia Rápida v1.0 - Sistema de Agendamiento Internet Cordillera**

Última actualización: Mayo 2026

