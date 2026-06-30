<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class StockController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = model('ProductModel');
    }

    public function index()
    {
        $filter = $this->request->getGet('filter') ?? 'all';

        $builder = $this->productModel
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.status', 'active');

        if ($filter === 'low') {
            $builder->where('stock <=', 5)->where('stock >', 0);
        } elseif ($filter === 'out') {
            $builder->where('stock', 0);
        }

        $products = $builder->orderBy('stock', 'ASC')->paginate(50);

        return view('admin/stock/index', [
            'title' => 'Gestao de Estoque',
            'products' => $products,
            'pager' => $this->productModel->pager,
            'filter' => $filter,
        ]);
    }

    public function adjust()
    {
        $productId = $this->request->getPost('product_id');
        $quantity = (int) $this->request->getPost('quantity');
        $type = $this->request->getPost('type');
        $reason = $this->request->getPost('reason');

        $product = $this->productModel->find($productId);

        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Produto nao encontrado.']);
        }

        $newStock = $type === 'add' ? $product['stock'] + $quantity : $product['stock'] - $quantity;

        if ($newStock < 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estoque nao pode ser negativo.']);
        }

        $this->productModel->update($productId, ['stock' => $newStock]);

        // Log movement
        $db = \Config\Database::connect();
        $db->table('stock_movements')->insert([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $product['stock'],
            'stock_after' => $newStock,
            'reason' => $reason,
            'user_id' => session()->get('admin_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'new_stock' => $newStock,
            'message' => 'Estoque atualizado!',
        ]);
    }

    public function movements()
    {
        $db = \Config\Database::connect();

        $movements = $db->table('stock_movements')
            ->select('stock_movements.*, products.name as product_name, users.name as user_name')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('users', 'users.id = stock_movements.user_id', 'left')
            ->orderBy('stock_movements.created_at', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();

        return view('admin/stock/movements', [
            'title' => 'Movimentacoes de Estoque',
            'movements' => $movements,
        ]);
    }

    public function alerts()
    {
        $products = $this->productModel
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->where('products.status', 'active')
            ->where('stock <=', 5)
            ->orderBy('stock', 'ASC')
            ->findAll();

        return view('admin/stock/alerts', [
            'title' => 'Alertas de Estoque',
            'products' => $products,
        ]);
    }
}
