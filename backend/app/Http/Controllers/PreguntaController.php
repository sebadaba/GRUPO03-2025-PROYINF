<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pregunta;
use Illuminate\Support\Facades\Validator;

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

        $pregunta = Pregunta::create($validated);

        return response()->json([
            'message' => 'Pregunta creada exitosamente',
            'data' => $pregunta
        ], 201);
    }

    // GET /api/preguntas
    public function index()
    {
        $preguntas = Pregunta::all();
        return response()->json($preguntas, 200);
    }

    // GET /api/preguntas/{id}
    public function show($id)
    {
        $pregunta = Pregunta::find($id);

        if (!$pregunta) {
            return response()->json([
                'message' => 'Pregunta no encontrada'
            ], 404);
        }

        return response()->json($pregunta, 200);
    }

    // PUT /api/preguntas/{id}
    public function update(Request $request, $id)
    {
        $pregunta = Pregunta::find($id);

        if (!$pregunta) {
            return response()->json([
                'message' => 'Pregunta no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'enunciado' => 'sometimes|required|string',
            'alternativa_a' => 'sometimes|required|string',
            'alternativa_b' => 'sometimes|required|string',
            'alternativa_c' => 'sometimes|required|string',
            'alternativa_d' => 'sometimes|required|string',
            'correcta' => 'sometimes|required|in:A,B,C,D',
            'categoria_id' => 'sometimes|required|exists:categoria_pregunta,id',
        ]);

        $pregunta->update($validated);

        return response()->json([
            'message' => 'Pregunta actualizada exitosamente',
            'data' => $pregunta
        ], 200);
    }

    // DELETE /api/preguntas/{id}
    public function destroy($id)
    {
        $pregunta = Pregunta::find($id);

        if (!$pregunta) {
            return response()->json([
                'message' => 'Pregunta no encontrada'
            ], 404);
        }

        $pregunta->delete();

        return response()->json([
            'message' => 'Pregunta eliminada exitosamente'
        ], 204);
    }
}
