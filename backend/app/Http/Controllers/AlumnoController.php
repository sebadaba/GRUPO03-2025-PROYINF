<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller {
    public function index() {
        return Alumno::all(); // Devuelve todos los alumnos
    }

    public function store(Request $request) {
        return Alumno::create($request->all());
    }

    public function show($id) {
        return Alumno::findOrFail($id);
    }

    public function update(Request $request, $id) {
        $alumno = Alumno::findOrFail($id);
        $alumno->update($request->all());
        return $alumno;
    }

    public function destroy($id) {
        $alumno = Alumno::findOrFail($id);
        $alumno->delete();
        return response()->noContent();
    }
}
