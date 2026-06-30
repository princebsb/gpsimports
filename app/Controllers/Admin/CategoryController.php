<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class CategoryController extends BaseController
{
    protected $categoryModel;
    protected $brandModel;

    public function __construct()
    {
        $this->categoryModel = model('CategoryModel');
        $this->brandModel = model('BrandModel');
    }

    // CATEGORIES

    public function index()
    {
        $categories = $this->categoryModel->orderBy('sort_order')->orderBy('name')->findAll();

        return view('admin/categories/index', [
            'title' => 'Categorias',
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return view('admin/categories/form', [
            'title' => 'Nova Categoria',
            'category' => null,
            'parents' => $this->categoryModel->getDropdownOptions(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $id = $data['id'] ?? null;
        unset($data['id']);

        if (empty($data['slug'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/categories/', $newName);
            $data['image'] = $newName;
        }

        if ($id) {
            $this->categoryModel->update($id, $data);
            $message = 'Categoria atualizada com sucesso!';
        } else {
            $this->categoryModel->insert($data);
            $message = 'Categoria criada com sucesso!';
        }

        return redirect()->to('/admin/categorias')->with('success', $message);
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('/admin/categorias')->with('error', 'Categoria nao encontrada.');
        }

        return view('admin/categories/form', [
            'title' => 'Editar Categoria',
            'category' => $category,
            'parents' => $this->categoryModel->getDropdownOptions($id),
        ]);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(FCPATH . 'uploads/categories/', $newName);
            $data['image'] = $newName;
        }

        $this->categoryModel->update($id, $data);

        return redirect()->to('/admin/categorias')->with('success', 'Categoria atualizada!');
    }

    public function delete($id)
    {
        // Check for products
        $productCount = model('ProductModel')->where('category_id', $id)->countAllResults();

        if ($productCount > 0) {
            return redirect()->back()->with('error', 'Nao e possivel excluir. Existem produtos nesta categoria.');
        }

        // Check for subcategories
        $subCount = $this->categoryModel->where('parent_id', $id)->countAllResults();

        if ($subCount > 0) {
            return redirect()->back()->with('error', 'Nao e possivel excluir. Existem subcategorias.');
        }

        $this->categoryModel->delete($id);

        return redirect()->to('/admin/categorias')->with('success', 'Categoria excluida!');
    }

    public function sort()
    {
        $order = $this->request->getPost('order');

        if (is_array($order)) {
            foreach ($order as $position => $id) {
                $this->categoryModel->update($id, ['sort_order' => $position]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    // BRANDS

    public function brands()
    {
        $brands = $this->brandModel->orderBy('sort_order')->orderBy('name')->findAll();

        return view('admin/brands/index', [
            'title' => 'Marcas',
            'brands' => $brands,
        ]);
    }

    public function createBrand()
    {
        return view('admin/brands/form', [
            'title' => 'Nova Marca',
            'brand' => null,
        ]);
    }

    public function storeBrand()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $id = $data['id'] ?? null;
        unset($data['id']);

        if (empty($data['slug'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/brands/', $newName);
            $data['logo'] = $newName;
        }

        if ($id) {
            $this->brandModel->update($id, $data);
            $message = 'Marca atualizada com sucesso!';
        } else {
            $this->brandModel->insert($data);
            $message = 'Marca criada com sucesso!';
        }

        return redirect()->to('/admin/marcas')->with('success', $message);
    }

    public function editBrand($id)
    {
        $brand = $this->brandModel->find($id);

        if (!$brand) {
            return redirect()->to('/admin/marcas')->with('error', 'Marca nao encontrada.');
        }

        return view('admin/brands/form', [
            'title' => 'Editar Marca',
            'brand' => $brand,
        ]);
    }

    public function updateBrand($id)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/brands/', $newName);
            $data['logo'] = $newName;
        }

        $this->brandModel->update($id, $data);

        return redirect()->to('/admin/marcas')->with('success', 'Marca atualizada!');
    }

    public function deleteBrand($id)
    {
        $productCount = model('ProductModel')->where('brand_id', $id)->countAllResults();

        if ($productCount > 0) {
            return redirect()->back()->with('error', 'Nao e possivel excluir. Existem produtos desta marca.');
        }

        $this->brandModel->delete($id);

        return redirect()->to('/admin/marcas')->with('success', 'Marca excluida!');
    }
}
