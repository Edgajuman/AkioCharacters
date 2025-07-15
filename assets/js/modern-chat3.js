// Sistema de Chat Moderno adaptado para usar solo TTS del navegador
class ModernChat {
  constructor() {
    this.currentCharacter = null;
    this.messages = [];
    this.isTyping = false;
    this.voiceEnabled = false;
    this.currentUtterance = null;
    this.messageHistory = new Map();

    this.init();
  }

  init() {
    this.setupEventListeners();
    this.loadChatCharacters();
    this.setupVoiceToggle();
    this.setupMessageInput();
  }

  setupEventListeners() {
    const sendBtn = document.getElementById("sendBtn");
    const messageInput = document.getElementById("messageInput");

    if (sendBtn) {
      sendBtn.addEventListener("click", () => this.sendMessage());
    }

    if (messageInput) {
      messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          this.sendMessage();
        }
      });
    }

    const clearChatBtn = document.getElementById("clearChatBtn");
    if (clearChatBtn) {
      clearChatBtn.addEventListener("click", () => this.clearChat());
    }

    const voiceToggle = document.getElementById("voiceToggle");
    if (voiceToggle) {
      voiceToggle.addEventListener("click", () => this.toggleVoice());
    }

    const collapseBtn = document.querySelector(".sidebar-collapse-btn");
    if (collapseBtn) {
      collapseBtn.addEventListener("click", () => this.toggleChatSidebar());
    }
  }

  setupMessageInput() {
    const messageInput = document.getElementById("messageInput");
    if (!messageInput) return;

    messageInput.addEventListener("input", () => {
      messageInput.style.height = "auto";
      messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + "px";
    });

    let typingTimer;
    messageInput.addEventListener("input", () => {
      clearTimeout(typingTimer);
      this.showTypingIndicator(true);
      typingTimer = setTimeout(() => {
        this.showTypingIndicator(false);
      }, 1000);
    });
  }

  setupVoiceToggle() {
    const voiceToggle = document.getElementById("voiceToggle");
    if (voiceToggle) {
      this.updateVoiceToggleState();
    }
  }

  async loadChatCharacters() {
    const container = document.getElementById("chatCharacterList");
    if (!container) return;

    const characters = window.modernApp?.characters || [];

    if (characters.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-robot"></i>
          <h3>No hay personajes</h3>
          <p>Crea tu primer personaje para comenzar a chatear</p>
          <button class="btn btn-primary" data-section="create">
            <i class="fas fa-plus"></i>
            Crear Personaje
          </button>
        </div>`;
      return;
    }

    container.innerHTML = characters.map((character) => `
      <div class="character-list-item" data-character-id="${character.id}">
        <img src="${character.avatar || "/placeholder.svg?height=45&width=45"}" alt="${character.name}">
        <div class="character-list-info">
          <h4>${character.name}</h4>
          <p>${character.description || "Sin descripción"}</p>
        </div>
      </div>`).join("");

    container.querySelectorAll(".character-list-item").forEach((item) => {
      item.addEventListener("click", () => {
        const characterId = item.getAttribute("data-character-id");
        this.selectCharacter(characterId);
      });
    });
  }

  selectCharacter(characterId) {
    const character = window.modernApp?.characters.find((c) => c.id == characterId);
    if (!character) return;

    document.querySelectorAll(".character-list-item").forEach((item) => {
      item.classList.remove("active");
    });

    const selectedItem = document.querySelector(`[data-character-id="${characterId}"]`);
    if (selectedItem) {
      selectedItem.classList.add("active");
    }

    this.currentCharacter = character;
    this.loadMessageHistory(characterId);
    this.updateChatHeader();
    this.showChatInput();
    this.updateVoiceToggleState();

    window.modernApp?.showToast(`Iniciando chat con ${character.name}`, "info", 2000);
  }

  updateChatHeader() {
    const chatHeader = document.getElementById("chatHeader");
    if (!chatHeader || !this.currentCharacter) return;

    chatHeader.innerHTML = `
      <div class="character-info">
        <img src="${this.currentCharacter.avatar || "/placeholder.svg?height=50&width=50"}" alt="${this.currentCharacter.name}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-primary);">
        <div style="margin-left: var(--spacing-lg);">
          <h3 style="margin: 0; color: var(--text-primary); font-size: 1.25rem; font-weight: 700;">
            ${this.currentCharacter.name}
            ${this.currentCharacter.voice ? '<i class="fas fa-volume-up" style="color: var(--success-500); margin-left: var(--spacing-sm);"></i>' : ""}
          </h3>
          <p style="margin: 0; color: var(--text-secondary); font-size: 0.875rem;">
            ${this.currentCharacter.description || "Personaje de IA"}
          </p>
        </div>
      </div>
      <div class="chat-controls">
        ${
          this.currentCharacter.voice
            ? `<button id="voiceToggle" class="btn btn-icon" title="Alternar voz">
                <i class="fas fa-volume-${this.voiceEnabled ? "up" : "off"}"></i>
              </button>`
            : ""
        }
        <button id="clearChatBtn" class="btn btn-icon" title="Limpiar chat">
          <i class="fas fa-trash"></i>
        </button>
        <button class="btn btn-icon" title="Configuración">
          <i class="fas fa-cog"></i>
        </button>
      </div>`;

    this.setupEventListeners();
  }

  showChatInput() {
    const chatInput = document.getElementById("chatInput");
    if (chatInput) chatInput.style.display = "block";
  }

  async sendMessage() {
    const messageInput = document.getElementById("messageInput");
    if (!messageInput || !this.currentCharacter) return;

    const content = messageInput.value.trim();
    if (!content) return;

    messageInput.disabled = true;
    const sendBtn = document.getElementById("sendBtn");
    if (sendBtn) {
      sendBtn.disabled = true;
      sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    const userMessage = {
      id: Date.now(),
      type: "user",
      content,
      timestamp: new Date(),
    };

    this.messages.push(userMessage);
    this.renderMessages();
    this.saveMessageHistory();

    messageInput.value = "";
    messageInput.style.height = "auto";

    this.showTypingIndicator(true);

    try {
      const response = await fetch("/AkiomaeCodes/AkioCharacters/api/chat.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          character_id: this.currentCharacter.id,
          message: content,
          history: this.messages.slice(-10),
        }),
      });

      if (response.ok) {
        const data = await response.json();

        const characterMessage = {
          id: Date.now() + 1,
          type: "character",
          content: data.response,
          timestamp: new Date(),
          character: this.currentCharacter,
        };

        this.messages.push(characterMessage);
        this.renderMessages();
        this.saveMessageHistory();

        if (this.voiceEnabled && this.currentCharacter.voice) {
          this.playMessageVoice(characterMessage.id);
        }
      }
    } catch (error) {
      console.error("Error al enviar mensaje:", error);
    } finally {
      messageInput.disabled = false;
      if (sendBtn) {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
      }
      this.showTypingIndicator(false);
      messageInput.focus();
    }
  }

  playMessageVoice(messageId) {
    const message = this.messages.find((m) => m.id == messageId);
    if (!message || message.type === "user" || !this.currentCharacter?.voice) return;

    if (this.currentUtterance) {
      window.speechSynthesis.cancel();
      this.currentUtterance = null;
    }

    const utterance = new SpeechSynthesisUtterance(message.content);
    utterance.lang = "es-ES";
    utterance.voice = speechSynthesis.getVoices().find((v) => v.name === this.currentCharacter.voice) || null;
    this.currentUtterance = utterance;
    window.speechSynthesis.speak(utterance);
  }

  toggleVoice() {
    this.voiceEnabled = !this.voiceEnabled;
    this.updateVoiceToggleState();
    const status = this.voiceEnabled ? "activada" : "desactivada";
    window.modernApp?.showToast(`Voz ${status}`, "info", 2000);
    localStorage.setItem("voiceEnabled", this.voiceEnabled.toString());
  }

  updateVoiceToggleState() {
    const voiceToggle = document.getElementById("voiceToggle");
    if (!voiceToggle) return;

    const savedPreference = localStorage.getItem("voiceEnabled");
    if (savedPreference !== null) {
      this.voiceEnabled = savedPreference === "true";
    }

    const icon = voiceToggle.querySelector("i");
    if (icon) {
      icon.className = `fas fa-volume-${this.voiceEnabled ? "up" : "off"}`;
    }

    if (this.currentCharacter?.voice) {
      voiceToggle.style.display = "flex";
    } else {
      voiceToggle.style.display = "none";
    }
  }

  renderMessages() {
    const container = document.getElementById("chatMessages");
    if (!container) return;

    container.innerHTML = this.messages.map((message) => `
      <div class="message ${message.type}" data-message-id="${message.id}">
        <div class="message-content">${message.content}</div>
        <div class="message-time">${new Date(message.timestamp).toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" })}</div>
        ${message.type === "character" && this.voiceEnabled && this.currentCharacter?.voice ? `
          <button class="message-voice-btn" onclick="window.modernChat.playMessageVoice('${message.id}')">
            <i class="fas fa-volume-up"></i>
          </button>` : ""}
      </div>`).join("");
    this.scrollToBottom();
  }

  scrollToBottom() {
    const container = document.getElementById("chatMessages");
    if (container) {
      setTimeout(() => {
        container.scrollTop = container.scrollHeight;
      }, 100);
    }
  }

  showTypingIndicator(show) {
    const container = document.getElementById("chatMessages");
    if (!container) return;
    const existing = container.querySelector(".typing-indicator");
    if (existing) existing.remove();
    if (show) {
      const el = document.createElement("div");
      el.className = "typing-indicator";
      el.textContent = "...";
      container.appendChild(el);
    }
  }

  clearChat() {
    if (!this.currentCharacter) return;
    if (!confirm("¿Estás seguro de que quieres limpiar el historial de chat?")) return;
    this.messages = [];
    localStorage.removeItem(`chat_history_${this.currentCharacter.id}`);
    this.renderMessages();
    window.modernApp?.showToast("Chat limpiado", "success");
  }

  loadMessageHistory(characterId) {
    const key = `chat_history_${characterId}`;
    const stored = localStorage.getItem(key);
    this.messages = stored ? JSON.parse(stored) : [];
    this.renderMessages();
  }

  saveMessageHistory() {
    if (!this.currentCharacter) return;
    const key = `chat_history_${this.currentCharacter.id}`;
    localStorage.setItem(key, JSON.stringify(this.messages));
  }

  toggleChatSidebar() {
    const sidebar = document.querySelector(".chat-sidebar");
    if (sidebar) sidebar.classList.toggle("collapsed");
  }

  selectCharacterById(characterId) {
    this.selectCharacter(characterId);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.modernChat = new ModernChat();
});
