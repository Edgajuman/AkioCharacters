<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

function sendJsonResponse($data, $statusCode = 200) {
    ob_clean();
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

function handleError($message, $statusCode = 500) {
    error_log("User API Error: " . $message);
    sendJsonResponse(['error' => $message], $statusCode);
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['status' => 'ok']);
}

if (!file_exists('../config.php')) {
    handleError('Configuración no encontrada', 500);
}

try {
    require_once '../config.php';
} catch (Exception $e) {
    handleError('Error de configuración', 500);
}

if (!function_exists('isAuthenticated') || !isAuthenticated()) {
    handleError('No autorizado', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    handleError('Método no permitido', 405);
}

try {
    $currentUser = getCurrentUser();
    
    if (!$currentUser) {
        handleError('Usuario no encontrado', 404);
    }
    
    // Agregar estadísticas del usuario
    $userStats = [
        'characters_created' => count(getUserCharacters($currentUser['id'])),
        'total_messages' => getTotalUserMessages($currentUser['id']),
        'join_date' => $currentUser['created_at'] ?? date('Y-m-d H:i:s'),
        'last_active' => date('Y-m-d H:i:s')
    ];
    
    $response = array_merge($currentUser, ['stats' => $userStats]);
    sendJsonResponse($response);
    
} catch (Exception $e) {
    handleError('Error interno', 500);
}

function getUserCharacters($userId) {
    try {
        if (!function_exists('loadCharacters')) {
            return [];
        }
        $characters = loadCharacters();
        return array_filter($characters, function($char) use ($userId) {
            return isset($char['creator_id']) && $char['creator_id'] === $userId;
        });
    } catch (Exception $e) {
        return [];
    }
}

function getTotalUserMessages($userId) {
    try {
        $characters = getUserCharacters($userId);
        $total = 0;
        foreach ($characters as $character) {
            $total += $character['message_count'] ?? 0;
        }
        return $total;
    } catch (Exception $e) {
        return 0;
    }
}
?>
