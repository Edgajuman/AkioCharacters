<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

function sendJsonResponse($data, $statusCode = 200) {
    ob_clean();
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

function handleError($message, $statusCode = 500) {
    error_log("Chat API Error: " . $message);
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Método no permitido', 405);
}

$currentUser = getCurrentUser();
if (!$currentUser) {
    handleError('Usuario no válido', 401);
}

try {
    $rawInput = file_get_contents('php://input');
    if (empty($rawInput)) {
        handleError('No se recibieron datos', 400);
    }
    
    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        handleError('JSON inválido', 400);
    }

    if (empty($input['character_id']) || empty($input['message'])) {
        handleError('Faltan datos requeridos', 400);
    }

    $character = getCharacterById($input['character_id']);
    if (!$character) {
        handleError('Personaje no encontrado', 404);
    }
    
    $context = buildChatContext($character, $input['history'] ?? [], $input['message']);
    $response = generateAIResponse($context, $character);
    
    if ($response) {
        incrementMessageCount($character['id']);
        sendJsonResponse([
            'success' => true,
            'response' => $response,
            'character_id' => $character['id']
        ]);
    } else {
        handleError('Error al generar respuesta', 500);
    }
    
} catch (Exception $e) {
    handleError('Error interno', 500);
}

function buildChatContext($character, $history, $userMessage) {
    $context = "Eres " . $character['name'] . ". ";
    $context .= "Personalidad: " . $character['personality'] . " ";
    $context .= "Instrucciones: " . $character['system_prompt'] . "\n\n";
    
    if (!empty($history)) {
        $context .= "Historial reciente:\n";
        foreach (array_slice($history, -5) as $msg) {
            $sender = $msg['type'] === 'user' ? 'Usuario' : $character['name'];
            $context .= $sender . ": " . $msg['content'] . "\n";
        }
    }
    
    $context .= "\nUsuario: " . $userMessage . "\n";
    $context .= $character['name'] . ":";
    
    return $context;
}

function generateAIResponse($context, $character) {
    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
        return "Lo siento, el servicio de IA no está disponible.";
    }
    
    $apiKey = GEMINI_API_KEY;
    $model = 'gemini-1.5-flash'; // GRATIS
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=" . $apiKey;

    $data = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $context]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.8,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 1024,
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("cURL Error: $curlError");
        return "Error de conexión con el servicio de IA.";
    }

    if ($httpCode !== 200) {
        error_log("Gemini API HTTP Error ($httpCode): $response");
        return "El servicio de IA respondió con error ($httpCode).";
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error: " . json_last_error_msg());
        return "Error al procesar la respuesta del servicio de IA.";
    }

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($result['candidates'][0]['content']['parts'][0]['text']);
    }

    error_log("Respuesta inesperada de Gemini: " . $response);
    return "Lo siento, no pude generar una respuesta.";
}

function getCharacterById($id) {
    try {
        if (!function_exists('loadCharacters')) {
            return null;
        }
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

function incrementMessageCount($characterId) {
    try {
        if (!function_exists('loadCharacters') || !function_exists('saveCharacters')) {
            return;
        }
        $characters = loadCharacters();
        foreach ($characters as $index => $character) {
            if ($character['id'] === $characterId) {
                $characters[$index]['message_count'] = ($character['message_count'] ?? 0) + 1;
                saveCharacters($characters);
                break;
            }
        }
    } catch (Exception $e) {
        // Silenciar error
    }
}
?>
