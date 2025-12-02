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

        // 1. BUSCAR DOCUMENTOS EN LA BD POR PALABRAS SIMPLES
        $contexto = $this->buscarContexto($mensaje);

        // 2. PEDIR RESPUESTA A LA IA CON EL CONTEXTO
        return $this->respuestaAI($mensaje, $contexto);
    }

    /**
     * Busca coincidencias simples en la BD para usar como contexto
     */
    private function buscarContexto($mensaje)
    {
        $mensajeNormalizado = $this->normalizar($mensaje);

        $resultados = KnowledgeBase::whereRaw("MATCH(titulo, contenido) AGAINST(? IN NATURAL LANGUAGE MODE)", [$mensaje])->limit(5)->get();

        if ($resultados->isEmpty()) {
            return null;
        }

        // Se une todo el contenido para el prompt
        return $resultados->pluck('contenido')->implode("\n\n---\n\n");
    }

    /**
 * Envíar el mensaje + contexto a Groq/Llama
 */
private function respuestaAI($mensaje, $contexto = null)
{
    try {
        $client = new Client();

        // Recuperar historial de la sesión
        $messages = session('chat_history', []);

        // Si es la primera interacción, se agrega el mensaje del sistema
        if (empty($messages)) {
            $contextPrompt = $contexto
                ? "Información interna de la universidad:\n\n$contexto\n\n"
                : "No se encontró información interna relevante para esta consulta.\n\n";

            $messages[] = [
                'role' => 'system',
                'content' =>
                    "Eres el asistente virtual de la Universidad Católica (Unicatólica). 
                    Responde SIEMPRE basado en la información proporcionada en el CONTEXTO.
                    Si no puedes responder, pide aclaraciones de manera amable, nunca respondas con información inventada o que no sepas.
                    Si no hay información suficiente en el contexto, responde:
                    'No tengo información suficiente para responder.'

                    Si te saludan, responde:
                    'Hola! soy tu asistente virtual de la Unicatolica, ¿en qué puedo ayudarte el día de hoy?'
                    si no te saludan, ve directo al grano.

                    Si te preguntan cosas que no son de la universidad di:
                    'No estoy programado para responder preguntas fuera del ámbito universitario.'

                    Si puedes resumir la informacion en el contexto, hazlo de manera clara y concisa.

                    CONTEXTO:
                    $contextPrompt
                    FIN DEL CONTEXTO."
            ];
        }

        // Agregar mensaje del usuario al historial
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

        // Guardar respuesta del asistente en el historial
        $messages[] = [
            'role' => 'assistant',
            'content' => $reply
        ];

        // Guardar historial actualizado en sesión
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
        // Convertir a minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');

        // Quitar acentos y normalizar caracteres
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        );

        // Eliminar caracteres que no son letras, números o espacios
        $texto = preg_replace('/[^a-z0-9\s]/', ' ', $texto);

        // Quitar espacios múltiples
        $texto = preg_replace('/\s+/', ' ', $texto);

        // Quitar espacios al inicio y final
        return trim($texto);
    }

}
