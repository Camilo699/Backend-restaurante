<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;
use Camilo\MsAuth\Controllers\AuthController;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Auth funcionando',
        'status' => 'ok',
        'rutas' => [
            'POST /login',
            'POST /logout'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/login', [AuthController::class, 'login']);
$app->post('/logout', [AuthController::class, 'logout']);

$app->run();