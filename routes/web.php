<?php

use App\Http\Controllers\EstadoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EstadoController::class, 'index'])->name('estados.index');
Route::get('/estados/data', [EstadoController::class, 'data'])->name('estados.data');
Route::post('/estados/sync', [EstadoController::class, 'sync'])->name('estados.sync');
Route::get('/estados/{cveEnt}/municipios', [EstadoController::class, 'municipios'])
    ->whereNumber('cveEnt')
    ->name('estados.municipios');
