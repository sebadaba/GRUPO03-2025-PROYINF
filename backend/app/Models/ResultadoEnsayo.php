<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResultadoEnsayo extends Model {
	use HasFactory;

	protected $table = 'resultado_ensayo';

	protected $fillable = [
		'alumno_id',
		'ensayo_id',
		'puntaje_total',
		'fecha_realizacion',
	];

	public $timestamps = false;

	// Relaciones
	public function alumno() {
		return $this->belongsTo(Alumno::class);
	}

	public function ensayo() {
		return $this->belongsTo(Ensayo::class);
	}

	public function respuestas() {
		return $this->hasMany(ResultadoPregunta::class, 'resultado_ensayo_id');
	}
}
