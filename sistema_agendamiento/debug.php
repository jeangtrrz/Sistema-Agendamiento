<?php
/**
 * Script de Depuración - Verificar Conexión a BD y Datos
 * IMPORTANTE: Elimina este archivo después de usar, no lo dejes en producción
 */

// Prevenir caché del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Debug - Verificación de BD</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }";
echo ".success { border-left: 4px solid #4CAF50; }";
echo ".error { border-left: 4px solid #f44336; color: #c62828; }";
echo ".info { border-left: 4px solid #2196F3; }";
echo "pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }";
echo "h2 { color: #333; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 Verificación de Conexión a Base de Datos</h1>";

// 1. Verificar credenciales
echo "<div class='box info'>";
echo "<h2>1. Credenciales configuradas en config.php:</h2>";
echo "<pre>";
echo "Host: " . DB_HOST . "\n";
echo "Usuario: " . DB_USER . "\n";
echo "Base de datos: " . DB_NAME . "\n";
echo "Contraseña: " . (DB_PASS ? "***[configurada]***" : "[vacía]") . "\n";
echo "</pre>";
echo "</div>";

// 2. Intentar conexión
echo "<div class='box'>";
echo "<h2>2. Intento de conexión:</h2>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "<div class='error'>";
        echo "❌ <strong>Error de conexión:</strong><br>";
        echo "<pre>" . htmlspecialchars($conn->connect_error) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "✅ <strong>Conexión exitosa a la base de datos</strong><br>";
        echo "<pre>Charset: " . $conn->get_charset()->charset . "</pre>";
        echo "</div>";
        
        // 3. Verificar tabla usuarios
        echo "<div class='box'>";
        echo "<h2>3. Verificar tabla 'usuarios':</h2>";
        
        $result = $conn->query("SHOW TABLES LIKE 'usuarios'");
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='success'>";
            echo "✅ <strong>Tabla 'usuarios' existe</strong>";
            echo "</div>";
            
            // 4. Contar usuarios
            echo "<div class='box'>";
            echo "<h2>4. Verificar datos de usuarios:</h2>";
            
            $count_result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
            $count_row = $count_result->fetch_assoc();
            
            echo "<p><strong>Total de usuarios en BD:</strong> " . $count_row['total'] . "</p>";
            
            if ($count_row['total'] > 0) {
                echo "<div class='success'>";
                echo "✅ <strong>Base de datos tiene usuarios</strong>";
                echo "</div>";
                
                // 5. Listar usuarios
                echo "<div class='box'>";
                echo "<h2>5. Lista de usuarios:</h2>";
                echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
                echo "<tr style='background: #f0f0f0;'>";
                echo "<th>ID</th><th>Nombre</th><th>Email</th><th>Perfil</th><th>Estado</th><th>Password Hash</th>";
                echo "</tr>";
                
                $users_result = $conn->query("SELECT id, nombre, email, perfil, estado, password FROM usuarios");
                
                if ($users_result) {
                    while ($user = $users_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['perfil']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['estado']) . "</td>";
                        echo "<td><code style='font-size: 11px;'>" . substr(htmlspecialchars($user['password']), 0, 30) . "...</code></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='error'>Error al obtener usuarios</td></tr>";
                }
                
                echo "</table>";
                echo "</div>";
                
                // 6. Prueba de autenticación
                echo "<div class='box'>";
                echo "<h2>6. Prueba de autenticación:</h2>";
                
                $test_email = 'jean@internetcordillera.cl';
                $test_password = '123456';
                
                $user = null;
                $query = "SELECT id, nombre, email, password, perfil, estado FROM usuarios WHERE email = ?";
                $stmt = $conn->prepare($query);
                
                if ($stmt) {
                    $stmt->bind_param("s", $test_email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();
                    
                    if ($user) {
                        echo "<p><strong>Usuario encontrado:</strong> " . htmlspecialchars($user['nombre']) . " (" . htmlspecialchars($user['email']) . ")</p>";
                        
                        $password_valid = password_verify($test_password, $user['password']);
                        
                        if ($password_valid) {
                            echo "<div class='success'>";
                            echo "✅ <strong>Contraseña válida para usuario de prueba</strong>";
                            echo "</div>";
                        } else {
                            echo "<div class='error'>";
                            echo "❌ <strong>Contraseña inválida para usuario de prueba</strong>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='error'>";
                        echo "❌ <strong>Usuario no encontrado:</strong> " . htmlspecialchars($test_email);
                        echo "</div>";
                    }
                } else {
                    echo "<div class='error'>";
                    echo "❌ <strong>Error en preparar consulta:</strong> " . htmlspecialchars($conn->error);
                    echo "</div>";
                }
                
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "❌ <strong>La tabla 'usuarios' está vacía, los datos del init.sql no se importaron</strong>";
                echo "</div>";
                
                echo "<div class='box info'>";
                echo "<h2>Solución:</h2>";
                echo "<ol>";
                echo "<li>Abre cPanel → phpMyAdmin</li>";
                echo "<li>Selecciona la BD 'internet_agenda'</li>";
                echo "<li>Ve a la pestaña 'Importar'</li>";
                echo "<li>Selecciona el archivo <code>database/init.sql</code></li>";
                echo "<li>Haz clic en 'Ir' para importar</li>";
                echo "<li>Espera a que termine y actualiza esta página</li>";
                echo "</ol>";
                echo "</div>";
            }
        } else {
            echo "<div class='error'>";
            echo "❌ <strong>Tabla 'usuarios' no existe</strong>";
            echo "</div>";
            
            echo "<div class='box info'>";
            echo "<h2>Solución:</h2>";
            echo "<p>Debes importar el archivo <code>database/init.sql</code> en cPanel → phpMyAdmin</p>";
            echo "<ol>";
            echo "<li>Abre cPanel → phpMyAdmin</li>";
            echo "<li>Selecciona la BD 'internet_agenda'</li>";
            echo "<li>Ve a la pestaña 'Importar'</li>";
            echo "<li>Selecciona el archivo <code>database/init.sql</code></li>";
            echo "<li>Haz clic en 'Ir' para importar</li>";
            echo "</ol>";
            echo "</div>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ <strong>Excepción:</strong><br>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}

echo "<div class='box info'>";
echo "<h2>⚠️ Seguridad:</h2>";
echo "<p><strong>IMPORTANTE:</strong> Después de terminar con la depuración, <strong>elimina este archivo (debug.php)</strong> del servidor.</p>";
echo "<p>No dejes archivos de depuración en producción, pueden exponer información sensible.</p>";
echo "</div>";

echo "</body>";
echo "</html>";
?>
