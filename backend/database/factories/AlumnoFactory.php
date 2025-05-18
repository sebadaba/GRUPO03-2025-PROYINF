<?php

namespace Database\Factories;

use App\Models\Alumno;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlumnoFactory extends Factory {
	protected $model = Alumno::class;

	public function definition() {
		return [
			'nombre' => $this->faker->name,
			'rut' => $this->faker->unique()->bothify('########-#'),
			'curso' => $this->faker->randomElement(['1°A', '2°B', '3°C']),
			'colegio' => $this->faker->company.' School',
		];
	}
}
