<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportRequestController extends Controller
{
    /**
     * Listar solicitudes
     */
    public function index()
    {
        $requests = SupportRequest::whereIn('status', ['waiting', 'active'])
            ->orderBy('created_at')
            ->get();

        return view('advisor.requests', compact('requests'));
    }

    /**
     * Tomar una solicitud
     */
    public function take($id)
    {
        $request = SupportRequest::where('id', $id)
            ->where('status', 'waiting')
            ->firstOrFail();

        $request->update([
            'status' => 'active',
            'advisor_id' => Auth::id(),
        ]);

        return redirect()->route('advisor.chat', $request->id);
    }

    /**
     * Abrir chat
     */
    public function chat($id)
    {
        $supportRequest = SupportRequest::with('messages')
            ->where('id', $id)
            ->where('advisor_id', Auth::id())
            ->firstOrFail();

        $messages = $supportRequest->messages()->orderBy('id')->get();

        return view('advisor.chat', compact('supportRequest', 'messages'));
    }

}

