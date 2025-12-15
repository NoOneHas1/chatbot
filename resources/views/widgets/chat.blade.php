 <!-- BOTÓN FLOTANTE -->
    <button id="chat-btn" class="chat-bubble"><img src="{{ asset('images/chatbot.svg') }}" alt="Robot"></button>

    <!-- WIDGET -->
    <div id="chat-widget">
        <div id="chat-header">
            <div class="chat-header-left">
                <img id="chat-header-icon" src="{{ asset('images/chatbot.svg') }}" alt="Robot">
                <span>Asistente Unicatolica</span>
            </div>

            <button id="closeChatBtn" class="close-chat-btn"><img class="image-btn" src="{{ asset('images/cancel.svg') }}" alt="";></button>
            

        </div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="msgInput" placeholder="¿En qué puedo ayudarte?">
            <button onclick="sendMessage()">Enviar</button>
        </div>

        <!-- MODAL DE CONFIRMACIÓN PARA CERRAR EL CHAT -->
    <div id="closeModal" class="chat-modal-overlay">
        <div class="chat-modal-box">
            <h1>¿Seguro que deseas cerrar el chat?</h1>
            <p>La conversación finalizará al cerrar la ventana</p>

            <div class="modal-buttons">
                <button id="confirmClose" class="modal-confirm">Sí, cerrar</button>
                <button id="cancelClose" class="modal-cancel">Cancelar</button>
            </div>
        </div>
    </div>

    </div>

    
