<?php

namespace Database\Factories;

use App\Models\Profesor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfesorFactory extends Factory {
	protected $model = Profesor::class;

	public function definition() {
		return [
			'nombre' => $this->faker->name,
			'rut' => $this->faker->unique()->bothify('########-#'),
			'correo' => $this->faker->unique()->safeEmail,
			'especialidad' => $this->faker->randomElement(['Matemáticas', 'Lenguaje', 'Historia', 'Ciencias']),
		];
	}
}
