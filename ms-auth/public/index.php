<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Auth funcionando',
        'status' => 'ok'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();