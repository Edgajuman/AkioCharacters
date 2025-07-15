<?php
// Configuración de la aplicación AkioCharacters Platform

// Configuración de la base de datos (si decides usar una en el futuro)
define('DB_HOST', 'localhost');
define('DB_NAME', 'akiocharacters');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de Discord OAuth
define('DISCORD_CLIENT_ID', 'CLIENT_ID');
define('DISCORD_CLIENT_SECRET', 'CLIENT_SECRET');
define('DISCORD_REDIRECT_URI', 'https://misitio.com/AkioCharacters/auth/discord-callback.php');

// Configuración de Gemini AI
define('GEMINI_API_KEY', 'API_KEY');

// Configuración de archivos
define('CHARACTERS_FILE', __DIR__ . '/data/characters.json');
define('USERS_FILE', __DIR__ . '/data/users.json');
define('CHAT_HISTORY_DIR', __DIR__ . '/data/chat_history/');

// Configuración de la aplicación
define('APP_NAME', 'AkioCharacters Platform');
define('APP_VERSION', '2.0.0');
define('MAX_CHARACTERS_PER_USER', 2);
define('MAX_MESSAGE_LENGTH', 1000);
define('MAX_CHARACTER_NAME_LENGTH', 50);
define('MAX_CHARACTER_DESCRIPTION_LENGTH', 200);
define('MAX_CHARACTER_PERSONALITY_LENGTH', 300);
define('MAX_SYSTEM_PROMPT_LENGTH', 500);

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
ini_set('session.use_strict_mode', 1);
session_start();

// Crear directorios necesarios
createDirectories();

// Funciones de utilidad
function createDirectories() {
    $dirs = [
        dirname(CHARACTERS_FILE),
        dirname(USERS_FILE),
        CHAT_HISTORY_DIR
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['user_id']) {
            return $user;
        }
    }
    
    return null;
}

function loadUsers() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    
    $content = file_get_contents(USERS_FILE);
    $users = json_decode($content, true);
    
    return is_array($users) ? $users : [];
}

function saveUsers($users) {
    $content = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(USERS_FILE, $content) !== false;
}

function loadCharacters() {
    if (!file_exists(CHARACTERS_FILE)) {
        return [];
    }
    
    $content = file_get_contents(CHARACTERS_FILE);
    $characters = json_decode($content, true);
    
    return is_array($characters) ? $characters : [];
}

function saveCharacters($characters) {
    $content = json_encode($characters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(CHARACTERS_FILE, $content) !== false;
}

function sanitizeString($string, $maxLength = null) {
    $string = trim($string);
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    
    if ($maxLength && strlen($string) > $maxLength) {
        $string = substr($string, 0, $maxLength);
    }
    
    return $string;
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function generateUniqueId($prefix = '') {
    return $prefix . uniqid() . '_' . bin2hex(random_bytes(8));
}

function logError($message, $context = []) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context,
        'user_id' => $_SESSION['user_id'] ?? 'anonymous',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $logFile = __DIR__ . '/logs/error_' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

function getRateLimitKey($action, $userId = null) {
    $userId = $userId ?? ($_SESSION['user_id'] ?? $_SERVER['REMOTE_ADDR']);
    return "rate_limit_{$action}_{$userId}";
}

function checkRateLimit($action, $maxRequests = 10, $timeWindow = 60) {
    $key = getRateLimitKey($action);
    $cacheFile = __DIR__ . "/cache/{$key}.json";
    
    if (!is_dir(dirname($cacheFile))) {
        mkdir(dirname($cacheFile), 0755, true);
    }
    
    $now = time();
    $requests = [];
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        $requests = $data['requests'] ?? [];
    }
    
    // Limpiar requests antiguos
    $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });
    
    if (count($requests) >= $maxRequests) {
        return false;
    }
    
    // Agregar request actual
    $requests[] = $now;
    
    // Guardar en cache
    file_put_contents($cacheFile, json_encode(['requests' => $requests]));
    
    return true;
}

function cleanupOldFiles() {
    // Limpiar archivos de cache antiguos
    $cacheDir = __DIR__ . '/cache/';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '*.json');
        $now = time();
        
        foreach ($files as $file) {
            if (($now - filemtime($file)) > 3600) { // 1 hora
                unlink($file);
            }
        }
    }
    
    // Limpiar logs antiguos
    $logDir = __DIR__ . '/logs/';
    if (is_dir($logDir)) {
        $files = glob($logDir . '*.log');
        $now = time();
        
        foreach ($files as $file) {
            if (($now - filemtime($file)) > (30 * 24 * 3600)) { // 30 días
                unlink($file);
            }
        }
    }
}

// Ejecutar limpieza ocasionalmente
if (rand(1, 100) === 1) {
    cleanupOldFiles();
}

// Configuración de manejo de errores
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    logError("PHP Error: $message", [
        'severity' => $severity,
        'file' => $file,
        'line' => $line
    ]);
    
    return true;
});

set_exception_handler(function($exception) {
    logError("Uncaught Exception: " . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});
?>
