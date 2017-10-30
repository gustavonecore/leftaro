<?php

$settings = require_once __DIR__ . '/../config/local/settings.php';
$url = str_replace(['http://', '/'], ['', ''], $settings['host']);
$server = 'php -S ' . $url . ' -t app app/index.php';

echo "Running on $url\n";

shell_exec($server);