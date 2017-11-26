<?php
declare(strict_types = 1);
require __DIR__ . '/bootstrap.php';

// Uncomment this if you want a web server
/*
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|map|woff2|woff|ttf)$/', $_SERVER["REQUEST_URI"]))
{
	return false;
}
*/

$container = require __DIR__ . '/../config/local/container.php';

$application = new \Leftaro\Core\Application($container);

$application->setRoutingPoligy(\Leftaro\Core\Application::ROUTING_FIXED);

$application->run(\Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
));
