<?php
/**
 * Controlador de Eventos y compromisos del equipo
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Evento.php';

class EventoController {
    /** @var mysqli */
    private $conn;

    /** @var Evento */
    private $eventoModel;

    public function __construct() {
        $this->conn = getDBConnection();
        $this->eventoModel = new Evento($this->conn);
    }

    public function create(array $data): array {
        $validations = $this->validateEventoData($data);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        if (empty($data['hora_fin'] ?? '')) {
            $horaInicio = new DateTime($data['fecha_evento'] . ' ' . $data['hora_inicio']);
            $horaFin = clone $horaInicio;
            $horaFin->add(new DateInterval('PT1H'));
            $data['hora_fin'] = $horaFin->format('H:i:s');
        }

        $eventoData = [
            'titulo' => $data['titulo'],
            'tipo_evento' => $data['tipo_evento'],
            'fecha_evento' => $data['fecha_evento'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'responsable_id' => intval($data['responsable_id'] ?? 0),
            'lugar' => $data['lugar'] ?? '',
            'descripcion' => $data['descripcion'] ?? '',
            'estado' => $data['estado'] ?? 'programado',
            'created_by' => $_SESSION['user_id']
        ];

        return $this->eventoModel->create($eventoData);
    }

    public function update(int $id, array $data): array {
        $validations = $this->validateEventoData($data, true);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        if (isset($data['hora_inicio']) && !isset($data['hora_fin']) && isset($data['fecha_evento'])) {
            $horaInicio = new DateTime($data['fecha_evento'] . ' ' . $data['hora_inicio']);
            $horaFin = clone $horaInicio;
            $horaFin->add(new DateInterval('PT1H'));
            $data['hora_fin'] = $horaFin->format('H:i:s');
        }

        return $this->eventoModel->update($id, $data);
    }

    public function delete(int $id): bool {
        return $this->eventoModel->delete($id);
    }

    public function getById(int $id): ?array {
        return $this->eventoModel->getById($id);
    }

    public function getAll(array $filtros = []): array {
        return $this->eventoModel->getAll($filtros);
    }

    public function getWeekEventos(?int $responsable_id = null): array {
        return $this->eventoModel->getWeekEventos($responsable_id);
    }

    public function getUpcoming(int $limit = 5): array {
        return $this->eventoModel->getUpcoming($limit);
    }

    private function validateEventoData(array $data, bool $partial = false): array {
        $required = $partial
            ? ['titulo', 'tipo_evento', 'fecha_evento', 'hora_inicio', 'responsable_id']
            : ['titulo', 'tipo_evento', 'fecha_evento', 'hora_inicio', 'responsable_id'];

        foreach ($required as $field) {
            if (empty($data[$field] ?? '')) {
                return ['valid' => false, 'error' => "El campo $field es requerido"];
            }
        }

        if (isset($data['fecha_evento'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_evento']);
            if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_evento']) {
                return ['valid' => false, 'error' => 'Formato de fecha inválido'];
            }
        }

        if (isset($data['hora_inicio'])) {
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data['hora_inicio'])) {
                return ['valid' => false, 'error' => 'Formato de hora inválido'];
            }
        }

        if (isset($data['tipo_evento']) && !in_array($data['tipo_evento'], array_keys(TIPOS_EVENTO))) {
            return ['valid' => false, 'error' => 'Tipo de evento inválido'];
        }

        return ['valid' => true];
    }

    public function __destruct() {
        closeDBConnection($this->conn);
    }
}
?>
