<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;
use Camilo\MsReservas\Controllers\ReservaController;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Reservas funcionando',
        'status' => 'ok',
        'rutas' => [
            'GET /mesas',
            'GET /reservas',
            'POST /reservas',
            'PUT /reservas/{id}/cancelar'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/mesas', [ReservaController::class, 'getMesas']);
$app->get('/reservas', [ReservaController::class, 'getReservas']);
$app->post('/reservas', [ReservaController::class, 'crearReserva']);
$app->put('/reservas/{id}/cancelar', [ReservaController::class, 'cancelarReserva']);

$app->run();