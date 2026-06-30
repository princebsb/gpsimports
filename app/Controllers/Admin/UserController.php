<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    public function index()
    {
        $users = $this->userModel->findAll();

        return view('admin/users/index', [
            'title' => 'Usuarios',
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('admin/users/form', [
            'title' => 'Novo Usuario',
            'user' => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->userModel->insert($data);

        return redirect()->to('/admin/usuarios')->with('success', 'Usuario criado!');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/usuarios')->with('error', 'Usuario nao encontrado.');
        }

        return view('admin/users/form', [
            'title' => 'Editar Usuario',
            'user' => $user,
        ]);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/usuarios')->with('success', 'Usuario atualizado!');
    }

    public function delete($id)
    {
        if ($id == session()->get('admin_id')) {
            return redirect()->back()->with('error', 'Voce nao pode excluir sua propria conta.');
        }

        $this->userModel->delete($id);

        return redirect()->to('/admin/usuarios')->with('success', 'Usuario excluido!');
    }
}
