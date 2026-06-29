<?php
/**
 * Controlador de Usuarios
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $conn;
    private $usuarioModel;

    public function __construct() {
        $this->conn = getDBConnection();
        $this->usuarioModel = new Usuario($this->conn);
    }

    /**
     * Crear usuario (solo admin)
     */
    public function create($data) {
        if (!isAdmin()) {
            return ['success' => false, 'error' => 'No tiene permisos'];
        }

        // Validaciones
        $validations = $this->validateUsuarioData($data);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        // Verificar que el email no exista
        if ($this->usuarioModel->getByEmail($data['email'])) {
            return ['success' => false, 'error' => 'El email ya existe'];
        }

        $usuarioData = [
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'password' => $data['password'],
            'perfil' => $data['perfil'],
            'estado' => $data['estado'] ?? 'activo'
        ];

        $id = $this->usuarioModel->create($usuarioData);

        if ($id) {
            return ['success' => true, 'id' => $id, 'message' => 'Usuario creado correctamente'];
        }

        return ['success' => false, 'error' => 'Error al crear el usuario'];
    }

    /**
     * Actualizar usuario (solo admin)
     */
    public function update($id, $data) {
        if (!isAdmin()) {
            return ['success' => false, 'error' => 'No tiene permisos'];
        }

        // Validaciones
        $validations = $this->validateUsuarioData($data, true);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        // Si cambió el email, verificar que no exista
        if (isset($data['email'])) {
            $usuario = $this->usuarioModel->getByEmail($data['email']);
            if ($usuario && $usuario['id'] != $id) {
                return ['success' => false, 'error' => 'El email ya existe'];
            }
        }

        if ($this->usuarioModel->update($id, $data)) {
            return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
        }

        return ['success' => false, 'error' => 'Error al actualizar el usuario'];
    }

    /**
     * Eliminar usuario (solo admin)
     */
    public function delete($id) {
        if (!isAdmin()) {
            return ['success' => false, 'error' => 'No tiene permisos'];
        }

        // No permitir eliminar a uno mismo
        if ($id == $_SESSION['user_id']) {
            return ['success' => false, 'error' => 'No puede eliminarse a sí mismo'];
        }

        if ($this->usuarioModel->delete($id)) {
            return ['success' => true, 'message' => 'Usuario eliminado correctamente'];
        }

        return ['success' => false, 'error' => 'Error al eliminar el usuario'];
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        $usuario = $this->usuarioModel->getById($id);
        if ($usuario) {
            unset($usuario['password']);
        }
        return $usuario;
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAll() {
        $usuarios = $this->usuarioModel->getAll();
        foreach ($usuarios as &$usuario) {
            unset($usuario['password']);
        }
        return $usuarios;
    }

    /**
     * Obtener todos los técnicos activos
     */
    public function getTechnicians() {
        return $this->usuarioModel->getTechnicians();
    }

    /**
     * Obtener estadísticas
     */
    public function getStats() {
        return $this->usuarioModel->getStats();
    }

    /**
     * Validar datos de usuario
     */
    private function validateUsuarioData($data, $partial = false) {
        $required = $partial 
            ? ['nombre', 'email', 'perfil']
            : ['nombre', 'email', 'password', 'perfil'];

        foreach ($required as $field) {
            if (!$partial || isset($data[$field])) {
                if (empty($data[$field] ?? '')) {
                    return ['valid' => false, 'error' => "El campo $field es requerido"];
                }
            }
        }

        // Validar nombre
        if (isset($data['nombre']) && strlen($data['nombre']) < 3) {
            return ['valid' => false, 'error' => 'El nombre debe tener al menos 3 caracteres'];
        }

        // Validar email
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Email inválido'];
        }

        // Validar contraseña
        if (isset($data['password']) && strlen($data['password']) < 6) {
            return ['valid' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        // Validar perfil
        if (isset($data['perfil']) && !in_array($data['perfil'], array_keys(PERFILES))) {
            return ['valid' => false, 'error' => 'Perfil inválido'];
        }

        // Validar estado
        if (isset($data['estado']) && !in_array($data['estado'], ['activo', 'inactivo'])) {
            return ['valid' => false, 'error' => 'Estado inválido'];
        }

        return ['valid' => true];
    }

    public function __destruct() {
        closeDBConnection($this->conn);
    }
}
?>
