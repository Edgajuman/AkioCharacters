<?php
require_once 'config.php';

// Verificar autenticación
if (!isAuthenticated()) {
    header('Location: auth/login.php');
    exit();
}

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkioCharacters Platform</title>
    <link rel="stylesheet" href="assets/css/modern-style2.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body data-theme="dark">
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <span class="logo-text">AkioCharacters</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="sidebar-content">
            <div class="nav-section">
                <span class="nav-section-title">Principal</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active" data-section="home">
                            <i class="fas fa-home"></i>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="characters">
                            <i class="fas fa-search"></i>
                            <span>Explorar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="chat">
                            <i class="fas fa-comments"></i>
                            <span>Chat</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <span class="nav-section-title">Mis Personajes</span>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="my-characters">
                            <i class="fas fa-star"></i>
                            <span>Ver Todos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" data-section="create">
                            <i class="fas fa-plus"></i>
                            <span>Crear Nuevo</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <img src="<?php echo htmlspecialchars($currentUser['avatar']); ?>" alt="Avatar" class="user-avatar">
                <div class="user-info">
                    <span class="username"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                    <span class="user-status">En línea</span>
                </div>
                <div class="user-menu-dropdown">
                    <button class="user-menu-toggle">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="user-menu">
                        <a href="#" class="user-menu-item" id="themeToggle">
                            <i class="fas fa-palette"></i>
                            <span>Cambiar Tema</span>
                        </a>
                        <a href="auth/logout.php" class="user-menu-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumb">
                    <span id="currentSectionTitle">Inicio</span>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="search-container">
                    <input type="text" id="globalSearch" placeholder="Buscar personajes..." class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
            </div>
        </header>

        <!-- Content Sections -->
        <div class="content-wrapper">
            <!-- Sección de Inicio -->
            <section id="home" class="section active">
                <div class="welcome-hero">
                    <div class="hero-content">
                        <div class="hero-text">
                            <h1>¡Hola, <?php echo htmlspecialchars($currentUser['username']); ?>! 👋</h1>
                            <p>Bienvenido de vuelta a tu mundo de personajes de IA</p>
                        </div>
                        <div class="hero-actions">
                            <button class="btn btn-primary" data-section="create">
                                <i class="fas fa-plus"></i>
                                Crear Personaje
                            </button>
                            <button class="btn btn-secondary" data-section="chat">
                                <i class="fas fa-comments"></i>
                                Iniciar Chat
                            </button>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <div class="floating-cards">
                            <div class="floating-card card-1">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="floating-card card-2">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="floating-card card-3">
                                <i class="fas fa-magic"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="stats-dashboard">
                    <div class="stats-grid">
                        <div class="stat-card primary">
                            <div class="stat-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="totalCharacters">0</h3>
                                <p>Personajes Totales</p>
                                <span class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i> +12%
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="totalMessages">0</h3>
                                <p>Conversaciones</p>
                                <span class="stat-trend positive">
                                    <i class="fas fa-arrow-up"></i> +8%
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card warning">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="myCharacterCount">0</h3>
                                <p>Mis Personajes</p>
                                <span class="stat-limit">Límite: 2</span>
                            </div>
                        </div>
                        
                        <div class="stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="activeUsers">1</h3>
                                <p>Usuarios Activos</p>
                                <span class="stat-trend neutral">
                                    <i class="fas fa-minus"></i> 0%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="featured-section">
                    <div class="section-header">
                        <h2>Personajes Destacados</h2>
                        <button class="btn btn-outline" data-section="characters">
                            Ver Todos
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="character-grid" id="featuredCharacters">
                        <!-- Los personajes se cargarán dinámicamente -->
                    </div>
                </div>
            </section>

            <!-- Sección de Explorar Personajes -->
            <section id="characters" class="section">
                <div class="section-header">
                    <h2>Explorar Personajes</h2>
                    <div class="section-actions">
                        <div class="search-filters">
                            <input type="text" id="characterSearch" placeholder="Buscar personajes..." class="search-input">
                            <button class="filter-btn">
                                <i class="fas fa-filter"></i>
                                Filtros
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="character-grid enhanced" id="allCharacters">
                    <!-- Los personajes se cargarán dinámicamente -->
                </div>
            </section>

            <!-- Sección de Crear Personaje -->
            <section id="create" class="section">
                <div class="create-container">
                    <div class="create-header">
                        <h2>Crear Nuevo Personaje</h2>
                        <div class="progress-indicator">
                            <div class="progress-step active" data-step="1">
                                <span>1</span>
                                <label>Básico</label>
                            </div>
                            <div class="progress-step" data-step="2">
                                <span>2</span>
                                <label>Personalidad</label>
                            </div>
                            <div class="progress-step" data-step="3">
                                <span>3</span>
                                <label>Configuración</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="create-content">
                        <div class="form-container">
                            <form id="createCharacterForm" class="character-form">
                                <!-- Paso 1: Información Básica -->
                                <div class="form-step active" data-step="1">
                                    <div class="step-header">
                                        <h3>Información Básica</h3>
                                        <p>Comencemos con los datos fundamentales de tu personaje</p>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterName">Nombre del Personaje *</label>
                                            <input type="text" id="characterName" name="name" required maxlength="50" placeholder="Ej: Luna, Alex, Dr. Smith...">
                                            <div class="form-hint">Máximo 50 caracteres</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterDescription">Descripción</label>
                                            <textarea id="characterDescription" name="description" rows="3" maxlength="200" placeholder="Una breve descripción de tu personaje..."></textarea>
                                            <div class="form-hint">Máximo 200 caracteres</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterAvatar">Avatar del Personaje</label>
                                            <div class="avatar-input-container">
                                                <input type="url" id="characterAvatar" name="avatar" placeholder="https://ejemplo.com/imagen.jpg">
                                                <div class="avatar-preview-container">
                                                    <div class="avatar-preview" id="avatarPreviewContainer">
                                                        <img id="avatarPreview" style="display: none;" alt="Vista previa del avatar">
                                                        <div class="avatar-placeholder">
                                                            <i class="fas fa-user"></i>
                                                            <span>Vista previa</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Paso 2: Personalidad -->
                                <div class="form-step" data-step="2">
                                    <div class="step-header">
                                        <h3>Personalidad y Comportamiento</h3>
                                        <p>Define cómo se comportará y responderá tu personaje</p>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterPersonality">Personalidad *</label>
                                            <textarea id="characterPersonality" name="personality" rows="4" required maxlength="300" placeholder="Ej: Amigable, inteligente, sarcástico, aventurero..."></textarea>
                                            <div class="form-hint">Describe los rasgos principales de personalidad</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterSystemPrompt">Instrucciones de Comportamiento *</label>
                                            <textarea id="characterSystemPrompt" name="system_prompt" rows="5" required maxlength="500" placeholder="Instrucciones específicas sobre cómo debe comportarse el personaje..."></textarea>
                                            <div class="form-hint">Instrucciones detalladas para el comportamiento del personaje</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Paso 3: Configuración Avanzada -->
                                <div class="form-step" data-step="3">
                                    <div class="step-header">
                                        <h3>Configuración de Voz</h3>
                                        <p>Personaliza la experiencia de audio de tu personaje</p>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="characterVoice">Tipo de Voz</label>
                                            <div class="voice-selector">
                                                <div class="voice-option" data-voice="">
                                                    <div class="voice-icon">
                                                        <i class="fas fa-volume-mute"></i>
                                                    </div>
                                                    <div class="voice-info">
                                                        <h4>Sin Voz</h4>
                                                        <p>Solo texto</p>
                                                    </div>
                                                    <button type="button" class="voice-preview-btn" disabled>
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </div>
                                                
                                                <div class="voice-option" data-voice="female">
                                                    <div class="voice-icon">
                                                        <i class="fas fa-female"></i>
                                                    </div>
                                                    <div class="voice-info">
                                                        <h4>Voz Femenina</h4>
                                                        <p>Tono suave y claro</p>
                                                    </div>
                                                    <button type="button" class="voice-preview-btn" data-voice="female">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </div>
                                                
                                                <div class="voice-option" data-voice="male">
                                                    <div class="voice-icon">
                                                        <i class="fas fa-male"></i>
                                                    </div>
                                                    <div class="voice-info">
                                                        <h4>Voz Masculina</h4>
                                                        <p>Tono profundo y claro</p>
                                                    </div>
                                                    <button type="button" class="voice-preview-btn" data-voice="male">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" id="characterVoice" name="voice" value="">
                                        </div>
                                    </div>
                                    
                                    <div class="character-limit-info">
                                        <div class="limit-card">
                                            <div class="limit-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="limit-content">
                                                <h4>Límite de Personajes</h4>
                                                <p>Puedes crear hasta <strong>2 personajes únicos</strong>. Cada personaje puede tener su propia personalidad, avatar y configuración de voz.</p>
                                                <div class="limit-progress">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" id="characterLimitProgress"></div>
                                                    </div>
                                                    <span class="progress-text" id="characterLimitText">0 / 2 personajes creados</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Navegación del formulario -->
                                <div class="form-navigation">
                                    <button type="button" id="prevStepBtn" class="btn btn-secondary" style="display: none;">
                                        <i class="fas fa-arrow-left"></i>
                                        Anterior
                                    </button>
                                    <button type="button" id="nextStepBtn" class="btn btn-primary">
                                        Siguiente
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                    <button type="submit" id="createBtn" class="btn btn-success" style="display: none;">
                                        <i class="fas fa-plus"></i>
                                        Crear Personaje
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="preview-container">
                            <div class="character-preview">
                                <h3>Vista Previa</h3>
                                <div class="preview-card">
                                    <div class="preview-avatar">
                                        <img id="previewAvatar" src="/placeholder.svg" alt="Avatar" style="display: none;">
                                        <div class="preview-avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="preview-info">
                                        <h4 id="previewName">Nombre del Personaje</h4>
                                        <p id="previewDescription">Descripción del personaje...</p>
                                        <div class="preview-personality">
                                            <strong>Personalidad:</strong>
                                            <span id="previewPersonality">Rasgos de personalidad...</span>
                                        </div>
                                        <div class="preview-voice">
                                            <i class="fas fa-volume-up"></i>
                                            <span id="previewVoice">Sin voz configurada</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección de Chat -->
            <section id="chat" class="section">
                <div class="chat-layout">
                    <div class="chat-sidebar">
                        <div class="chat-sidebar-header">
                            <h3>Personajes</h3>
                            <button class="sidebar-collapse-btn">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </div>
                        <div class="character-list" id="chatCharacterList">
                            <!-- Los personajes se cargarán dinámicamente -->
                        </div>
                    </div>
                    
                    <div class="chat-main">
                        <div class="chat-header" id="chatHeader">
                            <div class="character-info">
                                <div class="chat-welcome">
                                    <div class="welcome-icon">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                    <h3>Selecciona un personaje para comenzar</h3>
                                    <p>Elige cualquier personaje de la lista para iniciar una conversación fascinante</p>
                                </div>
                            </div>
                            <div class="chat-controls">
                                <button id="voiceToggle" class="btn btn-icon" style="display: none;" title="Alternar voz">
                                    <i class="fas fa-volume-off"></i>
                                </button>
                                <button id="clearChatBtn" class="btn btn-icon" title="Limpiar chat">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-icon" title="Configuración">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="chat-messages" id="chatMessages">
                            <div class="chat-empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <h3>¡Bienvenido al Chat!</h3>
                                <p>Selecciona un personaje de la lista para comenzar una conversación increíble</p>
                            </div>
                        </div>
                        
                        <div class="chat-input-container" id="chatInput" style="display: none;">
                            <div class="chat-input">
                                <div class="input-actions">
                                    <button class="input-action-btn" title="Adjuntar archivo">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <button class="input-action-btn" title="Emoji">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                </div>
                                <textarea id="messageInput" placeholder="Escribe tu mensaje..." rows="1"></textarea>
                                <button id="sendBtn" class="send-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección de Mis Personajes -->
            <section id="my-characters" class="section">
                <div class="section-header">
                    <h2>Mis Personajes</h2>
                    <div class="section-actions">
                        <button class="btn btn-primary" data-section="create">
                            <i class="fas fa-plus"></i>
                            Crear Nuevo
                        </button>
                    </div>
                </div>
                
                <div class="my-characters-container">
                    <div class="character-grid enhanced" id="myCharacters">
                        <!-- Los personajes del usuario se cargarán dinámicamente -->
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Theme Selector Modal -->
    <div class="theme-modal" id="themeModal">
        <div class="theme-modal-overlay"></div>
        <div class="theme-modal-content">
            <div class="theme-modal-header">
                <h3>Seleccionar Tema</h3>
                <button class="theme-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="theme-options">
                <div class="theme-option" data-theme="dark">
                    <div class="theme-preview dark"></div>
                    <div class="theme-info">
                        <h4>Oscuro</h4>
                        <p>Perfecto para sesiones nocturnas</p>
                    </div>
                </div>
                <div class="theme-option" data-theme="light">
                    <div class="theme-preview light"></div>
                    <div class="theme-info">
                        <h4>Claro</h4>
                        <p>Limpio y minimalista</p>
                    </div>
                </div>
                <div class="theme-option" data-theme="blue">
                    <div class="theme-preview blue"></div>
                    <div class="theme-info">
                        <h4>Azul Océano</h4>
                        <p>Tranquilo y profesional</p>
                    </div>
                </div>
                <div class="theme-option" data-theme="purple">
                    <div class="theme-preview purple"></div>
                    <div class="theme-info">
                        <h4>Púrpura Místico</h4>
                        <p>Creativo y vibrante</p>
                    </div>
                </div>
                <div class="theme-option" data-theme="green">
                    <div class="theme-preview green"></div>
                    <div class="theme-info">
                        <h4>Verde Natura</h4>
                        <p>Relajante y natural</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <h3>Procesando...</h3>
            <p>Por favor espera un momento</p>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script src="assets/js/modern-app2.js"></script>
    <script src="assets/js/modern-characters3.js"></script>
    <script src="assets/js/modern-chat3.js"></script>
    <script src="assets/js/modern-themes.js"></script>
</body>
</html>
