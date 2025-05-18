<?php

namespace Database\Factories;

use App\Models\ResultadoPregunta;
use App\Models\ResultadoEnsayo;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultadoPreguntaFactory extends Factory {
	protected $model = ResultadoPregunta::class;

	public function definition() {
		$respuesta = $this->faker->randomElement(['A', 'B', 'C', 'D']);

		return [
			'resultado_ensayo_id' => ResultadoEnsayo::factory(),
			'pregunta_id' => Pregunta::factory(),
			'alternativa_respondida' => $respuesta,
			'correcta' => $this->faker->boolean(70), // 70% de probabilidades de que esté correcta
		];
	}
}
