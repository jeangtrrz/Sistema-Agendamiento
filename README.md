# Sistema de Agendamiento - Internet Cordillera

## Descripción General

Sistema web moderno y completo para la gestión de citas de servicios de Internet. Permite administrar instalaciones, retiros de equipamiento y visitas de soporte, con calendar semanal y reportes en tiempo real.

**Versión:** 1.0
**Fecha de Creación:** 2026-05-22

---

## Características Principales

✅ **Gestión de Citas**
- Crear, editar y eliminar citas
- Validación automática de horarios (sin sobreposición)
- Asignación de técnicos específicos
- Tres tipos de citas: Instalación, Retiro, Soporte

✅ **Calendario Semanal**
- Vista clara de citas por día y hora
- Código de colores por tipo de cita
- Navegación fácil entre semanas
- Detalles de cita al hacer clic

✅ **Sistema de Usuarios**
- Dos perfiles: Técnico y Administrador
- Control de permisos granular
- Gestión completa de usuarios (solo admin)
- Autenticación segura con contraseñas hasheadas

✅ **Reportes y Estadísticas**
- Dashboard con estadísticas en tiempo real
- Reportes por período de fechas
- Filtros por tipo de cita y estado
- Resumen visual con badges de colores

✅ **Permisos Específicos**
- **Administrador:** Acceso completo, crear/editar/eliminar citas y usuarios
- **Técnico:** Solo marcar citas como completadas, ver sus citas asignadas

✅ **Diseño Moderno**
- Interfaz responsive (móvil, tablet, escritorio)
- Navegación intuitiva
- Paleta de colores profesional
- Animaciones suaves

---

## Requisitos del Sistema

**Software:**
- PHP 8.3+ (verificado con versión 8.3.31)
- MariaDB 10.6+ o MySQL 5.7+
- Servidor Web Apache o Nginx
- Acceso a cPanel

**Navegadores Compatibles:**
- Chrome/Chromium 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## Instalación

### 1. Preparación del Servidor

1. **Conectar vía SSH al servidor:**
```bash
ssh usuario@tu-dominio.com
cd public_html/
```

2. **Crear base de datos:**
   - Acceder a cPanel → MySQL Databases
   - Crear base de datos: `internet_agenda`
   - Crear usuario MySQL con permisos en esta BD

### 2. Subir Archivos

Subir la carpeta `sistema_agendamiento` a `public_html/` via FTP o cPanel File Manager.

### 3. Configurar Conexión a BD

Editar `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario_mysql');
define('DB_PASS', 'tu_contraseña_mysql');
define('DB_NAME', 'internet_agenda');
define('APP_URL', 'https://tu-dominio.com/sistema_agendamiento');
```

### 4. Ejecutar Script de BD

En cPanel → phpMyAdmin:
1. Seleccionar base de datos `internet_agenda`
2. Ir a "Importar"
3. Seleccionar archivo `database/init.sql`
4. Hacer clic en "Importar"

O via SSH:
```bash
mysql -u usuario -p internet_agenda < database/init.sql
```

### 5. Permisos de Carpetas

```bash
chmod 755 sistema_agendamiento/
chmod 755 sistema_agendamiento/config
chmod 755 sistema_agendamiento/controllers
```

### 6. Crear Carpeta de Logs

```bash
mkdir -p logs/
chmod 777 logs/
```

---

## Estructura del Proyecto

```
sistema_agendamiento/
├── index.php                 # Página de login
├── dashboard.php             # Panel principal
├── citas.php                # Gestión de citas
├── calendario.php           # Vista calendario semanal
├── usuarios.php             # Gestión de usuarios (admin)
├── reportes.php             # Reportes y análisis
│
├── config/
│   ├── config.php           # Configuración principal
│   └── session.php          # Gestión de sesiones
│
├── controllers/
│   ├── auth.api.php         # API de autenticación
│   ├── citas.api.php        # API de citas
│   ├── usuarios.api.php     # API de usuarios
│   ├── AuthController.php   # Controlador de auth
│   ├── CitaController.php   # Controlador de citas
│   └── UsuarioController.php # Controlador de usuarios
│
├── models/
│   ├── Usuario.php          # Modelo de usuario
│   └── Cita.php            # Modelo de cita
│
├── database/
│   └── init.sql            # Script de inicialización BD
│
└── assets/
    ├── css/
    │   └── style.css       # Estilos principales
    └── js/
        ├── main.js         # Scripts generales
        └── citas.js        # Scripts de citas
```

---

## Credenciales de Prueba

Al instalar, se crean automáticamente dos usuarios de prueba:

