<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnsayoPregunta extends Model {
	use HasFactory;

	protected $table = 'ensayo_pregunta';

	protected $fillable = [
		'ensayo_id',
		'pregunta_id',
		'orden',
	];

	public $timestamps = false;

	// Relaciones
	public function ensayo() {
		return $this->belongsTo(Ensayo::class);
	}

	public function pregunta() {
		return $this->belongsTo(Pregunta::class);
	}
}
