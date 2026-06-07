<?php

namespace Camilo\MsReservas;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): \Psr\Http\Message\ResponseInterface
    {
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Token requerido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $token);

        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Token inválido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}