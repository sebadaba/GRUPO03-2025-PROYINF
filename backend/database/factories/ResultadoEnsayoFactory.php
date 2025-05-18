<?php

namespace Database\Factories;

use App\Models\ResultadoEnsayo;
use App\Models\Alumno;
use App\Models\Ensayo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultadoEnsayoFactory extends Factory {
	protected $model = ResultadoEnsayo::class;

	public function definition() {
		return [
			'alumno_id' => Alumno::factory(),
			'ensayo_id' => Ensayo::factory(),
			'puntaje_total' => $this->faker->numberBetween(0, 100),
			'fecha_realizacion' => $this->faker->dateTime,
		];
	}
}
