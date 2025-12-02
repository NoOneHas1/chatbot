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
    </style>
</head>
<body>

   @include('widgets.chat')

    <script>
        const chatBtn = document.getElementById("chat-btn");
        const chatWidget = document.getElementById("chat-widget");
        const chatMessages = document.getElementById("chat-messages");
        const msgInput = document.getElementById("msgInput");

        // Mostrar / Ocultar widget al hacer clic en el botón
        chatBtn.addEventListener("click", () => {
             if (chatWidget.style.display === "flex") {
                    chatWidget.style.display = "none";
            } else {
                chatWidget.style.display = "flex";

        // >>> Solo mostrar el mensaje de bienvenida si aún no hay mensajes del bot
        const existingWelcome = chatMessages.querySelector(".message.bot");
        if (!existingWelcome) {
            const welcomeMsg = document.createElement("div");
            welcomeMsg.classList.add("message", "bot");
            welcomeMsg.textContent = "Hola! Soy tu asistente virtual de la Universidad Católica, ¿En qué puedo ayudarte hoy?";
            chatMessages.appendChild(welcomeMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
});


        // Función para enviar mensaje
        async function sendMessage() {
            const message = msgInput.value.trim();
            if (!message) return;

            // Mostrar mensaje del usuario
            const userMsg = document.createElement("div");
            userMsg.classList.add("message", "user");
            userMsg.textContent = message;
            chatMessages.appendChild(userMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            msgInput.value = "";

            try {
                const response = await fetch("/api/chatbot", {
                    method: "POST",
                    headers: {"Content-Type": "application/json"},
                    body: JSON.stringify({message})
                });
                const data = await response.json();

                // Mostrar mensaje del bot
                const botMsg = document.createElement("div");
                botMsg.classList.add("message", "bot");
                botMsg.innerHTML = data.reply.replace(/\n/g, "<br>") || "No hay respuesta.";
                chatMessages.appendChild(botMsg);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } catch (err) {
                const errorMsg = document.createElement("div");
                errorMsg.classList.add("message", "bot");
                errorMsg.textContent = "Error al conectarse al chatbot.";
                chatMessages.appendChild(errorMsg);
            }
        }

        // Enviar mensaje con Enter
        msgInput.addEventListener("keypress", function(e){
            if(e.key === 'Enter') sendMessage();
        });
    </script>

</body>
</html>
