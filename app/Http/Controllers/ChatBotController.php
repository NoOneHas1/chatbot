<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $mensaje = strtolower($request->input('message'));
        $menuPath = $request->input('menu_path', []);

        // 1. Manejo de menú dinámico
        $menuResponse = $this->handleMenu($mensaje, $menuPath);
        if ($menuResponse) {
            return response()->json($menuResponse);
        }

        // 2. Buscar información en la base de conocimiento
        $contexto = $this->buscarContexto($mensaje);

        // 3. Respuesta IA
        return $this->respuestaAI($mensaje, $contexto, $menuPath);
    }

    private function handleMenu($mensaje, $menuPath)
    {
        $parentId = count($menuPath) > 0 ? end($menuPath) : null;

        // Si mensaje vacío, mostrar menú raíz o submenú según nivel
        if ($mensaje === '') {
            $menuItems = $parentId
                ? MenuItem::where('parent_id', $parentId)->get()
                : MenuItem::whereNull('parent_id')->get();

            if ($menuItems->isEmpty()) return null;

            return [
                'reply' => 'Por favor, selecciona una opción:',
                'menu' => $menuItems->pluck('label')->toArray(),
                'menu_path' => $menuPath
            ];
        }

        // Buscar opción exacta en el menú actual
        $menuItems = $parentId
            ? MenuItem::where('parent_id', $parentId)->get()
            : MenuItem::whereNull('parent_id')->get();

        $selected = $menuItems->firstWhere('label', $mensaje);

        if (!$selected) return null;

        // Obtener hijos
        $children = MenuItem::where('parent_id', $selected->id)->get();

        if ($children->isNotEmpty()) {
            $newMenuPath = $menuPath;
            $newMenuPath[] = $selected->id;

            return [
                'reply' => 'Selecciona una opción:',
                'menu' => $children->pluck('label')->toArray(),
                'menu_path' => $newMenuPath
            ];
        }

        // No tiene hijos, respuesta final
        return [
            'reply' => $selected->respuesta ?: "No tengo información suficiente para responder. Por favor, contacta a un asesor en asesor@unicatolica.edu.co",
            'menu' => [],
            'menu_path' => $menuPath // Mantener historial
        ];
    }

    private function buscarContexto($mensaje)
    {
        $mensajeNormalizado = $this->normalizar($mensaje);

        $resultados = KnowledgeBase::whereRaw(
            "MATCH(titulo, contenido) AGAINST(? IN NATURAL LANGUAGE MODE)",
            [$mensajeNormalizado]
        )->limit(5)->get();

        return $resultados->isEmpty() ? null : $resultados->pluck('contenido')->implode("\n\n---\n\n");
    }

    private function respuestaAI($mensaje, $contexto = null, $menuPath = [])
    {
        try {
            $client = new Client();
            $messages = session('chat_history', []);

            if (empty($messages)) {
                $contextPrompt = $contexto
                    ? "Información interna de la universidad:\n\n$contexto\n\n"
                    : "No se encontró información interna relevante para esta consulta.\n\n";

                $messages[] = [
                    'role' => 'system',
                    'content' =>
                        "Eres el asistente virtual de la Universidad Católica (Unicatólica).
                        Responde únicamente con la información proporcionada en el CONTEXTO.
                        Si no puedes responder, nunca inventes información.
                        Si no hay información suficiente en el contexto, responde exactamente:
                        'No tengo información suficiente para responder. Por favor contacta a un asesor en asesor@unicatolica.edu.co'

                        Si te saludan, responde:
                        'Hola! soy tu asistente virtual de la Unicatolica, ¿en qué puedo ayudarte el día de hoy?'

                        Si te preguntan cosas que no son de la universidad di:
                        'No estoy programado para responder preguntas fuera del ámbito universitario.'

CONTEXTO:
$contextPrompt
FIN DEL CONTEXTO."
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $mensaje
            ];

            $response = $client->post(env('AI_BASE_URL') . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => env('AI_MODEL'),
                    'messages' => $messages
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? "No pude generar respuesta.";

            $messages[] = [
                'role' => 'assistant',
                'content' => $reply
            ];

            session(['chat_history' => $messages]);

            return [
                'reply' => $reply,
                'menu' => [],  // Menú vacío porque ya es respuesta IA
                'menu_path' => $menuPath
            ];

        } catch (\Exception $e) {
            return [
                'reply' => "Hubo un error con la IA: " . $e->getMessage(),
                'menu' => [],
                'menu_path' => $menuPath
            ];
        }
    }

    private function normalizar($texto)
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        );
        $texto = preg_replace('/[^a-z0-9\s]/', ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }
}
