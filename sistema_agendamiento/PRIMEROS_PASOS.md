# PRIMEROS PASOS - Guía Inicial

## Bienvenido al Sistema de Agendamiento

Hola! Este archivo te guiará en los primeros pasos para tener el sistema funcionando.

---

## ✓ PASO 1: Verificar Instalación (5 minutos)

### 1. Acceder a la verificación
```
https://tu-dominio.com/sistema_agendamiento/verificar.php
```

### 2. Revisar los checkmarks (✓)
- ✓ Verde = Todo bien
- ✕ Rojo = Error que debe corregirse
- ⚠ Naranja = Advertencia

### 3. Si hay errores rojos:
- Revisar la sección "Troubleshooting" en INSTALACION.md
- Verificar que config/config.php tiene credenciales correctas
- Confirmar que la BD se importó correctamente

---

## ✓ PASO 2: Primer Login (2 minutos)

### 1. Abrir página de login
```
https://tu-dominio.com/sistema_agendamiento/
```

### 2. Iniciar sesión como ADMIN
```
Email:    jean@internetcordillera.cl
Password: 123456
```

### 3. Si funciona, ¡bienvenido al dashboard!

---

## ✓ PASO 3: Cambiar Contraseñas (5 minutos) ⚠️ IMPORTANTE

### Cambiar contraseña del admin (Jean)

1. Login como Jean (arriba)
2. Click en tu nombre (arriba a la derecha)
3. "Cambiar Contraseña"
4. Ingresar:
   - Contraseña actual: 123456
   - Nueva contraseña: (algo seguro, mínimo 6 caracteres)
   - Confirmar nueva

### Cambiar contraseña del técnico (Freddy)

1. En admin, ir a "Usuarios"
2. Encontrar "Freddy Morales"
3. Click en "Editar"
4. Cambiar contraseña (dejar en blanco si no quieres cambiar)
5. Guardar

---

## ✓ PASO 4: Crear Primera Cita (5 minutos)

### Crear una cita de prueba

1. Click en "Citas" (menú superior)
2. Click en "+ Nueva Cita"
3. Llenar formulario:
   ```
   Cliente: [Tu nombre o cliente de prueba]
   Teléfono: 912345678
   Email: cliente@email.com
   Dirección: Calle 123, Piso 4
   Tipo: Instalación ← (elige una opción)
   Técnico: Freddy Morales ← (debe elegirse de la lista)
   Fecha: [Mañana o un día futuro]
   Hora: 10:00
   Descripción: [Opcional - descripción de la cita]
   ```
4. Click en "Guardar Cita"

### Verificar que aparecen en:
- Dashboard (resumen)
- Lista de Citas
- Calendario Semanal

---

## ✓ PASO 5: Probar Calendario (2 minutos)

### 1. Click en "Calendario"

Deberías ver:
- La semana actual (Lunes a Sábado)
- Horarios de 09:00 a 18:00
- Tu cita de prueba en color (según tipo)

### 2. Hacer click en la cita
Debería mostrar los detalles

### 3. Navegar a otra semana
- "← Semana Anterior"
- "Siguiente →"
- "Hoy" (vuelve a la actual)

---

## ✓ PASO 6: Crear Usuarios de Técnicos (5 minutos)

### Para cada técnico que trabajará en el sistema:

1. En admin, ir a "Usuarios"
2. Click en "+ Nuevo Usuario"
3. Llenar:
   ```
   Nombre: [Nombre del técnico]
   Email: [email unico]@internetcordillera.cl
   Contraseña: [Contraseña inicial]
   Perfil: Técnico
   Estado: Activo
   ```
4. Guardar

**Nota:** El técnico puede cambiar su contraseña luego

---

## ✓ PASO 7: Probar como Técnico (3 minutos)

### 1. Logout (arriba a la derecha, click "Salir")

### 2. Login como técnico
```
Email: freddy@internetcordillera.cl
Contraseña: [la que estableciste]
```

### 3. Verificar que:
- Solo ve el dashboard
- En "Citas" ve solo sus citas asignadas
- En "Calendario" solo se ven sus citas
- NO tiene acceso a "Usuarios"
- Puede ver "Reportes" pero solo lectura

