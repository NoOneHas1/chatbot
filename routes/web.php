<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('advisor')->group(function () {
    Route::view('/dashboard', 'advisor.dashboard')->name('advisor.dashboard');
    Route::view('/requests', 'advisor.request')->name('advisor.request');
    Route::view('/chat/{id}', 'advisor.chat')->name('advisor.chat');
});


