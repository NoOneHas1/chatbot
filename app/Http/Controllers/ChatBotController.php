<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $mensaje = strtolower($request->input('message'));

        // 1. BUSCAR FAQ
        $faq = $this->buscarFaq($mensaje);

        if ($faq) {
            return response()->json([
                'reply' => $faq
            ]);
        }

        // 2. SI NO HAY FAQ → pedir a la IA
        return $this->respuestaAI($mensaje);
    }

    private function buscarFaq($mensaje)
    {
        $faqs = Faq::all();

        foreach ($faqs as $faq) {
            if (str_contains(strtolower($mensaje), strtolower($faq->pregunta))) {
                return $faq->respuesta;
            }
        }

        return null;
    }

    private function respuestaAI($mensaje)
    {
        try {
            $client = new Client();

            $response = $client->post(env('AI_BASE_URL') . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('AI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => env('AI_MODEL'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Eres el asistente virtual de la unicatolica. Siempre que te saluden vas a decir: 
                            Hola! soy tu asistente virtual de la Unicatolica, ¿En que puedo ayudarte el dia de hoy?.  Si te preguntan cosas que no tienen nada que ver
                            con la Universidad Católica, responde: No estoy programado para responder preguntas fuera del ámbito Universitario.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $mensaje
                        ],
                    ]
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return response()->json([
                'reply' => $data['choices'][0]['message']['content'] ?? "No pude generar respuesta."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => "Hubo un error con la IA: " . $e->getMessage(),
            ]);
        }
    }
}


