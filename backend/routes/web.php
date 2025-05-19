<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AlumnoController;

Route::apiResource('alumnos', AlumnoController::class);

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});