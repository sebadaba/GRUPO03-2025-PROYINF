<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;


Route::apiResource('alumnos', AlumnoController::class);

Route::get('/ping', function () {
	return 'pong';
});