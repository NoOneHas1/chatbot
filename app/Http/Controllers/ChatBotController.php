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
    $tag = $request->input('tag'); // opcional

    // 1. Intentar clasificar el mensaje hacia un menú
    $menuId = $this->detectarMenu($mensaje);

    if ($menuId) {
        // Si detecta un menú, devolvemos el menú/submenú
        $menuResponse = (new MenuController)->children($menuId);
        
        // Retornamos un JSON uniforme con la clave 'type' y 'items' para JS
        if ($menuResponse->getData()->type === 'menu') {
            return response()->json([
                'type' => 'menu',
                'items' => $menuResponse->getData()->items
            ]);
        } else {
            return response()->json([
                'type' => 'response',
                'text' => $menuResponse->getData()->text
            ]);
        }
    }

    // 2. Si no coincide con menú → flujo RAG normal
    $contexto = $this->buscarContexto($mensaje, $tag);
    $aiReply = $this->respuestaAI($mensaje, $contexto);

    return response()->json([
        'type' => 'response',
        'text' => $aiReply
    ]);
}


    /**
     * Detecta si el mensaje corresponde a un menú usando IA
     */
    private function detectarMenu($mensaje)
    {
        try {
            $client = new Client();

            // Lista de menús actuales
            $menus = MenuItem::all(['id','title'])->map(function($m){
                return "{$m->id} - {$m->title}";
            })->implode("\n");

            $prompt = "
                        Eres un asistente que interpreta el mensaje de un usuario y determina
                        si corresponde a una opción del menú de la universidad.

                        Devuelve SOLO JSON:
                        { \"menu_id\": ID } si corresponde a un menú
                        { \"menu_id\": null } si no corresponde


                        Opciones disponibles:
                        $menus

                        Mensaje del usuario: \"$mensaje\"";

            $messages = [
                ['role' => 'system', 'content' => "Clasifica el mensaje hacia el menú correspondiente"],
                ['role' => 'user', 'content' => $prompt],
            ];

            $response = $client->post(env('AI_BASE_URL') . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => env('AI_MODEL'),
                    'messages' => $messages,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? '{"menu_id": null}';

            $json = json_decode($reply, true);
            return $json['menu_id'] ?? null;

        } catch (\Exception $e) {
            return null; // Si falla la IA, seguimos con RAG
        }
    }

    private function buscarContexto($mensaje, $tag = null)
    {
        $mensajeNormalizado = $this->normalizar($mensaje);

        $query = KnowledgeBase::query();

        if ($tag) {
            $query->whereRaw("FIND_IN_SET(?, tags)", [$tag]);
        }

        $resultados = $query
            ->whereRaw("MATCH(titulo, contenido) AGAINST(? IN NATURAL LANGUAGE MODE)", [$mensajeNormalizado])
            ->limit(5)
            ->get();

        if ($resultados->isEmpty() && $tag) {
            $resultados = KnowledgeBase::whereRaw("MATCH(titulo, contenido) AGAINST(? IN NATURAL LANGUAGE MODE)", [$mensajeNormalizado])
                ->limit(5)
                ->get();
        }

        if ($resultados->isEmpty()) {
            return null;
        }

        return $resultados->pluck('contenido')->implode("\n\n---\n\n");
    }

    private function respuestaAI($mensaje, $contexto = null)
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

            return response()->json([
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => "Hubo un error con la IA: " . $e->getMessage(),
            ]);
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
