<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('customer_logged_in')) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Voce precisa estar logado para acessar esta pagina.',
                    ])
                    ->setStatusCode(401);
            }

            $session->setFlashdata('error', 'Voce precisa estar logado para acessar esta pagina.');
            return redirect()->to('/login')->with('redirect_url', current_url());
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
