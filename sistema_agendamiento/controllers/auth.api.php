<?php
/**
 * API de Autenticación - Endpoints
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../controllers/AuthController.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new AuthController();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $result = $controller->login($email, $password);
            
            if ($result['success']) {
                http_response_code(200);
            } else {
                http_response_code(401);
            }
            
            echo json_encode($result);
            break;

        case 'logout':
            $result = $controller->logout();
            echo json_encode($result);
            break;

        case 'changePassword':
            if (!isAuthenticated()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'No autenticado']);
                break;
            }
            
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $result = $controller->changePassword(
                $_SESSION['user_id'],
                $current_password,
                $new_password,
                $confirm_password
            );
            
            echo json_encode($result);
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
