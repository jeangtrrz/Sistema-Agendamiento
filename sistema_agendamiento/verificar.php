<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Instalación</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1e40af; margin-bottom: 20px; font-size: 28px; }
        .check-item { padding: 15px; margin: 10px 0; border-radius: 6px; display: flex; align-items: center; }
        .check-item.success { background: #ecfdf5; border-left: 4px solid #4CAF50; }
        .check-item.error { background: #fef2f2; border-left: 4px solid #f43f5e; }
        .check-item.warning { background: #fffbeb; border-left: 4px solid #FF9800; }
        .check-icon { font-size: 20px; margin-right: 15px; font-weight: bold; }
        .check-text { flex: 1; }
        .check-title { font-weight: 600; color: #1e293b; }
        .check-detail { font-size: 13px; color: #64748b; margin-top: 4px; }
        .button { display: inline-block; margin-top: 20px; padding: 12px 24px; background: #1e40af; color: white; text-decoration: none; border-radius: 6px; text-align: center; }
        .button:hover { background: #1e3a8a; }
        .section-title { color: #1e40af; margin-top: 30px; margin-bottom: 15px; font-size: 18px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Verificar Instalación del Sistema</h1>
        <p style="color: #64748b; margin-bottom: 20px;">Comprobando que el sistema esté correctamente instalado y configurado.</p>

        <?php
        // Verificaciones básicas
        $checks = [];

        // 1. PHP Version
        $checks[] = [
            'title' => 'Versión de PHP',
            'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'error',
            'message' => 'PHP ' . PHP_VERSION,
            'required' => 'PHP 8.3.31 (detectado: ' . PHP_VERSION . ')'
        ];

        // 2. Extensiones PHP
        $extensions = ['mysqli', 'json', 'session'];
        foreach ($extensions as $ext) {
            $checks[] = [
                'title' => 'Extensión: ' . $ext,
                'status' => extension_loaded($ext) ? 'success' : 'error',
                'message' => extension_loaded($ext) ? 'Disponible' : 'No disponible',
            ];
        }

        // 3. Permisos de carpetas
        $folders = [
            'config' => 'config/',
            'controllers' => 'controllers/',
            'models' => 'models/',
            'views' => 'views/',
            'assets' => 'assets/',
            'database' => 'database/'
        ];

        foreach ($folders as $name => $folder) {
            $path = __DIR__ . '/' . $folder;
            $writable = is_writable($path);
            $exists = is_dir($path);
            
            $checks[] = [
                'title' => 'Carpeta: ' . $folder,
                'status' => ($exists && is_readable($path)) ? 'success' : 'error',
                'message' => $exists ? 'Existe' : 'No existe',
            ];
        }

        // 4. Archivos principales
        $files = [
            'config/config.php' => 'Configuración principal',
            'config/session.php' => 'Gestión de sesiones',
            'index.php' => 'Página de login',
            'dashboard.php' => 'Dashboard',
            'database/init.sql' => 'Script SQL'
        ];

        foreach ($files as $file => $desc) {
            $path = __DIR__ . '/' . $file;
            $checks[] = [
                'title' => 'Archivo: ' . $desc,
                'status' => is_file($path) ? 'success' : 'error',
                'message' => $file,
            ];
        }

        // 5. Conexión a Base de Datos
        require_once 'config/config.php';
        $db_check = false;
        $db_message = 'No disponible';

        try {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$conn->connect_error) {
                $db_check = true;
                $db_message = 'Conectado correctamente';
                $conn->close();
            } else {
                $db_message = 'Error: ' . $conn->connect_error;
            }
        } catch (Exception $e) {
            $db_message = 'Error de conexión: ' . $e->getMessage();
        }

        $checks[] = [
            'title' => 'Base de Datos',
            'status' => $db_check ? 'success' : 'error',
            'message' => $db_message,
        ];

        // 6. Tablas de BD
        if ($db_check) {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                $result = $conn->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
                $row = $result->fetch_assoc();
                $tableCount = $row['count'];
                
                $checks[] = [
                    'title' => 'Tablas de Base de Datos',
                    'status' => $tableCount > 0 ? 'success' : 'error',
                    'message' => $tableCount . ' tablas detectadas',
                ];
                
                $conn->close();
            } catch (Exception $e) {
                $checks[] = [
                    'title' => 'Tablas de Base de Datos',
                    'status' => 'error',
                    'message' => 'No se pudo verificar',
                ];
            }
        }

        // Mostrar resultados
        ?>

        <div class="section-title">Requisitos del Sistema</div>
        
        <?php
        $all_ok = true;
        foreach ($checks as $check):
            if ($check['status'] !== 'success') $all_ok = false;
            $class = $check['status'];
            $icon = $class === 'success' ? '✓' : ($class === 'error' ? '✕' : '⚠');
        ?>
            <div class="check-item <?php echo $class; ?>">
                <div class="check-icon"><?php echo $icon; ?></div>
                <div class="check-text">
                    <div class="check-title"><?php echo $check['title']; ?></div>
                    <div class="check-detail"><?php echo $check['message']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0284c7; border-radius: 6px;">
            <strong style="color: #0c4a6e;">📋 Instrucciones:</strong>
            <ol style="margin-top: 10px; padding-left: 20px; color: #0c4a6e;">
                <li>Si todos los items están ✓, el sistema está listo</li>
                <li>Si hay ✕ rojo, es un error crítico que debe corregirse</li>
                <li>Si hay ⚠ naranja, es una advertencia que podrías ajustar</li>
                <li>Revisar README.md para más información</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="button">
                <?php echo $all_ok ? 'Ir a Login →' : 'Resolver Problemas'; ?>
            </a>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #f3f4f6; border-radius: 6px; font-size: 13px; color: #64748b;">
            <strong>Información del Sistema:</strong><br>
            PHP: <?php echo PHP_VERSION; ?><br>
            Sistema: <?php echo php_uname(); ?><br>
            Base de Datos: <?php echo DB_HOST . '/' . DB_NAME; ?><br>
            URL: <?php echo APP_URL; ?>
        </div>
    </div>
</body>
</html>
