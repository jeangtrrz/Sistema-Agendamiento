<?php
/**
 * Clase Cita - Modelo para gestión de citas
 */

class Cita {
    private $conn;
    private $table = 'citas';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtener cita por ID
     */
    public function getById($id): ?array {
        $query = "SELECT c.*, u.nombre as tecnico_nombre, u.email as tecnico_email 
                  FROM {$this->table} c
                  LEFT JOIN usuarios u ON c.tecnico_id = u.id
                  WHERE c.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Obtener todas las citas
     */
    public function getAll($filtros = []): array {
        $query = "SELECT c.*, u.nombre as tecnico_nombre 
                  FROM {$this->table} c
                  LEFT JOIN usuarios u ON c.tecnico_id = u.id
                  WHERE 1=1";
        
        $types = "";
        $values = [];

        if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
            $query .= " AND c.fecha_cita >= ?";
            $types .= "s";
            $values[] = $filtros['fecha_inicio'];
        }

        if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
            $query .= " AND c.fecha_cita <= ?";
            $types .= "s";
            $values[] = $filtros['fecha_fin'];
        }

        if (isset($filtros['tecnico_id']) && !empty($filtros['tecnico_id'])) {
            $query .= " AND c.tecnico_id = ?";
            $types .= "i";
            $values[] = $filtros['tecnico_id'];
        }

