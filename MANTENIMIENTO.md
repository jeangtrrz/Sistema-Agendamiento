# 🔧 GUÍA DE MANTENIMIENTO Y ACTUALIZACIONES

## Sistema de Agendamiento - Internet Cordillera v1.0

---

## 📋 CONTENIDO

1. [Tareas de Mantenimiento Regular](#tareas-de-mantenimiento-regular)
2. [Backups y Recuperación](#backups-y-recuperación)
3. [Monitoreo de Logs](#monitoreo-de-logs)
4. [Optimización de Rendimiento](#optimización-de-rendimiento)
5. [Actualizaciones y Mejoras](#actualizaciones-y-mejoras)
6. [Seguridad Continua](#seguridad-continua)
7. [Troubleshooting Avanzado](#troubleshooting-avanzado)
8. [Checklist Mensual](#checklist-mensual)

---

## ⏰ TAREAS DE MANTENIMIENTO REGULAR

### DIARIAMENTE

**Fin de turno:**
- [ ] Verificar que todas las citas del día estén completadas o canceladas
- [ ] Revisar que no haya citas pendientes sin asignar
- [ ] Confirmar que los técnicos hayan hecho logout

**Inicio de turno:**
- [ ] Verificar acceso al sistema
- [ ] Confirmar que todas las citas aparecen
- [ ] Revisar si hay notificaciones o errores

**Comando de verificación:**
```bash
# Revisar que el sistema esté online
curl https://tu-dominio.com/sistema_agendamiento/index.php
# Debe retornar 200 OK
```

### SEMANALMENTE

**Cada lunes (o día seleccionado):**

1. **Revisar estadísticas de la semana anterior**
   - Citas completadas vs pendientes
   - Identificar patrones o problemas

2. **Limpiar datos innecesarios**
   - Eliminar citas de prueba
   - Archivar citas muy antiguas (opcional)

3. **Verificar permisos de archivos**
   ```bash
   # En cPanel → Configurar permisos
   # Carpetas: 755
   # Archivos: 644
   ```

4. **Revisar logs de error**
   - Ver si hay errores nuevos
   - Diagnosticar y resolver

**Checklist semanal:**
```php
// Ejecutar en navegador:
https://tu-dominio.com/sistema_agendamiento/verificar.php
// Asegurarse que todo esté ✓
```

### MENSUALMENTE

1. **Backup completo**
   - Descargar base de datos
   - Descargar archivos
   - Guardar en dos lugares diferentes

2. **Revisar uso de recursos**
   - cPanel → Resource Usage
   - Verificar espacio disco
   - Verificar ancho de banda

3. **Actualizar contraseñas**
   - Cambiar contraseñas de cuentas de admin
   - Revisar accesos inusuales

4. **Limpiar logs antiguos**
   - Archivar logs/error.log
   - Crear backup antes de limpiar

5. **Verificar integridad BD**
   ```sql
   -- En phpMyAdmin:
   -- Check table
   CHECK TABLE usuarios;
   CHECK TABLE citas;
   CHECK TABLE historial_citas;
   ```

### TRIMESTRALMENTE

1. **Actualización de sistema**
   - Revisar si hay actualizaciones PHP
   - Revisar si hay actualizaciones de dependencias

2. **Auditoria de seguridad**
   - Revisar .htaccess
   - Verificar permisos de carpetas sensibles
   - Revisar usuarios activos

3. **Optimización de BD**
   ```sql
   -- En phpMyAdmin:
   OPTIMIZE TABLE usuarios;
   OPTIMIZE TABLE citas;
   OPTIMIZE TABLE historial_citas;
   OPTIMIZE TABLE reportes;
   ```

4. **Revisión de capacidad**
   - ¿Hay suficiente espacio para crecer?
   - ¿El rendimiento es aceptable?
   - ¿Necesita más usuarios?

---

## 💾 BACKUPS Y RECUPERACIÓN

### CREAR BACKUP MANUAL

**Opción 1: cPanel (Recomendado)**

```
1. cPanel → MySQL Databases
2. Click en nombre de BD "internet_agenda"
3. Click "Backup" (esquina derecha)
4. Descargar archivo SQL
5. Guardar en lugar seguro
```

**Opción 2: phpMyAdmin**

```
1. phpMyAdmin → Seleccionar BD "internet_agenda"
2. Click en "Exportar"
3. Formato: SQL
4. Click "Ir"
5. Guardar archivo
```

**Opción 3: Comando (Si tienes SSH)**

```bash
# Backup de BD
mysqldump -u internet_agenda_user -p internet_agenda > backup_$(date +%Y%m%d).sql

# Backup de archivos
tar -czf sistema_agendamiento_$(date +%Y%m%d).tar.gz sistema_agendamiento/

# Comprimir todo
zip -r backup_$(date +%Y%m%d).zip backup_*.sql sistema_agendamiento_*.tar.gz
```

### RESTAURAR DESDE BACKUP

**Restaurar base de datos:**

```
1. phpMyAdmin → Seleccionar BD "internet_agenda"
2. Click en "Importar"
3. Elegir archivo SQL del backup
4. Click "Ejecutar"
5. Esperar a que se complete
6. Verificar en verificar.php
```

**Restaurar archivos:**

```
1. cPanel → File Manager
2. Ir a public_html/sistema_agendamiento
3. Eliminar la carpeta actual (CUIDADO!)
4. Subir nuevo archivo
5. Extraer
6. Verificar permisos
```

### ESTRATEGIA DE BACKUPS RECOMENDADA

**Diarios:**
- Backup automático de BD (cPanel programador)
- Guardar en 2 lugares diferentes

**Semanales:**
- Backup completo (BD + archivos)
- Descargar a computadora local

**Mensuales:**
- Backup a sitio externo (Google Drive, Dropbox, OneDrive)
- Verificar que se pueda restaurar

**Almacenamiento:**
```
Backups/
├─ Diarios (ultimos 7 días)
├─ Semanales (ultimas 4 semanas)
├─ Mensuales (ultimos 12 meses)
└─ Años anteriores (archivados)
```

---

## 📊 MONITOREO DE LOGS

### VER ERRORES

**Archivo de log:**
```
ubicación: logs/error.log
acceso: cPanel → File Manager → logs/error.log
```

**Tipos de error común:**

| Error | Causa | Solución |
|-------|-------|----------|
| Database connection error | BD no responde | Verificar credenciales, BD está corriendo |
| Permission denied | Permisos incorrectos | cPanel → Change Permissions |
| Session timeout | Sesión expirada | Usuario debe hacer login nuevamente |
| Array not found | Código incorrecto | Revisar source, contactar dev |
| File not found | Archivo eliminado | Restaurar desde backup |

### LIMPIAR LOGS

```bash
# Ver tamaño del log
ls -lh logs/error.log

# Si es muy grande (>10MB), limpiar:
# En cPanel: Entrar a archivo, borrar contenido
# O desde terminal:
echo "" > logs/error.log
```

### CONFIGURAR ALERTS

Para ser avisado de errores, puedes:

```php
// Agregar al config/config.php:
define('ADMIN_EMAIL', 'admin@example.com');

// Script que se ejecuta si hay error:
mail(ADMIN_EMAIL, 
     "Error en sistema de agendamiento", 
     "Se produjo un error. Revisar logs/error.log");
```

---

## ⚡ OPTIMIZACIÓN DE RENDIMIENTO

### VERIFICAR VELOCIDAD

```bash
# Herramientas online:
- https://pagespeed.web.dev/
- https://gtmetrix.com/
- https://tools.pingdom.com/

# Copiar URL:
https://tu-dominio.com/sistema_agendamiento/dashboard.php

# Buscar:
✓ Velocidad de carga < 2 segundos
✓ Lighthouse score > 80
✓ No hay errores 404
```

### OPTIMIZACIONES IMPLEMENTADAS

✓ Compresión gzip (.htaccess)
✓ Cache de navegador (.htaccess)
✓ Minificación de CSS
✓ Índices de BD optimizados
✓ Queries preparadas (rápidas)
✓ Lazy loading de imágenes (si hay)

### OPTIMIZACIONES ADICIONALES (Futuro)

Si el sistema se ralentiza:

```php
// En config.php, agregar:

// Cache de sesión
define('SESSION_CACHE', true);

// Cache de queries
define('QUERY_CACHE', 3600); // 1 hora

// Comprimir output
ob_start('ob_gzhandler');

// Maximizar tiempo de ejecución
set_time_limit(300);
```

### PROBLEMAS DE RENDIMIENTO

**Síntoma: Página carga lenta**
```
Posibles causas:
1. Muchas citas en BD (>10,000)
   → Solución: Agregar índices, archivar antiguas

2. Conexión BD lenta
   → Solución: Optimizar queries, contactar hosting

3. Navegador lento
   → Solución: Limpiar caché, cambiar navegador

4. Hosting sobrecargado
   → Solución: Contactar hosting, upgrade plan
```

**Síntoma: BD muy grande (>1GB)**
```
Soluciones:
1. Limpiar tabla historial_citas antiguas
2. Archivar citas completadas hace meses
3. Comprimir BD
4. Hacer upgrade de plan de hosting
```

---

## 🔄 ACTUALIZACIONES Y MEJORAS

### ACTUALIZAR A NUEVA VERSIÓN

**Cuando hay actualizaciones disponibles:**

```
1. HACER BACKUP PRIMERO
   └─ BD + archivos

2. DESCARGAR NUEVA VERSIÓN
   └─ Obtener archivos nuevos

3. COMPARAR ARCHIVOS
   └─ Ver qué cambió

4. ACTUALIZAR EN PRODUCCIÓN
   └─ Opción A: Directo (si cambios simples)
   └─ Opción B: En staging primero

5. VERIFICAR QUE TODO FUNCIONE
   └─ Ejecutar verificar.php
   └─ Probar flujo completo
   └─ Revisar logs

6. SI FALLA: RESTAURAR BACKUP
   └─ Volver a versión anterior
```

### APLICAR PARCHES DE SEGURIDAD

**Si se descubre una vulnerabilidad:**

```
1. Contactar desarrollador para patch
2. Hacer backup inmediatamente
3. Aplicar patch a archivo específico
4. Verificar integridad (hash)
5. Probar funcionalidad
6. Actualizar todo el equipo sobre cambio
```

### AGREGAR NUEVAS CARACTERÍSTICAS

**Proceso recomendado:**

```
1. Crear en servidor de prueba (staging)
2. Probar completamente
3. Hacer backup de producción
4. Aplicar en producción
5. Verificar con verificar.php
6. Entrenar al equipo
7. Documentar el cambio
```

---

## 🔐 SEGURIDAD CONTINUA

### CAMBIO DE CONTRASEÑAS

**Cada 3 meses:**

1. Admin debe cambiar su contraseña
2. Técnicos deben cambiar sus contraseñas
3. User de BD debe cambiar (si acceso SSH)

**Proceso:**
```
Sistema → Perfil → Cambiar Contraseña
Ingresar actual → Nueva → Confirmar → Guardar
```

### REVISAR ACCESOS

**Mensualmente:**

```sql
-- En phpMyAdmin, ver últimas acciones:
SELECT * FROM historial_citas 
ORDER BY created_at DESC 
LIMIT 100;
-- Buscar cambios sospechosos
```

### ACTUALIZAR PHP

**Cada 6 meses:**

Revisar si hay versión nueva de PHP:

```
cPanel → PHP Configuration
Versión actual: 8.3.x
¿Hay 8.4 disponible?
Si → Actualizar
   → Verificar compatibilidad
   → Probar sistema
```

### ACTUALIZAR CERTIFICADO SSL

**Anualmente:**

```
cPanel → SSL/TLS Status
Certificado expira: [fecha]
Si expira pronto:
1. Renovar automático (si está habilitado)
2. O renovar manualmente
3. Verificar no haya errores de SSL
```

### AUDITAR PERMISOS

**Trimestralmente:**

```bash
# Verificar que archivos sensibles estén protegidos:
- config/config.php → No debe ser accesible público
- logs/ → No debe ser accesible público
- database/ → No debe ser accesible público
- models/ → No debe ser accesible público

# En cPanel → Change Permissions:
Directorio: 750 (solo dueño puede leer/escribir)
Archivo: 640 (solo dueño puede leer/escribir)
```

### MONITOREO DE LOGS DE ACCESO

```
cPanel → Raw Access Logs
Buscar:
- Intentos de acceder a /config/
- Intentos de acceder a /logs/
- Muchos 404 errors
- Patrones de ataque SQL injection
```

---

## 🔍 TROUBLESHOOTING AVANZADO

### ERROR: Base de datos no responde

```
Síntomas:
- Página en blanco
- "Connection timed out"
- Error 500

Diagnóstico:
1. Ejecutar verificar.php
2. Revisar logs/error.log
3. Entrar a phpMyAdmin
   - Si funciona → problema en config.php
   - Si no funciona → BD está abajo

Soluciones:
1. Verificar credenciales en config/config.php
2. Reiniciar MySQL en cPanel
3. Contactar hosting si MySQL está caído
4. Restaurar desde backup si BD está corrupta
```

### ERROR: Permisos de carpeta

```
Síntomas:
- "Permission denied"
- No se pueden crear archivos
- No se pueden escribir logs

Solución:
1. cPanel → File Manager
2. Botón derecho → Properties
3. Cambiar permisos:
   - Carpetas: 755
   - Archivos: 644
4. Aplicar a todos los archivos recursivamente
5. Reintentar
```

### ERROR: Sesión expira muy rápido

```
Causa:
- Configuración de timeout
- Problema con servidor web

Solución:
1. Editar config/session.php
2. Aumentar session_timeout
3. Verificar en cPanel que PHP está bien
4. Limpiar cookies navegador
5. Probar en navegador diferente
```

### LENTITUD DEL SISTEMA

```
Diagnóstico:
1. Ejecutar https://tu-dominio.com/sistema_agendamiento/verificar.php
2. Ver:
   - Tiempo de respuesta BD
   - Cantidad de registros
   - Uso de memoria

Soluciones según causa:
- Si BD es lenta → Optimizar queries, agregar índices
- Si hay muchos registros → Archivar citas antiguas
- Si servidor lento → Upgrade plan hosting
- Si navegador lento → Limpiar caché, cambiar navegador
```

### PROBLEMAS CON EMAIL (Para futuro)

```
Si se implementan notificaciones por email:

Verificar:
- SMTP configurado
- Puerto 587 o 465 abierto
- Contraseña de email correcta
- SPF/DKIM records configurados
- No está en spam

Debugging:
- Revisar logs de email
- Probar envío manual
- Contactar hosting si no funciona
```

---

## 📅 CHECKLIST MENSUAL

**Primer día de mes:**

- [ ] Hacer backup de BD
- [ ] Revisar logs de error
- [ ] Verificar uso de recursos (espacio, ancho banda)
- [ ] Cambiar contraseñas (si hace 3 meses)
- [ ] Ejecutar verificar.php
- [ ] Revisar estadísticas del mes anterior
- [ ] Eliminar datos de prueba si hay
- [ ] Crear reporte de actividad
- [ ] Contactar usuarios para feedback
- [ ] Documentar cualquier problema encontrado

**Mitad de mes:**

- [ ] Revisar accesos anómalos
- [ ] Verificar que backups funcionan
- [ ] Probar restauración de backup (en staging)
- [ ] Revisar integridad de datos
- [ ] Optimizar base de datos
- [ ] Limpiar logs si son muy grandes

**Fin de mes:**

- [ ] Generar reporte mensual
- [ ] Hacer backup mensual a almacenamiento externo
- [ ] Revisar plan de hosting (¿hay upgrades disponibles?)
- [ ] Verificar certificado SSL
- [ ] Generar estadísticas para directiva
- [ ] Planificar mejoras para próximo mes

---

## 📞 REGISTRO DE MANTENIMIENTO

**Plantilla para registrar tareas completadas:**

```
Fecha: _______________
Responsable: _______________
Tarea: _______________
Duración: _______________
Problemas encontrados: _______________
Soluciones aplicadas: _______________
Estado final: ✓ OK / ✗ Problema
Observaciones: _______________
Firma: _______________
```

---

## 🆘 CONTACTOS DE EMERGENCIA

Guardar en lugar seguro:

```
Hosting Provider:
  Nombre: _______________
  Teléfono: _______________
  Email: _______________
  Portal: _______________
  Usuario: _______________
  
Desarrollador del Sistema:
  Nombre: _______________
  Teléfono: _______________
  Email: _______________
  
Admin del Sistema:
  Nombre: _______________
  Teléfono: _______________
  Email: _______________
  
User de Base de Datos:
  Usuario: _______________
  Contraseña: _______________
  (Guardar en lugar SEGURO)
```

---

## 📚 REFERENCIAS

- **README.md** → Documentación general
- **INSTALACION.md** → Pasos de instalación
- **REFERENCIA_DESARROLLADORES.md** → Consulta técnica
- **verificar.php** → Diagnóstico automático
- **logs/error.log** → Errores del sistema

---

## ✅ CONCLUSIÓN

Con este plan de mantenimiento:

✓ Tu sistema seguirá funcionando perfectamente
✓ Los problemas se detectarán temprano
✓ Los datos estarán seguros
✓ El rendimiento será óptimo
✓ La seguridad será mantenida

**Dedica 30 minutos diarios y 2 horas mensuales a mantenimiento**
**para evitar problemas mayores en el futuro.**

---

**Sistema de Agendamiento - Internet Cordillera v1.0**

Última actualización: Mayo 2026

