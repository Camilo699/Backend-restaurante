<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;
use Camilo\MsPedidos\Controllers\PedidoController;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Pedidos funcionando',
        'status' => 'ok',
        'rutas' => [
            'GET /pedidos',
            'GET /pedidos/{id}',
            'POST /pedidos',
            'PUT /pedidos/{id}/estado'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/pedidos', [PedidoController::class, 'getPedidos']);
$app->get('/pedidos/{id}', [PedidoController::class, 'getPedido']);
$app->post('/pedidos', [PedidoController::class, 'crearPedido']);
$app->put('/pedidos/{id}/estado', [PedidoController::class, 'actualizarEstado']);

$app->run();