<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_id',
        'image',
        'alt',
        'sort_order',
        'is_main',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Get images by product
     */
    public function getByProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
                    ->orderBy('is_main', 'DESC')
                    ->orderBy('sort_order')
                    ->findAll();
    }

    /**
     * Get main image
     */
    public function getMainImage(int $productId): ?array
    {
        return $this->where('product_id', $productId)
                    ->where('is_main', 1)
                    ->first();
    }

    /**
     * Set main image
     */
    public function setMainImage(int $imageId, int $productId): bool
    {
        // Remove main from all images
        $this->where('product_id', $productId)
             ->set('is_main', 0)
             ->update();

        // Set new main
        return $this->update($imageId, ['is_main' => 1]);
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $imageIds): bool
    {
        $db = \Config\Database::connect();

        foreach ($imageIds as $order => $id) {
            $db->table($this->table)
               ->where('id', $id)
               ->update(['sort_order' => $order]);
        }

        return true;
    }

    /**
     * Delete image and file
     */
    public function deleteWithFile(int $imageId): bool
    {
        $image = $this->find($imageId);

        if (!$image) {
            return false;
        }

        // Delete file
        $filePath = FCPATH . 'uploads/products/' . $image['image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->delete($imageId);
    }
}
