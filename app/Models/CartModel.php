<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'customer_id',
        'session_id',
        'coupon_id',
        'subtotal',
        'discount',
        'shipping_cost',
        'shipping_method',
        'shipping_zipcode',
        'total',
        'items_count',
        'notes',
        'expires_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get or create cart
     */
    public function getOrCreate(?int $customerId = null, ?string $sessionId = null): array
    {
        $cart = null;

        if ($customerId) {
            $cart = $this->where('customer_id', $customerId)->first();
        } elseif ($sessionId) {
            $cart = $this->where('session_id', $sessionId)->first();
        }

        if (!$cart) {
            $cartId = $this->insert([
                'customer_id' => $customerId,
                'session_id' => $sessionId,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            ]);

            $cart = $this->find($cartId);
        }

        return $cart;
    }

    /**
     * Get cart with items
     */
    public function getWithItems(int $cartId): array
    {
        $cart = $this->find($cartId);

        if (!$cart) {
            return [];
        }

        $cart['items'] = $this->getItems($cartId);

        return $cart;
    }

    /**
     * Get cart items
     */
    public function getItems(int $cartId): array
    {
        $db = \Config\Database::connect();

        return $db->table('cart_items')
                  ->select('cart_items.*, products.slug, products.stock, products.manage_stock')
                  ->join('products', 'products.id = cart_items.product_id')
                  ->where('cart_items.cart_id', $cartId)
                  ->get()
                  ->getResultArray();
    }

    /**
     * Add item to cart
     */
    public function addItem(int $cartId, array $item): bool
    {
        $db = \Config\Database::connect();

        // Check if item already exists
        $existing = $db->table('cart_items')
                       ->where('cart_id', $cartId)
                       ->where('product_id', $item['product_id'])
                       ->where('variation_id', $item['variation_id'] ?? null)
                       ->get()
                       ->getRowArray();

        if ($existing) {
            // Update quantity
            $newQty = $existing['quantity'] + $item['quantity'];

            $db->table('cart_items')
               ->where('id', $existing['id'])
               ->update([
                   'quantity' => $newQty,
                   'subtotal' => $item['price'] * $newQty,
                   'updated_at' => date('Y-m-d H:i:s'),
               ]);
        } else {
            // Insert new item
            $db->table('cart_items')->insert([
                'cart_id' => $cartId,
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'] ?? null,
                'name' => $item['name'],
                'sku' => $item['sku'] ?? null,
                'image' => $item['image'] ?? null,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
                'attributes' => isset($item['attributes']) ? json_encode($item['attributes']) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->recalculate($cartId);
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(int $cartId, int $itemId, int $quantity): bool
    {
        $db = \Config\Database::connect();

        $item = $db->table('cart_items')
                   ->where('id', $itemId)
                   ->where('cart_id', $cartId)
                   ->get()
                   ->getRowArray();

        if (!$item) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeItem($cartId, $itemId);
        }

        $db->table('cart_items')
           ->where('id', $itemId)
           ->update([
               'quantity' => $quantity,
               'subtotal' => $item['price'] * $quantity,
               'updated_at' => date('Y-m-d H:i:s'),
           ]);

        return $this->recalculate($cartId);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartId, int $itemId): bool
    {
        $db = \Config\Database::connect();

        $db->table('cart_items')
           ->where('id', $itemId)
           ->where('cart_id', $cartId)
           ->delete();

        return $this->recalculate($cartId);
    }

    /**
     * Recalculate cart totals
     */
    public function recalculate(int $cartId): bool
    {
        $db = \Config\Database::connect();

        $items = $db->table('cart_items')
                    ->select('SUM(subtotal) as subtotal, COUNT(*) as count, SUM(quantity) as items_count')
                    ->where('cart_id', $cartId)
                    ->get()
                    ->getRowArray();

        $subtotal = (float) ($items['subtotal'] ?? 0);
        $itemsCount = (int) ($items['items_count'] ?? 0);

        $cart = $this->find($cartId);
        $discount = (float) ($cart['discount'] ?? 0);
        $shippingCost = (float) ($cart['shipping_cost'] ?? 0);

        $total = $subtotal - $discount + $shippingCost;

        return $this->update($cartId, [
            'subtotal' => $subtotal,
            'total' => max(0, $total),
            'items_count' => $itemsCount,
        ]);
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(int $cartId, int $couponId, float $discount): bool
    {
        $this->update($cartId, [
            'coupon_id' => $couponId,
            'discount' => $discount,
        ]);

        return $this->recalculate($cartId);
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(int $cartId): bool
    {
        $this->update($cartId, [
            'coupon_id' => null,
            'discount' => 0,
        ]);

        return $this->recalculate($cartId);
    }

    /**
     * Set shipping
     */
    public function setShipping(int $cartId, string $method, float $cost, string $zipcode): bool
    {
        $this->update($cartId, [
            'shipping_method' => $method,
            'shipping_cost' => $cost,
            'shipping_zipcode' => $zipcode,
        ]);

        return $this->recalculate($cartId);
    }

    /**
     * Clear cart
     */
    public function clearCart(int $cartId): bool
    {
        $db = \Config\Database::connect();

        $db->table('cart_items')
           ->where('cart_id', $cartId)
           ->delete();

        return $this->update($cartId, [
            'subtotal' => 0,
            'discount' => 0,
            'shipping_cost' => 0,
            'total' => 0,
            'items_count' => 0,
            'coupon_id' => null,
        ]);
    }

    /**
     * Merge guest cart with customer cart
     */
    public function mergeCarts(string $sessionId, int $customerId): bool
    {
        $guestCart = $this->where('session_id', $sessionId)->first();
        $customerCart = $this->where('customer_id', $customerId)->first();

        if (!$guestCart) {
            return true;
        }

        if (!$customerCart) {
            // Just assign guest cart to customer
            return $this->update($guestCart['id'], [
                'customer_id' => $customerId,
                'session_id' => null,
            ]);
        }

        // Merge items
        $guestItems = $this->getItems($guestCart['id']);

        foreach ($guestItems as $item) {
            $this->addItem($customerCart['id'], $item);
        }

        // Delete guest cart
        $this->delete($guestCart['id']);

        return true;
    }

    /**
     * Clean expired carts
     */
    public function cleanExpired(): int
    {
        $db = \Config\Database::connect();

        // Get expired cart IDs
        $expiredCarts = $this->where('expires_at <', date('Y-m-d H:i:s'))
                             ->where('customer_id IS NULL')
                             ->findColumn('id');

        if (empty($expiredCarts)) {
            return 0;
        }

        // Delete items
        $db->table('cart_items')
           ->whereIn('cart_id', $expiredCarts)
           ->delete();

        // Delete carts
        return $this->whereIn('id', $expiredCarts)->delete();
    }
}
