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
            bottom: 20px;
            right: 20px;
            background-color: #f7c221;
            color: #ffff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        #chat-btn:hover {
            background-color: #dfb023;
        }

        /* WIDGET DEL CHAT */
        #chat-widget {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 360px;
            max-height: 520px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(15, 59, 83, 0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 999;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f3b53;
        }

        /* CABECERA */
        #chat-header {
            background: #f7c221;
            color: #0f3b53;
            padding: 15px 20px;
            font-weight: 700;
            font-size: 1.2rem;
            text-align: start;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            user-select: none;
        }

        /* CONTENEDOR DE MENSAJES */
        #chat-messages {
            flex: 1;
            padding: 20px 15px;
            overflow-y: auto;
            background: #f9f9f9;
            scrollbar-width: thin;
            scrollbar-color: #f7c221 transparent;
        }

        /* Scrollbar */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background-color: #0f3b53;
            border-radius: 10px;
        }

        /* MENSAJES */
        .message {
            margin-bottom: 14px;
            padding: 12px 18px;
            max-width: 75%;
            font-size: 14.5px;
            line-height: 1.4;
            position: relative;
            clear: both;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* MENSAJE DEL USUARIO */
        .message.user {
            background: #0f3b53;
            color: #fff;
            float: right;
            border-bottom-right-radius: 4px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }


        /* MENSAJE DEL BOT */
        .message.bot {
            background: #f5f5f5;
            color: #0f3b53;
            float: left;
            border-bottom-left-radius: 4px;
            border-top-right-radius: 20px;
            border-top-left-radius: 20px;
            border: 1px solid #e0e0e0;
        }

        /* Agregar "cola" en mensaje bot */
        .message.bot::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -8px;
            width: 0;
            height: 0;
            border-top: 12px solid #f7f7f7;
            border-right: 8px solid transparent;
        }

        .message.bot a {
            color: #0f3b53;
            text-decoration: underline;
        }

        /* INPUT Y BOTÓN */
        #chat-input {
            display: flex;
            border-top: 1px solid #ddd;
            background: #ffffff;
        }

        #chat-input input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            outline: none;
            font-size: 15px;
            font-weight: 500;
            color: #0f3b53;
        }

        #chat-input input::placeholder {
            color: #999;
            font-style: italic;
        }

        #chat-input button {
            background: #f7c221;
            font-size: 15px;
            font-weight: 700;
            color: #0f3b53;
            border: none;
            padding: 0 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #chat-input button:hover {
            background-color: #e6b320;
        }

    /* Botones de menú */
        .option-btn {
            display: block;
            width: 100%;
            margin: 6px 0;
            padding: 10px;
            background: #f7c221;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            color: #0f3b53;
            cursor: pointer;
            transition: background 0.3s;
        }
        .option-btn:hover { background: #e6b320; }
    </style>
</head>
<body>

   @include('widgets.chat')
<script>
    const chatBtn = document.getElementById("chat-btn");
const chatWidget = document.getElementById("chat-widget");
const chatMessages = document.getElementById("chat-messages");
const msgInput = document.getElementById("msgInput");

let currentMenuPath = [];

chatBtn.addEventListener("click", () => {
    if (chatWidget.style.display === "flex") {
        chatWidget.style.display = "none";
    } else {
        chatWidget.style.display = "flex";

        if (!chatMessages.querySelector(".message.bot")) {
            appendBotMessage("Hola! Soy tu asistente virtual de la Universidad Católica, ¿en qué puedo ayudarte hoy?");
            fetchMenu(''); // Mostrar menú raíz
        }
    }
});

function appendBotMessage(text) {
    const botMsg = document.createElement("div");
    botMsg.classList.add("message", "bot");
    botMsg.innerHTML = text.replace(/\n/g, "<br>");
    chatMessages.appendChild(botMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function appendUserMessage(text) {
    const userMsg = document.createElement("div");
    userMsg.classList.add("message", "user");
    userMsg.textContent = text;
    chatMessages.appendChild(userMsg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showMenuOptions(options) {
    options.forEach(option => {
        // Evitar duplicados
        if (!Array.from(chatMessages.querySelectorAll("button.option-btn")).some(btn => btn.textContent === option)) {
            const btn = document.createElement("button");
            btn.classList.add("option-btn");
            btn.textContent = option;
            btn.style.margin = "3px 5px 3px 0";
            btn.onclick = () => handleMenuSelection(option);
            chatMessages.appendChild(btn);
        }
    });

    chatMessages.scrollTop = chatMessages.scrollHeight;
}

async function fetchMenu(option) {
    try {
        const response = await fetch("/api/chatbot", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ message: option, menu_path: currentMenuPath })
        });

        const data = await response.json();
        currentMenuPath = data.menu_path || [];

        appendBotMessage(data.reply);

        if (data.menu && data.menu.length > 0) {
            showMenuOptions(data.menu);
        }
    } catch (err) {
        appendBotMessage("Error al comunicarse con el servidor.");
    }
}

async function handleMenuSelection(optionLabel) {
    appendUserMessage(optionLabel);

    // Solo deshabilitar el botón clickeado
    const buttons = chatMessages.querySelectorAll("button.option-btn");
    buttons.forEach(btn => {
        if (btn.textContent === optionLabel) {
            btn.disabled = true;
            btn.style.opacity = "0.6";
            btn.style.cursor = "default";
        } else {
            btn.disabled = false;
            btn.style.opacity = "1";
            btn.style.cursor = "pointer";
        }
    });

    await fetchMenu(optionLabel);
}

async function sendMessage() {
    const message = msgInput.value.trim();
    if (!message) return;

    appendUserMessage(message);
    msgInput.value = "";

    // Limpiar botones porque mensaje libre no es menú
    const buttons = chatMessages.querySelectorAll("button.option-btn");
    buttons.forEach(btn => btn.remove());

    try {
        const response = await fetch("/api/chatbot", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ message })
        });
        const data = await response.json();

        appendBotMessage(data.reply);
        currentMenuPath = [];
    } catch (err) {
        appendBotMessage("Error al conectarse al chatbot.");
    }
}

msgInput.addEventListener("keypress", e => {
    if (e.key === "Enter") sendMessage();
});
</script>





</body>
</html>