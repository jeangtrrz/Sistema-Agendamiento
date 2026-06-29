<?php
/**
 * Script para generar hash bcrypt de la contraseña 123456
 */

$password = '123456';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Hash Generator</title></head>";
echo "<body style='font-family: Arial; margin: 20px;'>";
echo "<h2>Hash Bcrypt para contraseña: <code>123456</code></h2>";
echo "<p><strong>Hash generado:</strong></p>";
echo "<code style='background: #f0f0f0; padding: 10px; display: block; word-break: break-all;'>";
echo $hash;
echo "</code>";
echo "<p><strong>Verifica:</strong> " . (password_verify($password, $hash) ? "✅ OK" : "❌ ERROR") . "</p>";
echo "</body></html>";
?>
