<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'website',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_featured',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[200]',
        'slug' => 'required|min_length[2]|max_length[200]|is_unique[brands.slug,id,{id}]',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Generate slug from name
     */
    protected function generateSlug(array $data): array
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }

        return $data;
    }

    /**
     * Get active brands
     */
    public function getActive(): array
    {
        return $this->select('brands.*')
                    ->join('products', 'products.brand_id = brands.id AND products.status = "active"', 'inner')
                    ->where('brands.status', 'active')
                    ->groupBy('brands.id')
                    ->orderBy('brands.name')
                    ->findAll();
    }

    /**
     * Get brand by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get featured brands
     */
    public function getFeatured(int $limit = 12): array
    {
        return $this->where('status', 'active')
                    ->where('is_featured', 1)
                    ->orderBy('sort_order')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Count products by brand
     */
    public function countProducts(int $brandId): int
    {
        $db = \Config\Database::connect();

        return $db->table('products')
                  ->where('brand_id', $brandId)
                  ->where('status', 'active')
                  ->countAllResults();
    }

    /**
     * Get brands with product count
     */
    public function getWithProductCount(): array
    {
        return $this->select('brands.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.brand_id = brands.id AND products.status = "active"', 'left')
                    ->where('brands.status', 'active')
                    ->groupBy('brands.id')
                    ->orderBy('brands.name')
                    ->findAll();
    }

    /**
     * Get dropdown options
     */
    public function getDropdownOptions(): array
    {
        $brands = $this->where('status', 'active')
                       ->orderBy('name')
                       ->findAll();

        $options = [];
        foreach ($brands as $brand) {
            $options[$brand['id']] = $brand['name'];
        }

        return $options;
    }

    /**
     * Get brands by category (only brands that have products in the category)
     */
    public function getByCategory(int $categoryId): array
    {
        // Get all child category IDs
        $categoryModel = model('CategoryModel');
        $categoryIds = $categoryModel->getAllChildIds($categoryId);

        return $this->select('brands.*')
                    ->join('products', 'products.brand_id = brands.id AND products.status = "active"', 'inner')
                    ->where('brands.status', 'active')
                    ->whereIn('products.category_id', $categoryIds)
                    ->groupBy('brands.id')
                    ->orderBy('brands.name')
                    ->findAll();
    }
}
