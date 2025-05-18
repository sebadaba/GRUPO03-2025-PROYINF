<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profesor extends Model {
	use HasFactory;

	protected $table = 'profesor';

	protected $fillable = [
		'nombre',
		'rut',
		'correo',
		'especialidad',
	];

	public $timestamps = false;

	// Relaciones
	public function ensayos() {
		return $this->hasMany(Ensayo::class);
	}

	public function resultados() {
		// Profesor → tiene muchos ensayos → cada ensayo tiene muchos resultados
		return $this->hasManyThrough(ResultadoEnsayo::class, Ensayo::class);
	}
}
