// Sistema de Login Moderno
class ModernLogin {
  constructor() {
    this.currentTestimonial = 0
    this.testimonials = [
      {
        text: "La plataforma más intuitiva para crear personajes de IA. ¡Mis personajes se sienten realmente vivos!",
        author: "María González",
        role: "Creadora de contenido",
        avatar: "https://via.placeholder.com/40/4A90E2/FFFFFF?text=M",
      },
      {
        text: "Increíble cómo cada personaje tiene su propia personalidad única. La IA es sorprendentemente natural.",
        author: "Alex Rivera",
        role: "Desarrollador",
        avatar: "https://via.placeholder.com/40/7B68EE/FFFFFF?text=A",
      },
      {
        text: "La función de voz hace que las conversaciones sean mucho más inmersivas. ¡Excelente trabajo!",
        author: "Sofia Chen",
        role: "Diseñadora UX",
        avatar: "https://via.placeholder.com/40/4CAF50/FFFFFF?text=S",
      },
    ]

    this.init()
  }

  init() {
    this.setupAnimations()
    this.setupTestimonialRotation()
    this.setupInteractiveElements()
    this.setupAccessibility()
  }

  setupAnimations() {
    // Animación de entrada para elementos
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in")
        }
      })
    }, observerOptions)

    // Observar elementos para animación
    document.querySelectorAll(".login-card, .side-panel, .benefit-item, .feature-item").forEach((el) => {
      observer.observe(el)
    })

    // Animación de las formas flotantes
    this.animateFloatingShapes()
  }

  animateFloatingShapes() {
    const shapes = document.querySelectorAll(".floating-shape")

    shapes.forEach((shape, index) => {
      // Animación inicial aleatoria
      const delay = Math.random() * 2000
      const duration = 15000 + Math.random() * 10000

      setTimeout(() => {
        shape.style.animation = `float ${duration}ms infinite linear`
      }, delay)

      // Cambio de color gradual
      setInterval(
        () => {
          const hue = Math.random() * 360
          shape.style.filter = `hue-rotate(${hue}deg)`
        },
        5000 + index * 1000,
      )
    })
  }

  setupTestimonialRotation() {
    const testimonialContainer = document.querySelector(".testimonials")
    if (!testimonialContainer) return

    // Rotar testimonios cada 5 segundos
    setInterval(() => {
      this.rotateTestimonial()
    }, 5000)

    // Configurar indicadores de testimonios
    this.createTestimonialIndicators()
  }

  rotateTestimonial() {
    const testimonials = document.querySelectorAll(".testimonial")
    if (testimonials.length === 0) return

    // Ocultar testimonio actual
    testimonials[this.currentTestimonial].classList.remove("active")

    // Avanzar al siguiente
    this.currentTestimonial = (this.currentTestimonial + 1) % this.testimonials.length

    // Mostrar nuevo testimonio
    testimonials[this.currentTestimonial].classList.add("active")

    // Actualizar indicadores
    this.updateTestimonialIndicators()
  }

  createTestimonialIndicators() {
    const container = document.querySelector(".testimonials")
    if (!container) return

    const indicatorsContainer = document.createElement("div")
    indicatorsContainer.className = "testimonial-indicators"
    indicatorsContainer.style.cssText = `
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        `

    this.testimonials.forEach((_, index) => {
      const indicator = document.createElement("button")
      indicator.className = "testimonial-indicator"
      indicator.style.cssText = `
                width: 8px;
                height: 8px;
                border-radius: 50%;
                border: none;
                background: rgba(255, 255, 255, 0.3);
                cursor: pointer;
                transition: all 0.3s ease;
            `

      if (index === 0) {
        indicator.style.background = "rgba(255, 255, 255, 0.8)"
      }

      indicator.addEventListener("click", () => {
        this.goToTestimonial(index)
      })

      indicatorsContainer.appendChild(indicator)
    })

    container.appendChild(indicatorsContainer)
  }

  updateTestimonialIndicators() {
    const indicators = document.querySelectorAll(".testimonial-indicator")
    indicators.forEach((indicator, index) => {
      if (index === this.currentTestimonial) {
        indicator.style.background = "rgba(255, 255, 255, 0.8)"
        indicator.style.transform = "scale(1.2)"
      } else {
        indicator.style.background = "rgba(255, 255, 255, 0.3)"
        indicator.style.transform = "scale(1)"
      }
    })
  }

  goToTestimonial(index) {
    const testimonials = document.querySelectorAll(".testimonial")
    if (testimonials.length === 0) return

    // Ocultar testimonio actual
    testimonials[this.currentTestimonial].classList.remove("active")

    // Ir al testimonio seleccionado
    this.currentTestimonial = index
    testimonials[this.currentTestimonial].classList.add("active")

    // Actualizar indicadores
    this.updateTestimonialIndicators()
  }

  setupInteractiveElements() {
    // Efecto hover mejorado para el botón de Discord
    const discordBtn = document.querySelector(".discord-login-btn")
    if (discordBtn) {
      discordBtn.addEventListener("mouseenter", () => {
        this.createRippleEffect(discordBtn)
      })
    }

    // Efecto parallax suave para las formas de fondo
    window.addEventListener("mousemove", (e) => {
      this.handleParallaxEffect(e)
    })

    // Animación de estadísticas
    this.animateStats()
  }

  createRippleEffect(element) {
    const ripple = document.createElement("div")
    ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `

    const rect = element.getBoundingClientRect()
    const size = Math.max(rect.width, rect.height)
    ripple.style.width = ripple.style.height = size + "px"
    ripple.style.left = rect.width / 2 - size / 2 + "px"
    ripple.style.top = rect.height / 2 - size / 2 + "px"

    element.style.position = "relative"
    element.appendChild(ripple)

    setTimeout(() => {
      ripple.remove()
    }, 600)
  }

  handleParallaxEffect(e) {
    const shapes = document.querySelectorAll(".floating-shape")
    const mouseX = e.clientX / window.innerWidth
    const mouseY = e.clientY / window.innerHeight

    shapes.forEach((shape, index) => {
      const speed = (index + 1) * 0.5
      const x = (mouseX - 0.5) * speed * 20
      const y = (mouseY - 0.5) * speed * 20

      shape.style.transform = `translate(${x}px, ${y}px)`
    })
  }

  animateStats() {
    const statItems = document.querySelectorAll(".stat-item h3")

    const animateNumber = (element, target) => {
      const duration = 2000
      const start = 0
      const increment = target / (duration / 16)
      let current = start

      const timer = setInterval(() => {
        current += increment
        if (current >= target) {
          current = target
          clearInterval(timer)
        }

        element.textContent = Math.floor(current).toLocaleString() + (element.textContent.includes("+") ? "+" : "")
      }, 16)
    }

    // Observer para iniciar animación cuando sea visible
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const text = entry.target.textContent
          const number = Number.parseInt(text.replace(/[^\d]/g, ""))
          if (number) {
            animateNumber(entry.target, number)
            observer.unobserve(entry.target)
          }
        }
      })
    })

    statItems.forEach((item) => observer.observe(item))
  }

  setupAccessibility() {
    // Mejorar accesibilidad del teclado
    const focusableElements = document.querySelectorAll("a, button, input, textarea, select")

    focusableElements.forEach((element) => {
      element.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          if (element.tagName === "BUTTON" || element.tagName === "A") {
            element.click()
          }
        }
      })
    })

    // Anunciar cambios de testimonio para lectores de pantalla
    const testimonialContainer = document.querySelector(".testimonials")
    if (testimonialContainer) {
      testimonialContainer.setAttribute("aria-live", "polite")
      testimonialContainer.setAttribute("aria-label", "Testimonios de usuarios")
    }

    // Mejorar etiquetas ARIA
    this.improveAriaLabels()
  }

  improveAriaLabels() {
    // Botón de Discord
    const discordBtn = document.querySelector(".discord-login-btn")
    if (discordBtn) {
      discordBtn.setAttribute("aria-label", "Iniciar sesión con Discord - Rápido y seguro")
    }

    // Elementos de beneficios
    document.querySelectorAll(".benefit-item").forEach((item, index) => {
      item.setAttribute("role", "listitem")
      item.setAttribute("aria-label", `Beneficio ${index + 1}: ${item.querySelector("h4").textContent}`)
    })

    // Elementos de características
    document.querySelectorAll(".feature-item").forEach((item, index) => {
      item.setAttribute("role", "listitem")
      item.setAttribute("aria-label", `Característica ${index + 1}: ${item.querySelector("span").textContent}`)
    })
  }

  // Método para mostrar mensajes de estado
  showStatus(message, type = "info") {
    const statusContainer = document.createElement("div")
    statusContainer.className = `status-message ${type}`
    statusContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === "error" ? "#ef4444" : "#3b82f6"};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `

    statusContainer.textContent = message
    document.body.appendChild(statusContainer)

    setTimeout(() => {
      statusContainer.style.animation = "slideOut 0.3s ease"
      setTimeout(() => {
        statusContainer.remove()
      }, 300)
    }, 3000)
  }
}

// Agregar estilos CSS para animaciones
const style = document.createElement("style")
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .animate-in {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`
document.head.appendChild(style)

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  window.modernLogin = new ModernLogin()
})
