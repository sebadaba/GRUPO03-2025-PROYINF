<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pregunta extends Model {
	use HasFactory;

	protected $table = 'pregunta';

	protected $fillable = [
		'enunciado',
		'alternativa_a',
		'alternativa_b',
		'alternativa_c',
		'alternativa_d',
		'correcta',
		'categoria_id',
	];

	public $timestamps = false;

	// Relaciones
	public function categoria() {
		return $this->belongsTo(CategoriaPregunta::class);
	}

	public function ensayos() {
		return $this->belongsToMany(Ensayo::class, 'ensayo_pregunta')
			->withPivot('orden');
	}

	public function resultados() {
		return $this->hasMany(ResultadoPregunta::class);
	}
}
