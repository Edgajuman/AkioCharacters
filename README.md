# AkioCharacters Platform

![AkioCharacters Platform](https://edgabot.lucnodes.es/AkiomaeCodes/AkioCharacters/main.png)

¡Bienvenido al repositorio de AkioCharacters Platform! Este proyecto representa una plataforma innovadora donde los usuarios podían interactuar con personajes de IA únicos, cada uno con su propia personalidad, cualidades, avatar y voz personalizada. Inspirado en plataformas como Character.ai, AkioCharacters permitía a la comunidad explorar y chatear tanto con sus propios personajes como con los creados por otros usuarios.

Aunque el servicio de AkioCharacters ha cesado sus operaciones, el código fuente se ha liberado para la comunidad con el objetivo de fomentar el aprendizaje, la experimentación y el desarrollo futuro en el ámbito de la inteligencia artificial conversacional y la creación de personajes virtuales.

Este `README.md` te guiará a través de la estructura del proyecto, sus características principales y, lo más importante, cómo configurar y poner en marcha tu propia instancia de AkioCharacters.

## Características Principales

AkioCharacters Platform fue diseñado con las siguientes funcionalidades clave:

*   **Autenticación con Discord:** Los usuarios podían iniciar sesión de forma segura y sencilla utilizando sus cuentas de Discord. Esto facilitaba el acceso y la gestión de perfiles de usuario.

    ![Discord Login](https://edgabot.lucnodes.es/AkiomaeCodes/AkioCharacters/login.png)

*   **Creación de Personajes de IA:** Cada usuario tenía la capacidad de crear hasta dos personajes de IA únicos. La personalización incluía:
    *   **Nombre y Descripción:** Información básica para identificar al personaje.
    *   **Personalidad y Comportamiento:** Definición detallada de los rasgos de personalidad y un `system prompt` para guiar las respuestas de la IA.
    *   **Avatar Personalizado:** Posibilidad de asignar una imagen única a cada personaje.
    *   **Voz Personalizada:** Opción de seleccionar un tipo de voz (femenina o masculina) para las interacciones con el personaje.

*   **Interacción Conversacional:** Una interfaz de chat intuitiva permitía a los usuarios conversar con los personajes de IA, simulando una experiencia de conversación natural.

    ![AI Chat Interface](https://edgabot.lucnodes.es/AkiomaeCodes/AkioCharacters/chat.png)

*   **Exploración de la Comunidad:** Los usuarios podían descubrir y chatear con personajes creados por otros miembros de la comunidad, fomentando la interacción y el intercambio.

*   **Gestión de Datos Basada en Archivos:** Para simplificar la configuración y el despliegue, el proyecto utilizaba archivos JSON para almacenar datos de usuarios y personajes, eliminando la necesidad de una base de datos compleja.

*   **Integración con Gemini AI:** La inteligencia de los personajes se potenciaba mediante la integración con la API de Gemini AI, permitiendo respuestas coherentes y contextualmente relevantes.

## Estructura del Proyecto

El proyecto AkioCharacters está organizado de la siguiente manera:

```
.
├── api/
│   ├── characters.php
│   ├── chat.php
│   └── user.php
├── assets/
│   ├── css/
│   │   ├── modern-login.css
│   │   └── modern-style2.css
│   └── js/
│       ├── modern-app2.js
│       ├── modern-characters3.js
│       ├── modern-chat3.js
│       ├── modern-login.js
│       └── modern-themes.js
├── auth/
│   ├── discord-auth.php
│   ├── discord-callback.php
│   ├── login.php
│   └── logout.php
├── data/
│   ├── characters.json
│   └── users.json
├── logs/
│   └── error_YYYY-MM-DD.log
├── config.php
├── debug.php
└── index.php
```

*   **`api/`**: Contiene los scripts PHP que manejan las interacciones con la API, como la creación y gestión de personajes, el chat y las operaciones de usuario.
*   **`assets/`**: Almacena los archivos estáticos como CSS para el estilo y JavaScript para la interactividad del frontend.
*   **`auth/`**: Incluye los scripts relacionados con la autenticación de usuarios, especialmente la integración con Discord OAuth.
*   **`data/`**: Directorio para almacenar los datos de la aplicación en formato JSON (personajes y usuarios).
*   **`logs/`**: Guarda los archivos de registro de errores de la aplicación.
*   **`config.php`**: El archivo de configuración principal donde se definen las variables esenciales para el funcionamiento de la aplicación.
*   **`debug.php`**: Un script auxiliar para propósitos de depuración.
*   **`index.php`**: El punto de entrada principal de la aplicación web.

## Configuración Inicial (`config.php`)

El archivo `config.php` es crucial para el correcto funcionamiento de AkioCharacters. Deberás editarlo para proporcionar tus propias credenciales y configuraciones. A continuación, se detalla cada sección:

```php
<?php
// Configuración de la aplicación AkioCharacters Platform

// Configuración de la base de datos (si decides usar una en el futuro)
define(\'DB_HOST\', \'localhost\');
define(\'DB_NAME\', \'akiocharacters\');
define(\'DB_USER\', \'root\');
define(\'DB_PASS\', \'\');

// Configuración de Discord OAuth
define(\'DISCORD_CLIENT_ID\', \'CLIENT_ID\');
define(\'DISCORD_CLIENT_SECRET\', \'CLIENT_SECRET\');
define(\'DISCORD_REDIRECT_URI\', \'https://misitio.com/AkioCharacters/auth/discord-callback.php\');

// Configuración de Gemini AI
define(\'GEMINI_API_KEY\', \'API_KEY\');

// Configuración de archivos
define(\'CHARACTERS_FILE\', __DIR__ . \'/data/characters.json\');
define(\'USERS_FILE\', __DIR__ . \'/data/users.json\');
define(\'CHAT_HISTORY_DIR\', __DIR__ . \'/data/chat_history/\');

// Configuración de la aplicación
define(\'APP_NAME\', \'AkioCharacters Platform\');
define(\'APP_VERSION\', \'2.0.0\');
define(\'MAX_CHARACTERS_PER_USER\', 2);
define(\'MAX_MESSAGE_LENGTH\', 1000);
define(\'MAX_CHARACTER_NAME_LENGTH\', 50);
define(\'MAX_CHARACTER_DESCRIPTION_LENGTH\', 200);
define(\'MAX_CHARACTER_PERSONALITY_LENGTH\', 300);
define(\'MAX_SYSTEM_PROMPT_LENGTH\', 500);

// Configuración de sesión
ini_set(\'session.cookie_httponly\', 1);
ini_set(\'session.cookie_secure\', 0); // Cambiar a 1 en producción con HTTPS
ini_set(\'session.use_strict_mode\', 1);
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
    return isset($_SESSION[\'user_id\']) && !empty($_SESSION[\'user_id\']);
}

function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user[\'id\'] === $_SESSION[\'user_id\']) {
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
    $string = htmlspecialchars($string, ENT_QUOTES, \'UTF-8\');
    
    if ($maxLength && strlen($string) > $maxLength) {
        $string = substr($string, 0, $maxLength);
    }
    
    return $string;
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function generateUniqueId($prefix = \'\') {
    return $prefix . uniqid() . \'_\' . bin2hex(random_bytes(8));
}

function logError($message, $context = []) {
    $logEntry = [
        \'timestamp\' => date(\'Y-m-d H:i:s\'),
        \'message\' => $message,
        \'context\' => $context,
        \'user_id\' => $_SESSION[\'user_id\'] ?? \'anonymous\',
        \'ip\' => $_SERVER[\'REMOTE_ADDR\'] ?? \'unknown\'
    ];
    
    $logFile = __DIR__ . \'/logs/error_\' . date(\'Y-m-d\') . \'.log\';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\\n", FILE_APPEND | LOCK_EX);
}

function getRateLimitKey($action, $userId = null) {
    $userId = $userId ?? ($_SESSION[\'user_id\'] ?? $_SERVER[\'REMOTE_ADDR\']);
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
        $requests = $data[\'requests\'] ?? [];
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
    file_put_contents($cacheFile, json_encode([\'requests\' => $requests]));
    
    return true;
}

function cleanupOldFiles() {
    // Limpiar archivos de cache antiguos
    $cacheDir = __DIR__ . \'/cache/\';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . \'*.json\');
        $now = time();
        
        foreach ($files as $file) {
            if (($now - filemtime($file)) > 3600) { // 1 hora
                unlink($file);
            }
        }
    }
    
    // Limpiar logs antiguos
    $logDir = __DIR__ . \'/logs/\';
    if (is_dir($logDir)) {
        $files = glob($logDir . \'*.log\');
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
        \'severity\' => $severity,
        \'file\' => $file,
        \'line\' => $line
    ]);
    
    return true;
});

set_exception_handler(function($exception) {
    logError("Uncaught Exception: " . $exception->getMessage(), [
        \'file\' => $exception->getFile(),
        \'line\' => $exception->getLine(),
        \'trace\' => $exception->getTraceAsString()
    ]);
});
?>
```

### 1. Configuración de Discord OAuth

Para permitir que los usuarios inicien sesión con Discord, necesitarás crear una aplicación en el [Portal de Desarrolladores de Discord](https://discord.com/developers/applications). Sigue estos pasos:

1.  Inicia sesión en el [Portal de Desarrolladores de Discord](https://discord.com/developers/applications).
2.  Haz clic en `New Application`.
3.  Dale un nombre a tu aplicación (ej. `AkioCharacters`).
4.  En la sección `OAuth2` -> `General`, copia el `Client ID` y el `Client Secret`. Estos valores deben ser pegados en `config.php`:
    *   `DISCORD_CLIENT_ID`: Tu ID de cliente de Discord.
    *   `DISCORD_CLIENT_SECRET`: Tu secreto de cliente de Discord.
5.  En la misma sección `OAuth2` -> `General`, añade la `Redirect URI` (URL de redirección). Esta es la URL a la que Discord redirigirá a los usuarios después de la autenticación. **Es crucial que esta URL sea correcta y coincida con la de tu despliegue.** Por ejemplo, si tu sitio está en `https://misitio.com/AkioCharacters/`, la URI de redirección será `https://misitio.com/AkioCharacters/auth/discord-callback.php`.
    *   `DISCORD_REDIRECT_URI`: La URL completa a `auth/discord-callback.php` en tu servidor.

### 2. Configuración de Gemini AI

AkioCharacters utiliza la API de Gemini para la generación de respuestas de los personajes de IA. Necesitarás una clave de API de Gemini:

1.  Obtén una clave de API de Gemini desde la [Google AI Studio](https://aistudio.google.com/app/apikey).
2.  Pega tu clave de API en `config.php`:
    *   `GEMINI_API_KEY`: Tu clave de API de Gemini.

### 3. Configuración de Archivos y Directorios

El proyecto utiliza archivos JSON para almacenar datos. Asegúrate de que los directorios `data/` y `logs/` tengan permisos de escritura para el servidor web.

*   `CHARACTERS_FILE`: Ruta al archivo JSON donde se guardarán los datos de los personajes. Por defecto, es `data/characters.json`.
*   `USERS_FILE`: Ruta al archivo JSON donde se guardarán los datos de los usuarios. Por defecto, es `data/users.json`.
*   `CHAT_HISTORY_DIR`: Directorio donde se almacenará el historial de chat. Por defecto, es `data/chat_history/`.

### 4. Configuración de la Aplicación

Estas definiciones controlan el comportamiento general de la aplicación:

*   `APP_NAME`: Nombre de la aplicación (ej. `AkioCharacters Platform`).
*   `APP_VERSION`: Versión actual de la aplicación.
*   `MAX_CHARACTERS_PER_USER`: Número máximo de personajes que un usuario puede crear (por defecto, 2).
*   `MAX_MESSAGE_LENGTH`: Longitud máxima de los mensajes de chat.
*   `MAX_CHARACTER_NAME_LENGTH`: Longitud máxima del nombre de un personaje.
*   `MAX_CHARACTER_DESCRIPTION_LENGTH`: Longitud máxima de la descripción de un personaje.
*   `MAX_CHARACTER_PERSONALITY_LENGTH`: Longitud máxima de la descripción de la personalidad de un personaje.
*   `MAX_SYSTEM_PROMPT_LENGTH`: Longitud máxima del `system prompt` para la IA.

### 5. Configuración de Sesión

*   `session.cookie_secure`: **IMPORTANTE:** En un entorno de producción con HTTPS, debes cambiar `ini_set('session.cookie_secure', 0);` a `ini_set('session.cookie_secure', 1);` para asegurar que las cookies de sesión solo se envíen a través de conexiones seguras.

## Instalación y Despliegue

1.  **Servidor Web:** Asegúrate de tener un servidor web (como Apache o Nginx) con PHP instalado (versión 7.4 o superior recomendada).
2.  **Clonar el Repositorio:** Clona este repositorio en tu servidor web.
    ```bash
    git clone <https://github.com/Edgajuman/AkioCharacters/>
    cd AkioCharacters
    ```
3.  **Configurar `config.php`:** Edita el archivo `config.php` con tus credenciales de Discord y Gemini AI, y ajusta las rutas si es necesario.
4.  **Permisos de Escritura:** Asegúrate de que el servidor web tenga permisos de escritura en los directorios `data/` y `logs/`.
    ```bash
    chmod -R 775 data/
    chmod -R 775 logs/
    ```
    (Ajusta los permisos según la configuración de tu servidor para mayor seguridad).
5.  **Acceder a la Aplicación:** Abre tu navegador y navega a la URL donde has desplegado el proyecto (ej. `https://misitio.com/AkioCharacters/`). Serás redirigido a la página de inicio de sesión de Discord.

## Contribución

Este proyecto se libera con fines educativos y de referencia. Las contribuciones son bienvenidas para mejorar la funcionalidad, corregir errores o añadir nuevas características. Si deseas contribuir, por favor, abre un *issue* o envía un *pull request*.

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

**AkioCharacters Platform** - *Un legado de innovación en IA conversacional.*


