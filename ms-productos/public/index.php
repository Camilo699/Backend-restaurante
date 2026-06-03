<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;
use Camilo\MsProductos\Controllers\ProductoController;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Productos funcionando',
        'status' => 'ok',
        'rutas' => [
            'GET /categorias',
            'GET /productos',
            'GET /productos/categoria/{id}',
            'POST /productos',
            'PUT /productos/{id}/disponibilidad'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/categorias', [ProductoController::class, 'getCategorias']);
$app->get('/productos', [ProductoController::class, 'getProductos']);
$app->get('/productos/categoria/{id}', [ProductoController::class, 'getProductosPorCategoria']);
$app->post('/productos', [ProductoController::class, 'crearProducto']);
$app->put('/productos/{id}/disponibilidad', [ProductoController::class, 'actualizarDisponibilidad']);

$app->run();