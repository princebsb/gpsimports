<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'banner',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
        'is_featured',
        'is_menu',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[200]',
        'slug' => 'required|min_length[2]|max_length[200]|is_unique[categories.slug,id,{id}]',
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
     * Get active categories
     */
    public function getActive(): array
    {
        return $this->select('categories.*')
                    ->join('products', 'products.category_id = categories.id AND products.status = "active"', 'inner')
                    ->where('categories.status', 'active')
                    ->groupBy('categories.id')
                    ->orderBy('categories.name')
                    ->findAll();
    }

    /**
     * Get categories for menu
     */
    public function getMenuCategories(): array
    {
        return $this->where('status', 'active')
                    ->where('is_menu', 1)
                    ->whereIn('parent_id', [null, 0])
                    ->orderBy('sort_order')
                    ->findAll();
    }

    /**
     * Get category tree
     */
    public function getTree(): array
    {
        $categories = $this->where('status', 'active')
                           ->orderBy('sort_order')
                           ->orderBy('name')
                           ->findAll();

        return $this->buildTree($categories);
    }

    /**
     * Build hierarchical tree
     */
    protected function buildTree(array $categories, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }

        return $tree;
    }

    /**
     * Get subcategories
     */
    public function getSubcategories(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
                    ->where('status', 'active')
                    ->orderBy('sort_order')
                    ->findAll();
    }

    /**
     * Get category by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get breadcrumb trail
     */
    public function getBreadcrumb(int $categoryId): array
    {
        $breadcrumb = [];
        $category = $this->find($categoryId);

        while ($category) {
            array_unshift($breadcrumb, $category);

            if ($category['parent_id']) {
                $category = $this->find($category['parent_id']);
            } else {
                break;
            }
        }

        return $breadcrumb;
    }

    /**
     * Get all category IDs including children
     */
    public function getAllChildIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = $this->where('parent_id', $categoryId)->findAll();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getAllChildIds($child['id']));
        }

        return $ids;
    }

    /**
     * Get featured categories
     */
    public function getFeatured(int $limit = 8): array
    {
        return $this->where('status', 'active')
                    ->where('is_featured', 1)
                    ->orderBy('sort_order')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Count products in category
     */
    public function countProducts(int $categoryId): int
    {
        $db = \Config\Database::connect();

        return $db->table('products')
                  ->where('category_id', $categoryId)
                  ->where('status', 'active')
                  ->countAllResults();
    }

    /**
     * Get categories as dropdown options
     */
    public function getDropdownOptions(?int $excludeId = null): array
    {
        $categories = $this->orderBy('name')->findAll();
        $options = [];

        foreach ($categories as $category) {
            if ($excludeId && $category['id'] == $excludeId) {
                continue;
            }

            $prefix = $category['parent_id'] ? '-- ' : '';
            $options[$category['id']] = $prefix . $category['name'];
        }

        return $options;
    }
}
