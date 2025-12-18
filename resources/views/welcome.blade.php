<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Institucional</title>
    <style>
       /* BOTÓN FLOTANTE */
    #chat-btn {
        position: fixed;
        bottom: 22px;
        right: 22px;
        background: #f7c221;
        border: none;
        border-radius: 50%;
        width: 62px;
        height: 62px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #0f3b53;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        z-index: 1000;
        transition: 0.25s ease;
    }

    #chat-btn:hover { 
        transform: scale(1.07); 
        background: #e6b320; 
    }

    .chat-bubble img {
        width: 35px;
        margin-top: 5px;
        transition: transform 0.4s ease, opacity 0.3s ease;
    }

    #chat-header-icon {
    width: 26px;
    margin-right: 8px;
    opacity: 0;
    transform: translateX(-10px) scale(0.8);
    transition: all 0.35s ease;
    }

    /* cuando el chat está activo */
    #chat-widget.open #chat-header-icon {
        opacity: 1;
        transform: translateX(0) scale(1);
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #chat-btn.hide {
    opacity: 0;
    pointer-events: none;
    transform: scale(0.8);
    transition: all 0.3s ease;
}



    /* WIDGET */
    #chat-widget {
        position: fixed;
        bottom: 50px;
        right: 22px;
        width: 370px;
        max-height: 540px;
        background: #ffffff;
        border-radius: 18px;
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 999;
        font-family: 'Segoe UI', Tahoma, sans-serif;
        color: #0f3b53;
        box-shadow: 0 10px 25px rgba(15, 59, 83, 0.25);
        transform-origin: bottom right;
        animation: popFromBubble 0.28s ease-out;
    }

    /* Animación suave */
    @keyframes popFromBubble {
        from {
            transform: scale(0.85);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }


    /* HEADER */
    #chat-header {
        display: flex;
        justify-content: space-between;
        background: linear-gradient(90deg, #f7c221, #ffdd5e);
        padding: 16px 20px;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f3b53;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* MENSAJES */
    #chat-messages {
        flex: 1;
        padding: 20px 18px;
        overflow-y: auto;
        background: #f3f3f3;
        scrollbar-width: thin;
        scrollbar-color: #0f3b53 transparent;
    }

    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #0f3b53; border-radius: 3px; }

    .message {
        margin-bottom: 16px;
        padding: 12px 18px;
        max-width: 78%;
        border-radius: 16px;
        font-size: 14.5px;
        line-height: 1.45;
        position: relative;
        clear: both;
    }

    /* Usuario */
    .message.user {
        background: #0f3b53;
        color: #fff;
        float: right;
        border-bottom-right-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Bot */
    .message.bot {
        background: #ffffff;
        color: #0f3b53;
        float: left;
        border: 1px solid #e4e4e4;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* BOTONES DE MENÚ (DISEÑO “TARJETA”) */
    .menu-btn {
        display: block;
        width: 100%;
        text-align: left;
        padding: 12px 14px;
        background: #f7c221;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        margin-top: 12px;
        color: #0f3b53;
        transition: 0.2s ease;
    }
    .menu-btn:hover {
        background: #dfb023;
        transform: translateY(-2px);
    }

    /* BOTÓN VOLVER AL INICIO */
    .back-btn {
        display: block;
        margin-top: 15px;
        padding: 10px;
        width: 100%;
        border-radius: 12px;
        background: #0f3b53;
        color: #fff;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: 0.2s ease;
    }
    .back-btn:hover {
        background: #0d3246;
        transform: translateY(-2px);
    }

    /* INPUT */
    #chat-input { 
        display: flex;
        border-top: 1px solid #ddd;
        background: #ffffff;
    }

    #chat-input input {
        flex: 1;
        padding: 14px 20px;
        border: none;
        outline: none;
        font-size: 15px;
        color: #0f3b53;
        background: #ffffff;
    }

    #chat-input button {
        background: #f7c221;
        border: none;
        padding: 0 25px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s ease;
    }
    #chat-input button:hover { background-color: #e6b320; }


    /*Cerrar chat*/
    .close-chat-btn {
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #0f3b53;
        font-weight: bold;
        transition: 0.2s ease;
    }

    .close-chat-btn:hover {
        transform: scale(1.2);
    }

    .image-btn {
        width: 15px;
        margin-top: 5px;
    }



    /*Minimizar chat*/
    .minimize-chat-btn {
        background: transparent;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #0f3b53;
        font-weight: bold;
        transition: 0.2s ease;
    }

    .minimize-chat-btn:hover {
        transform: scale(1.2);
    }

    /* Por defecto: solo minimizar visible */
    .close-chat-btn {
        display: none;
    }




    /*Estilos overlay de modal para cerrar chat*/

    /* Modal dentro del chat */
    .chat-modal-overlay {
        display: none;
        position: absolute;
        top: 0; 
        left: 0;
        width: 100%; 
        height: 100%;
        background: rgba(0,0,0,0.25);
        justify-content: center;
        align-items: center;
        z-index: 1001;
    }

    .chat-modal-box {
        background: #ffffff;
        padding: 15px 20px;
        border-radius: 12px;
        text-align: center;
        width: 85%;
        max-width: 280px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        font-family: 'Segoe UI', Tahoma, sans-serif; 
        font-size: 14px;
        color: #0f3b53;
    }

    .chat-modal-box h1 {
        font-size: 15px;
        margin-bottom: 10px;
    }

    .chat-modal-box p {
        color: #787a7c
    }

    .modal-buttons {
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .modal-confirm {
        background: #c00;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
    }

    .modal-cancel {
        background: #ddd;
        color: #333;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
    }

    .modal-confirm:hover { background: #a00; }
    .modal-cancel:hover { background: #bbb; }




    /* CHECKBOX TÉRMINOS */
    .terms-container label a {
        color: #0f3b53;
        font-weight: bold;
    }

    .message.bot a {
        word-break: break-word; /* evita que el link rompa el chat */
    }

    .typing {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 5px 0;
    }

    .typing span {
        display: block;
        width: 5px;
        height: 5px;
        background-color: #0f3b53;
        border-radius: 50%;
        animation: bounce 1.2s infinite ease-in-out;
    }

    .typing span:nth-child(1) { animation-delay: 0s; }
    .typing span:nth-child(2) { animation-delay: 0.2s; }
    .typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-8px); }
    } 



    </style>
