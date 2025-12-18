<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportMessageController;

//endponint para el chatbot
Route::post('/chatbot', [ChatBotController::class, 'handle']);

//endpoints para el menú
Route::get('/menu', [MenuController::class, 'root']);
Route::get('/menu/{id}', [MenuController::class, 'children']);

//endpoint para limpiar el historial de chat
Route::post('/chatbot/clear-session', [ChatBotController::class, 'clearSession']);

//endopoint para iniciar soporte
Route::post('/support/start', [SupportController::class, 'start']);

//endpoint para enviar mensajes del usuario al soporte
Route::post('/support/message', [SupportMessageController::class, 'store']);

//polling
Route::get('/support/messages', [SupportMessageController::class, 'fetch']);

//endpoint para que el asesor envíe mensajes
Route::post('/support/agent/message', [SupportMessageController::class, 'agentSend']);

//cerrar soporte
Route::post('/support/close', [SupportController::class, 'close']);

