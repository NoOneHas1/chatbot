 <!-- BOTÓN FLOTANTE -->
    <button id="chat-btn"><img src="{{ asset('images/chatbot.svg') }}" alt="Robot" style="width: 35px; margin-top: 5px;"></button>

    <!-- WIDGET -->
    <div id="chat-widget">
        <div id="chat-header">
            <span>Asistente Unicatolica</span>
            <button id="closeChatBtn" class="close-chat-btn">X</button>
        </div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="msgInput" placeholder="¿En qué puedo ayudarte?">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN PARA CERRAR EL CHAT -->
    <div id="closeChatModal" class="modal-overlay">
    <div class="modal-box">
        <p>¿Seguro que deseas cerrar el chat y borrar el historial?</p>

        <div class="modal-buttons">
            <button id="confirmCloseChat" class="modal-confirm">Sí, cerrar</button>
            <button id="cancelCloseChat" class="modal-cancel">Cancelar</button>
        </div>
    </div>
</div>
