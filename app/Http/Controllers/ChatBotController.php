<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $mensaje = strtolower($request->input('message'));
        $tag = $request->input('tag'); // parámetro opcional para filtrar por etiqueta

        // 1. Buscar documentos en la BD
        $contexto = $this->buscarContexto($mensaje, $tag);

        // 2. Pedir respuesta a la IA con el contexto
        return $this->respuestaAI($mensaje, $contexto);
    }

    /**
     * Busca coincidencias en la BD filtrando por tag si existe
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

        // Si no hay resultados filtrando por tag, buscar en toda la tabla
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

    /**
     * Envía el mensaje + contexto a Groq/Llama
     */
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
