<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $brandModel;
    protected $productService;

    public function __construct()
    {
        $this->productModel = model('ProductModel');
        $this->categoryModel = model('CategoryModel');
        $this->brandModel = model('BrandModel');
        $this->productService = service('product');
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'category_id' => $this->request->getGet('category'),
            'brand_id' => $this->request->getGet('brand'),
            'status' => $this->request->getGet('status'),
        ];

        $builder = $this->productModel->select('products.*, categories.name as category_name, brands.name as brand_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('brands', 'brands.id = products.brand_id', 'left');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('products.name', $filters['search'])
                ->orLike('products.sku', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['category_id'])) {
            $builder->where('products.category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $builder->where('products.brand_id', $filters['brand_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('products.status', $filters['status']);
        }

        $products = $builder->orderBy('products.created_at', 'DESC')->paginate(20);

        return view('admin/products/index', [
            'title' => 'Produtos',
            'products' => $products,
            'pager' => $this->productModel->pager,
            'categories' => $this->categoryModel->getDropdownOptions(),
            'brands' => $this->brandModel->getDropdownOptions(),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/products/form', [
            'title' => 'Novo Produto',
            'product' => null,
            'categories' => $this->categoryModel->getDropdownOptions(),
            'brands' => $this->brandModel->getDropdownOptions(),
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[300]',
            'price' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $data['slug'] = url_title($data['name'], '-', true);

        // Handle featured image upload
        $image = $this->request->getFile('featured_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $data['featured_image'] = $this->productService->uploadImage($image);
        }

        $productId = $this->productService->createProduct($data);

        if (!$productId) {
            return redirect()->back()->withInput()->with('error', 'Erro ao criar produto.');
        }

        return redirect()->to('/admin/produtos/editar/' . $productId)->with('success', 'Produto criado com sucesso!');
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/admin/produtos')->with('error', 'Produto nao encontrado.');
        }

        $product['images'] = $this->productModel->getImages($id);
        $product['variations'] = $this->productModel->getVariations($id);

        return view('admin/products/form', [
            'title' => 'Editar Produto',
            'product' => $product,
            'categories' => $this->categoryModel->getDropdownOptions(),
            'brands' => $this->brandModel->getDropdownOptions(),
        ]);
    }

    public function update($id)
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[300]',
            'price' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle featured image upload
        $image = $this->request->getFile('featured_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $data['featured_image'] = $this->productService->uploadImage($image);
        }

        $result = $this->productService->updateProduct($id, $data);

        if (!$result) {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar produto.');
        }

        return redirect()->back()->with('success', 'Produto atualizado com sucesso!');
    }

    public function delete($id)
    {
        $result = $this->productService->deleteProduct($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $result]);
        }

        if (!$result) {
            return redirect()->back()->with('error', 'Erro ao excluir produto.');
        }

        return redirect()->to('/admin/produtos')->with('success', 'Produto excluido com sucesso!');
    }

    public function toggleStatus($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto nao encontrado.']);
        }

        $newStatus = $product['status'] === 'active' ? 'inactive' : 'active';
        $this->productModel->update($id, ['status' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status atualizado!',
        ]);
    }

    public function uploadImage()
    {
        $productId = $this->request->getPost('product_id');
        $image = $this->request->getFile('image');

        if (!$image || !$image->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Imagem invalida.']);
        }

        $imageName = $this->productService->uploadImage($image);

        if (!$imageName) {
            return $this->response->setJSON(['success' => false, 'message' => 'Erro ao fazer upload.']);
        }

        // Save to database
        $imageModel = model('ProductImageModel');
        $imageId = $imageModel->insert([
            'product_id' => $productId,
            'image' => $imageName,
            'sort_order' => 0,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'image_id' => $imageId,
            'image_url' => base_url('uploads/products/' . $imageName),
        ]);
    }

    public function removeImage($id)
    {
        $imageModel = model('ProductImageModel');
        $result = $imageModel->deleteWithFile($id);

        return $this->response->setJSON(['success' => $result]);
    }

    public function duplicate($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Produto nao encontrado.');
        }

        unset($product['id'], $product['created_at'], $product['updated_at']);
        $product['name'] = $product['name'] . ' (Copia)';
        $product['slug'] = $product['slug'] . '-copia-' . time();
        $product['sku'] = $product['sku'] ? $product['sku'] . '-COPY' : null;
        $product['status'] = 'draft';

        $newId = $this->productModel->insert($product);

        return redirect()->to('/admin/produtos/editar/' . $newId)->with('success', 'Produto duplicado!');
    }
}
