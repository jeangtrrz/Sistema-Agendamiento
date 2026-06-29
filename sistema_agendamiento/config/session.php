<?php
/**
 * Gestión de sesiones
 */

session_name(SESSION_NAME);
session_start();

// Verificar timeout de sesión
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: ' . APP_URL . '/index.php?logout=timeout');
    exit();
}

$_SESSION['last_activity'] = time();

/**
 * Función para verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Función para verificar el perfil del usuario
 */
function hasProfile($profile) {
    return isAuthenticated() && $_SESSION['user_profile'] === $profile;
}

/**
 * Función para verificar si es administrador
 */
function isAdmin() {
    return hasProfile('administrador');
}

/**
 * Función para verificar si es técnico
 */
function isTechnician() {
    return hasProfile('tecnico');
}

/**
 * Función para obtener datos del usuario actual
 */
function getCurrentUser() {
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['user_nombre'],
            'email' => $_SESSION['user_email'],
            'perfil' => $_SESSION['user_profile'],
            'estado' => $_SESSION['user_estado']
        ];
    }
    return null;
}

/**
 * Función para redirigir si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ' . APP_URL . '/index.php');
        exit();
    }
}

/**
 * Función para redirigir si no es administrador
 */
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: ' . APP_URL . '/dashboard.php?error=permiso_denegado');
        exit();
    }
}

/**
 * Función para redirigir si no es técnico
 */
function requireTechnician() {
    requireAuth();
    if (!isTechnician() && !isAdmin()) {
        header('Location: ' . APP_URL . '/dashboard.php?error=permiso_denegado');
        exit();
    }
}
?>
