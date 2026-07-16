<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table = 'coupons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'code',
        'description',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'applies_to',
        'product_ids',
        'category_ids',
        'brand_ids',
        'exclude_sale_items',
        'first_purchase_only',
        'starts_at',
        'expires_at',
        'status',
        'created_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'code' => 'required|min_length[3]|max_length[50]|is_unique[coupons.code,id,{id}]',
        'type' => 'required|in_list[percentage,fixed,free_shipping]',
        'value' => 'required|decimal',
    ];

    protected array $casts = [
        'product_ids' => '?json-array',
        'category_ids' => '?json-array',
        'brand_ids' => '?json-array',
    ];

    protected $beforeInsert = ['handleJsonFields'];
    protected $beforeUpdate = ['handleJsonFields'];

    /**
     * Handle JSON fields to prevent null issues
     */
    protected function handleJsonFields(array $data): array
    {
        $jsonFields = ['product_ids', 'category_ids', 'brand_ids'];

        foreach ($jsonFields as $field) {
            if (array_key_exists($field, $data['data'])) {
                if (empty($data['data'][$field])) {
                    $data['data'][$field] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Get coupon by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('code', strtoupper($code))->first();
    }

    /**
     * Validate coupon
     */
    public function validateCoupon(string $code, float $cartTotal, ?int $customerId = null, array $cartItems = []): array
    {
        $coupon = $this->getByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Cupom nao encontrado.'];
        }

        // Check status
        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Este cupom esta inativo.'];
        }

        // Check dates
        $now = date('Y-m-d H:i:s');

        if (!empty($coupon['starts_at']) && $coupon['starts_at'] > $now) {
            return ['valid' => false, 'message' => 'Este cupom ainda nao esta ativo.'];
        }

        if (!empty($coupon['expires_at']) && $coupon['expires_at'] < $now) {
            return ['valid' => false, 'message' => 'Este cupom expirou.'];
        }

        // Check usage limit
        if (!empty($coupon['usage_limit']) && $coupon['usage_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Este cupom atingiu o limite de uso.'];
        }

        // Check per-user limit
        if ($customerId && $coupon['usage_limit_per_user'] > 0) {
            $userUsage = $this->getUserUsageCount($coupon['id'], $customerId);
            if ($userUsage >= $coupon['usage_limit_per_user']) {
                return ['valid' => false, 'message' => 'Voce ja utilizou este cupom o maximo de vezes permitido.'];
            }
        }

        // Check minimum order value
        if ($coupon['min_order_value'] > 0 && $cartTotal < $coupon['min_order_value']) {
            return [
                'valid' => false,
                'message' => 'Valor minimo do pedido: R$ ' . number_format($coupon['min_order_value'], 2, ',', '.'),
            ];
        }

        // Check first purchase only
        if ($coupon['first_purchase_only'] && $customerId) {
            $orderCount = model('OrderModel')
                ->where('customer_id', $customerId)
                ->where('payment_status', 'approved')
                ->countAllResults();

            if ($orderCount > 0) {
                return ['valid' => false, 'message' => 'Este cupom e valido apenas para primeira compra.'];
            }
        }

        // Calculate discount
        $discount = $this->calculateDiscount($coupon, $cartTotal, $cartItems);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Cupom aplicado com sucesso!',
        ];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(array $coupon, float $cartTotal, array $cartItems = []): float
    {
        $discount = 0;

        if ($coupon['type'] === 'free_shipping') {
            return 0; // Shipping discount handled separately
        }

        $applicableTotal = $cartTotal;

        // Filter applicable items if needed (trata null/vazio como 'all')
        $appliesTo = $coupon['applies_to'] ?? 'all';
        if ($appliesTo !== 'all' && !empty($appliesTo) && !empty($cartItems)) {
            $applicableTotal = 0;

            foreach ($cartItems as $item) {
                $applicable = false;

                switch ($appliesTo) {
                    case 'products':
                        $productIds = $coupon['product_ids'] ?? [];
                        $applicable = in_array($item['product_id'], $productIds);
                        break;
                    case 'categories':
                        $categoryIds = $coupon['category_ids'] ?? [];
                        $product = model('ProductModel')->find($item['product_id']);
                        $applicable = $product && in_array($product['category_id'], $categoryIds);
                        break;
                    case 'brands':
                        $brandIds = $coupon['brand_ids'] ?? [];
                        $product = model('ProductModel')->find($item['product_id']);
                        $applicable = $product && in_array($product['brand_id'], $brandIds);
                        break;
                }

                if ($applicable) {
                    $applicableTotal += $item['subtotal'];
                }
            }
        }

        // Log para debug
        log_message('debug', 'calculateDiscount: type=' . $coupon['type'] . ', value=' . $coupon['value'] . ', applicableTotal=' . $applicableTotal);

        if ($coupon['type'] === 'percentage') {
            $discount = $applicableTotal * ($coupon['value'] / 100);
        } else { // fixed
            $discount = $coupon['value'];
        }

        log_message('debug', 'calculateDiscount: discount calculado=' . $discount);

        // Apply max discount limit
        if (!empty($coupon['max_discount']) && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }

        // Don't exceed cart total
        if ($discount > $applicableTotal) {
            $discount = $applicableTotal;
        }

        return round($discount, 2);
    }

    /**
     * Get user usage count
     */
    public function getUserUsageCount(int $couponId, int $customerId): int
    {
        $db = \Config\Database::connect();

        return $db->table('coupon_usage')
                  ->where('coupon_id', $couponId)
                  ->where('customer_id', $customerId)
                  ->countAllResults();
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(int $couponId, int $customerId, int $orderId, float $discountApplied): bool
    {
        $db = \Config\Database::connect();

        $db->table('coupon_usage')->insert([
            'coupon_id' => $couponId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_applied' => $discountApplied,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Increment usage count
        return $this->set('usage_count', 'usage_count + 1', false)
                    ->where('id', $couponId)
                    ->update();
    }

    /**
     * Get active coupons
     */
    public function getActive(): array
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('status', 'active')
                    ->groupStart()
                    ->where('starts_at IS NULL')
                    ->orWhere('starts_at <=', $now)
                    ->groupEnd()
                    ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >=', $now)
                    ->groupEnd()
                    ->findAll();
    }
}
