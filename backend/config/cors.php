<?php

return [

	'paths' => ['/*'],

	'allowed_methods' => ['*'], // Permitir todos los métodos (GET, POST, etc.)

	'allowed_origins' => ['http://localhost:5173'], // o el puerto de tu Vue app

	'allowed_origins_patterns' => [],

	'allowed_headers' => ['*'],

	'exposed_headers' => [],

	'max_age' => 0,

	'supports_credentials' => false,

];
