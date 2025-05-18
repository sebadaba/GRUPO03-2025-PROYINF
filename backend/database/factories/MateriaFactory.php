<?php

namespace Database\Factories;

use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaFactory extends Factory {
	protected $model = Materia::class;

	public function definition() {
		return [
			'nombre' => $this->faker->unique()->word,
		];
	}
}
