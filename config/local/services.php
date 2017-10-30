<?php

use Interop\Container\ContainerInterface;
use function DI\object;
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
		$log->pushHandler(new \Monolog\Handler\ErrorLogHandler());
		return $log;
	},

	LoggerInterface::class => function (ContainerInterface $container)
	{
		return $container->get(Logger::class);
	},
];