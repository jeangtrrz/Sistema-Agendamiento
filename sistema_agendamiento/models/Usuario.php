<?php
/**
 * Clase Usuario - Modelo para gestión de usuarios
 */

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email): ?array {
        $query = "SELECT id, nombre, email, password, perfil, estado 
                  FROM {$this->table} 
                  WHERE email = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id): ?array {
        $query = "SELECT id, nombre, email, perfil, estado, created_at 
                  FROM {$this->table} 
                  WHERE id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAll(): array {
        $query = "SELECT id, nombre, email, perfil, estado, created_at, updated_at 
                  FROM {$this->table} 
                  ORDER BY nombre ASC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener todos los técnicos activos
     */
    public function getTechnicians(): array {
        $query = "SELECT id, nombre, email, perfil, estado 
                  FROM {$this->table} 
                  WHERE perfil = 'tecnico' AND estado = 'activo'
                  ORDER BY nombre ASC";
        
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Crear nuevo usuario
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nombre, email, password, perfil, estado) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", 
            $data['nombre'],
            $data['email'],
            $hashedPassword,
            $data['perfil'],
            $data['estado']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }

    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        $fields = [];
        $types = "i";
        $values = [$id];

        if (isset($data['nombre'])) {
            $fields[] = "nombre = ?";
            $types .= "s";
            $values[] = $data['nombre'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $types .= "s";
            $values[] = $data['email'];
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = ?";
            $types .= "s";
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $values[] = $hashedPassword;
        }

        if (isset($data['perfil'])) {
            $fields[] = "perfil = ?";
            $types .= "s";
            $values[] = $data['perfil'];
        }

        if (isset($data['estado'])) {
            $fields[] = "estado = ?";
            $types .= "s";
            $values[] = $data['estado'];
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Reordenar valores con id al final
        $finalValues = array_merge(array_slice($values, 1), [$id]);
        
        $stmt->bind_param($types, ...$finalValues);
        
        return $stmt->execute();
    }

    /**
     * Eliminar usuario
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    /**
     * Verificar contraseña
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Contar usuarios por perfil
     */
    public function countByProfile($profile) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE perfil = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $profile);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }

    /**
     * Obtener estadísticas de usuarios
     */
    public function getStats(): array {
        $query = "SELECT 
                    perfil,
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                  FROM {$this->table}
                  GROUP BY perfil";
        
        $result = $this->conn->query($query);
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC) ?? [];
    }
}
?>
