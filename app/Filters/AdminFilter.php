<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('admin_logged_in')) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON([
                        'success' => false,
                        'message' => 'Sessao expirada. Faca login novamente.',
                    ])
                    ->setStatusCode(401);
            }

            return redirect()->to('/admin/login');
        }

        // Check if user is still active
        $userModel = model('UserModel');
        $user = $userModel->find($session->get('admin_id'));

        if (!$user || $user['status'] !== 'active') {
            $session->destroy();
            return redirect()->to('/admin/login')->with('error', 'Sua conta foi desativada.');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
