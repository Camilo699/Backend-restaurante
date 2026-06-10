<?php

namespace Camilo\MsPedidos\Controllers;

use Camilo\MsPedidos\Models\Pedido;
use Camilo\MsPedidos\Models\DetallePedido;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoController
{
    public function getPedidos(Request $request, Response $response): Response
    {
        $pedidos = Pedido::with('detalles')->get();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $pedidos
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getPedido(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::with('detalles')->find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $pedido
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function crearPedido(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();

        $pedido = Pedido::create([
            'mesa_id'  => $datos['mesa_id'],
            'fecha'    => date('Y-m-d'),
            'hora'     => date('H:i:s'),
            'subtotal' => 0,
            'total'    => 0,
            'estado'   => 'pendiente'
        ]);

        $subtotal = 0;
        foreach ($datos['productos'] as $item) {
            $itemSubtotal = $item['cantidad'] * $item['precio_unitario'];
            $subtotal += $itemSubtotal;

            DetallePedido::create([
                'pedido_id'       => $pedido->id,
                'producto_id'     => $item['producto_id'],
                'nombre_producto' => $item['nombre_producto'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'        => $itemSubtotal
            ]);
        }

        $pedido->subtotal = $subtotal;
        $pedido->total = $subtotal;
        $pedido->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Pedido creado correctamente',
            'data' => $pedido->load('detalles')
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function actualizarEstado(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $datos = $request->getParsedBody();
        $pedido->estado = $datos['estado'];
        $pedido->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Estado actualizado correctamente',
            'data' => $pedido
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function eliminarPedido(Request $request, Response $response, array $args): Response
    {
        $pedido = Pedido::find($args['id']);

        if (!$pedido) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Pedido no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        if (!in_array($pedido->estado, ['pagado', 'cancelado'])) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Solo se pueden eliminar pedidos pagados o cancelados'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        DetallePedido::where('pedido_id', $pedido->id)->delete();
        $pedido->delete();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Pedido eliminado correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}