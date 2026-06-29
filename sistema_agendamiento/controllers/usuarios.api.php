<?php
/**
 * API de Usuarios - Endpoints
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

requireAuth();

$controller = new UsuarioController();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'getUsuarios':
            requireAdmin();
            
            $usuarios = $controller->getAll();
            echo json_encode(['success' => true, 'data' => $usuarios]);
            break;

        case 'getUsuario':
            $id = intval($_REQUEST['id'] ?? 0);
            $usuario = $controller->getById($id);
            
            if ($usuario) {
                echo json_encode(['success' => true, 'data' => $usuario]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
            break;

        case 'getTechnicians':
            $tecnicos = $controller->getTechnicians();
            echo json_encode(['success' => true, 'data' => $tecnicos]);
            break;

        case 'createUsuario':
            requireAdmin();
            
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'perfil' => $_POST['perfil'] ?? 'tecnico',
                'estado' => $_POST['estado'] ?? 'activo'
            ];
            
            $result = $controller->create($data);
            echo json_encode($result);
            break;

        case 'updateUsuario':
            requireAdmin();
            
            $id = intval($_REQUEST['id'] ?? 0);
            $data = [];
            
            if (isset($_POST['nombre'])) $data['nombre'] = $_POST['nombre'];
            if (isset($_POST['email'])) $data['email'] = $_POST['email'];
            if (isset($_POST['password']) && !empty($_POST['password'])) $data['password'] = $_POST['password'];
            if (isset($_POST['perfil'])) $data['perfil'] = $_POST['perfil'];
            if (isset($_POST['estado'])) $data['estado'] = $_POST['estado'];
            
            $result = $controller->update($id, $data);
            echo json_encode($result);
            break;

        case 'deleteUsuario':
            requireAdmin();
            
            $id = intval($_REQUEST['id'] ?? 0);
            $result = $controller->delete($id);
            echo json_encode($result);
            break;

        case 'getStats':
            requireAdmin();
            
            $stats = $controller->getStats();
            echo json_encode(['success' => true, 'data' => $stats]);
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
