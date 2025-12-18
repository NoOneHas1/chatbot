<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SupportRequest;
use App\Models\SupportMessage;

class SupportController extends Controller
{
    public function start(Request $request)
    {
        $sessionId = $request->input('session_id');

        $supportRequest = SupportRequest::where('session_id', $sessionId)
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        if (!$supportRequest) {
            $supportRequest = SupportRequest::create([
                'session_id' => $sessionId,
                'status' => 'waiting',
                'last_user_message_at' => now(),
            ]);

            SupportMessage::create([
                'support_request_id' => $supportRequest->id,
                'sender_type' => 'system',
                'message' => 'El usuario solicitó contacto con un asesor.',
            ]);
        }

        if ($supportRequest->status === 'waiting' && $supportRequest->advisor_id === null) {
            $this->assignAdvisor($supportRequest);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Te estamos conectando con un asesor.',
            'support_request_id' => $supportRequest->id,
            'advisor_assigned' => $supportRequest->advisor_id !== null,
        ]);
    }

    private function assignAdvisor(SupportRequest $supportRequest)
    {
        $advisor = User::where('role', 'advisor')
            ->whereDoesntHave('supportRequests', function ($q) {
                $q->where('status', 'active');
            })
            ->first();

        if (!$advisor) {
            return;
        }

        $supportRequest->update([
            'advisor_id' => $advisor->id,
            'status'     => 'active',
        ]);

        SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'sender_type' => 'system',
            'message' => 'Te estamos conectando con un asesor.',
        ]);
    }

    public function close(Request $request)
    {
        $sessionId = $request->session()->getId();

        $supportRequest = SupportRequest::where('session_id', $sessionId)
            ->where('status', 'active')
            ->first();

        if (!$supportRequest) {
            return response()->json(['status' => 'ok']);
        }

        $supportRequest->update([
            'status'     => 'closed',
            'advisor_id'=> null,
            'closed_at' => now(),
        ]);

        SupportMessage::create([
            'support_request_id' => $supportRequest->id,
            'sender_type' => 'system',
            'message' => 'El chat fue cerrado por el usuario.',
        ]);

        return response()->json(['status' => 'ok']);
    }
}
