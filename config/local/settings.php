<?php
return [
	'host' => 'http://0.0.0.0:8000/leftaro/',
	'database' => [
		'user' => '',
		'pass' => '',
		'name' => '',
	],
	'paths' => [
		'logfile' => __DIR__ . '/../../log/leftaro.log',
	],
	'middlewares' => [
		'before' => [
			\Leftaro\App\Middlewares\AuthMiddleware::class,
		],
		'after' => [
			\Leftaro\App\Middlewares\LoggerMiddleware::class,
		],
	]
];