<?php

namespace Camilo\MsAuth\Controllers;

use Camilo\MsAuth\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    public function getUsuarios(Request $request, Response $response): Response
    {
        $usuarios = Usuario::all();
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'data' => $usuarios
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function crearUsuario(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();

        $existe = Usuario::where('usuario', $datos['usuario'])->first();
        if ($existe) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'El usuario ya existe'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $usuario = Usuario::create([
            'nombre'     => $datos['nombre'],
            'correo'     => $datos['correo'],
            'usuario'    => $datos['usuario'],
            'contrasena' => $datos['contrasena'],
            'rol'        => $datos['rol'],
            'estado'     => 'activo'
        ]);

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Usuario creado correctamente',
            'data' => $usuario
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarEstado(Request $request, Response $response, array $args): Response
    {
        $usuario = Usuario::find($args['id']);

        if (!$usuario) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Usuario no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $datos = $request->getParsedBody();
        $usuario->estado = $datos['estado'];
        $usuario->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Estado actualizado correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}