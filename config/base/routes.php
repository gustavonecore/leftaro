<?php

return [
	['GET', '/text', \Leftaro\App\Controller\WelcomeController::class, 'textAction'],
	['GET', '/json', \Leftaro\App\Controller\WelcomeController::class, 'jsonAction'],
	['GET', '/html', \Leftaro\App\Controller\WelcomeController::class, 'htmlAction'],
	['GET', '/html/{id}', \Leftaro\App\Controller\WelcomeController::class, 'htmlResourceAction'],
];