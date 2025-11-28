 <!-- BOTÓN FLOTANTE -->
    <button id="chat-btn"><img src="{{ asset('storage/images/icons/robot-solid-full.svg') }}" alt="Robot" style="width: 35px; margin-top: 5px;"></button>

    <!-- WIDGET -->
    <div id="chat-widget">
        <div id="chat-header">Asistente Unicatolica</div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="msgInput" placeholder="¿En qué puedo ayudarte?">
            <button onclick="sendMessage()">Enviar</button>
        </div>
    </div>