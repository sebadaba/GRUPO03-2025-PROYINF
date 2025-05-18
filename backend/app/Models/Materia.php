<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Materia extends Model {
	use HasFactory;

	protected $table = 'materia';

	protected $fillable = [
		'nombre',
	];

	public $timestamps = false;

	// Relaciones
	public function categorias() {
		return $this->hasMany(CategoriaPregunta::class, 'materia_id');
	}
}
