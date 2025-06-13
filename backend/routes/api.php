<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\PreguntaController;

Route::apiResource('alumnos', AlumnoController::class);
Route::apiResource('preguntas', PreguntaController::class)->only(['index', 'show', 'update', 'destroy']);
Route::post('/preguntas', [PreguntaController::class, 'store']);

# ignorar esto
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');