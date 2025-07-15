<?php
// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Incluir configuración
require_once '../config.php';

// Funciones necesarias
if (!function_exists('loadJSON')) {
    function loadJSON($file) {
        if (!file_exists($file)) return [];
        $data = file_get_contents($file);
        return json_decode($data, true) ?? [];
    }
}

if (!function_exists('saveJSON')) {
    function saveJSON($file, $data) {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

// Validar parámetros de Discord
if (!isset($_GET['code']) || !isset($_GET['state'])) {
    die("Error: Faltan parámetros 'code' o 'state'.");
}

// Validar el estado para prevenir CSRF
if (!isset($_SESSION['oauth_state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die("Error: CSRF detectado (state no coincide).");
}

$code = $_GET['code'];

// Intercambiar código por token
$tokenData = [
    'client_id' => DISCORD_CLIENT_ID,
    'client_secret' => DISCORD_CLIENT_SECRET,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => DISCORD_REDIRECT_URI
];

$ch = curl_init('https://discord.com/api/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

$tokenResponse = curl_exec($ch);
if (curl_errno($ch)) {
    die("Error de cURL: " . curl_error($ch));
}
curl_close($ch);

$tokenInfo = json_decode($tokenResponse, true);
if (!isset($tokenInfo['access_token'])) {
    die("Error al obtener el token de acceso: " . ($tokenResponse ?: 'respuesta vacía'));
}

$accessToken = $tokenInfo['access_token'];

// Obtener información del usuario
$ch = curl_init('https://discord.com/api/users/@me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);

$userResponse = curl_exec($ch);
if (curl_errno($ch)) {
    die("Error al obtener usuario: " . curl_error($ch));
}
curl_close($ch);

$discordUser = json_decode($userResponse, true);
if (!isset($discordUser['id'])) {
    die("Error: No se pudo obtener el ID del usuario de Discord. Respuesta: $userResponse");
}

// Extraer datos del usuario
$userId = $discordUser['id'];
$username = $discordUser['username'];
$discriminator = $discordUser['discriminator'] ?? '0';
$avatar = $discordUser['avatar']
    ? "https://cdn.discordapp.com/avatars/{$userId}/{$discordUser['avatar']}.png"
    : "https://cdn.discordapp.com/embed/avatars/" . ($discriminator % 5) . ".png";

// Cargar o crear archivo de usuarios
$users = loadJSON(USERS_FILE);

// Verificar si ya existe
$existingUser = null;
foreach ($users as &$user) {
    if ($user['id'] === $userId) {
        $existingUser = &$user;
        break;
    }
}

if ($existingUser) {
    $existingUser['username'] = $username;
    $existingUser['avatar'] = $avatar;
    $existingUser['last_login'] = date('Y-m-d H:i:s');
} else {
    $users[] = [
        'id' => $userId,
        'username' => $username,
        'avatar' => $avatar,
        'created_at' => date('Y-m-d H:i:s'),
        'last_login' => date('Y-m-d H:i:s'),
        'character_count' => 0
    ];
}

// Guardar
saveJSON(USERS_FILE, $users);

// Crear sesión
$_SESSION['user_id'] = $userId;
$_SESSION['discord_user'] = [
    'username' => $username,
    'avatar' => $avatar
];

// Limpiar state
unset($_SESSION['oauth_state']);

// Redirigir
header('Location: ../../AkioCharacters/');
exit();
