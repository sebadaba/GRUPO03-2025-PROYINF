<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AlumnoController;

Route::apiResource('alumnos', AlumnoController::class);

# ignorar esto
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');