</head>
<body>

@include('widgets.chat')

<script>
const chatBtn = document.getElementById("chat-btn");
const chatWidget = document.getElementById("chat-widget");
const chatMessages = document.getElementById("chat-messages");
const msgInput = document.getElementById("msgInput");

let acceptedTerms = false;

let supportRequestId = null;
let lastAgentMessageId = null;
let supportPollingInterval = null;



let supportSessionId = localStorage.getItem('support_session_id');

if (!supportSessionId) {
    supportSessionId = crypto.randomUUID();
    localStorage.setItem('support_session_id', supportSessionId);
}


// —————————————————————————————————————————————
// TEXTOS INTRODUCTORIOS PARA MENÚS 
// —————————————————————————————————————————————
const menuIntros = [
    "Elige una categoría de tu interés:",
    "Aquí tienes las opciones disponibles:",
    "Selecciona una de las siguientes opciones:"
];

function getRandomMenuIntro() {
    return menuIntros[Math.floor(Math.random() * menuIntros.length)];
}

// Mostrar / Ocultar widget
chatBtn.addEventListener("click", () => {
    if(chatWidget.style.display === "flex") {
        chatWidget.style.display = "none";
    } else {
        chatWidget.style.display = "flex";
        if(!chatMessages.querySelector(".message.bot")) {
            showWelcome();
        }
    }
});