**Administrador:**
- Email: `admin@correo.cl`
- Contraseña: `123456`
- Perfil: Administrador (acceso completo)

**Técnico:**
- Email: `tecnico@dominio.cl`
- Contraseña: `123456`
- Perfil: Técnico (ver y completar citas)

⚠️ **Importante:** Cambiar estas contraseñas en producción.

---

## Guía de Uso

### Para Administradores

1. **Crear Cita:**
   - Ir a "Citas" → "Nueva Cita"
   - Llenar datos del cliente
   - Seleccionar técnico, tipo y fecha/hora
   - Guardar

2. **Editar Cita:**
   - En tabla de citas, clic en "Editar"
   - Modificar datos necesarios
   - Guardar

3. **Eliminar Cita:**
   - En tabla de citas, clic en "Eliminar"
   - Confirmar eliminación

4. **Gestionar Usuarios:**
   - Ir a "Usuarios"
   - Crear, editar o eliminar usuarios
   - Asignar perfil (Técnico o Administrador)

5. **Ver Reportes:**
   - Ir a "Reportes"
   - Seleccionar período de fechas
   - Ver estadísticas en tiempo real

### Para Técnicos

1. **Ver Citas Asignadas:**
   - Dashboard muestra citas del día
   - "Citas" muestra todas sus citas asignadas
   - "Calendario" muestra vista semanal

2. **Marcar Cita Completada:**
   - En tabla de citas, clic en "Completar"
   - Opcionalmente añadir observaciones
   - Confirmar

3. **Ver Calendario:**
   - "Calendario" → Vista semanal de sus citas
   - Clic en cita para ver detalles

---

## Funcionalidades Técnicas

### Validaciones

✅ **Horarios:**
- No permite sobreposición de citas por técnico
- Valida rango horario (09:00 - 18:00)
- Solo permite lunes a sábado
- No permite fechas pasadas

✅ **Datos:**
- Validación de email
- Validación de teléfono
- Campos obligatorios verificados
- Longitud mínima de contraseña (6 caracteres)

### Seguridad

✅ **Autenticación:**
- Contraseñas hasheadas con bcrypt
- Sesiones con timeout (1 hora)
- CSRF protection ready
- SQL Injection prevention con prepared statements

✅ **Permisos:**
- Control de acceso por rol
- Validación en servidor (no solo cliente)
- Admin es único que puede crear/editar/eliminar
- Técnico solo completa sus citas

### Base de Datos

✅ **Optimización:**
- Índices en columnas frecuentes
- Constraint unique en horarios
- Vistas para reportes
- Foreign keys para integridad

---

## Mantenimiento y Soporte

### Logs

Los errores se registran en `logs/error.log`. Revisar regularmente:

```bash
tail -f logs/error.log
```

### Backup de BD

Crear backup vía cPanel o SSH:

```bash
mysqldump -u usuario -p internet_agenda > backup_$(date +%Y%m%d).sql
```

## API Endpoints

### Autenticación
- `POST /controllers/auth.api.php` - login, logout, changePassword

### Citas
- `POST /controllers/citas.api.php` - getCitas, getCita, createCita, updateCita, deleteCita, markCompleted, getWeekCitas

### Usuarios
- `POST /controllers/usuarios.api.php` - getUsuarios, getUsuario, createUsuario, updateUsuario, deleteUsuario, getTechnicians, getStats

---

## Solución de Problemas

**Problema:** "Error de conexión a base de datos"
- ✓ Verificar credenciales en config.php
- ✓ Verificar que BD y usuario existan en MySQL
- ✓ Verificar permisos del usuario en la BD

**Problema:** "Sesión expira rápido"
- ✓ Ajustar `SESSION_TIMEOUT` en config.php (en segundos)

**Problema:** "Las citas no se cargan"
- ✓ Verificar que init.sql se ejecutó correctamente
- ✓ Revisar logs en `logs/error.log`
- ✓ Verificar permisos de la carpeta

**Problema:** "No puedo crear citas"
- ✓ Si es técnico, solo admin puede crear
- ✓ Verificar que al menos hay un técnico disponible
- ✓ Verificar fecha y hora estén dentro del rango permitido

---

## Mejoras Futuras

Funcionalidades planeadas para próximas versiones:

- [ ] Exportación a PDF y Excel
- [ ] Notificaciones por email
- [ ] SMS de confirmación
- [ ] Aplicación móvil
- [ ] Integración con Google Calendar
- [ ] Multi-idioma
- [ ] Temas oscuros

---

**Versión:** 1.0 | **Última Actualización:** 2026-05-22
