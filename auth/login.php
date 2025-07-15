<?php
require_once '../config.php';

// Si ya está autenticado, redirigir al inicio
if (isAuthenticated()) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AkioCharacters Platform</title>
    <link rel="stylesheet" href="../assets/css/modern-login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <!-- Background Animation -->
        <div class="background-animation">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
            <div class="floating-shape shape-4"></div>
            <div class="floating-shape shape-5"></div>
        </div>
        
        <!-- Login Content -->
        <div class="login-content">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h1>AkioCharacters</h1>
                    </div>
                    <p class="tagline">Crea y chatea con personajes de IA únicos</p>
                </div>
                
                <div class="login-body">
                    <div class="welcome-section">
                        <h2>¡Bienvenido de vuelta!</h2>
                        <p>Conecta con Discord para acceder a tu mundo de personajes de IA</p>
                    </div>
                    
                    <div class="login-form">
                        <a href="discord-auth.php" class="discord-login-btn">
                            <div class="btn-icon">
                                <i class="fab fa-discord"></i>
                            </div>
                            <div class="btn-content">
                                <span class="btn-text">Continuar con Discord</span>
                                <span class="btn-subtext">Rápido y seguro</span>
                            </div>
                            <div class="btn-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                        
                        <div class="login-divider">
                            <span>¿Por qué Discord?</span>
                        </div>
                        
                        <div class="benefits-grid">
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Seguro</h4>
                                    <p>Autenticación confiable</p>
                                </div>
                            </div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Rápido</h4>
                                    <p>Acceso instantáneo</p>
                                </div>
                            </div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div class="benefit-content">
                                    <h4>Social</h4>
                                    <p>Conecta con la comunidad</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="login-footer">
                    <div class="features-showcase">
                        <h3>¿Qué puedes hacer?</h3>
                        <div class="features-list">
                            <div class="feature-item">
                                <i class="fas fa-plus-circle"></i>
                                <span>Crea hasta 2 personajes únicos con personalidades detalladas</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-comments"></i>
                                <span>Chat ilimitado con IA avanzada y respuestas naturales</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-search"></i>
                                <span>Explora y chatea con personajes de otros usuarios</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-volume-up"></i>
                                <span>Voces personalizadas para una experiencia inmersiva</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-palette"></i>
                                <span>Múltiples temas y personalización de interfaz</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Experiencia optimizada para todos los dispositivos</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="copyright">
                        <p>&copy; 2025 AkioCharacters Platform. Desarrollado con ❤️ por Akiomae.</p>
                    </div>
                </div>
            </div>
            
            <!-- Side Panel -->
            <div class="side-panel">
                <div class="panel-content">
                    <div class="panel-header">
                        <h2>Únete a la Revolución de la IA</h2>
                        <p>Más de <strong>10,000</strong> personajes creados por nuestra comunidad</p>
                    </div>
                    
                    <div class="testimonials">
                        <div class="testimonial active">
                            <div class="testimonial-content">
                                <p>"La plataforma más intuitiva para crear personajes de IA. ¡Mis personajes se sienten realmente vivos!"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="https://via.placeholder.com/40/4A90E2/FFFFFF?text=M" alt="Usuario">
                                <div class="author-info">
                                    <h4>María González</h4>
                                    <span>Creadora de contenido</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial">
                            <div class="testimonial-content">
                                <p>"Increíble cómo cada personaje tiene su propia personalidad única. La IA es sorprendentemente natural."</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="https://via.placeholder.com/40/7B68EE/FFFFFF?text=A" alt="Usuario">
                                <div class="author-info">
                                    <h4>Alex Rivera</h4>
                                    <span>Desarrollador</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial">
                            <div class="testimonial-content">
                                <p>"La función de voz hace que las conversaciones sean mucho más inmersivas. ¡Excelente trabajo!"</p>
                            </div>
                            <div class="testimonial-author">
                                <img src="https://via.placeholder.com/40/4CAF50/FFFFFF?text=S" alt="Usuario">
                                <div class="author-info">
                                    <h4>Sofia Chen</h4>
                                    <span>Diseñadora UX</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-preview">
                        <div class="stat-item">
                            <h3>10K+</h3>
                            <p>Personajes Creados</p>
                        </div>
                        <div class="stat-item">
                            <h3>50K+</h3>
                            <p>Conversaciones</p>
                        </div>
                        <div class="stat-item">
                            <h3>2K+</h3>
                            <p>Usuarios Activos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/modern-login.js"></script>
</body>
</html>
