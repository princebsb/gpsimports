<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_id',
        'variation_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Record stock movement
     */
    public function recordMovement(array $data): bool
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data) !== false;
    }

    /**
     * Get movements by product
     */
    public function getByProduct(int $productId, int $limit = 50): array
    {
        return $this->where('product_id', $productId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get recent movements
     */
    public function getRecent(int $limit = 50): array
    {
        return $this->select('stock_movements.*, products.name as product_name, users.name as user_name')
                    ->join('products', 'products.id = stock_movements.product_id')
                    ->join('users', 'users.id = stock_movements.user_id', 'left')
                    ->orderBy('stock_movements.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get type label
     */
    public function getTypeLabel(string $type): string
    {
        $labels = [
            'in' => 'Entrada',
            'out' => 'Saida',
            'adjustment' => 'Ajuste',
            'return' => 'Devolucao',
            'reserved' => 'Reserva',
            'released' => 'Liberacao',
        ];

        return $labels[$type] ?? $type;
    }
}
