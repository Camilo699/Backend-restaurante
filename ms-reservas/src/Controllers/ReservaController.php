<?php

namespace Camilo\MsReservas\Controllers;

use Camilo\MsReservas\Models\Mesa;
use Camilo\MsReservas\Models\Reserva;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReservaController
{
    public function getMesas(Request $request, Response $response): Response
    {
        $mesas = Mesa::all();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $mesas
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getReservas(Request $request, Response $response): Response
    {
        $reservas = Reserva::with('mesa')->get();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $reservas
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function crearReserva(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();

        $mesa = Mesa::where('id', $datos['mesa_id'])
            ->where('estado', 'disponible')
            ->first();

        if (!$mesa) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Mesa no disponible'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $reserva = Reserva::create([
            'nombre_cliente'   => $datos['nombre_cliente'],
            'telefono_cliente' => $datos['telefono_cliente'],
            'cantidad_personas'=> $datos['cantidad_personas'],
            'fecha'            => $datos['fecha'],
            'hora'             => $datos['hora'],
            'observaciones'    => $datos['observaciones'] ?? null,
            'estado'           => 'pendiente',
            'mesa_id'          => $datos['mesa_id']
        ]);

        $mesa->estado = 'reservada';
        $mesa->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Reserva creada correctamente',
            'data' => $reserva
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cancelarReserva(Request $request, Response $response, array $args): Response
    {
        $reserva = Reserva::find($args['id']);

        if (!$reserva) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Reserva no encontrada'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $mesa = Mesa::find($reserva->mesa_id);
        if ($mesa) {
            $mesa->estado = 'disponible';
            $mesa->save();
        }

        $reserva->estado = 'cancelada';
        $reserva->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Reserva cancelada correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}