<?php
declare(strict_types = 1);
require __DIR__ . '/bootstrap.php';

if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"]))
{
	return false;
}

$container = require __DIR__ . '/../config/local/container.php';

$application = new \Leftaro\Core\Application($container);

/* TODO
$application->add(function($request, $response, $next) {
	return $next($request, $response);
});
*/

/**
 * If you want to auto-load the controller/methods by URI convention, use:
 * Application::ROUTING_AUTO
 * ROUTING_FIXED: Used to load a defined class/method from an URI string
 */
$application->setRoutingPoligy(\Leftaro\Core\Application::ROUTING_FIXED);

$application->run(\Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
));
