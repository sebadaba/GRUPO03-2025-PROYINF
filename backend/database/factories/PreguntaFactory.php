<?php

namespace Database\Factories;

use App\Models\Pregunta;
use App\Models\CategoriaPregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreguntaFactory extends Factory {
	protected $model = Pregunta::class;

	public function definition() {
		$opciones = ['A', 'B', 'C', 'D'];

		return [
			'enunciado' => $this->faker->sentence(10),
			'alternativa_a' => $this->faker->word,
			'alternativa_b' => $this->faker->word,
			'alternativa_c' => $this->faker->word,
			'alternativa_d' => $this->faker->word,
			'correcta' => $this->faker->randomElement($opciones),
			'categoria_id' => CategoriaPregunta::factory(),
		];
	}
}
