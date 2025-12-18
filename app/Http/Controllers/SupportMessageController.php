<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportRequest;
use App\Models\SupportMessage;

class SupportMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'support_request_id' => 'required|exists:support_requests,id',
            'message' => 'required|string|max:2000',
        ]);

        $supportRequest = SupportRequest::find($request->support_request_id);

        // Validar estado
        if (!in_array($supportRequest->status, ['waiting', 'active'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'La solicitud de soporte ya no está activa.',
            ], 409);
        }

        // Guardar mensaje
        SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'sender_type' => 'user',
            'message' => $request->message,
        ]);

        // Actualizar última actividad del usuario
        $supportRequest->update([
            'last_user_message_at' => now(),
        ]);

        return response()->json([
            'status' => 'ok',
        ]);
    }


    public function fetch(Request $request)
    {
        $request->validate([
            'support_request_id' => 'required|exists:support_requests,id',
            'last_message_id' => 'nullable|integer',
        ]);

        $supportRequest = SupportRequest::find($request->support_request_id);

        // Si ya está cerrada
        if ($supportRequest->status === 'closed') {
            return response()->json([
                'status' => 'closed',
                'message' => 'La conversación fue cerrada.',
            ]);
        }

        $query = SupportMessage::where('support_request_id', $supportRequest->id)
            ->where('sender_type', 'agent');

        if ($request->last_message_id) {
            $query->where('id', '>', $request->last_message_id);
        }

        $messages = $query->orderBy('id')->get();

        return response()->json([
            'status' => 'ok',
            'messages' => $messages,
        ]);
    }

    public function agentSend(Request $request)
    {
        $request->validate([
            'support_request_id' => 'required|exists:support_requests,id',
            'message' => 'required|string|min:1',
            'agent_id' => 'required|integer', // luego será auth()->id()
        ]);

        $supportRequest = SupportRequest::find($request->support_request_id);

        if ($supportRequest->status === 'closed') {
            return response()->json([
                'error' => 'La conversación está cerrada.'
            ], 400);
        }

        // Crear mensaje del asesor
        $message = SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'sender_type'        => 'agent',
            'sender_id'          => $request->agent_id,
            'message'            => $request->message,
        ]);

        // Actualizar estado y actividad
        $supportRequest->update([
            'status' => 'active',
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => $message
        ]);
    }
}