// —————————————————————————————————————————————
// MENSAJE DE BIENVENIDA
// —————————————————————————————————————————————
function showWelcome() {
    const termsMsg = document.createElement("div");
    termsMsg.classList.add("message", "bot");
    termsMsg.innerHTML = `
        <div class="terms-container" style="margin-bottom: 10px;">
            <input type="checkbox" id="acceptTerms" />
            <label for="acceptTerms">
                Antes de iniciar, por favor acepta nuestros 
                <a href="#" target="_blank">términos y condiciones</a>:
            </label>
            <button class='menu-btn' id='accept-terms-btn' disabled style="opacity: 0.6; cursor: not-allowed;">Aceptar Términos</button>
        </div>
    `;
    chatMessages.appendChild(termsMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    const checkbox = termsMsg.querySelector("#acceptTerms");
    const acceptBtn = termsMsg.querySelector("#accept-terms-btn");

    checkbox.addEventListener("change", () => {
        acceptBtn.disabled = !checkbox.checked;
        acceptBtn.style.opacity = checkbox.checked ? "1" : "0.6";
        acceptBtn.style.cursor  = checkbox.checked ? "pointer" : "not-allowed";
    });

    acceptBtn.addEventListener("click", () => {
    acceptedTerms = true;
    termsMsg.remove();

    // Cambiar botones
    document.getElementById("minimizeChatBtn").style.display = "none";
    document.getElementById("closeChatBtn").style.display = "inline";

    showBotMessage(
        "¡Hola! Soy tu asistente virtual de la Universidad Católica. ¿En qué puedo ayudarte hoy?"
    );

    loadRootMenu();

    });
}

// —————————————————————————————————————————————
// MOSTRAR MENSAJE BOT
// —————————————————————————————————————————————
function showBotMessage(text) {
    const botMsg = document.createElement("div");
    botMsg.classList.add("message", "bot");

    const urlRegex = /(https?:\/\/[^\s]+)/g;
    text = text.replace(urlRegex, '<a href="$1" target="_blank" style="color:#0f3b53;text-decoration:underline;">$1</a>');

    botMsg.innerHTML = text.replace(/\n/g, "<br>");
    chatMessages.appendChild(botMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// —————————————————————————————————————————————
// CARGAR MENÚ RAÍZ
// —————————————————————————————————————————————
async function loadRootMenu() {
    try {
        const res = await fetch("/api/menu");
        const data = await res.json();

        if (data.type === "menu") {
            showMenuButtons(data.items, null, true);
        } else {
            showBotMessage(data.text);
        }
    } catch (err) {
        showBotMessage("No se pudo cargar el menú.");
    }
}

// —————————————————————————————————————————————
// MOSTRAR MENÚ + INTRO AUTOMÁTICA
// —————————————————————————————————————————————
function showMenuButtons(items, messageText = null, isRoot = false) {
    const wrapper = document.createElement("div");
    wrapper.classList.add("message", "bot");

    const introText = messageText ?? getRandomMenuIntro();

    const introDiv = document.createElement("div");
    introDiv.style.marginBottom = "8px";
    introDiv.textContent = introText;
    wrapper.appendChild(introDiv);

    items.forEach(item => {
        const btn = document.createElement("button");
        btn.classList.add("menu-btn");
        btn.textContent = item.title;
        btn.onclick = () => selectMenu(item);
        wrapper.appendChild(btn);
    });

    if (!isRoot) {
        const backBtn = document.createElement("button");
        backBtn.classList.add("menu-btn");
        backBtn.style.background = "#ddd";
        backBtn.style.color = "#0f3b53";
        backBtn.textContent = "⬅ Volver al menú principal";
        backBtn.onclick = () => loadRootMenu();
        wrapper.appendChild(backBtn);
    }

    chatMessages.appendChild(wrapper);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// —————————————————————————————————————————————
// SELECCIONAR MENÚ / SUBMENÚ
// —————————————————————————————————————————————
    async function selectMenu(item) {

        // acción especial
        if (item.action === 'contact_advisor') {
            showBotMessage("Te estamos conectando con un asesor...");

            try {
                const res = await fetch('/api/support/start', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },    
                    body: JSON.stringify({
                        session_id: supportSessionId
                    })
                });

                const data = await res.json();

                supportRequestId = data.support_request_id;

                if (data.advisor_assigned) {
                    showBotMessage("Un asesor se ha conectado contigo.");
                } else {
                    showBotMessage("Todos los asesores están ocupados, espera un momento.");
                }

                startSupportPolling();

            } catch (e) {
                console.error(e);
                showBotMessage('Error técnico al iniciar el soporte.');
            }

            return;
        }

        // comportamiento normal (menús)
        const userMsg = document.createElement("div");
        userMsg.classList.add("message", "user");
        userMsg.textContent = item.title;
        chatMessages.appendChild(userMsg);

        const typing = showTyping();

        try {
            const res = await fetch(`/api/menu/${item.id}`);
            const data = await res.json();

            typing.remove();

            if (data.type === "menu") {
                showMenuButtons(data.items);
            } else if (data.type === "response") {
                showBotMessage(data.text);
            }

        } catch {
            typing.remove();
            showBotMessage("Error al cargar el menú.");
        }
    }


// —————————————————————————————————————————————
// ANIMACIÓN "tres puntos"
// —————————————————————————————————————————————
function showTyping() {
    const wrapper = document.createElement("div");
    wrapper.classList.add("message", "bot");

    const typing = document.createElement("div");
    typing.classList.add("typing");
    typing.innerHTML = "<span></span><span></span><span></span>";

    wrapper.appendChild(typing);
    chatMessages.appendChild(wrapper);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    return wrapper;
}

// —————————————————————————————————————————————
// ENVÍO DE MENSAJE
// —————————————————————————————————————————————
async function sendMessage() {
    if (!acceptedTerms) {
        alert("Debes aceptar los términos y condiciones antes de escribir.");
        return;
    }

    const message = msgInput.value.trim();
    if (!message) return;

    const userMsg = document.createElement("div");
    userMsg.classList.add("message", "user");
    userMsg.textContent = message;
    chatMessages.appendChild(userMsg);
    msgInput.value = "";

    const typing = showTyping();

    try {
        const response = await fetch("/api/chatbot", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                message,
                session_id: supportSessionId
            })

        });

        const data = await response.json();

        setTimeout(() => {
            typing.remove();

            if (data.type === 'menu') {
                showMenuButtons(data.items, data.intro);
            } else if (data.type === 'response') {
                showBotMessage(data.text);
            } else {
                showBotMessage("No hay información disponible.");
            }

        }, 800 + Math.random() * 700);

    } catch (err) {
        showBotMessage("Error al conectarse al chatbot.");
    }
}

