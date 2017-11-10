<?php

use FastRoute\Dispatcher;
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Noodlehaus\Config;
use Psr\Log\LoggerInterface;

return [
	'config' => function ()
	{
        return new Config(__DIR__ . '/settings.php');
	},

	Logger::class => function (ContainerInterface $container)
	{
		$log = new Logger('leftaro');
		$log->pushHandler(new StreamHandler($container->get('config')->get('paths.logfile'), Logger::DEBUG));
		return $log;
	},

	LoggerInterface::class => function (ContainerInterface $container)
	{
		return $container->get(Logger::class);
	},

	Dispatcher::class => function (ContainerInterface $container)
	{
		$dispatcher = \FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($container)
		{
			$routes = require_once __DIR__ . '/routes.php';

			foreach ($routes as $route)
			{
				list($method, $endpoint, $handlerClass, $handlerMethod) = $route;

				$r->addRoute($method, $method, $handlerClass . '::' . $handlerMethod);
			}
		});
	},
];