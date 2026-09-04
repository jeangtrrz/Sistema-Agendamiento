<?php
/**
 * API de eventos y compromisos del equipo
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/EventoController.php';

header('Content-Type: application/json; charset=utf-8');
requireAuth();

$controller = new EventoController();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'getEventos':
            $filtros = [
                'fecha_inicio' => $_REQUEST['fecha_inicio'] ?? '',
                'fecha_fin' => $_REQUEST['fecha_fin'] ?? '',
                'responsable_id' => $_REQUEST['responsable_id'] ?? '',
                'tipo_evento' => $_REQUEST['tipo_evento'] ?? '',
                'estado' => $_REQUEST['estado'] ?? ''
            ];
            $eventos = $controller->getAll($filtros);
            echo json_encode(['success' => true, 'data' => $eventos]);
            break;

        case 'getEvento':
            $id = intval($_REQUEST['id'] ?? 0);
            $evento = $controller->getById($id);
            if ($evento) {
                echo json_encode(['success' => true, 'data' => $evento]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Evento no encontrado']);
            }
            break;

        case 'createEvento':
            $data = [
                'titulo' => $_POST['titulo'] ?? '',
                'tipo_evento' => $_POST['tipo_evento'] ?? '',
                'fecha_evento' => $_POST['fecha_evento'] ?? '',
                'hora_inicio' => $_POST['hora_inicio'] ?? '',
                'hora_fin' => $_POST['hora_fin'] ?? '',
                'responsable_id' => intval($_POST['responsable_id'] ?? 0),
                'lugar' => $_POST['lugar'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'estado' => $_POST['estado'] ?? 'programado'
            ];
            $result = $controller->create($data);
            echo json_encode($result);
            break;

        case 'updateEvento':
            $id = intval($_REQUEST['id'] ?? 0);
            $data = [];
            if (isset($_POST['titulo'])) $data['titulo'] = $_POST['titulo'];
            if (isset($_POST['tipo_evento'])) $data['tipo_evento'] = $_POST['tipo_evento'];
            if (isset($_POST['fecha_evento'])) $data['fecha_evento'] = $_POST['fecha_evento'];
            if (isset($_POST['hora_inicio'])) $data['hora_inicio'] = $_POST['hora_inicio'];
            if (isset($_POST['hora_fin'])) $data['hora_fin'] = $_POST['hora_fin'];
            if (isset($_POST['responsable_id'])) $data['responsable_id'] = intval($_POST['responsable_id']);
            if (isset($_POST['lugar'])) $data['lugar'] = $_POST['lugar'];
            if (isset($_POST['descripcion'])) $data['descripcion'] = $_POST['descripcion'];
            if (isset($_POST['estado'])) $data['estado'] = $_POST['estado'];
            $result = $controller->update($id, $data);
            echo json_encode($result);
            break;

        case 'deleteEvento':
            $id = intval($_REQUEST['id'] ?? 0);
            $success = $controller->delete($id);
            echo json_encode(['success' => $success, 'message' => $success ? 'Evento eliminado' : 'Error al eliminar']);
            break;

        case 'getWeekEventos':
            $responsable_id = null;
            if (isset($_REQUEST['responsable_id'])) {
                $responsable_id = intval($_REQUEST['responsable_id']);
            }
            $eventos = $controller->getWeekEventos($responsable_id);
            echo json_encode(['success' => true, 'data' => $eventos]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
