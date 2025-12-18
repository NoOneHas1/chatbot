<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Advisor\SupportRequestController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS (ASESOR)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    | Dashboard principal (Breeze / asesor)
    */
    Route::get('/dashboard', function () {
        return view('advisor.dashboard');
    })->name('dashboard');

    /*
    | Soporte - solicitudes
    */
    Route::prefix('advisor')->group(function () {

        // Listar solicitudes (waiting / active)
        Route::get('/requests', [SupportRequestController::class, 'index'])
            ->name('advisor.requests');

        // Tomar una solicitud
        Route::post('/requests/{id}/take', [SupportRequestController::class, 'take'])
            ->name('advisor.requests.take');

        // Chat con el usuario
        Route::get('/chat/{id}', [SupportRequestController::class, 'chat'])
            ->name('advisor.chat');
    });

    /*
    | Perfil (Laravel Breeze)
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