### 4. Marcar cita como completada:
- En tabla de citas
- Click en "Completar"
- Opcional: Añadir observaciones
- Confirmar

---

## ✓ CONFIGURACIÓN RECOMENDADA

### Horarios
El sistema viene configurado así:
- Inicio: 09:00
- Fin: 18:00
- Duración de cita: 60 minutos
- Días: Lunes a Sábado

Si necesitas cambiar, editar `config/config.php`:
```php
define('HORA_INICIO', '09:00');     // Cambiar si es necesario
define('HORA_FIN', '18:00');        // Cambiar si es necesario
define('DURACION_CITA_MINUTOS', 60); // Cambiar si es necesario
```

### Zona Horaria
Ya está configurada para Santiago (Chile):
```php
define('APP_TIMEZONE', 'America/Santiago');
```

### Timeout de Sesión
Actualmente 1 hora (3600 segundos). Para cambiar:
```php
define('SESSION_TIMEOUT', 3600); // En segundos
```

---

## ✓ CONSEJOS IMPORTANTES

### Seguridad
- ✓ Cambiar contraseñas iniciales (PASO 3)
- ✓ Usar contraseñas seguras (mínimo 6 caracteres, mejor 12+)
- ✓ Hacer backups regularmente
- ✓ Solo admin debe tener acceso al cPanel

### Rendimiento
- ✓ Las citas se cargan dinámicamente
- ✓ El calendario es responsive
- ✓ Los reportes se generan en tiempo real

### Acceso
- Desde cualquier navegador (Chrome, Firefox, Safari, Edge)
- Desde cualquier dispositivo (móvil, tablet, escritorio)
- La interfaz se adapta automáticamente

---

## 🆘 Si Algo No Funciona

### Problema: No puedo acceder a verificar.php

**Solución:**
1. Verificar que la URL es correcta
2. Esperar 5 minutos a que se propague el DNS
3. Limpiar caché del navegador (Ctrl+Shift+Del)

### Problema: "Error de conexión a BD"

**Solución:**
1. Verificar config/config.php tiene credenciales correctas
2. Ir a cPanel → phpMyAdmin
3. Verificar que la BD existe
4. Verificar que el usuario existe y tiene permisos

### Problema: No aparecen citas en el calendario

**Solución:**
1. Asegurarse de crear la cita correctamente
2. Verificar que la fecha sea Lunes-Sábado
3. Verificar que la hora esté entre 09:00-18:00
4. Revisar en tabla de "Citas" si aparecen ahí

### Problema: La sesión se cierra rápido

**Solución:**
1. Editar config/config.php
2. Aumentar SESSION_TIMEOUT
3. Guardar

---

## 📞 Soporte

Si necesitas ayuda:

1. Revisar README.md (documentación completa)
2. Revisar INSTALACION.md (solución de problemas)
3. Revisar logs/error.log (errores del sistema)
4. Contactar a soporte@internetcordillera.cl

---

## ✓ Checklist Final

Antes de considerar el sistema "listo":

- [ ] ✓ Verificación de instalación pasa todos los tests
- [ ] ✓ Puedo hacer login como admin
- [ ] ✓ Puedo hacer login como técnico
- [ ] ✓ Puedo crear una cita
- [ ] ✓ La cita aparece en dashboard
- [ ] ✓ La cita aparece en calendario
- [ ] ✓ Puedo marcar cita como completada
- [ ] ✓ Las contraseñas de prueba están cambiadas
- [ ] ✓ He creado usuarios para mi equipo
- [ ] ✓ Los técnicos pueden acceder con sus usuarios
- [ ] ✓ Los reportes funcionan

---

## 🎉 ¡Listo!

Si cumpliste todos los pasos anteriores:

**El sistema está funcionando correctamente y listo para usar.**

Próximos pasos:
1. Entrenar al equipo en su uso
2. Importar datos históricos si existen
3. Hacer backups regularmente
4. Disfrutar del sistema

---

¿Dudas? Revisar la documentación completa en README.md

Sistema de Agendamiento - Internet Cordillera v1.0
