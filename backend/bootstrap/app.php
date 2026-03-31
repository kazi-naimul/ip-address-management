<?php

use Illuminate\Foundation\Application;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

return $app;
