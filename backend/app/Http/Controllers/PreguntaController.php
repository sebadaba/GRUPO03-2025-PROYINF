<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pregunta;
use App\Models\CategoriaPregunta;

class PreguntaController extends Controller
{
    // POST /api/preguntas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enunciado' => 'required|string',
            'alternativa_a' => 'required|string',
            'alternativa_b' => 'required|string',
            'alternativa_c' => 'required|string',
            'alternativa_d' => 'required|string',
            'correcta' => 'required|in:A,B,C,D',
            'categoria_id' => 'required|exists:categoria_pregunta,id',
        ]);
        return Pregunta::create($validated);
    }

    // GET /api/preguntas
    public function index()
    {
        return Pregunta::all();
    }

    // GET /api/preguntas/{id}
    public function show($id)
    {
        return Pregunta::findOrFail($id);
    }

    // PUT /api/preguntas/{id}
    public function update(Request $request, $id)
    {
        $pregunta = Pregunta::findOrFail($id);
        $pregunta->update($request->all());
        return $pregunta;
    }

    // DELETE /api/preguntas/{id}
    public function destroy($id)
    {
        $pregunta = Pregunta::findOrFail($id);
        $pregunta->delete();
        return response()->noContent();
    }
}
