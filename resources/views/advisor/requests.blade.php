<x-app-layout>

    <div class="max-w-5xl mx-auto py-6">

        <h2 class="text-2xl font-bold mb-6">
            Solicitudes de soporte
        </h2>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">Estado</th>
                    <th class="border px-3 py-2">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td class="border px-3 py-2 text-center">
                            {{ $request->id }}
                        </td>

                        <td class="border px-3 py-2 text-center">
                            {{ $request->status }}
                        </td>

                        <td class="border px-3 py-2 text-center">

                            @if ($request->status === 'waiting')
                                <form method="POST"
                                      action="{{ route('advisor.requests.take', $request->id) }}">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1 rounded">
                                        Tomar solicitud
                                    </button>
                                </form>

                            @elseif ($request->status === 'active' && $request->advisor_id === auth()->id())
                                <a href="{{ route('advisor.chat', $request->id) }}"
                                   class="bg-blue-600 text-white px-3 py-1 rounded">
                                    Abrir chat
                                </a>
                            @else
                                —
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">
                            No hay solicitudes
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</x-app-layout>
