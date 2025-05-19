<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {
	/**
	 * Define your route model bindings, pattern filters, etc.
	 */
	public function boot(): void {
		$this->routes(function () {
			// Rutas de la API (usarán el prefijo "/api")
			Route::middleware('api')
				->prefix('api')
				->group(base_path('routes/api.php'));

			// Rutas web (usarán el middleware de sesiones y CSRF)
			Route::middleware('web')
				->group(base_path('routes/web.php'));
		});
	}
}
