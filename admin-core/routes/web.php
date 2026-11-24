<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('productos.index'));

Route::get('/login', [AuthController::class, 'formulario'])->name('login');
Route::post('/login', [AuthController::class, 'entrar']);
Route::post('/logout', [AuthController::class, 'salir'])->name('logout');

// todo lo de productos requiere sesion iniciada
Route::middleware('auth')->group(function () {
    Route::resource('productos', ProductoController::class)->except('show');
});
