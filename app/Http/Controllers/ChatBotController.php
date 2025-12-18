<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\MenuItem;
use App\Models\SupportRequest;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    /**
     * Maneja el mensaje del usuario
     */
    public function handle(Request $request)
    {
        $mensaje   = strtolower($request->input('message'));
        $tag       = $request->input('tag');
        $sessionId = $request->input('session_id'); 

        /**
         * ======================================
         * BLOQUEO DE IA SI HAY SOPORTE ACTIVO
         * ======================================
         */
        $supportRequest = SupportRequest::where('session_id', $sessionId)
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        if ($supportRequest) {
            SupportMessage::create([
                'support_request_id' => $supportRequest->id,
                'sender_type'        => 'user',
                'message'            => $mensaje,
            ]);

            $supportRequest->update([
                'last_user_message_at' => now(),
            ]);

            return response()->json([
                'type' => 'response',
                'text' => 'Un asesor revisará tu mensaje en breve.',
            ]);
        }

        /**
         * ======================================
         * FLUJO NORMAL (MENÚ / IA)
         * ======================================
         */

        // Intentar clasificar hacia un menú
        $menuId = $this->detectarMenu($mensaje);

        if ($menuId) {
            $menuResponse = (new MenuController)->children($menuId);
            $payload = $menuResponse->getData();

            if ($payload->type === "menu") {
                return response()->json([
                    'type'  => 'menu',
                    'items' => $payload->items,
                    'intro' => null,
                    'title' => $payload->menu_title
                ]);
            }

            return response()->json([
                'type' => 'response',
                'text' => $payload->text
            ]);
        }

        // Buscar contexto en knowledge_base
        $contexto = $this->buscarContexto($mensaje, $tag);
        $aiReply  = $this->respuestaAI($mensaje, $contexto);

        return response()->json([
            'type' => 'response',
            'text' => $aiReply
        ]);
    }

    /**
     * Detecta si un mensaje corresponde a un menú
     */
    private function detectarMenu($mensaje)
    {
        try {
            $client = new Client();
            $menus = MenuItem::all(['id','title'])
                ->map(fn($m)=> "{$m->id} - {$m->title}")
                ->implode("\n");

            $prompt = "
                Devuelve SOLO JSON:
                { \"menu_id\": ID } si corresponde a un menú
                { \"menu_id\": null } si no corresponde

                Opciones:
                $menus

                Mensaje: \"$mensaje\"";

            $response = $client->post(env('AI_BASE_URL') . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'    => env('AI_MODEL'),
                    'messages' => [
                        ['role' => 'system', 'content' => "Clasifica el mensaje hacia un menú"],
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ],
            ]);

            $data  = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? '{"menu_id":null}';
            $json  = json_decode($reply, true);

            return $json['menu_id'] ?? null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Busca información relevante en knowledge_base
     */
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
            $resultados = KnowledgeBase::whereRaw(
                "MATCH(titulo, contenido) AGAINST(? IN NATURAL LANGUAGE MODE)",
                [$mensajeNormalizado]
            )->limit(5)->get();
        }

        if ($resultados->isEmpty()) return null;

        return $resultados->pluck('contenido')->implode("\n\n---\n\n");
    }

    /**
     * Genera la respuesta de la IA (NO MODIFICADA)
     */
    private function respuestaAI($mensaje, $contexto = null)
    {
        try {
            $client = new Client();
            $messages = session('chat_history', []);

            if (empty($messages)) {
                $messages[] = [
                    'role' => 'system',
                    'content' =>
                        "Eres un asistente institucional.

                        REGLAS ESTRICTAS:
                        - Usa EXCLUSIVAMENTE la información del CONTEXTO.
                        - NO inventes datos.
                        - NO hagas suposiciones.
                        - NO agregues información externa.
                        - Si el contexto NO contiene la respuesta, responde EXACTAMENTE:
                        'No tengo información suficiente para responder esa pregunta. ¿Podrías darme un poco más de contexto o reformularla?'"
                ];

                $messages[] = [
                    'role' => 'system',
                    'content' => "CONTEXTO DISPONIBLE:\n\n" . $contexto
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $mensaje
            ];

            $response = $client->post(env('AI_BASE_URL') . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model'    => env('AI_MODEL'),
                    'messages' => $messages
                ]
            ]);

            $data  = json_decode($response->getBody(), true);
            $reply = $data['choices'][0]['message']['content'] ?? "No pude generar respuesta.";

            $messages[] = [
                'role' => 'assistant',
                'content' => $reply
            ];

            session(['chat_history' => $messages]);

            return $reply;

        } catch (\Exception $e) {
            return "Hubo un error con la IA: " . $e->getMessage();
        }
    }

    /**
     * Normaliza texto para búsquedas
     */
    private function normalizar($texto)
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = str_replace(
            ['á','é','í','ó','ú','ü','ñ'],
            ['a','e','i','o','u','u','n'],
            $texto
        );
        $texto = preg_replace('/[^a-z0-9\s]/', ' ', $texto);
        return trim(preg_replace('/\s+/', ' ', $texto));
    }

    /**
     * Limpia historial del chatbot
     */
    public function clearSession(Request $request)
    {
        $request->session()->forget('chat_history');
        return response()->json(['status' => 'ok']);
    }
}
