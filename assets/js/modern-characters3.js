// Gestión de Personajes Moderna
class ModernCharacters {
  constructor() {
    this.currentStep = 1
    this.maxSteps = 3
    this.formData = {}
    this.editingCharacterId = null
    this.voicePreviewAudio = null

    this.init()
  }

  init() {
    this.setupEventListeners()
    this.setupVoiceSelector()
    this.setupFormValidation()
    this.updateCharacterLimit()
  }

  setupEventListeners() {
    const nextBtn = document.getElementById("nextStepBtn")
    const prevBtn = document.getElementById("prevStepBtn")
    const createBtn = document.getElementById("createBtn")

    if (nextBtn) nextBtn.addEventListener("click", () => this.nextStep())
    if (prevBtn) prevBtn.addEventListener("click", () => this.prevStep())
    if (createBtn) createBtn.addEventListener("click", (e) => {
      e.preventDefault()
      this.submitForm()
    })

    const avatarInput = document.getElementById("characterAvatar")
    if (avatarInput) avatarInput.addEventListener("input", (e) => this.updateAvatarPreview(e.target.value))

    this.setupRealTimePreview()

    const form = document.getElementById("createCharacterForm")
    if (form) form.addEventListener("submit", (e) => {
      e.preventDefault()
      this.submitForm()
    })
  }

