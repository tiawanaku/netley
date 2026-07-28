<?php

use App\Http\Controllers\ContratoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/casos/{caso}/contrato', [ContratoController::class, 'descargar'])
    ->middleware(['web', 'auth'])
    ->name('casos.contrato');
