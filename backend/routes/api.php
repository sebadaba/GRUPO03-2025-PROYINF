<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\CategoriaPreguntaController;

Route::apiResource('alumnos', AlumnoController::class);
Route::apiResource('preguntas', PreguntaController::class);

Route::get('categorias', function() {
    return \App\Models\CategoriaPregunta::all();
});

# ignorar esto
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');