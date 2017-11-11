<?php
return [
	'host' => 'http://0.0.0.0:8000/',
	'database' => [
		'dbname' => '',
		'user' => '',
		'password' => '',
		'host' => 'localhost',
		'driver' => 'pdo_mysql',
	],
	'paths' => [
		'logfile' => __DIR__ . '/../../log/leftaro.log',
		'views' => __DIR__ . '/../../resource/views/',
		'views_cache' => __DIR__ . '/../../resource/cache/',
	],
	'middlewares' => [
		'before' => [
			// Use SmartRouteMiddleware instead of RouteMiddleware if you want to auto-discover the endpoints
			\Leftaro\Core\Middleware\RouteMiddleware::class,
			\Leftaro\App\Middleware\AuthMiddleware::class,
		],
		'after' => [
			\Leftaro\App\Middleware\LoggerMiddleware::class,
		],
	],
];