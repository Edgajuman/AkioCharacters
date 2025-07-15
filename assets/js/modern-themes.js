// Sistema de Temas Moderno
class ModernThemes {
  constructor() {
    this.currentTheme = localStorage.getItem("theme") || "dark"
    this.themes = {
      dark: {
        name: "Oscuro",
        description: "Perfecto para sesiones nocturnas",
        colors: {
          primary: "#0f172a",
          secondary: "#1e293b",
          accent: "#3b82f6",
        },
      },
      light: {
        name: "Claro",
        description: "Limpio y minimalista",
        colors: {
          primary: "#ffffff",
          secondary: "#f8fafc",
          accent: "#3b82f6",
        },
      },
      blue: {
        name: "Azul Océano",
        description: "Tranquilo y profesional",
        colors: {
          primary: "#0c1426",
          secondary: "#1e2a47",
          accent: "#29b6f6",
        },
      },
      purple: {
        name: "Púrpura Místico",
        description: "Creativo y vibrante",
        colors: {
          primary: "#1a0d26",
          secondary: "#2d1b47",
          accent: "#a855f7",
        },
      },
      green: {
        name: "Verde Natura",
        description: "Relajante y natural",
        colors: {
          primary: "#0d1f17",
          secondary: "#1b2f26",
          accent: "#10b981",
        },
      },
    }

    this.init()
  }

  init() {
    this.applyTheme(this.currentTheme)
    this.setupThemeRotation()
  }

  applyTheme(themeName) {
    if (!this.themes[themeName]) return

    document.body.setAttribute("data-theme", themeName)
    this.currentTheme = themeName
    localStorage.setItem("theme", themeName)

    // Actualizar meta theme-color para móviles
    this.updateMetaThemeColor(this.themes[themeName].colors.primary)

    // Disparar evento personalizado
    window.dispatchEvent(
      new CustomEvent("themeChanged", {
        detail: { theme: themeName, themeData: this.themes[themeName] },
      }),
    )
  }

  updateMetaThemeColor(color) {
    let metaThemeColor = document.querySelector('meta[name="theme-color"]')
    if (!metaThemeColor) {
      metaThemeColor = document.createElement("meta")
      metaThemeColor.name = "theme-color"
      document.head.appendChild(metaThemeColor)
    }
    metaThemeColor.content = color
  }

  getThemeList() {
    return Object.entries(this.themes).map(([key, theme]) => ({
      key,
      ...theme,
      active: key === this.currentTheme,
    }))
  }

  getNextTheme() {
    const themeKeys = Object.keys(this.themes)
    const currentIndex = themeKeys.indexOf(this.currentTheme)
    const nextIndex = (currentIndex + 1) % themeKeys.length
    return themeKeys[nextIndex]
  }

  cycleTheme() {
    const nextTheme = this.getNextTheme()
    this.applyTheme(nextTheme)
    return this.themes[nextTheme]
  }

  setupThemeRotation() {
    // Auto-cambio de tema basado en la hora del día (opcional)
    if (localStorage.getItem("autoTheme") === "true") {
      this.setupAutoTheme()
    }
  }

  setupAutoTheme() {
    const hour = new Date().getHours()
    let autoTheme

    if (hour >= 6 && hour < 12) {
      autoTheme = "light" // Mañana
    } else if (hour >= 12 && hour < 18) {
      autoTheme = "blue" // Tarde
    } else if (hour >= 18 && hour < 22) {
      autoTheme = "purple" // Atardecer
    } else {
      autoTheme = "dark" // Noche
    }

    if (autoTheme !== this.currentTheme) {
      this.applyTheme(autoTheme)
    }
  }

  enableAutoTheme(enabled) {
    localStorage.setItem("autoTheme", enabled.toString())
    if (enabled) {
      this.setupAutoTheme()
    }
  }

  exportThemePreferences() {
    return {
      currentTheme: this.currentTheme,
      autoTheme: localStorage.getItem("autoTheme") === "true",
      customThemes: JSON.parse(localStorage.getItem("customThemes") || "{}"),
    }
  }

  importThemePreferences(preferences) {
    if (preferences.currentTheme && this.themes[preferences.currentTheme]) {
      this.applyTheme(preferences.currentTheme)
    }

    if (preferences.autoTheme !== undefined) {
      this.enableAutoTheme(preferences.autoTheme)
    }

    if (preferences.customThemes) {
      localStorage.setItem("customThemes", JSON.stringify(preferences.customThemes))
    }
  }
}

// Inicializar sistema de temas
document.addEventListener("DOMContentLoaded", () => {
  window.modernThemes = new ModernThemes()
})
