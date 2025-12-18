
<x-app-layout>

<div class="max-w-4xl mx-auto py-6">

    <h2 class="text-xl font-bold mb-4">
        Chat con usuario #{{ $supportRequest->id }}
    </h2>

    {{-- CONTENEDOR DE MENSAJES --}}
    <div id="chat-box"
         class="border rounded p-4 mb-4 h-96 overflow-y-auto bg-gray-50">

        @forelse ($messages as $message)
            <div class="mb-2">
                @if ($message->sender_type === 'agent')
                    <div class="text-right">
                        <span class="inline-block bg-blue-600 text-white px-3 py-2 rounded">
                            {{ $message->message }}
                        </span>
                    </div>
                @elseif ($message->sender_type === 'user')
                    <div class="text-left">
                        <span class="inline-block bg-gray-300 px-3 py-2 rounded">
                            {{ $message->message }}
                        </span>
                    </div>
                @else
                    <div class="text-center text-sm text-gray-500">
                        {{ $message->message }}
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-center">
                No hay mensajes aún.
            </p>
        @endforelse
    </div>

    {{-- INPUT DEL ASESOR --}}
    <form id="agent-form" class="flex gap-2">
        @csrf
        <input type="text"
               id="agent-message"
               class="flex-1 border rounded px-3 py-2"
               placeholder="Escribe tu mensaje..."
               autocomplete="off">

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded">
            Enviar
        </button>
    </form>

</div>

<script>
const form = document.getElementById('agent-form');
const input = document.getElementById('agent-message');
const chatBox = document.getElementById('chat-box');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const message = input.value.trim();
    if (!message) return;

    input.value = '';

    await fetch('/api/support/agent/message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            support_request_id: {{ $supportRequest->id }},
            agent_id: {{ auth()->id() }},
            message: message
        })
    });

    // Recarga simple (sin tiempo real aún)
    location.reload();
});
</script>
</x-app-layout>

