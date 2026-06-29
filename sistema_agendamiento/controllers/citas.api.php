<?php
/**
 * API de Citas - Endpoints
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/CitaController.php';

header('Content-Type: application/json; charset=utf-8');

requireAuth();

$controller = new CitaController();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'getCitas':
            $filtros = [
                'fecha_inicio' => $_REQUEST['fecha_inicio'] ?? '',
                'fecha_fin' => $_REQUEST['fecha_fin'] ?? '',
                'tecnico_id' => $_REQUEST['tecnico_id'] ?? '',
                'tipo_cita' => $_REQUEST['tipo_cita'] ?? '',
                'estado' => $_REQUEST['estado'] ?? ''
            ];
            
            $citas = $controller->getAll($filtros);
            
            // Agregar permisos a cada cita
            foreach ($citas as &$cita) {
                $cita['can_edit'] = isAdmin();
                $cita['can_delete'] = isAdmin();
                $cita['can_complete'] = (isAdmin() || ($cita['tecnico_id'] == $_SESSION['user_id'] && $cita['estado'] === 'pendiente'));
            }
            
            echo json_encode(['success' => true, 'data' => $citas]);
            break;

        case 'getCita':
            $id = intval($_REQUEST['id'] ?? 0);
            $cita = $controller->getById($id);
            
            if ($cita) {
                echo json_encode(['success' => true, 'data' => $cita]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Cita no encontrada']);
            }
            break;

        case 'createCita':
            requireAdmin();
            
            $data = [
                'cliente_nombre' => $_POST['cliente_nombre'] ?? '',
                'cliente_telefono' => $_POST['cliente_telefono'] ?? '',
                'cliente_email' => $_POST['cliente_email'] ?? '',
                'cliente_direccion' => $_POST['cliente_direccion'] ?? '',
                'tipo_cita' => $_POST['tipo_cita'] ?? '',
                'fecha_cita' => $_POST['fecha_cita'] ?? '',
                'hora_inicio' => $_POST['hora_inicio'] ?? '',
                'tecnico_id' => intval($_POST['tecnico_id'] ?? 0),
                'descripcion' => $_POST['descripcion'] ?? ''
            ];
            
            $result = $controller->create($data);
            echo json_encode($result);
            break;

        case 'updateCita':
            requireAdmin();
            
            $id = intval($_REQUEST['id'] ?? 0);
            $data = [];
            
            if (isset($_POST['cliente_nombre'])) $data['cliente_nombre'] = $_POST['cliente_nombre'];
            if (isset($_POST['cliente_telefono'])) $data['cliente_telefono'] = $_POST['cliente_telefono'];
            if (isset($_POST['cliente_email'])) $data['cliente_email'] = $_POST['cliente_email'];
            if (isset($_POST['cliente_direccion'])) $data['cliente_direccion'] = $_POST['cliente_direccion'];
            if (isset($_POST['tipo_cita'])) $data['tipo_cita'] = $_POST['tipo_cita'];
            if (isset($_POST['fecha_cita'])) $data['fecha_cita'] = $_POST['fecha_cita'];
            if (isset($_POST['hora_inicio'])) $data['hora_inicio'] = $_POST['hora_inicio'];
            if (isset($_POST['tecnico_id'])) $data['tecnico_id'] = intval($_POST['tecnico_id']);
            if (isset($_POST['descripcion'])) $data['descripcion'] = $_POST['descripcion'];
            
            $result = $controller->update($id, $data);
            echo json_encode($result);
            break;

        case 'deleteCita':
            requireAdmin();
            
            $id = intval($_REQUEST['id'] ?? 0);
            if ($controller->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Cita eliminada']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar']);
            }
            break;

        case 'markCompleted':
            $id = intval($_REQUEST['id'] ?? 0);
            $observaciones = $_REQUEST['observaciones'] ?? '';
            
            $result = $controller->markCompleted($id, $observaciones);
            echo json_encode($result);
            break;

        case 'getWeekCitas':
            $technician_id = null;
            if (isTechnician() && !isAdmin()) {
                $technician_id = $_SESSION['user_id'];
            } elseif (isset($_REQUEST['tecnico_id'])) {
                $technician_id = intval($_REQUEST['tecnico_id']);
            }
            
            $citas = $controller->getWeekCitas($technician_id);
            echo json_encode(['success' => true, 'data' => $citas]);
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
