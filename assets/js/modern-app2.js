// Aplicación Principal Moderna
class ModernApp {
  constructor() {
    this.currentSection = "home"
    this.currentUser = null
    this.characters = []
    this.myCharacters = []
    this.currentTheme = localStorage.getItem("theme") || "dark"
    this.sidebarCollapsed = false

    this.init()
  }

  async init() {
    this.setupEventListeners()
    this.applyTheme(this.currentTheme)
    await this.loadUserData()
    await this.loadCharacters()
    this.updateStats()
    this.showSection("home")

    // Auto-actualizar estadísticas cada 30 segundos
    setInterval(() => this.updateStats(), 30000)
  }

  setupEventListeners() {
    // Navegación
    document.querySelectorAll("[data-section]").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const section = e.currentTarget.getAttribute("data-section")
        this.showSection(section)
      })
    })

    // Toggle sidebar
    const sidebarToggle = document.getElementById("sidebarToggle")
    const mobileMenuToggle = document.getElementById("mobileMenuToggle")

    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => this.toggleSidebar())
    }

    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener("click", () => this.toggleMobileSidebar())
    }

    // Búsqueda global
    const globalSearch = document.getElementById("globalSearch")
    if (globalSearch) {
      globalSearch.addEventListener("input", (e) => this.handleGlobalSearch(e.target.value))
    }

    // Búsqueda de personajes
    const characterSearch = document.getElementById("characterSearch")
    if (characterSearch) {
      characterSearch.addEventListener("input", (e) => this.handleCharacterSearch(e.target.value))
    }

    // Tema
    const themeToggle = document.getElementById("themeToggle")
    if (themeToggle) {
      themeToggle.addEventListener("click", (e) => {
        e.preventDefault()
        this.showThemeModal()
      })
    }

    // Cerrar modales al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("theme-modal-overlay")) {
        this.hideThemeModal()
      }
    })

    // Responsive
    window.addEventListener("resize", () => this.handleResize())
    this.handleResize()
  }

  async loadUserData() {
    try {
      const response = await fetch("/AkiomaeCodes/AkioCharacters/api/user.php")
      if (response.ok) {
        this.currentUser = await response.json()
      }
    } catch (error) {
      console.error("Error loading user data:", error)
    }
  }

  async loadCharacters() {
    try {
      const response = await fetch("/AkiomaeCodes/AkioCharacters/api/characters.php")
      if (response.ok) {
        const data = await response.json()
        this.characters = data.characters || []
        this.myCharacters = this.characters.filter((char) => char.creator_id === this.currentUser?.id)

        this.renderCharacters()
        this.renderFeaturedCharacters()
        this.renderMyCharacters()
      }
    } catch (error) {
      console.error("Error loading characters:", error)
      this.showToast("Error al cargar personajes", "error")
    }
  }

  showSection(sectionName) {
    // Ocultar todas las secciones
    document.querySelectorAll(".section").forEach((section) => {
      section.classList.remove("active")
    })

    // Mostrar la sección seleccionada
    const targetSection = document.getElementById(sectionName)
    if (targetSection) {
      targetSection.classList.add("active")
      this.currentSection = sectionName

      // Actualizar navegación activa
      document.querySelectorAll(".nav-link").forEach((link) => {
        link.classList.remove("active")
      })

      const activeLink = document.querySelector(`[data-section="${sectionName}"]`)
      if (activeLink && activeLink.classList.contains("nav-link")) {
        activeLink.classList.add("active")
      }

      // Actualizar título de la sección
      const sectionTitles = {
        home: "Inicio",
        characters: "Explorar Personajes",
        create: "Crear Personaje",
        chat: "Chat",
        "my-characters": "Mis Personajes",
      }

      const titleElement = document.getElementById("currentSectionTitle")
      if (titleElement) {
        titleElement.textContent = sectionTitles[sectionName] || "AkioCharacters"
      }

      // Cargar contenido específico de la sección
      this.loadSectionContent(sectionName)
    }
  }

  loadSectionContent(sectionName) {
    switch (sectionName) {
      case "characters":
        this.renderCharacters()
        break
      case "my-characters":
        this.renderMyCharacters()
        break
      case "chat":
        if (window.modernChat) {
          window.modernChat.loadChatCharacters()
        }
        break
      case "create":
        if (window.modernCharacters) {
          window.modernCharacters.resetForm()
        }
        break
    }
  }

  renderFeaturedCharacters() {
    const container = document.getElementById("featuredCharacters")
    if (!container) return

    const featured = this.characters.slice(0, 6)

    if (featured.length === 0) {
      container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-robot"></i>
                    <h3>No hay personajes disponibles</h3>
                    <p>Sé el primero en crear un personaje increíble</p>
                    <button class="btn btn-primary" data-section="create">
                        <i class="fas fa-plus"></i>
                        Crear Personaje
                    </button>
                </div>
            `
      return
    }

    container.innerHTML = featured.map((character) => this.createCharacterCard(character)).join("")
    this.attachCharacterCardEvents(container)
  }

  renderCharacters() {
    const container = document.getElementById("allCharacters")
    if (!container) return

    if (this.characters.length === 0) {
      container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No hay personajes para explorar</h3>
                    <p>Parece que aún no hay personajes creados</p>
                    <button class="btn btn-primary" data-section="create">
                        <i class="fas fa-plus"></i>
                        Crear el Primero
                    </button>
                </div>
            `
      return
    }

    container.innerHTML = this.characters.map((character) => this.createCharacterCard(character, true)).join("")
    this.attachCharacterCardEvents(container)
  }

  renderMyCharacters() {
    const container = document.getElementById("myCharacters")
    if (!container) return

    if (this.myCharacters.length === 0) {
      container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-star"></i>
                    <h3>Aún no has creado personajes</h3>
                    <p>Crea tu primer personaje y comienza a chatear</p>
                    <button class="btn btn-primary" data-section="create">
                        <i class="fas fa-plus"></i>
                        Crear Mi Primer Personaje
                    </button>
                </div>
            `
      return
    }

    container.innerHTML = this.myCharacters.map((character) => this.createCharacterCard(character, true, true)).join("")
    this.attachCharacterCardEvents(container)
  }

  createCharacterCard(character, enhanced = false, isOwner = false) {
    const isMyCharacter = character.creator_id === this.currentUser?.id

    return `
            <div class="character-card ${enhanced ? "enhanced" : ""}" data-character-id="${character.id}">
                ${isMyCharacter ? '<div class="my-character-badge">Mi Personaje</div>' : ""}
                
                <div class="character-header">
                    <img src="${character.avatar || "/placeholder.svg?height=60&width=60"}" 
                         alt="${character.name}" class="character-avatar">
                    <div class="character-info">
                        <h3>
                            ${character.name}
                            ${character.verified ? '<i class="fas fa-check-circle verified-badge"></i>' : ""}
                        </h3>
                        <div class="character-creator">Por ${character.creator_name || "Usuario"}</div>
                    </div>
                </div>
                
                <div class="character-description">
                    ${character.description || "Sin descripción disponible"}
                </div>
                
                ${
                  enhanced
                    ? `
                    <div class="character-personality">
                        <strong>Personalidad:</strong> ${character.personality || "No especificada"}
                    </div>
                `
                    : ""
                }
                
                <div class="character-stats">
                    <div class="message-count">
                        <i class="fas fa-comments"></i>
                        <span>${character.message_count || 0} mensajes</span>
                    </div>
                    <div class="voice-indicator">
                        <i class="fas fa-${character.voice ? "volume-up" : "volume-mute"}"></i>
                        <span>${character.voice ? "Con voz" : "Sin voz"}</span>
                    </div>
                </div>
                
                <div class="character-actions">
                    <button class="btn btn-primary btn-chat" data-character-id="${character.id}">
                        <i class="fas fa-comments"></i>
                        Chatear
                    </button>
                    ${
                      isOwner
                        ? `
                        <button class="btn btn-secondary btn-edit" data-character-id="${character.id}">
                            <i class="fas fa-edit"></i>
                            Editar
                        </button>
                        <button class="btn btn-outline btn-delete" data-character-id="${character.id}">
                            <i class="fas fa-trash"></i>
                            Eliminar
                        </button>
                    `
                        : ""
                    }
                </div>
            </div>
        `
  }

  attachCharacterCardEvents(container) {
    // Botones de chat
    container.querySelectorAll(".btn-chat").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation()
        const characterId = btn.getAttribute("data-character-id")
        this.startChat(characterId)
      })
    })

    // Botones de editar
    container.querySelectorAll(".btn-edit").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation()
        const characterId = btn.getAttribute("data-character-id")
        this.editCharacter(characterId)
      })
    })

    // Botones de eliminar
    container.querySelectorAll(".btn-delete").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation()
        const characterId = btn.getAttribute("data-character-id")
        this.deleteCharacter(characterId)
      })
    })

    // Click en tarjeta para ver detalles
    container.querySelectorAll(".character-card").forEach((card) => {
      card.addEventListener("click", () => {
        const characterId = card.getAttribute("data-character-id")
        this.showCharacterDetails(characterId)
      })
    })
  }

  startChat(characterId) {
    this.showSection("chat")
    if (window.modernChat) {
      window.modernChat.selectCharacter(characterId)
    }
  }

  editCharacter(characterId) {
    this.showSection("create")
    if (window.modernCharacters) {
      window.modernCharacters.loadCharacterForEdit(characterId)
    }
  }

  async deleteCharacter(characterId) {
    const character = this.characters.find((c) => c.id == characterId)
    if (!character) return

    if (!confirm(`¿Estás seguro de que quieres eliminar a "${character.name}"? Esta acción no se puede deshacer.`)) {
      return
    }

    this.showLoading("Eliminando personaje...")

    try {
      const response = await fetch("/AkiomaeCodes/AkioCharacters/api/characters.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: characterId }),
      })

      if (response.ok) {
        this.showToast("Personaje eliminado exitosamente", "success")
        await this.loadCharacters()
        this.updateStats()
      } else {
        const error = await response.json()
        this.showToast(error.message || "Error al eliminar personaje", "error")
      }
    } catch (error) {
      console.error("Error deleting character:", error)
      this.showToast("Error al eliminar personaje", "error")
    } finally {
      this.hideLoading()
    }
  }

  showCharacterDetails(characterId) {
    const character = this.characters.find((c) => c.id == characterId)
    if (!character) return

    // Implementar modal de detalles del personaje
    console.log("Show character details:", character)
  }

  handleGlobalSearch(query) {
    if (!query.trim()) {
      this.renderCharacters()
      return
    }

    const filtered = this.characters.filter(
      (character) =>
        character.name.toLowerCase().includes(query.toLowerCase()) ||
        character.description.toLowerCase().includes(query.toLowerCase()) ||
        character.personality.toLowerCase().includes(query.toLowerCase()),
    )

    const container = document.getElementById("allCharacters")
    if (container) {
      if (filtered.length === 0) {
        container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No se encontraron resultados</h3>
                        <p>No hay personajes que coincidan con "${query}"</p>
                    </div>
                `
      } else {
        container.innerHTML = filtered.map((character) => this.createCharacterCard(character, true)).join("")
        this.attachCharacterCardEvents(container)
      }
    }
  }

  handleCharacterSearch(query) {
    this.handleGlobalSearch(query)
  }

  updateStats() {
    // Actualizar estadísticas en tiempo real
    const totalCharacters = document.getElementById("totalCharacters")
    const myCharacterCount = document.getElementById("myCharacterCount")
    const totalMessages = document.getElementById("totalMessages")
    const activeUsers = document.getElementById("activeUsers")

    if (totalCharacters) {
      totalCharacters.textContent = this.characters.length
    }

    if (myCharacterCount) {
      myCharacterCount.textContent = this.myCharacters.length
    }

    if (totalMessages) {
      const messageCount = this.characters.reduce((sum, char) => sum + (char.message_count || 0), 0)
      totalMessages.textContent = messageCount
    }

    if (activeUsers) {
      activeUsers.textContent = "1" // Por ahora solo el usuario actual
    }

    // Actualizar progreso de límite de personajes
    const progressFill = document.getElementById("characterLimitProgress")
    const progressText = document.getElementById("characterLimitText")

    if (progressFill && progressText) {
      const progress = (this.myCharacters.length / 2) * 100
      progressFill.style.width = `${progress}%`
      progressText.textContent = `${this.myCharacters.length} / 2 personajes creados`
    }
  }

  toggleSidebar() {
    const sidebar = document.getElementById("sidebar")
    const mainContent = document.querySelector(".main-content")

    this.sidebarCollapsed = !this.sidebarCollapsed

    if (this.sidebarCollapsed) {
      sidebar.classList.add("collapsed")
      mainContent.classList.add("expanded")
    } else {
      sidebar.classList.remove("collapsed")
      mainContent.classList.remove("expanded")
    }
  }

  toggleMobileSidebar() {
    const sidebar = document.getElementById("sidebar")
    sidebar.classList.toggle("open")
  }

  showThemeModal() {
    const modal = document.getElementById("themeModal")
    if (modal) {
      modal.classList.add("active")
      this.setupThemeModalEvents()
    }
  }

  hideThemeModal() {
    const modal = document.getElementById("themeModal")
    if (modal) {
      modal.classList.remove("active")
    }
  }

  setupThemeModalEvents() {
    const modal = document.getElementById("themeModal")
    if (!modal) return

    // Marcar tema actual
    modal.querySelectorAll(".theme-option").forEach((option) => {
      option.classList.remove("active")
      if (option.getAttribute("data-theme") === this.currentTheme) {
        option.classList.add("active")
      }
    })

    // Event listeners para opciones de tema
    modal.querySelectorAll(".theme-option").forEach((option) => {
      option.addEventListener("click", () => {
        const theme = option.getAttribute("data-theme")
        this.applyTheme(theme)
        this.hideThemeModal()
      })
    })

    // Cerrar modal
    const closeBtn = modal.querySelector(".theme-modal-close")
    if (closeBtn) {
      closeBtn.addEventListener("click", () => this.hideThemeModal())
    }
  }

  applyTheme(theme) {
    document.body.setAttribute("data-theme", theme)
    this.currentTheme = theme
    localStorage.setItem("theme", theme)

    this.showToast(`Tema "${this.getThemeName(theme)}" aplicado`, "success")
  }

  getThemeName(theme) {
    const names = {
      dark: "Oscuro",
      light: "Claro",
      blue: "Azul Océano",
      purple: "Púrpura Místico",
      green: "Verde Natura",
    }
    return names[theme] || theme
  }

  handleResize() {
    const isMobile = window.innerWidth <= 1024
    const sidebar = document.getElementById("sidebar")
    const mainContent = document.querySelector(".main-content")

    if (isMobile) {
      sidebar.classList.remove("collapsed")
      mainContent.classList.remove("expanded")
    }
  }

  showLoading(message = "Cargando...") {
    const overlay = document.getElementById("loadingOverlay")
    if (overlay) {
      overlay.querySelector("h3").textContent = message
      overlay.classList.add("active")
    }
  }

  hideLoading() {
    const overlay = document.getElementById("loadingOverlay")
    if (overlay) {
      overlay.classList.remove("active")
    }
  }

  showToast(message, type = "info", duration = 5000) {
    const container = document.getElementById("toastContainer")
    if (!container) return

    const toast = document.createElement("div")
    toast.className = `toast ${type}`

    const icons = {
      success: "fas fa-check-circle",
      error: "fas fa-exclamation-circle",
      warning: "fas fa-exclamation-triangle",
      info: "fas fa-info-circle",
    }

    const titles = {
      success: "Éxito",
      error: "Error",
      warning: "Advertencia",
      info: "Información",
    }

    toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icons[type]}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close">
                <i class="fas fa-times"></i>
            </button>
        `

    container.appendChild(toast)

    // Mostrar toast
    setTimeout(() => toast.classList.add("show"), 100)

    // Auto-ocultar
    const hideToast = () => {
      toast.classList.remove("show")
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast)
        }
      }, 300)
    }

    setTimeout(hideToast, duration)

    // Botón cerrar
    const closeBtn = toast.querySelector(".toast-close")
    if (closeBtn) {
      closeBtn.addEventListener("click", hideToast)
    }
  }
}

// Inicializar aplicación cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  window.modernApp = new ModernApp()
})
