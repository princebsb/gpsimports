<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\ProductVariationModel;
use App\Models\StockMovementModel;

class StockService
{
    protected ProductModel $productModel;
    protected ProductVariationModel $variationModel;
    protected StockMovementModel $movementModel;

    public function __construct()
    {
        $this->productModel = model('ProductModel');
        $this->variationModel = model('ProductVariationModel');
        $this->movementModel = model('StockMovementModel');
    }

    /**
     * Add stock
     */
    public function add(int $productId, int $quantity, ?int $variationId = null, ?string $reason = null, ?int $userId = null): bool
    {
        return $this->adjust($productId, $quantity, $variationId, 'in', $reason, null, null, $userId);
    }

    /**
     * Remove stock
     */
    public function remove(int $productId, int $quantity, ?int $variationId = null, ?string $reason = null, ?int $userId = null): bool
    {
        return $this->adjust($productId, -$quantity, $variationId, 'out', $reason, null, null, $userId);
    }

    /**
     * Reserve stock (for pending orders)
     */
    public function reserve(int $productId, int $quantity, ?int $variationId = null, ?string $referenceType = null, ?int $referenceId = null): bool
    {
        return $this->adjust($productId, -$quantity, $variationId, 'reserved', 'Reserva para pedido', $referenceType, $referenceId);
    }

    /**
     * Release reservation
     */
    public function releaseReservation(int $productId, int $quantity, ?int $variationId = null, ?string $referenceType = null, ?int $referenceId = null): bool
    {
        return $this->adjust($productId, $quantity, $variationId, 'released', 'Liberacao de reserva', $referenceType, $referenceId);
    }

    /**
     * Confirm reservation (convert to actual deduction)
     */
    public function confirmReservation(int $productId, int $quantity, ?int $variationId = null): bool
    {
        // Reservation already deducted stock, just log confirmation
        return $this->movementModel->recordMovement([
            'product_id' => $productId,
            'variation_id' => $variationId,
            'type' => 'out',
            'quantity' => $quantity,
            'previous_stock' => $this->getCurrentStock($productId, $variationId),
            'current_stock' => $this->getCurrentStock($productId, $variationId),
            'reason' => 'Confirmacao de venda',
        ]);
    }

    /**
     * Adjust stock
     */
    public function adjust(
        int $productId,
        int $quantity,
        ?int $variationId = null,
        string $type = 'adjustment',
        ?string $reason = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): bool {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $previousStock = $this->getCurrentStock($productId, $variationId);
            $newStock = $previousStock + $quantity;

            // Update stock
            if ($variationId) {
                $this->variationModel->update($variationId, ['stock' => max(0, $newStock)]);
            } else {
                $this->productModel->update($productId, ['stock' => max(0, $newStock)]);
            }

            // Record movement
            $this->movementModel->recordMovement([
                'product_id' => $productId,
                'variation_id' => $variationId,
                'type' => $type,
                'quantity' => abs($quantity),
                'previous_stock' => $previousStock,
                'current_stock' => max(0, $newStock),
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => $userId ?? session()->get('admin_id'),
            ]);

            // Check for alerts
            $this->checkStockAlerts($productId, $variationId, max(0, $newStock));

            $db->transComplete();

            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'StockService::adjust - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get current stock
     */
    public function getCurrentStock(int $productId, ?int $variationId = null): int
    {
        if ($variationId) {
            $variation = $this->variationModel->find($variationId);
            return $variation ? (int) $variation['stock'] : 0;
        }

        $product = $this->productModel->find($productId);
        return $product ? (int) $product['stock'] : 0;
    }

    /**
     * Check and create stock alerts
     */
    protected function checkStockAlerts(int $productId, ?int $variationId, int $currentStock): void
    {
        $product = $this->productModel->find($productId);
        $threshold = $product['stock_min'] ?? 5;

        $db = \Config\Database::connect();

        // Check existing alert
        $existingAlert = $db->table('stock_alerts')
            ->where('product_id', $productId)
            ->where('variation_id', $variationId)
            ->where('is_resolved', 0)
            ->get()
            ->getRowArray();

        if ($currentStock <= 0) {
            // Out of stock alert
            if (!$existingAlert || $existingAlert['type'] !== 'out_of_stock') {
                if ($existingAlert) {
                    $db->table('stock_alerts')
                       ->where('id', $existingAlert['id'])
                       ->update(['is_resolved' => 1, 'resolved_at' => date('Y-m-d H:i:s')]);
                }

                $db->table('stock_alerts')->insert([
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'type' => 'out_of_stock',
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } elseif ($currentStock <= $threshold) {
            // Low stock alert
            if (!$existingAlert || $existingAlert['type'] !== 'low_stock') {
                if ($existingAlert) {
                    $db->table('stock_alerts')
                       ->where('id', $existingAlert['id'])
                       ->update(['is_resolved' => 1, 'resolved_at' => date('Y-m-d H:i:s')]);
                }

                $db->table('stock_alerts')->insert([
                    'product_id' => $productId,
                    'variation_id' => $variationId,
                    'type' => 'low_stock',
                    'current_stock' => $currentStock,
                    'threshold' => $threshold,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } else {
            // Resolve existing alert
            if ($existingAlert) {
                $db->table('stock_alerts')
                   ->where('id', $existingAlert['id'])
                   ->update(['is_resolved' => 1, 'resolved_at' => date('Y-m-d H:i:s')]);
            }
        }
    }

    /**
     * Get stock alerts
     */
    public function getAlerts(bool $unresolvedOnly = true): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('stock_alerts')
            ->select('stock_alerts.*, products.name as product_name, products.sku, product_variations.name as variation_name')
            ->join('products', 'products.id = stock_alerts.product_id')
            ->join('product_variations', 'product_variations.id = stock_alerts.variation_id', 'left');

        if ($unresolvedOnly) {
            $builder->where('stock_alerts.is_resolved', 0);
        }

        return $builder->orderBy('stock_alerts.created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $limit = 20): array
    {
        return $this->productModel
            ->where('manage_stock', 1)
            ->where('stock <=', 'stock_min', false)
            ->where('status', 'active')
            ->orderBy('stock', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(int $limit = 20): array
    {
        return $this->productModel
            ->where('manage_stock', 1)
            ->where('stock <=', 0)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Bulk adjust stock
     */
    public function bulkAdjust(array $adjustments, ?int $userId = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($adjustments as $adjustment) {
            $success = $this->adjust(
                $adjustment['product_id'],
                $adjustment['quantity'],
                $adjustment['variation_id'] ?? null,
                'adjustment',
                $adjustment['reason'] ?? 'Ajuste em lote',
                null,
                null,
                $userId
            );

            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Falha ao ajustar produto ID: {$adjustment['product_id']}";
            }
        }

        return $results;
    }
}
