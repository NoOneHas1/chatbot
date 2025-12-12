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
    #chat-btn:hover { transform: scale(1.07); background: #e6b320; }

    /* WIDGET */
    #chat-widget {
        position: fixed;
        bottom: 95px;
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
        animation: popIn 0.25s ease;
        box-shadow: 0 10px 25px rgba(15, 59, 83, 0.25);
    }

    /* Animación suave */
    @keyframes popIn {
        from { transform: scale(.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
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


    /*Boton de cerrar el chat*/
    .close-chat-btn {
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #0f3b53;
        font-weight: bold;
    }

    .close-chat-btn:hover {
        color: #c00;
    }

    /*Estilos overlay de modal para cerrar chat*/

    .modal-overlay {
        display: none; 
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(2px);
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .modal-box {
        background: white;
        padding: 20px;
        width: 85%;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .modal-buttons {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .modal-confirm {
        background: #c00;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
    }

    .modal-cancel {
        background: #ddd;
        color: #333;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
    }



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

// Variables de estado
let acceptedTerms = false;

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

// Función de bienvenida y términos
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

    // Activar/desactivar botón según checkbox
    checkbox.addEventListener("change", () => {
        if (checkbox.checked) {
            acceptBtn.disabled = false;
            acceptBtn.style.opacity = "1";
            acceptBtn.style.cursor = "pointer";
        } else {
            acceptBtn.disabled = true;
            acceptBtn.style.opacity = "0.6";
            acceptBtn.style.cursor = "not-allowed";
        }
    });

    // Click del botón
    acceptBtn.addEventListener("click", () => {
        acceptedTerms = true;
        termsMsg.remove();
        showBotMessage("¡Hola! Soy tu asistente virtual de la Universidad Católica. ¿En qué puedo ayudarte hoy?");
        loadRootMenu();
    });
}




// Función para mostrar mensaje bot
function showBotMessage(text) {
    const botMsg = document.createElement("div");
    botMsg.classList.add("message", "bot");
    
    // Convertir URLs a links clickeables
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    text = text.replace(urlRegex, '<a href="$1" target="_blank" style="color:#0f3b53;text-decoration:underline;">$1</a>');

    botMsg.innerHTML = text.replace(/\n/g, "<br>");
    chatMessages.appendChild(botMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}




// Función para cargar el menú raíz
async function loadRootMenu() {
    try {
        const res = await fetch("/api/menu");
        const data = await res.json();

        if (data.type === "menu") {
            showMenuButtons(data.items, "", true); // indicamos que es menú raíz
        } else {
            showBotMessage(data.text); // si es respuesta final
        }
    } catch(err) {
        console.error(err);
        showBotMessage("No se pudo cargar el menú.");
    }
}




// Función para mostrar botones de menú
function showMenuButtons(items, messageText = "", isRoot = false) {
    // Contenedor único del mensaje del bot
    const wrapper = document.createElement("div");
    wrapper.classList.add("message", "bot");

    // Si hay texto dinámico, se muestra arriba de los botones
    if (messageText) {
        const textDiv = document.createElement("div");
        textDiv.textContent = messageText;
        wrapper.appendChild(textDiv);
    }

    // Botones de submenú
    items.forEach(item => {
        const btn = document.createElement("button");
        btn.classList.add("menu-btn");
        btn.textContent = item.title;
        // Pasamos también el texto al selectMenu
        btn.onclick = () => selectMenu(item.id, item.title);
        wrapper.appendChild(btn);
    });

    // Solo mostrar botón de volver si NO es menú raíz
    if (!isRoot) {
        const backBtn = document.createElement("button");
        backBtn.classList.add("menu-btn");
        backBtn.style.background = "#ddd";
        backBtn.style.color = "#0f3b53";
        backBtn.textContent = "⬅ Volver al menú principal";
        backBtn.onclick = () => loadRootMenu(); // carga menú raíz
        wrapper.appendChild(backBtn);
    }

    // Agregamos todo al chat
    chatMessages.appendChild(wrapper);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}



// Función para seleccionar menú/submenú
async function selectMenu(menuId, menuText = null) {
    // Mostrar la opción seleccionada como mensaje de usuario
    if(menuText) {
        const userMsg = document.createElement("div");
        userMsg.classList.add("message", "user");
        userMsg.textContent = menuText;
        chatMessages.appendChild(userMsg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Animación "bot está escribiendo"
    const typing = showTyping();

    try {
        const res = await fetch(`/api/menu/${menuId}`);
        const data = await res.json();

        // Quitar "escribiendo..." inmediatamente para que se vea la animación
        typing.remove();

        // Mostrar el resultado del menú
        if (data.type === "menu" && data.items.length > 0) {
            const messageText = data.message || "Por favor, selecciona una opción:";
            showMenuButtons(data.items, messageText); 
        }
        else if (data.type === "response") {
            showBotMessage(data.text);
        }
        else {
            showBotMessage("No hay información disponible.");
        }

    } catch(err) {
        typing.remove();
        console.error(err);
        showBotMessage("Error al cargar el menú.");
    }
}


//Funcion para mostrar que el bot está escribiendo
function showTyping() {
    // Contenedor del mensaje del bot
    const wrapper = document.createElement("div");
    wrapper.classList.add("message", "bot");

    // Contenedor de los puntos animados
    const typing = document.createElement("div");
    typing.classList.add("typing");
    typing.innerHTML = "<span></span><span></span><span></span>";

    wrapper.appendChild(typing);
    chatMessages.appendChild(wrapper);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    return wrapper; // para poder removerlo después
}





// Función para volver al menú raíz (usada en botones de volver)
function showBackToRoot() {
    // Eliminamos cualquier botón de volver anterior
    const existingBack = chatMessages.querySelector(".back-btn-wrap");
    if (existingBack) existingBack.remove();

    const wrap = document.createElement("div");
    wrap.classList.add("message", "bot", "back-btn-wrap"); // agregamos clase para identificarlo

    const btn = document.createElement("button");
    btn.classList.add("menu-btn");
    btn.style.background = "#ddd";
    btn.style.color = "#0f3b53";
    btn.textContent = "⬅ Volver al menú principal";

    btn.onclick = () => {
        loadRootMenu(); // carga menú raíz
    };

    wrap.appendChild(btn);
    chatMessages.appendChild(wrap);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}




//Funcion para enviar mensaje
async function sendMessage() {
    if(!acceptedTerms) {
        alert("Debes aceptar los términos y condiciones antes de escribir.");
        return;
    }

    const message = msgInput.value.trim();
    if (!message) return;

    // Mostrar mensaje usuario
    const userMsg = document.createElement("div");
    userMsg.classList.add("message", "user");
    userMsg.textContent = message;
    chatMessages.appendChild(userMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    msgInput.value = "";

    // Animación "bot está escribiendo"
    const typing = showTyping();

    try {
        const response = await fetch("/api/chatbot", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({message})
        });

        const data = await response.json();


        setTimeout(() => {
            typing.remove();

            // Revisamos si es menú
            if(data.type === 'menu' && data.items && data.items.length > 0) {
                showMenuButtons(data.items);
            } 
            // Si es respuesta normal de IA
            else if(data.type === 'response') {
                let replyText = "";
                if(data.text) {
                    if(typeof data.text === "object" && data.text.original && data.text.original.reply) {
                        replyText = data.text.original.reply;
                    } else if(typeof data.text === "string") {
                        replyText = data.text;
                    } else {
                        replyText = "No hay información disponible.";
                    }
                } else {
                    replyText = "No hay información disponible.";
                }
                showBotMessage(replyText);
            } 
            else {
                showBotMessage("No hay información disponible.");
            }

        }, 800 + Math.random() * 700);

    } catch(err) {
        showBotMessage("Error al conectarse al chatbot: " + err);
    }
}

// Enviar con Enter
msgInput.addEventListener("keypress", function(e){
    if(e.key === 'Enter') sendMessage();
});



</script>

</body>
</html>
