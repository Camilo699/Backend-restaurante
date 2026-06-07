<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/database.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Camilo\MsReservas\Controllers\ReservaController;
use Camilo\MsReservas\AuthMiddleware;

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->options('/{routes:.+}', fn($req, $res) => $res);

$app->add(function (Request $request, $handler) {
    $origin = $request->getHeaderLine('Origin') ?: '*';
    $response = $handler->handle($request);
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
    if ($request->getMethod() === 'OPTIONS') {
        return $response->withStatus(200);
    }
    return $response;
});

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'mensaje' => 'Microservicio Reservas funcionando',
        'status' => 'ok'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/mesas', [ReservaController::class, 'getMesas'])->add(new AuthMiddleware());
$app->get('/reservas', [ReservaController::class, 'getReservas'])->add(new AuthMiddleware());
$app->post('/reservas', [ReservaController::class, 'crearReserva'])->add(new AuthMiddleware());
$app->put('/reservas/{id}/cancelar', [ReservaController::class, 'cancelarReserva'])->add(new AuthMiddleware());

$app->run();