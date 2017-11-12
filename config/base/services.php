<?php

use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
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

	Config::class => function (ContainerInterface $container)
	{
		return $container->get('config');
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

	'logger' => function (ContainerInterface $container)
	{
		return $container->get(Logger::class);
	},

	Dispatcher::class => function (ContainerInterface $container)
	{
		return FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($container)
		{
			foreach (require_once __DIR__ . '/routes.php' as $route)
			{
				list($method, $endpoint, $handlerClass, $handlerMethod) = $route;

				$r->addRoute(strtoupper($method), $endpoint, $handlerClass . '::' . $handlerMethod);
			}
		});
	},

	'twig' => function (ContainerInterface $container)
	{
		$loader = new Twig_Loader_Filesystem($container->get('config')->get('paths.views'));

		return new Twig_Environment($loader,
		[
			'cache' => $container->get('config')->get('paths.views_cache'),
		]);
	},

	Container::class => function(ContainerInterface $container)
	{
		return $container;
	},

	'database' => function(ContainerInterface $container)
	{
		return DriverManager::getConnection($container->get('config')->get('database'), new Configuration());
	},

	'dispatcher' => function(ContainerInterface $container)
	{
		return $container->get(Dispatcher::class);
	},
];