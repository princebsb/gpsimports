<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class ProductController extends BaseController
{
    protected $productService;

    public function __construct()
    {
        $this->productService = service('product');
    }

    public function index()
    {
        $filters = [
            'category_id' => $this->request->getGet('categoria'),
            'brand_id' => $this->request->getGet('marca'),
            'min_price' => $this->request->getGet('preco_min'),
            'max_price' => $this->request->getGet('preco_max'),
            'on_sale' => $this->request->getGet('promocao'),
            'sort' => $this->request->getGet('ordenar') ?? 'newest',
            'search' => $this->request->getGet('busca'),
        ];

        $result = $this->productService->getFilteredProducts($filters, 20);

        $categoryModel = model('CategoryModel');
        $brandModel = model('BrandModel');

        // Filtrar marcas pela categoria selecionada
        if (!empty($filters['category_id'])) {
            $brands = $brandModel->getByCategory((int) $filters['category_id']);
        } else {
            $brands = $brandModel->getActive();
        }

        return view('front/products/index', [
            'title' => 'Produtos',
            'products' => $result['products'],
            'pager' => $result['pager'],
            'categories' => $categoryModel->getActive(),
            'brands' => $brands,
            'filters' => $filters,
        ]);
    }

    public function show($slug)
    {
        $product = $this->productService->getProductForDisplay($slug);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if in wishlist
        $inWishlist = false;
        if (session()->get('customer_id')) {
            $inWishlist = model('WishlistModel')->isInWishlist(
                session()->get('customer_id'),
                $product['id']
            );
        }

        return view('front/products/show', [
            'title' => $product['name'],
            'product' => $product,
            'inWishlist' => $inWishlist,
        ]);
    }

    public function byBrand($slug)
    {
        $brand = model('BrandModel')->getBySlug($slug);

        if (!$brand) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filters = [
            'brand_id' => $brand['id'],
            'sort' => $this->request->getGet('ordenar') ?? 'newest',
        ];

        $result = $this->productService->getFilteredProducts($filters, 20);

        return view('front/products/brand', [
            'title' => $brand['name'],
            'brand' => $brand,
            'products' => $result['products'],
            'pager' => $result['pager'],
            'filters' => $filters,
        ]);
    }

    public function promotions()
    {
        $filters = [
            'on_sale' => true,
            'sort' => $this->request->getGet('ordenar') ?? 'newest',
        ];

        $result = $this->productService->getFilteredProducts($filters, 20);

        $categoryModel = model('CategoryModel');
        $brandModel = model('BrandModel');

        return view('front/products/index', [
            'title' => 'Promocoes',
            'products' => $result['products'],
            'pager' => $result['pager'],
            'categories' => $categoryModel->getActive(),
            'brands' => $brandModel->getActive(),
            'filters' => $filters,
        ]);
    }

    public function newArrivals()
    {
        $filters = [
            'is_new' => true,
            'sort' => $this->request->getGet('ordenar') ?? 'newest',
        ];

        $result = $this->productService->getFilteredProducts($filters, 20);

        $categoryModel = model('CategoryModel');
        $brandModel = model('BrandModel');

        return view('front/products/index', [
            'title' => 'Lancamentos',
            'products' => $result['products'],
            'pager' => $result['pager'],
            'categories' => $categoryModel->getActive(),
            'brands' => $brandModel->getActive(),
            'filters' => $filters,
        ]);
    }

    /**
     * Retorna marcas de uma categoria (AJAX)
     */
    public function brandsByCategory()
    {
        $categoryId = (int) $this->request->getGet('category_id');

        $db = \Config\Database::connect();

        if ($categoryId > 0) {
            // Buscar marcas que têm produtos nessa categoria
            $brands = $db->table('brands')
                ->select('brands.id, brands.name')
                ->join('products', 'products.brand_id = brands.id')
                ->where('products.category_id', $categoryId)
                ->where('products.status', 'active')
                ->where('brands.status', 'active')
                ->groupBy('brands.id')
                ->orderBy('brands.name', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            // Todas as marcas ativas
            $brands = $db->table('brands')
                ->select('id, name')
                ->where('status', 'active')
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'brands' => $brands,
        ]);
    }

    /**
     * Calculate shipping for a single product
     */
    public function calculateShipping()
    {
        $zipcode = $this->request->getPost('zipcode');
        $productId = (int) $this->request->getPost('product_id');
        $quantity = (int) ($this->request->getPost('quantity') ?? 1);

        if (empty($zipcode)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Digite seu CEP.']);
        }

        if (empty($productId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto nao informado.']);
        }

        $product = model('ProductModel')->find($productId);

        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto nao encontrado.']);
        }

        // Clean zipcode
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

        if (strlen($zipcode) !== 8) {
            return $this->response->setJSON(['success' => false, 'message' => 'CEP invalido.']);
        }

        // Prepare product data for shipping calculation
        $products = [
            [
                'id' => $product['id'],
                'weight' => (float) ($product['weight'] ?? 0.3),
                'width' => (int) ($product['width'] ?? 11),
                'height' => (int) ($product['height'] ?? 2),
                'length' => (int) ($product['length'] ?? 16),
                'price' => (float) $product['price'],
                'quantity' => $quantity,
            ]
        ];

        // Use Melhor Envio service
        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $options = $melhorEnvio->calculate($zipcode, $products);

        if (empty($options)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nao foi possivel calcular o frete para este CEP.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'options' => $options,
        ]);
    }
}
