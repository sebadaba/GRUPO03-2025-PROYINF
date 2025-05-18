<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\CategoriaPregunta;
use App\Models\Profesor;
use App\Models\Alumno;
use App\Models\Pregunta;
use App\Models\Ensayo;
use App\Models\EnsayoPregunta;
use App\Models\ResultadoEnsayo;
use App\Models\ResultadoPregunta;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // 1. Crear materias con categorías
        Materia::factory(3)->create()->each(function ($materia) {
            CategoriaPregunta::factory(2)->create([
                'materia_id' => $materia->id,
            ]);
        });

        // 2. Crear preguntas asociadas a categorías
        CategoriaPregunta::all()->each(function ($categoria) {
            Pregunta::factory(5)->create([
                'categoria_id' => $categoria->id,
            ]);
        });

        // 3. Crear profesores con ensayos
        Profesor::factory(3)->create()->each(function ($profesor) {
            Ensayo::factory(2)->create([
                'profesor_id' => $profesor->id,
            ]);
        });

        // 4. Crear alumnos
        Alumno::factory(10)->create();

        // 5. Asociar preguntas a ensayos (ensayo_pregunta)
        Ensayo::all()->each(function ($ensayo) {
            $preguntas = Pregunta::inRandomOrder()->take(5)->get();
            $orden = 1;
            foreach($preguntas as $pregunta) {
                EnsayoPregunta::create([
                    'ensayo_id' => $ensayo->id,
                    'pregunta_id' => $pregunta->id,
                    'orden' => $orden++,
                ]);
            }
        });

        // 6. Crear resultados de ensayo para cada alumno
        $alumnos = Alumno::all();
        $ensayos = Ensayo::all();

        foreach($alumnos as $alumno) {
            foreach($ensayos->random(2) as $ensayo) {
                $resultado = ResultadoEnsayo::create([
                    'alumno_id' => $alumno->id,
                    'ensayo_id' => $ensayo->id,
                    'puntaje_total' => rand(0, 100),
                ]);

                // 7. Crear resultado_pregunta
                $preguntas = $ensayo->preguntas;
                foreach($preguntas as $pregunta) {
                    $respuesta = fake()->randomElement(['A', 'B', 'C', 'D']);
                    ResultadoPregunta::create([
                        'resultado_ensayo_id' => $resultado->id,
                        'pregunta_id' => $pregunta->id,
                        'alternativa_respondida' => $respuesta,
                        'correcta' => $respuesta === $pregunta->correcta,
                    ]);
                }
            }
        }
    }
}