msgInput.addEventListener("keypress", e => {
    if (e.key === 'Enter') sendMessage();
});


// —————————————————————————————————————————————
// CERRAR CHAT CON MODAL DE CONFIRMACIÓN + MINIMIZAR
// —————————————————————————————————————————————


//FUNCION MINIMIZAR CHAT
const minimizeBtn = document.getElementById("minimizeChatBtn");

minimizeBtn.addEventListener("click", () => {
    chatWidget.style.display = "none";
    chatWidget.classList.remove("open");
    chatBtn.classList.remove("hide");
});

//Funcion resetear chat
function resetChatUI() {
    acceptedTerms = false;

    // Botones
    document.getElementById("closeChatBtn").style.display = "none";
    document.getElementById("minimizeChatBtn").style.display = "inline";

    // Limpiar mensajes
    chatMessages.innerHTML = "";
}




// Botón de cerrar chat
const closeBtn = document.querySelector(".close-chat-btn");
const closeModal = document.getElementById("closeModal");
const confirmClose = document.getElementById("confirmClose");
const cancelClose = document.getElementById("cancelClose");

// Mostrar modal al hacer clic en la X
closeBtn.addEventListener("click", () => {
    closeModal.style.display = "flex";
});

// Cancelar cierre
cancelClose.addEventListener("click", () => {
    closeModal.style.display = "none";
});

// Confirmar cierre
confirmClose.addEventListener("click", async () => {
    closeModal.style.display = "none";
    chatWidget.style.display = "none";
    chatWidget.classList.remove("open");
    chatBtn.classList.remove("hide");

    resetChatUI();

    stopSupportPolling();


    try {
        await fetch("/api/chatbot/clear-session", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
        });
    } catch (err) {
        console.error("Error al limpiar la sesión:", err);
    }
});

// —————————————————————————————————————————————
// ANIMACIÓN ÍCONO HEADER
// —————————————————————————————————————————————

chatBtn.addEventListener("click", () => {
    chatWidget.style.display = "flex";
    chatWidget.classList.add("open");

    chatBtn.classList.add("hide");

    if (!chatMessages.querySelector(".message.bot")) {
        showWelcome();
    }
});

confirmClose.addEventListener("click", async () => {
    closeModal.style.display = "none";
    chatWidget.style.display = "none";
    chatWidget.classList.remove("open");

    chatBtn.classList.remove("hide");

    chatMessages.innerHTML = "";

    await fetch("/api/chatbot/clear-session", { method: "POST" });
});

// —————————————————————————————————————————
// FUNCION PARA ESCUCHAR MENSAJES DEL ASESOR
// —————————————————————————————————————————
function startSupportPolling() {
    if (supportPollingInterval) return;

    supportPollingInterval = setInterval(async () => {
        if (!supportRequestId) return;

        try {
            const res = await fetch(`/api/support/messages?support_request_id=${supportRequestId}&last_message_id=${lastAgentMessageId ?? ''}`);
            const data = await res.json();

            if (data.status === 'closed') {
                showBotMessage("El chat fue cerrado por el asesor.");
                stopSupportPolling();
                return;
            }

            if (data.messages && data.messages.length) {
                data.messages.forEach(msg => {
                    showBotMessage(msg.message);
                    lastAgentMessageId = msg.id;
                });
            }

        } catch (e) {
            console.error("Error polling soporte:", e);
        }
    }, 3000);
}

// —————————————————————————————————————————
// DETENER POLLING AL CERRAR CHAT
// —————————————————————————————————————————
function stopSupportPolling() {
    if (supportPollingInterval) {
        clearInterval(supportPollingInterval);
        supportPollingInterval = null;
    }

    supportRequestId = null;
    lastAgentMessageId = null;
}





</script>



</body>
</html>
