<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\MenuController;

//endponint para el chatbot
Route::post('/chatbot', [ChatBotController::class, 'handle']);

//endpoints para el menú
Route::get('/menu', [MenuController::class, 'root']);
Route::get('/menu/{id}', [MenuController::class, 'children']);

//endpoint para limpiar el historial de chat
Route::post('/chatbot/clear-session', [ChatBotController::class, 'clearSession']);