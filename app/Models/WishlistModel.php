<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table = 'wishlists';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'customer_id',
        'product_id',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Get wishlist by customer
     */
    public function getByCustomer(int $customerId): array
    {
        return $this->select('wishlists.*, products.name, products.slug, products.price, products.sale_price, products.featured_image, products.stock, products.status')
                    ->join('products', 'products.id = wishlists.product_id')
                    ->where('wishlists.customer_id', $customerId)
                    ->orderBy('wishlists.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist(int $customerId, int $productId): bool
    {
        return $this->where('customer_id', $customerId)
                    ->where('product_id', $productId)
                    ->countAllResults() > 0;
    }

    /**
     * Add to wishlist
     */
    public function addToWishlist(int $customerId, int $productId): bool
    {
        if ($this->isInWishlist($customerId, $productId)) {
            return true;
        }

        return $this->insert([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    /**
     * Remove from wishlist
     */
    public function removeFromWishlist(int $customerId, int $productId): bool
    {
        return $this->where('customer_id', $customerId)
                    ->where('product_id', $productId)
                    ->delete();
    }

    /**
     * Toggle wishlist item
     */
    public function toggle(int $customerId, int $productId): array
    {
        if ($this->isInWishlist($customerId, $productId)) {
            $this->removeFromWishlist($customerId, $productId);
            return ['action' => 'removed', 'in_wishlist' => false];
        }

        $this->addToWishlist($customerId, $productId);
        return ['action' => 'added', 'in_wishlist' => true];
    }

    /**
     * Count wishlist items
     */
    public function countByCustomer(int $customerId): int
    {
        return $this->where('customer_id', $customerId)->countAllResults();
    }

    /**
     * Clear wishlist
     */
    public function clearWishlist(int $customerId): bool
    {
        return $this->where('customer_id', $customerId)->delete();
    }
}
