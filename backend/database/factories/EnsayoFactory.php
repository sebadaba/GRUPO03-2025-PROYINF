<?php

namespace Database\Factories;

use App\Models\Ensayo;
use App\Models\Profesor;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnsayoFactory extends Factory {
	protected $model = Ensayo::class;

	public function definition() {
		return [
			'titulo' => $this->faker->sentence,
			'descripcion' => $this->faker->paragraph,
			'fecha_creacion' => $this->faker->date(),
			'profesor_id' => Profesor::factory(),
		];
	}
}
