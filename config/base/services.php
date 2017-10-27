<?php

use function DI\object;
use Noodlehaus\Config;

return [
    // Configure Twig
	'config' => function ()
	{
        return new Config(__DIR__ . '/settings.php');
    },
];