        if (isset($filtros['tipo_cita']) && !empty($filtros['tipo_cita'])) {
            $query .= " AND c.tipo_cita = ?";
            $types .= "s";
            $values[] = $filtros['tipo_cita'];
        }

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $query .= " AND c.estado = ?";
            $types .= "s";
            $values[] = $filtros['estado'];
        }

        $query .= " ORDER BY c.fecha_cita DESC, c.hora_inicio DESC";

        if (!empty($types)) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($query);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener citas de la semana actual
     */
    public function getWeekCitas($technician_id = null): array {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $saturday = date('Y-m-d', strtotime('saturday this week'));

        $query = "SELECT c.*, u.nombre as tecnico_nombre 
                  FROM {$this->table} c
                  LEFT JOIN usuarios u ON c.tecnico_id = u.id
                  WHERE c.fecha_cita BETWEEN ? AND ? AND c.estado != 'cancelada'";

        $types = "ss";
        $values = [$monday, $saturday];

        if ($technician_id) {
            $query .= " AND c.tecnico_id = ?";
            $types .= "i";
            $values[] = $technician_id;
        }

        $query .= " ORDER BY c.fecha_cita ASC, c.hora_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Crear nueva cita
     */
    public function create($data) {
        // Validar que no exista cita en el mismo horario
        if (!$this->isTimeSlotAvailable($data['fecha_cita'], $data['hora_inicio'], $data['tecnico_id'])) {
            return ['success' => false, 'error' => 'El horario no está disponible'];
        }

        $query = "INSERT INTO {$this->table} 
                  (cliente_nombre, cliente_telefono, cliente_email, cliente_direccion, 
                   tipo_cita, fecha_cita, hora_inicio, hora_fin, tecnico_id, 
                   descripcion, estado, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssssissi",
            $data['cliente_nombre'],
            $data['cliente_telefono'],
            $data['cliente_email'],
            $data['cliente_direccion'],
            $data['tipo_cita'],
            $data['fecha_cita'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['tecnico_id'],
            $data['descripcion'],
            $data['estado'],
            $data['created_by']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }

        return ['success' => false, 'error' => $stmt->error];
    }

    /**
     * Actualizar cita
     */
    public function update($id, $data): array {
        // Si cambió la fecha u hora, validar disponibilidad
        if ((isset($data['fecha_cita']) || isset($data['hora_inicio'])) && isset($data['tecnico_id'])) {
            $cita = $this->getById($id);
            $fecha = $data['fecha_cita'] ?? $cita['fecha_cita'];
            $hora = $data['hora_inicio'] ?? $cita['hora_inicio'];
            $tecnico = $data['tecnico_id'] ?? $cita['tecnico_id'];

            if (!$this->isTimeSlotAvailable($fecha, $hora, $tecnico, $id)) {
                return ['success' => false, 'error' => 'El horario no está disponible'];
            }
        }

        $fields = [];
        $types = "i";
        $values = [$id];

        if (isset($data['cliente_nombre'])) {
            $fields[] = "cliente_nombre = ?";
            $types .= "s";
            $values[] = $data['cliente_nombre'];
        }

        if (isset($data['cliente_telefono'])) {
            $fields[] = "cliente_telefono = ?";
            $types .= "s";
            $values[] = $data['cliente_telefono'];
        }

        if (isset($data['cliente_email'])) {
            $fields[] = "cliente_email = ?";
            $types .= "s";
            $values[] = $data['cliente_email'];
        }

        if (isset($data['cliente_direccion'])) {
            $fields[] = "cliente_direccion = ?";
            $types .= "s";
            $values[] = $data['cliente_direccion'];
        }

        if (isset($data['fecha_cita'])) {
            $fields[] = "fecha_cita = ?";
            $types .= "s";
            $values[] = $data['fecha_cita'];
        }

        if (isset($data['hora_inicio'])) {
            $fields[] = "hora_inicio = ?";
            $types .= "s";
            $values[] = $data['hora_inicio'];
        }

        if (isset($data['hora_fin'])) {
            $fields[] = "hora_fin = ?";
            $types .= "s";
            $values[] = $data['hora_fin'];
        }

        if (isset($data['tecnico_id'])) {
            $fields[] = "tecnico_id = ?";
            $types .= "i";
            $values[] = $data['tecnico_id'];
        }

        if (isset($data['estado'])) {
            $fields[] = "estado = ?";
            $types .= "s";
            $values[] = $data['estado'];
        }

        if (isset($data['descripcion'])) {
            $fields[] = "descripcion = ?";
            $types .= "s";
            $values[] = $data['descripcion'];
        }

        if (isset($data['observaciones'])) {
            $fields[] = "observaciones = ?";
            $types .= "s";
            $values[] = $data['observaciones'];
        }

        if (empty($fields)) {
            return ['success' => false, 'error' => 'No hay campos para actualizar'];
        }

        $query = "UPDATE {$this->table} SET " . implode(", ", $fields) . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Reordenar valores y tipos con id al final
        $finalTypes = substr($types, 1) . "i";  // Quitar el "i" inicial y agregar "i" al final
        $finalValues = array_merge(array_slice($values, 1), [$id]);  // Quitar id del inicio y agregar al final

        $stmt->bind_param($finalTypes, ...$finalValues);

        if ($stmt->execute()) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => $stmt->error];
    }

    /**
     * Eliminar cita (solo admin)
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /**
     * Validar disponibilidad de horario
     */
    public function isTimeSlotAvailable($fecha, $hora, $tecnico_id, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE fecha_cita = ? AND hora_inicio = ? AND tecnico_id = ? AND estado != 'cancelada'";

        $types = "ssi";
        $values = [$fecha, $hora, $tecnico_id];

        if ($exclude_id) {
            $query .= " AND id != ?";
            $types .= "i";
            $values[] = $exclude_id;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] == 0;
    }

    /**
     * Obtener estadísticas de citas
     */
    public function getStats($filtros = []): array {
        $query = "SELECT 
                    estado,
                    tipo_cita,
                    COUNT(*) as total
                  FROM {$this->table}
                  WHERE 1=1";

        $types = "";
        $values = [];

        if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
            $query .= " AND fecha_cita >= ?";
            $types .= "s";
            $values[] = $filtros['fecha_inicio'];
        }

        if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
            $query .= " AND fecha_cita <= ?";
            $types .= "s";
            $values[] = $filtros['fecha_fin'];
        }

        $query .= " GROUP BY estado, tipo_cita";

        if (!empty($types)) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($query);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtener citas por técnico
     */
    public function getCitasByTechnician($tecnico_id, $filtros = []): array {
        $query = "SELECT c.*, u.nombre as tecnico_nombre 
                  FROM {$this->table} c
                  LEFT JOIN usuarios u ON c.tecnico_id = u.id
                  WHERE c.tecnico_id = ?";

        $types = "i";
        $values = [$tecnico_id];

        if (isset($filtros['fecha_inicio']) && !empty($filtros['fecha_inicio'])) {
            $query .= " AND c.fecha_cita >= ?";
            $types .= "s";
            $values[] = $filtros['fecha_inicio'];
        }

        if (isset($filtros['fecha_fin']) && !empty($filtros['fecha_fin'])) {
            $query .= " AND c.fecha_cita <= ?";
            $types .= "s";
            $values[] = $filtros['fecha_fin'];
        }

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $query .= " AND c.estado = ?";
            $types .= "s";
            $values[] = $filtros['estado'];
        }

        $query .= " ORDER BY c.fecha_cita DESC, c.hora_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Marcar cita como completada
     */
    public function markAsCompleted($id, $observaciones = '') {
        $estado = 'completada';
        $query = "UPDATE {$this->table} SET estado = ?, observaciones = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $estado, $observaciones, $id);

        return $stmt->execute();
    }
}
?>
