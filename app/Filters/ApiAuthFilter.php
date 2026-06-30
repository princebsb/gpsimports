<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JWT\JWTHandler;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Token de autenticacao nao fornecido.',
                ])
                ->setStatusCode(401);
        }

        // Extract token from "Bearer <token>"
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Formato de token invalido.',
                ])
                ->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $jwt = new JWTHandler();
            $payload = $jwt->decode($token);

            if (!$payload) {
                return service('response')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Token invalido ou expirado.',
                    ])
                    ->setStatusCode(401);
            }

            // Store user data in request for later use
            $request->customer_id = $payload['sub'] ?? null;
            $request->customer_email = $payload['email'] ?? null;

        } catch (\Exception $e) {
            return service('response')
                ->setJSON([
                    'success' => false,
                    'message' => 'Erro ao validar token: ' . $e->getMessage(),
                ])
                ->setStatusCode(401);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
