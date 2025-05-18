<?php

namespace Database\Factories;

use App\Models\CategoriaPregunta;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaPreguntaFactory extends Factory {
	protected $model = CategoriaPregunta::class;

	public function definition() {
		return [
			'nombre' => $this->faker->word,
			'materia_id' => Materia::factory(),
		];
	}
}
