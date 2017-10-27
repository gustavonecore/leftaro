<?php
return [
	'host' => 'http://0.0.0.0:8000/leftaro/',
	'database' => [
		'user' => '',
		'pass' => '',
		'name' => '',
	],
	'middlewares' => [
		'before' => [
			\Leftaro\App\AuthMiddleware::__CLASS__,
		],
		'after' => [
			\Leftaro\App\LoggerMiddleware::__CLASS__,
		],
	]
];