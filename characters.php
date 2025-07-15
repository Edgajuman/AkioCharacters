<?php
// Configurar salida JSON limpia
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Función para enviar respuesta JSON y terminar
function sendJsonResponse($data, $statusCode = 200) {
    ob_clean();
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Función para manejar errores
function handleError($message, $statusCode = 500) {
    error_log("Characters API Error: " . $message);
    sendJsonResponse(['error' => $message], $statusCode);
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['status' => 'ok']);
}

// Verificar que config.php existe
if (!file_exists('../config.php')) {
    handleError('Configuración no encontrada', 500);
}

try {
    require_once '../config.php';
} catch (Exception $e) {
    handleError('Error de configuración: ' . $e->getMessage(), 500);
}

// Verificar autenticación
if (!function_exists('isAuthenticated') || !isAuthenticated()) {
    handleError('No autorizado', 401);
}

$currentUser = getCurrentUser();
if (!$currentUser) {
    handleError('Usuario no válido', 401);
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetCharacters();
            break;
        case 'POST':
            handleCreateCharacter();
            break;
        case 'PUT':
            handleUpdateCharacter();
            break;
        case 'DELETE':
            handleDeleteCharacter();
            break;
        default:
            handleError('Método no permitido', 405);
    }
} catch (Exception $e) {
    handleError('Error interno: ' . $e->getMessage(), 500);
}

function handleGetCharacters() {
    try {
        if (!function_exists('loadCharacters')) {
            handleError('Función loadCharacters no existe', 500);
        }
        
        $characters = loadCharacters();
        sendJsonResponse(['characters' => $characters]);
    } catch (Exception $e) {
        handleError('Error al cargar personajes: ' . $e->getMessage(), 500);
    }
}

function handleCreateCharacter() {
    global $currentUser;
    
    try {
        $rawInput = file_get_contents('php://input');
        if (empty($rawInput)) {
            handleError('No se recibieron datos', 400);
        }
        
        $input = json_decode($rawInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            handleError('JSON inválido: ' . json_last_error_msg(), 400);
        }
        
        // Validar campos requeridos
        if (empty($input['name'])) {
            handleError('El nombre es requerido', 400);
        }
        if (empty($input['personality'])) {
            handleError('La personalidad es requerida', 400);
        }
        if (empty($input['system_prompt'])) {
            handleError('Las instrucciones son requeridas', 400);
        }
        
        // Verificar límite de personajes
        $userCharacters = getUserCharacters($currentUser['id']);
        if (count($userCharacters) >= 2) {
            handleError('Has alcanzado el límite de 2 personajes', 400);
        }
        
        // Crear personaje
        $character = [
            'id' => uniqid('char_', true),
            'name' => trim(htmlspecialchars($input['name'], ENT_QUOTES, 'UTF-8')),
            'description' => trim(htmlspecialchars($input['description'] ?? '', ENT_QUOTES, 'UTF-8')),
            'personality' => trim(htmlspecialchars($input['personality'], ENT_QUOTES, 'UTF-8')),
            'system_prompt' => trim(htmlspecialchars($input['system_prompt'], ENT_QUOTES, 'UTF-8')),
            'avatar' => filter_var($input['avatar'] ?? '', FILTER_VALIDATE_URL) ?: '',
            'voice' => in_array($input['voice'] ?? '', ['', 'male', 'female']) ? $input['voice'] : '',
            'creator_id' => $currentUser['id'],
            'creator_name' => $currentUser['username'],
            'created_at' => date('Y-m-d H:i:s'),
            'message_count' => 0,
            'verified' => false
        ];
        
        if (saveCharacter($character)) {
            sendJsonResponse(['success' => true, 'character' => $character]);
        } else {
            handleError('Error al guardar personaje', 500);
        }
    } catch (Exception $e) {
        handleError('Error al crear personaje: ' . $e->getMessage(), 500);
    }
}

function handleUpdateCharacter() {
    global $currentUser;
    
    try {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            handleError('JSON inválido', 400);
        }
        
        if (empty($input['id'])) {
            handleError('ID requerido', 400);
        }
        
        $character = getCharacterById($input['id']);
        if (!$character) {
            handleError('Personaje no encontrado', 404);
        }
        
        if ($character['creator_id'] !== $currentUser['id']) {
            handleError('Sin permisos', 403);
        }
        
        // Actualizar campos
        $character['name'] = trim(htmlspecialchars($input['name'] ?? $character['name'], ENT_QUOTES, 'UTF-8'));
        $character['description'] = trim(htmlspecialchars($input['description'] ?? $character['description'], ENT_QUOTES, 'UTF-8'));
        $character['personality'] = trim(htmlspecialchars($input['personality'] ?? $character['personality'], ENT_QUOTES, 'UTF-8'));
        $character['system_prompt'] = trim(htmlspecialchars($input['system_prompt'] ?? $character['system_prompt'], ENT_QUOTES, 'UTF-8'));
        $character['avatar'] = filter_var($input['avatar'] ?? '', FILTER_VALIDATE_URL) ?: $character['avatar'];
        $character['voice'] = in_array($input['voice'] ?? '', ['', 'male', 'female']) ? $input['voice'] : $character['voice'];
        $character['updated_at'] = date('Y-m-d H:i:s');
        
        if (updateCharacter($character)) {
            sendJsonResponse(['success' => true, 'character' => $character]);
        } else {
            handleError('Error al actualizar', 500);
        }
    } catch (Exception $e) {
        handleError('Error: ' . $e->getMessage(), 500);
    }
}

function handleDeleteCharacter() {
    global $currentUser;
    
    try {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        
        if (empty($input['id'])) {
            handleError('ID requerido', 400);
        }
        
        $character = getCharacterById($input['id']);
        if (!$character) {
            handleError('Personaje no encontrado', 404);
        }
        
        if ($character['creator_id'] !== $currentUser['id']) {
            handleError('Sin permisos', 403);
        }
        
        if (deleteCharacter($input['id'])) {
            sendJsonResponse(['success' => true]);
        } else {
            handleError('Error al eliminar', 500);
        }
    } catch (Exception $e) {
        handleError('Error: ' . $e->getMessage(), 500);
    }
}

function getUserCharacters($userId) {
    try {
        $characters = loadCharacters();
        return array_filter($characters, function($char) use ($userId) {
            return isset($char['creator_id']) && $char['creator_id'] === $userId;
        });
    } catch (Exception $e) {
        return [];
    }
}

function getCharacterById($id) {
    try {
        $characters = loadCharacters();
        foreach ($characters as $character) {
            if ($character['id'] === $id) {
                return $character;
            }
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}

function saveCharacter($character) {
    try {
        $characters = loadCharacters();
        $characters[] = $character;
        return saveCharacters($characters);
    } catch (Exception $e) {
        return false;
    }
}

function updateCharacter($updatedCharacter) {
    try {
        $characters = loadCharacters();
        foreach ($characters as $index => $character) {
            if ($character['id'] === $updatedCharacter['id']) {
                $characters[$index] = $updatedCharacter;
                return saveCharacters($characters);
            }
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function deleteCharacter($id) {
    try {
        $characters = loadCharacters();
        $characters = array_filter($characters, function($char) use ($id) {
            return $char['id'] !== $id;
        });
        return saveCharacters(array_values($characters));
    } catch (Exception $e) {
        return false;
    }
}
?>
