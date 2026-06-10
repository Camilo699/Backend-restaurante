<?php

namespace Camilo\MsProductos\Controllers;

use Camilo\MsProductos\Models\Categoria;
use Camilo\MsProductos\Models\Producto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController
{
    public function getCategorias(Request $request, Response $response): Response
    {
        $categorias = Categoria::all();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $categorias
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getProductos(Request $request, Response $response): Response
    {
        $productos = Producto::with('categoria')->get();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $productos
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getProductosPorCategoria(Request $request, Response $response, array $args): Response
    {
        $productos = Producto::with('categoria')
            ->where('categoria_id', $args['id'])
            ->where('disponible', true)
            ->get();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $productos
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function crearProducto(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();

        $producto = Producto::create([
            'nombre'       => $datos['nombre'],
            'descripcion'  => $datos['descripcion'] ?? null,
            'precio'       => $datos['precio'],
            'disponible'   => $datos['disponible'] ?? true,
            'categoria_id' => $datos['categoria_id']
        ]);

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Producto creado correctamente',
            'data' => $producto
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function actualizarDisponibilidad(Request $request, Response $response, array $args): Response
    {
        $producto = Producto::find($args['id']);

        if (!$producto) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Producto no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $datos = $request->getParsedBody();
        $producto->disponible = $datos['disponible'];
        $producto->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Disponibilidad actualizada',
            'data' => $producto
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