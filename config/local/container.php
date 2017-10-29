<?php

use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/services.php');
$container = $containerBuilder->build();

return $container;