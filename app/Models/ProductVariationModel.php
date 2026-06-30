<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariationModel extends Model
{
    protected $table = 'product_variations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_id',
        'sku',
        'name',
        'attributes',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'weight',
        'image',
        'barcode',
        'sort_order',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected array $casts = [
        'attributes' => '?json-array',
    ];

    /**
     * Get variations by product
     */
    public function getByProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
                    ->where('status', 'active')
                    ->orderBy('sort_order')
                    ->findAll();
    }

    /**
     * Get variation by attributes
     */
    public function getByAttributes(int $productId, array $attributes): ?array
    {
        $variations = $this->where('product_id', $productId)
                           ->where('status', 'active')
                           ->findAll();

        foreach ($variations as $variation) {
            $varAttributes = is_string($variation['attributes'])
                ? json_decode($variation['attributes'], true)
                : $variation['attributes'];

            if ($varAttributes == $attributes) {
                return $variation;
            }
        }

        return null;
    }

    /**
     * Get price (considering sale)
     */
    public function getCurrentPrice(array $variation): float
    {
        if (!empty($variation['sale_price']) && $variation['sale_price'] < $variation['price']) {
            return (float) $variation['sale_price'];
        }

        return (float) $variation['price'];
    }

    /**
     * Update stock
     */
    public function updateStock(int $variationId, int $quantity): bool
    {
        return $this->set('stock', 'stock + ' . $quantity, false)
                    ->where('id', $variationId)
                    ->update();
    }

    /**
     * Decrement stock
     */
    public function decrementStock(int $variationId, int $quantity): bool
    {
        return $this->set('stock', 'stock - ' . $quantity, false)
                    ->where('id', $variationId)
                    ->update();
    }

    /**
     * Get total stock for product
     */
    public function getTotalStock(int $productId): int
    {
        $result = $this->selectSum('stock')
                       ->where('product_id', $productId)
                       ->where('status', 'active')
                       ->first();

        return (int) ($result['stock'] ?? 0);
    }
}
