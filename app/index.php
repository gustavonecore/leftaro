<?php
declare(strict_types = 1);
require __DIR__ . '/bootstrap.php';

$container = require __DIR__ . '/../config/local/container.php';

$application = new \Leftaro\Core\Application($container);

/* TODO
$application->add(function($request, $response, $next) {
	return $next($request, $response);
});
*/

$application->run(\Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
));
