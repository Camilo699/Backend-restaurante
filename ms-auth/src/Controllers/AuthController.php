<?php

namespace Camilo\MsAuth\Controllers;

use Camilo\MsAuth\Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function login(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();
        $usuario = $datos['usuario'] ?? '';
        $contrasena = $datos['contrasena'] ?? '';

        $user = Usuario::where('usuario', $usuario)
            ->where('contrasena', $contrasena)
            ->where('estado', 'activo')
            ->first();

        if (!$user) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Usuario o contraseña incorrectos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = bin2hex(random_bytes(32));
        $user->token = $token;
        $user->sesion_activa = true;
        $user->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Login exitoso',
            'token' => $token,
            'rol' => $user->rol,
            'nombre' => $user->nombre
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response): Response
    {
        $datos = $request->getParsedBody();
        $token = $datos['token'] ?? '';

        $user = Usuario::where('token', $token)->first();

        if (!$user) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'mensaje' => 'Token inválido'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $user->token = null;
        $user->sesion_activa = false;
        $user->save();

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'mensaje' => 'Sesión cerrada correctamente'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}