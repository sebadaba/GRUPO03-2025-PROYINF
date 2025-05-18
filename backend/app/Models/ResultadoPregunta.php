<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResultadoPregunta extends Model {
	use HasFactory;

	protected $table = 'resultado_pregunta';

	protected $fillable = [
		'resultado_ensayo_id',
		'pregunta_id',
		'alternativa_respondida',
		'correcta',
	];

	public $timestamps = false;

	// Relaciones
	public function resultadoEnsayo() {
		return $this->belongsTo(ResultadoEnsayo::class);
	}

	public function pregunta() {
		return $this->belongsTo(Pregunta::class);
	}
}
