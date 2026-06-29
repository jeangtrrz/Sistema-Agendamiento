<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $conn;
    private $usuarioModel;

    public function __construct() {
        $this->conn = getDBConnection();
        $this->usuarioModel = new Usuario($this->conn);
    }

    /**
     * Login
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email y contraseña requeridos'];
        }

        $usuario = $this->usuarioModel->getByEmail($email);

        if (!$usuario) {
            return ['success' => false, 'error' => 'Credenciales inválidas'];
        }

        if ($usuario['estado'] === 'inactivo') {
            return ['success' => false, 'error' => 'Usuario inactivo'];
        }

        if (!$this->usuarioModel->verifyPassword($password, $usuario['password'])) {
            return ['success' => false, 'error' => 'Credenciales inválidas'];
        }

        // Crear sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_profile'] = $usuario['perfil'];
        $_SESSION['user_estado'] = $usuario['estado'];

        return ['success' => true, 'message' => 'Bienvenido ' . $usuario['nombre']];
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword($user_id, $current_password, $new_password, $confirm_password) {
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            return ['success' => false, 'error' => 'Todos los campos son requeridos'];
        }

        if ($new_password !== $confirm_password) {
            return ['success' => false, 'error' => 'Las contraseñas no coinciden'];
        }

        if (strlen($new_password) < 6) {
            return ['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        $usuario = $this->usuarioModel->getById($user_id);

        if (!$this->usuarioModel->verifyPassword($current_password, $usuario['password'])) {
            return ['success' => false, 'error' => 'Contraseña actual incorrecta'];
        }

        if ($this->usuarioModel->update($user_id, ['password' => $new_password])) {
            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        }

        return ['success' => false, 'error' => 'Error al actualizar la contraseña'];
    }

    public function __destruct() {
        closeDBConnection($this->conn);
    }
}
?>
