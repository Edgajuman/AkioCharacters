<?php
require_once '../config.php';

// Generar state para seguridad
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Construir URL de autorización de Discord
$params = [
    'client_id' => DISCORD_CLIENT_ID,
    'redirect_uri' => DISCORD_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'identify',
    'state' => $state
];

$authUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);

// Redirigir a Discord
header('Location: ' . $authUrl);
exit();
?>

