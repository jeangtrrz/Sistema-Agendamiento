<?php
/**
 * Controlador de Citas
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cita.php';

class CitaController {
    private $conn;
    private $citaModel;

    public function __construct() {
        $this->conn = getDBConnection();
        $this->citaModel = new Cita($this->conn);
    }

    /**
     * Crear cita
     */
    public function create($data) {
        // Validaciones
        $validations = $this->validateCitaData($data);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        // Calcular hora fin basada en duración
        $hora_inicio = new DateTime($data['fecha_cita'] . ' ' . $data['hora_inicio']);
        $hora_fin = clone $hora_inicio;
        $hora_fin->add(new DateInterval('PT' . DURACION_CITA_MINUTOS . 'M'));

        $citaData = [
            'cliente_nombre' => $data['cliente_nombre'],
            'cliente_telefono' => $data['cliente_telefono'],
            'cliente_email' => $data['cliente_email'] ?? '',
            'cliente_direccion' => $data['cliente_direccion'] ?? '',
            'tipo_cita' => $data['tipo_cita'],
            'fecha_cita' => $data['fecha_cita'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $hora_fin->format('H:i:s'),
            'tecnico_id' => $data['tecnico_id'],
            'descripcion' => $data['descripcion'] ?? '',
            'estado' => 'pendiente',
            'created_by' => $_SESSION['user_id']
        ];

        return $this->citaModel->create($citaData);
    }

    /**
     * Actualizar cita
     */
    public function update($id, $data): array {
        // Validar permisos
        if (!isAdmin()) {
            return ['success' => false, 'error' => 'No tiene permisos para editar citas'];
        }

        // Validaciones
        $validations = $this->validateCitaData($data, true);
        if (!$validations['valid']) {
            return ['success' => false, 'error' => $validations['error']];
        }

        // Calcular hora fin si se cambia hora inicio
        if (isset($data['hora_inicio']) && isset($data['fecha_cita'])) {
            $hora_inicio = new DateTime($data['fecha_cita'] . ' ' . $data['hora_inicio']);
            $hora_fin = clone $hora_inicio;
            $hora_fin->add(new DateInterval('PT' . DURACION_CITA_MINUTOS . 'M'));
            $data['hora_fin'] = $hora_fin->format('H:i:s');
        }

        return $this->citaModel->update($id, $data);
    }

    /**
     * Eliminar cita (solo admin)
     */
    public function delete($id) {
        if (!isAdmin()) {
            return ['success' => false, 'error' => 'No tiene permisos para eliminar citas'];
        }

        if ($this->citaModel->delete($id)) {
            return ['success' => true, 'message' => 'Cita eliminada'];
        }

        return ['success' => false, 'error' => 'Error al eliminar la cita'];
    }

    /**
     * Marcar cita como completada (técnico o admin)
     */
    public function markCompleted($id, $observaciones = '') {
        $cita = $this->citaModel->getById($id);

        // Validar permisos: solo si es el técnico asignado o es admin
        if (!isAdmin() && $cita['tecnico_id'] != $_SESSION['user_id']) {
            return ['success' => false, 'error' => 'No tiene permisos para completar esta cita'];
        }

        if ($this->citaModel->markAsCompleted($id, $observaciones)) {
            return ['success' => true, 'message' => 'Cita marcada como completada'];
        }

        return ['success' => false, 'error' => 'Error al completar la cita'];
    }

    /**
     * Obtener cita por ID
     */
    public function getById($id) {
        return $this->citaModel->getById($id);
    }

    /**
     * Obtener todas las citas con filtros
     */
    public function getAll($filtros = []) {
        // Si es técnico, solo ver sus citas
        if (isTechnician() && !isAdmin()) {
            $filtros['tecnico_id'] = $_SESSION['user_id'];
        }

        return $this->citaModel->getAll($filtros);
    }

    /**
     * Obtener citas de la semana
     */
    public function getWeekCitas($technician_id = null) {
        if (isTechnician() && !isAdmin()) {
            $technician_id = $_SESSION['user_id'];
        }

        return $this->citaModel->getWeekCitas($technician_id);
    }

    /**
     * Validar datos de cita
     */
    private function validateCitaData($data, $partial = false) {
        $required = $partial 
            ? ['tipo_cita', 'fecha_cita', 'hora_inicio', 'tecnico_id']
            : ['cliente_nombre', 'cliente_telefono', 'cliente_direccion', 'tipo_cita', 'fecha_cita', 'hora_inicio', 'tecnico_id'];

        foreach ($required as $field) {
            if (!$partial || isset($data[$field])) {
                if (empty($data[$field] ?? '')) {
                    return ['valid' => false, 'error' => "El campo $field es requerido"];
                }
            }
        }

        // Validar fecha
        if (isset($data['fecha_cita'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_cita']);
            if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_cita']) {
                return ['valid' => false, 'error' => 'Formato de fecha inválido'];
            }

            // No permitir fechas pasadas
            if ($fecha < new DateTime('today')) {
                return ['valid' => false, 'error' => 'No se pueden agendar citas en fechas pasadas'];
            }

            // Validar que sea día de operación (Lunes a Sábado)
            $dayOfWeek = $fecha->format('N'); // 1=Lunes, 7=Domingo
            if ($dayOfWeek == 7) {
                return ['valid' => false, 'error' => 'No se pueden agendar citas los domingos'];
            }
        }

        // Validar hora
        if (isset($data['hora_inicio'])) {
            // Normalizar hora: aceptar tanto HH:MM como HH:MM:SS, convertir a HH:MM
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data['hora_inicio'])) {
                return ['valid' => false, 'error' => 'Formato de hora inválido'];
            }

            // Extraer solo HH:MM (sin segundos)
            $data['hora_inicio'] = substr($data['hora_inicio'], 0, 5);

            // Validar que la hora esté dentro del horario de operación
            $hora = DateTime::createFromFormat('H:i', $data['hora_inicio']);
            $horaInicio = DateTime::createFromFormat('H:i', HORA_INICIO);
            $horaFin = DateTime::createFromFormat('H:i', HORA_FIN);

            if ($hora < $horaInicio || $hora >= $horaFin) {
                return ['valid' => false, 'error' => 'La hora debe estar entre ' . HORA_INICIO . ' y ' . HORA_FIN];
            }
        }

        // Validar tipo de cita
        if (isset($data['tipo_cita']) && !in_array($data['tipo_cita'], array_keys(TIPOS_CITA))) {
            return ['valid' => false, 'error' => 'Tipo de cita inválido'];
        }

        return ['valid' => true];
    }

    public function __destruct() {
        closeDBConnection($this->conn);
    }
}
?>
