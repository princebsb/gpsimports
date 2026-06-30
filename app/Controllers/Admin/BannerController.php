<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class BannerController extends BaseController
{
    protected $bannerModel;

    public function __construct()
    {
        $this->bannerModel = model('BannerModel');
    }

    public function index()
    {
        $banners = $this->bannerModel->orderBy('sort_order')->findAll();

        return view('admin/banners/index', [
            'title' => 'Banners',
            'banners' => $banners,
        ]);
    }

    public function create()
    {
        return view('admin/banners/form', [
            'title' => 'Novo Banner',
            'banner' => null,
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/banners/', $newName);
            $data['image'] = $newName;
        }

        $this->bannerModel->insert($data);

        return redirect()->to('/admin/banners')->with('success', 'Banner criado!');
    }

    public function edit($id)
    {
        $banner = $this->bannerModel->find($id);

        if (!$banner) {
            return redirect()->to('/admin/banners')->with('error', 'Banner nao encontrado.');
        }

        return view('admin/banners/form', [
            'title' => 'Editar Banner',
            'banner' => $banner,
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/banners/', $newName);
            $data['image'] = $newName;
        }

        $this->bannerModel->update($id, $data);

        return redirect()->to('/admin/banners')->with('success', 'Banner atualizado!');
    }

    public function delete($id)
    {
        $this->bannerModel->delete($id);
        return redirect()->to('/admin/banners')->with('success', 'Banner excluido!');
    }

    public function sort()
    {
        $order = $this->request->getPost('order');

        if (is_array($order)) {
            foreach ($order as $position => $id) {
                $this->bannerModel->update($id, ['sort_order' => $position]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }
}
