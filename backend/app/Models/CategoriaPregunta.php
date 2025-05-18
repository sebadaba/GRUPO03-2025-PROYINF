<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaPregunta extends Model {
	use HasFactory;

	protected $table = 'categoria_pregunta';

	protected $fillable = [
		'nombre',
		'materia_id',
	];

	public $timestamps = false;

	// Relaciones
	public function materia() {
		return $this->belongsTo(Materia::class);
	}

	public function preguntas() {
		return $this->hasMany(Pregunta::class, 'categoria_id');
	}
}
