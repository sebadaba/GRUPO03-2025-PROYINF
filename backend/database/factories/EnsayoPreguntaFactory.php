<?php

namespace Database\Factories;

use App\Models\EnsayoPregunta;
use App\Models\Ensayo;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnsayoPreguntaFactory extends Factory {
	protected $model = EnsayoPregunta::class;

	public function definition() {
		return [
			'ensayo_id' => Ensayo::factory(),
			'pregunta_id' => Pregunta::factory(),
			'orden' => $this->faker->numberBetween(1, 20),
		];
	}
}
