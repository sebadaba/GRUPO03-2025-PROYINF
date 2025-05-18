<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alumno extends Model {
	use HasFactory;

	protected $table = 'alumno';

	protected $fillable = [
		'nombre',
		'rut',
		'curso',
		'colegio',
	];

	public $timestamps = false;

	// Relaciones
	public function resultados() {
		return $this->hasMany(ResultadoEnsayo::class);
	}
}
