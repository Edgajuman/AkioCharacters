<?php
// Archivo de debug para verificar el estado del sistema
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug AkioCharacters</h1>";

// Verificar config.php
echo "<h2>1. Verificando config.php</h2>";
if (file_exists('config.php')) {
    echo "✅ config.php existe<br>";
    try {
        require_once 'config.php';
        echo "✅ config.php se carga correctamente<br>";
        
        // Verificar constantes
        $constants = ['DISCORD_CLIENT_ID', 'DISCORD_CLIENT_SECRET', 'GEMINI_API_KEY'];
        foreach ($constants as $const) {
            if (defined($const)) {
                echo "✅ $const está definida<br>";
            } else {
                echo "❌ $const NO está definida<br>";
            }
        }
        
        // Verificar directorios
        echo "<h3>Directorios:</h3>";
        $dirs = [DATA_DIR, USERS_FILE, CHARACTERS_FILE];
        foreach ($dirs as $dir) {
            if (file_exists($dir)) {
                echo "✅ $dir existe<br>";
            } else {
                echo "❌ $dir NO existe<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error al cargar config.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ config.php NO existe<br>";
}

// Verificar sesión
echo "<h2>2. Verificando sesión</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sesión activa<br>";
    echo "Session ID: " . session_id() . "<br>";
    
    if (function_exists('isAuthenticated')) {
        if (isAuthenticated()) {
            echo "✅ Usuario autenticado<br>";
            $user = getCurrentUser();
            if ($user) {
                echo "Usuario: " . htmlspecialchars($user['username']) . "<br>";
            }
        } else {
            echo "❌ Usuario NO autenticado<br>";
        }
    } else {
        echo "❌ Función isAuthenticated() no existe<br>";
    }
} else {
    echo "❌ Sesión NO activa<br>";
}

// Verificar permisos de archivos
echo "<h2>3. Verificando permisos</h2>";
if (defined('DATA_DIR')) {
    if (is_writable(DATA_DIR)) {
        echo "✅ " . DATA_DIR . " es escribible<br>";
    } else {
        echo "❌ " . DATA_DIR . " NO es escribible<br>";
    }
}

// Verificar funciones
echo "<h2>4. Verificando funciones</h2>";
$functions = ['loadCharacters', 'saveCharacters', 'loadUsers', 'saveUsers'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "✅ $func() existe<br>";
    } else {
        echo "❌ $func() NO existe<br>";
    }
}

// Test de carga de datos
echo "<h2>5. Test de carga de datos</h2>";
if (function_exists('loadCharacters')) {
    try {
        $characters = loadCharacters();
        echo "✅ Personajes cargados: " . count($characters) . "<br>";
    } catch (Exception $e) {
        echo "❌ Error cargando personajes: " . $e->getMessage() . "<br>";
    }
}

if (function_exists('loadUsers')) {
    try {
        $users = loadUsers();
        echo "✅ Usuarios cargados: " . count($users) . "<br>";
    } catch (Exception $e) {
        echo "❌ Error cargando usuarios: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>6. Información del servidor</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";
?>
