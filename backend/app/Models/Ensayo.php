<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ensayo extends Model {
	use HasFactory;

	protected $table = 'ensayo';

	protected $fillable = [
		'titulo',
		'descripcion',
		'fecha_creacion',
		'profesor_id',
	];

	public $timestamps = false;

	// Relaciones
	public function profesor() {
		return $this->belongsTo(Profesor::class);
	}

	public function preguntas() {
		return $this->hasMany(EnsayoPregunta::class);
	}

	public function resultados() {
		return $this->hasMany(ResultadoEnsayo::class);
	}
}