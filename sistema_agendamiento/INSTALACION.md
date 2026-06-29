# Guía de Instalación - Sistema de Agendamiento

## Pasos Rápidos

### Paso 1: Preparar cPanel

1. Acceder a cPanel con tus credenciales
2. Ir a **MySQL Databases** (o bases de datos MySQL)
3. Crear nueva base de datos: `internet_agenda`
4. Crear nuevo usuario MySQL: `internet_agenda_user` con contraseña segura
5. Asignar todos los permisos al usuario sobre la base de datos

### Paso 2: Subir Archivos

1. En cPanel, ir a **File Manager**
2. Navegar a `public_html`
3. Subir la carpeta `sistema_agendamiento` completa
4. O usar FTP para subir los archivos

### Paso 3: Ejecutar Script SQL

1. En cPanel, ir a **phpMyAdmin**
2. Seleccionar la base de datos `internet_agenda`
3. Ir a pestaña **Importar**
4. Seleccionar el archivo `database/init.sql`
5. Hacer clic en **Importar**

✓ Esto creará todas las tablas necesarias y usuarios iniciales

### Paso 4: Configurar conexión

1. Editar archivo `config/config.php`
2. Cambiar las siguientes líneas:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'internet_agenda_user');  // Tu usuario MySQL
define('DB_PASS', 'tu_contraseña');        // Tu contraseña MySQL
define('DB_NAME', 'internet_agenda');
define('APP_URL', 'https://tu-dominio.com/sistema_agendamiento');
```

3. Guardar el archivo

### Paso 5: Acceder al Sistema

1. Abrir navegador
2. Ir a: `https://tu-dominio.com/sistema_agendamiento/`
3. Iniciar sesión con:
   - Email: `jean@internetcordillera.cl`
   - Contraseña: `123456`

✓ ¡Sistema instalado y listo!

---

## Configuración Post-Instalación

### 1. Cambiar Contraseñas

**MUY IMPORTANTE:** Cambiar las contraseñas de los usuarios de prueba.

1. Iniciar sesión como administrador
2. Ir a **Usuarios**
3. Editar usuarios: `jean@internetcordillera.cl` y `freddy@internetcordillera.cl`
4. Cambiar contraseñas a valores seguros

### 2. Crear Más Usuarios

1. Ir a **Usuarios**
2. Clic en **+ Nuevo Usuario**
3. Llenar datos:
   - Nombre: Nombre del técnico/admin
   - Email: Email único
   - Contraseña: Mínimo 6 caracteres
   - Perfil: Técnico o Administrador
   - Estado: Activo

4. Guardar

### 3. Ajustar Configuración

Editar `config/config.php` para:

```php
// Cambiar la URL de producción
define('APP_URL', 'https://tu-dominio.com/sistema_agendamiento');

// Cambiar zona horaria si es necesario
define('APP_TIMEZONE', 'America/Santiago');  // Ya está por defecto

// Ajustar duración estándar de citas
define('DURACION_CITA_MINUTOS', 60);  // 60 minutos = 1 hora

// Ajustar timeout de sesión
define('SESSION_TIMEOUT', 3600);  // 3600 segundos = 1 hora
```

---

## Verificar Instalación

Después de instalar, verificar:

1. ✓ Puedes acceder a `https://tu-dominio.com/sistema_agendamiento/`
2. ✓ Puedes iniciar sesión
3. ✓ El dashboard muestra citas
4. ✓ Puedes crear una nueva cita (como admin)
5. ✓ El calendario semanal funciona
6. ✓ Los reportes generan datos

Si algo no funciona:

1. Revisar `logs/error.log` en la carpeta del sistema
2. Verificar que la carpeta tiene permisos 755
3. Verificar que la base de datos tiene las tablas
4. Verificar que `config/config.php` tiene credenciales correctas

---

## Troubleshooting

### Error: "Error de conexión a base de datos"

**Solución:**
1. Verificar credenciales en `config/config.php`
2. En cPanel → phpMyAdmin, verificar que:
   - Base de datos existe: `internet_agenda`
   - Usuario existe y tiene permisos
3. Probar conexión con SSH:
```bash
mysql -h localhost -u usuario -p internet_agenda
```

### Error: "Tabla no encontrada"

**Solución:**
1. Ir a cPanel → phpMyAdmin
2. Seleccionar `internet_agenda`
3. Ir a **Importar** y subir `database/init.sql` de nuevo

### Las imágenes/estilos no se cargan

**Solución:**
1. Verificar que `APP_URL` en config.php es correcto
2. Verificar permisos de carpeta `assets`:
```bash
chmod -R 755 sistema_agendamiento/assets/
```

### Las sesiones expiran muy rápido

**Solución:**
1. Editar `config/config.php`
2. Aumentar `SESSION_TIMEOUT`:
```php
define('SESSION_TIMEOUT', 7200);  // 2 horas
```

---

## Seguridad

### Cambiar contraseña de admin vía phpMyAdmin

Si no puedes acceder:

1. En phpMyAdmin, seleccionar tabla `usuarios`
2. Buscar el registro de `jean@internetcordillera.cl`
3. Editar el campo `password`
4. Reemplazar con este hash (contraseña: "123456"):
```
$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm
```
5. Guardar

### Hacer backup de la base de datos

Vía SSH:
```bash
mysqldump -u usuario -p internet_agenda > backup_$(date +%Y%m%d_%H%M%S).sql
```

Vía cPanel:
1. Ir a **Backup**
2. Descargar backup completo o selectivo

---

## Próximos Pasos

Una vez instalado:

1. **Crear equipos de técnicos:**
   - Ir a Usuarios
   - Crear un usuario para cada técnico disponible

2. **Configurar primer agendamiento:**
   - Ir a Citas
   - Crear cita de prueba
   - Verificar que aparece en calendario

3. **Entrenar al equipo:**
   - Mostrar cómo crear citas
   - Mostrar cómo marcar completadas
   - Mostrar cómo ver calendario

4. **Datos históricos (opcional):**
   - Si tiene citas anteriores, puede importarlas directamente a BD

---

¿Necesitas ayuda? Revisar README.md para documentación completa.
