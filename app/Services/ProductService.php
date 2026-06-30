<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\ProductImageModel;
use App\Models\ProductVariationModel;
use App\Models\CategoryModel;

class ProductService
{
    protected ProductModel $productModel;
    protected ProductImageModel $imageModel;
    protected ProductVariationModel $variationModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel = model('ProductModel');
        $this->imageModel = model('ProductImageModel');
        $this->variationModel = model('ProductVariationModel');
        $this->categoryModel = model('CategoryModel');
    }

    /**
     * Get product for display
     */
    public function getProductForDisplay(string $slug): ?array
    {
        $product = $this->productModel->getBySlug($slug);

        if (!$product) {
            return null;
        }

        // Increment views
        $this->productModel->incrementViews($product['id']);

        // Get additional data
        $product['current_price'] = $this->productModel->getCurrentPrice($product);
        $product['discount_percent'] = $this->calculateDiscountPercent($product);
        $product['breadcrumb'] = $product['category_id']
            ? $this->categoryModel->getBreadcrumb($product['category_id'])
            : [];
        $product['related'] = $this->productModel->getRelated($product['id']);
        $product['in_stock'] = $this->productModel->isInStock($product['id']);

        return $product;
    }

    /**
     * Get filtered products for listing
     */
    public function getFilteredProducts(array $filters, int $perPage = 20): array
    {
        $result = $this->productModel->getFiltered($filters, $perPage);

        // Add calculated fields to each product
        foreach ($result['products'] as &$product) {
            $product['current_price'] = $this->productModel->getCurrentPrice($product);
            $product['discount_percent'] = $this->calculateDiscountPercent($product);
        }

        return $result;
    }

    /**
     * Calculate discount percentage
     */
    protected function calculateDiscountPercent(array $product): int
    {
        if (empty($product['sale_price']) || $product['sale_price'] >= $product['price']) {
            return 0;
        }

        return (int) round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
    }

    /**
     * Create product
     */
    public function createProduct(array $data): ?int
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create product
            $productId = $this->productModel->insert($data);

            if (!$productId) {
                throw new \Exception('Failed to create product');
            }

            // Handle images
            if (!empty($data['images'])) {
                $this->saveImages($productId, $data['images']);
            }

            // Handle variations
            if (!empty($data['variations'])) {
                $this->saveVariations($productId, $data['variations']);
            }

            // Handle specifications
            if (!empty($data['specifications'])) {
                $this->saveSpecifications($productId, $data['specifications']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log action
            model('AuditLogModel')->log('product_created', 'Product', $productId, null, $data);

            return $productId;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'ProductService::createProduct - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update product
     */
    public function updateProduct(int $productId, array $data): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $oldData = $this->productModel->find($productId);

            // Adicionar id para validacao do slug unico funcionar
            $data['id'] = $productId;

            // Update product (desabilitar validacao pois ja foi feita no controller)
            $this->productModel->skipValidation(true)->update($productId, $data);

            // Handle images
            if (isset($data['images'])) {
                $this->saveImages($productId, $data['images']);
            }

            // Handle variations
            if (isset($data['variations'])) {
                $this->saveVariations($productId, $data['variations']);
            }

            // Handle specifications
            if (isset($data['specifications'])) {
                $this->saveSpecifications($productId, $data['specifications']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            // Log action
            model('AuditLogModel')->log('product_updated', 'Product', $productId, $oldData, $data);

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'ProductService::updateProduct - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save product images
     */
    protected function saveImages(int $productId, array $images): void
    {
        foreach ($images as $index => $image) {
            if (is_array($image)) {
                $image['product_id'] = $productId;
                $image['sort_order'] = $index;
                $image['is_main'] = $index === 0 ? 1 : 0;

                if (!empty($image['id'])) {
                    $this->imageModel->update($image['id'], $image);
                } else {
                    $this->imageModel->insert($image);
                }
            }
        }
    }

    /**
     * Save product variations
     */
    protected function saveVariations(int $productId, array $variations): void
    {
        // Mark product as having variations
        $this->productModel->update($productId, ['has_variations' => !empty($variations) ? 1 : 0]);

        foreach ($variations as $index => $variation) {
            $variation['product_id'] = $productId;
            $variation['sort_order'] = $index;

            if (is_array($variation['attributes'] ?? null)) {
                $variation['attributes'] = json_encode($variation['attributes']);
            }

            if (!empty($variation['id'])) {
                $this->variationModel->update($variation['id'], $variation);
            } else {
                $this->variationModel->insert($variation);
            }
        }
    }

    /**
     * Save product specifications
     */
    protected function saveSpecifications(int $productId, array $specifications): void
    {
        $db = \Config\Database::connect();

        // Delete existing
        $db->table('product_specifications')
           ->where('product_id', $productId)
           ->delete();

        // Insert new
        foreach ($specifications as $index => $spec) {
            if (!empty($spec['name']) && !empty($spec['value'])) {
                $db->table('product_specifications')->insert([
                    'product_id' => $productId,
                    'name' => $spec['name'],
                    'value' => $spec['value'],
                    'group' => $spec['group'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $productId): bool
    {
        $product = $this->productModel->find($productId);

        if (!$product) {
            return false;
        }

        // Soft delete
        $result = $this->productModel->delete($productId);

        if ($result) {
            model('AuditLogModel')->log('product_deleted', 'Product', $productId, $product);
        }

        return $result;
    }

    /**
     * Upload product image
     */
    public function uploadImage($file): ?string
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $path = FCPATH . 'uploads/products/';

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if ($file->move($path, $newName)) {
            return $newName;
        }

        return null;
    }

    /**
     * Search products
     */
    public function search(string $term, int $limit = 10): array
    {
        $products = $this->productModel->search($term, $limit);

        foreach ($products as &$product) {
            $product['current_price'] = $this->productModel->getCurrentPrice($product);
        }

        return $products;
    }

    /**
     * Get home page products
     */
    public function getHomeProducts(): array
    {
        return [
            'featured' => $this->productModel->getFeatured(8),
            'new' => $this->productModel->getNew(8),
            'bestsellers' => $this->productModel->getBestsellers(8),
            'on_sale' => $this->productModel->getOnSale(8),
        ];
    }
}
