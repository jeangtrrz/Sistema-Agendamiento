<?php
/**
 * Clase Evento - Modelo para gestión de eventos y compromisos del equipo
 */

class Evento {
    /** @var mysqli */
    private $conn;

    private string $table = 'eventos';

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function getById(int $id): ?array {
        $query = "SELECT e.*, u.nombre as responsable_nombre, c.nombre as creado_por_nombre 
                  FROM {$this->table} e
                  LEFT JOIN usuarios u ON e.responsable_id = u.id
                  LEFT JOIN usuarios c ON e.created_by = c.id
                  WHERE e.id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getAll($filtros = []): array {
        $query = "SELECT e.*, u.nombre as responsable_nombre
                  FROM {$this->table} e
                  LEFT JOIN usuarios u ON e.responsable_id = u.id
                  WHERE 1=1";

        $types = '';
        $values = [];

        if (!empty($filtros['fecha_inicio'])) {
            $query .= ' AND e.fecha_evento >= ?';
            $types .= 's';
            $values[] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $query .= ' AND e.fecha_evento <= ?';
            $types .= 's';
            $values[] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['responsable_id'])) {
            $query .= ' AND e.responsable_id = ?';
            $types .= 'i';
            $values[] = intval($filtros['responsable_id']);
        }

        if (!empty($filtros['tipo_evento'])) {
            $query .= ' AND e.tipo_evento = ?';
            $types .= 's';
            $values[] = $filtros['tipo_evento'];
        }

        if (!empty($filtros['estado'])) {
            $query .= ' AND e.estado = ?';
            $types .= 's';
            $values[] = $filtros['estado'];
        }

        $query .= ' ORDER BY e.fecha_evento ASC, e.hora_inicio ASC';

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

    public function getWeekEventos($responsable_id = null): array {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $saturday = date('Y-m-d', strtotime('saturday this week'));

        $query = "SELECT e.*, u.nombre as responsable_nombre
                  FROM {$this->table} e
                  LEFT JOIN usuarios u ON e.responsable_id = u.id
                  WHERE e.fecha_evento BETWEEN ? AND ? AND e.estado != 'cancelado'";

        $types = 'ss';
        $values = [$monday, $saturday];

        if ($responsable_id) {
            $query .= ' AND e.responsable_id = ?';
            $types .= 'i';
            $values[] = intval($responsable_id);
        }

        $query .= ' ORDER BY e.fecha_evento ASC, e.hora_inicio ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUpcoming($limit = 5): array {
        $query = "SELECT e.*, u.nombre as responsable_nombre
                  FROM {$this->table} e
                  LEFT JOIN usuarios u ON e.responsable_id = u.id
                  WHERE e.fecha_evento >= CURDATE() AND e.estado != 'cancelado'
                  ORDER BY e.fecha_evento ASC, e.hora_inicio ASC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): array {
        $query = "INSERT INTO {$this->table}
                  (titulo, tipo_evento, fecha_evento, hora_inicio, hora_fin, responsable_id, lugar, descripcion, estado, created_by)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param(
            'ssssssssis',
            $data['titulo'],
            $data['tipo_evento'],
            $data['fecha_evento'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['responsable_id'],
            $data['lugar'],
            $data['descripcion'],
            $data['estado'],
            $data['created_by']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }

        return ['success' => false, 'error' => $stmt->error];
    }

    public function update(int $id, array $data): array {
        $fields = [];
        $types = '';
        $values = [];

        if (isset($data['titulo'])) {
            $fields[] = 'titulo = ?';
            $types .= 's';
            $values[] = $data['titulo'];
        }

        if (isset($data['tipo_evento'])) {
            $fields[] = 'tipo_evento = ?';
            $types .= 's';
            $values[] = $data['tipo_evento'];
        }

        if (isset($data['fecha_evento'])) {
            $fields[] = 'fecha_evento = ?';
            $types .= 's';
            $values[] = $data['fecha_evento'];
        }

        if (isset($data['hora_inicio'])) {
            $fields[] = 'hora_inicio = ?';
            $types .= 's';
            $values[] = $data['hora_inicio'];
        }

        if (isset($data['hora_fin'])) {
            $fields[] = 'hora_fin = ?';
            $types .= 's';
            $values[] = $data['hora_fin'];
        }

        if (isset($data['responsable_id'])) {
            $fields[] = 'responsable_id = ?';
            $types .= 'i';
            $values[] = intval($data['responsable_id']);
        }

        if (isset($data['lugar'])) {
            $fields[] = 'lugar = ?';
            $types .= 's';
            $values[] = $data['lugar'];
        }

        if (isset($data['descripcion'])) {
            $fields[] = 'descripcion = ?';
            $types .= 's';
            $values[] = $data['descripcion'];
        }

        if (isset($data['estado'])) {
            $fields[] = 'estado = ?';
            $types .= 's';
            $values[] = $data['estado'];
        }

        if (empty($fields)) {
            return ['success' => false, 'error' => 'No hay campos para actualizar'];
        }

        $types .= 'i';
        $values[] = $id;

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => $stmt->error];
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
?>