  setupVoiceSelector() {
    const voiceOptions = document.querySelectorAll(".voice-option")
    const voiceInput = document.getElementById("characterVoice")

    voiceOptions.forEach((option) => {
      option.addEventListener("click", () => {
        voiceOptions.forEach((opt) => opt.classList.remove("selected"))
        option.classList.add("selected")

        const voiceType = option.getAttribute("data-voice")
        if (voiceInput) voiceInput.value = voiceType
        this.updateVoicePreview(voiceType)
      })
    })

    document.querySelectorAll(".voice-preview-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation()
        const voiceType = btn.getAttribute("data-voice")
        if (voiceType) this.playVoicePreview(voiceType)
      })
    })
  }

  setupRealTimePreview() {
    const inputs = ["characterName", "characterDescription", "characterPersonality", "characterAvatar"]
    inputs.forEach((id) => {
      const input = document.getElementById(id)
      if (input) input.addEventListener("input", () => this.updatePreview())
    })
  }

  setupFormValidation() {
    const requiredFields = ["characterName", "characterPersonality", "characterSystemPrompt"]
    requiredFields.forEach((id) => {
      const field = document.getElementById(id)
      if (field) {
        field.addEventListener("blur", () => this.validateField(id))
        field.addEventListener("input", () => this.clearFieldError(id))
      }
    })
  }

  validateField(fieldId) {
    const field = document.getElementById(fieldId)
    if (!field) return true

    const value = field.value.trim()
    let isValid = true
    let errorMessage = ""

    switch (fieldId) {
      case "characterName":
        if (!value) { isValid = false; errorMessage = "El nombre es obligatorio" }
        else if (value.length > 50) { isValid = false; errorMessage = "Máximo 50 caracteres" }
        break
      case "characterPersonality":
        if (!value) { isValid = false; errorMessage = "La personalidad es obligatoria" }
        else if (value.length > 300) { isValid = false; errorMessage = "Máximo 300 caracteres" }
        break
      case "characterSystemPrompt":
        if (!value) { isValid = false; errorMessage = "Instrucciones requeridas" }
        else if (value.length > 500) { isValid = false; errorMessage = "Máximo 500 caracteres" }
        break
    }

    this.showFieldError(fieldId, isValid ? "" : errorMessage)
    return isValid
  }

  showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId)
    if (!field) return

    this.clearFieldError(fieldId)

    if (message) {
      field.classList.add("error")
      const errorDiv = document.createElement("div")
      errorDiv.className = "field-error"
      errorDiv.textContent = message
      field.parentNode.appendChild(errorDiv)
    }
  }

  clearFieldError(fieldId) {
    const field = document.getElementById(fieldId)
    if (!field) return

    field.classList.remove("error")
    const errorDiv = field.parentNode.querySelector(".field-error")
    if (errorDiv) errorDiv.remove()
  }

  nextStep() {
    if (!this.validateCurrentStep()) return
    if (this.currentStep < this.maxSteps) {
      this.currentStep++
      this.updateStepDisplay()
    }
  }

  prevStep() {
    if (this.currentStep > 1) {
      this.currentStep--
      this.updateStepDisplay()
    }
  }

  validateCurrentStep() {
    const stepEl = document.querySelector(`.form-step[data-step="${this.currentStep}"]`)
    if (!stepEl) return true

    const required = stepEl.querySelectorAll("input[required], textarea[required]")
    let isValid = true
    required.forEach((field) => {
      if (!this.validateField(field.id)) isValid = false
    })
    return isValid
  }

  updateStepDisplay() {
    document.querySelectorAll(".form-step").forEach((step) => step.classList.remove("active"))
    const currentStepElement = document.querySelector(`.form-step[data-step="${this.currentStep}"]`)
    if (currentStepElement) currentStepElement.classList.add("active")

    document.querySelectorAll(".progress-step").forEach((step) => step.classList.remove("active"))
    for (let i = 1; i <= this.currentStep; i++) {
      const progressStep = document.querySelector(`.progress-step[data-step="${i}"]`)
      if (progressStep) progressStep.classList.add("active")
    }

    const prevBtn = document.getElementById("prevStepBtn")
    const nextBtn = document.getElementById("nextStepBtn")
    const createBtn = document.getElementById("createBtn")

    if (prevBtn) prevBtn.style.display = this.currentStep === 1 ? "none" : "inline-flex"
    if (nextBtn) nextBtn.style.display = this.currentStep === this.maxSteps ? "none" : "inline-flex"
    if (createBtn) createBtn.style.display = this.currentStep === this.maxSteps ? "inline-flex" : "none"
  }

  updateAvatarPreview(url) {
    const preview = document.getElementById("avatarPreview")
    const previewImg = document.getElementById("previewAvatar")
    if (url && this.isValidImageUrl(url)) {
      if (preview) preview.src = url
      if (previewImg) previewImg.src = url
    }
  }

  isValidImageUrl(url) {
    try {
      new URL(url)
      return /\.(jpg|jpeg|png|gif|webp)$/i.test(url)
    } catch {
      return false
    }
  }

  updatePreview() {
    const name = document.getElementById("characterName")?.value || "Nombre del Personaje"
    const description = document.getElementById("characterDescription")?.value || "Descripción del personaje..."
    const personality = document.getElementById("characterPersonality")?.value || "Rasgos de personalidad..."
    const avatar = document.getElementById("characterAvatar")?.value
    const voice = document.getElementById("characterVoice")?.value

    document.getElementById("previewName").textContent = name
    document.getElementById("previewDescription").textContent = description
    document.getElementById("previewPersonality").textContent = personality

    this.updateVoicePreview(voice)
    if (avatar) this.updateAvatarPreview(avatar)
  }

  updateVoicePreview(voiceType) {
    const previewVoice = document.getElementById("previewVoice")
    const voiceNames = {
      "": "Sin voz configurada",
      female: "Voz Femenina",
      male: "Voz Masculina",
    }
    if (previewVoice) previewVoice.textContent = voiceNames[voiceType] || "Sin voz configurada"
  }

  async playVoicePreview(voiceType) {
    const utterance = new SpeechSynthesisUtterance("Hola, soy tu nuevo personaje de IA. ¡Estoy emocionado de conocerte!")

    // Seleccionar voz según tipo
    const voices = speechSynthesis.getVoices()
    if (voiceType === "female") {
      utterance.voice = voices.find(v => v.name.toLowerCase().includes("female") || v.name.toLowerCase().includes("mujer"))
    } else if (voiceType === "male") {
      utterance.voice = voices.find(v => v.name.toLowerCase().includes("male") || v.name.toLowerCase().includes("hombre"))
    }

    speechSynthesis.speak(utterance)
  }

  async submitForm() {
    let isValid = true
    for (let step = 1; step <= this.maxSteps; step++) {
      this.currentStep = step
      if (!this.validateCurrentStep()) {
        isValid = false
        this.updateStepDisplay()
        break
      }
    }

    if (!isValid) {
      window.modernApp?.showToast("Completa todos los campos requeridos", "error")
      return
    }

    if (!this.editingCharacterId && window.modernApp?.myCharacters.length >= 2) {
      window.modernApp?.showToast("Has alcanzado el límite de 2 personajes", "warning")
      return
    }

    const formData = {
      name: document.getElementById("characterName")?.value.trim(),
      description: document.getElementById("characterDescription")?.value.trim(),
      personality: document.getElementById("characterPersonality")?.value.trim(),
      system_prompt: document.getElementById("characterSystemPrompt")?.value.trim(),
      avatar: document.getElementById("characterAvatar")?.value.trim(),
      voice: document.getElementById("characterVoice")?.value,
    }

    if (this.editingCharacterId) formData.id = this.editingCharacterId

    window.modernApp?.showLoading(this.editingCharacterId ? "Actualizando personaje..." : "Creando personaje...")

    try {
      const method = this.editingCharacterId ? "PUT" : "POST"
      const response = await fetch("/AkiomaeCodes/AkioCharacters/api/characters.php", {
        method,
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      })

      const result = await response.json()

      if (response.ok) {
        window.modernApp?.showToast(this.editingCharacterId ? "Personaje actualizado" : "Personaje creado", "success")
        await window.modernApp?.loadCharacters()
        window.modernApp?.updateStats()
        this.resetForm()
        window.modernApp?.showSection("home")
      } else {
        window.modernApp?.showToast(result.message || "Error al procesar", "error")
      }
    } catch (error) {
      console.error("Error:", error)
      window.modernApp?.showToast("Error de conexión", "error")
    } finally {
      window.modernApp?.hideLoading()
    }
  }

  resetForm() {
    const form = document.getElementById("createCharacterForm")
    if (form) form.reset()

    this.currentStep = 1
    this.editingCharacterId = null
    this.updateStepDisplay()

    document.querySelectorAll(".field-error").forEach((e) => e.remove())
    document.querySelectorAll(".error").forEach((e) => e.classList.remove("error"))
    document.querySelectorAll(".voice-option").forEach((o) => o.classList.remove("selected"))

    this.updateAvatarPreview("")
    this.updatePreview()
    this.updateCharacterLimit()
  }

  updateCharacterLimit() {
    const myCharacterCount = window.modernApp?.myCharacters.length || 0
    const progressFill = document.getElementById("characterLimitProgress")
    const progressText = document.getElementById("characterLimitText")

    if (progressFill && progressText) {
      const progress = (myCharacterCount / 2) * 100
      progressFill.style.width = `${progress}%`
      progressText.textContent = `${myCharacterCount} / 2 personajes creados`
    }

    const createBtn = document.getElementById("createBtn")
    if (createBtn && !this.editingCharacterId && myCharacterCount >= 2) {
      createBtn.disabled = true
      createBtn.innerHTML = '<i class="fas fa-lock"></i> Límite Alcanzado'
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.modernCharacters = new ModernCharacters()
})
