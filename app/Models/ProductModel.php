<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'category_id',
        'brand_id',
        'sku',
        'codigo_produto',
        'barcode',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'sale_price',
        'cost_price',
        'preco_usd',
        'sale_start',
        'sale_end',
        'weight',
        'width',
        'height',
        'length',
        'stock',
        'stock_min',
        'manage_stock',
        'allow_backorder',
        'has_variations',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'featured_image',
        'images_json',
        'video_url',
        'url_origem',
        'especificacoes',
        'fonte',
        'marca',
        'disponivel',
        'is_featured',
        'is_new',
        'is_bestseller',
        'views',
        'sales_count',
        'rating_average',
        'rating_count',
        'sort_order',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[300]',
        'slug' => 'required|min_length[3]|max_length[300]|is_unique[products.slug,id,{id}]',
        'price' => 'required|decimal',
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
     * Get product by slug with relations
     */
    public function getBySlug(string $slug): ?array
    {
        $product = $this->select('products.*, categories.name as category_name, categories.slug as category_slug, brands.name as brand_name, brands.slug as brand_slug')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->join('brands', 'brands.id = products.brand_id', 'left')
                        ->where('products.slug', $slug)
                        ->where('products.status', 'active')
                        ->first();

        if ($product) {
            $product['images'] = $this->getImages($product['id']);
            $product['variations'] = $this->getVariations($product['id']);
        }

        return $product;
    }

    /**
     * Get product images
     */
    public function getImages(int $productId): array
    {
        $db = \Config\Database::connect();

        return $db->table('product_images')
                  ->where('product_id', $productId)
                  ->orderBy('sort_order')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Get product variations
     */
    public function getVariations(int $productId): array
    {
        $db = \Config\Database::connect();

        return $db->table('product_variations')
                  ->where('product_id', $productId)
                  ->where('status', 'active')
                  ->orderBy('sort_order')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Get active products with filters
     */
    public function getFiltered(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->select('products.*, categories.name as category_name, brands.name as brand_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->join('brands', 'brands.id = products.brand_id', 'left')
                        ->where('products.status', 'active');

        // Category filter
        if (!empty($filters['category_id'])) {
            $categoryModel = model('CategoryModel');
            $categoryIds = $categoryModel->getAllChildIds((int) $filters['category_id']);
            $builder->whereIn('products.category_id', $categoryIds);
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $builder->where('products.brand_id', $filters['brand_id']);
        }

        // Price range
        if (!empty($filters['min_price'])) {
            $builder->where('COALESCE(products.sale_price, products.price) >=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $builder->where('COALESCE(products.sale_price, products.price) <=', $filters['max_price']);
        }

        // Featured
        if (!empty($filters['is_featured'])) {
            $builder->where('products.is_featured', 1);
        }

        // On sale
        if (!empty($filters['on_sale'])) {
            $builder->where('products.sale_price IS NOT NULL');
            $builder->where('products.sale_price <', 'products.price', false);
        }

        // New arrivals
        if (!empty($filters['is_new'])) {
            $builder->where('products.is_new', 1);
        }

        // Search
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('products.name', $filters['search'])
                    ->orLike('products.sku', $filters['search'])
                    ->orLike('products.tags', $filters['search'])
                    ->groupEnd();
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_asc' => $builder->orderBy('COALESCE(products.sale_price, products.price)', 'ASC'),
            'price_desc' => $builder->orderBy('COALESCE(products.sale_price, products.price)', 'DESC'),
            'name_asc' => $builder->orderBy('products.name', 'ASC'),
            'name_desc' => $builder->orderBy('products.name', 'DESC'),
            'bestseller' => $builder->orderBy('products.sales_count', 'DESC'),
            'rating' => $builder->orderBy('products.rating_average', 'DESC'),
            default => $builder->orderBy('products.created_at', 'DESC'),
        };

        return [
            'products' => $builder->paginate($perPage),
            'pager' => $this->pager,
        ];
    }

    /**
     * Get featured products
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
     * Get new products
     */
    public function getNew(int $limit = 8): array
    {
        return $this->where('status', 'active')
                    ->where('is_new', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get bestsellers
     */
    public function getBestsellers(int $limit = 8): array
    {
        return $this->where('status', 'active')
                    ->where('is_bestseller', 1)
                    ->orderBy('sales_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get on sale products
     */
    public function getOnSale(int $limit = 8): array
    {
        return $this->where('status', 'active')
                    ->where('sale_price IS NOT NULL')
                    ->where('sale_price <', 'price', false)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get related products
     */
    public function getRelated(int $productId, int $limit = 4): array
    {
        $product = $this->find($productId);

        if (!$product) {
            return [];
        }

        return $this->where('status', 'active')
                    ->where('id !=', $productId)
                    ->where('category_id', $product['category_id'])
                    ->orderBy('RAND()')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Search products
     */
    public function search(string $term, int $limit = 20): array
    {
        return $this->select('products.id, products.name, products.slug, products.price, products.sale_price, products.featured_image')
                    ->where('status', 'active')
                    ->groupStart()
                    ->like('name', $term)
                    ->orLike('sku', $term)
                    ->orLike('tags', $term)
                    ->groupEnd()
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get current price (considering sale)
     */
    public function getCurrentPrice(array $product): float
    {
        if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']) {
            // Check sale dates
            $now = date('Y-m-d H:i:s');

            if (!empty($product['sale_start']) && $product['sale_start'] > $now) {
                return (float) $product['price'];
            }

            if (!empty($product['sale_end']) && $product['sale_end'] < $now) {
                return (float) $product['price'];
            }

            return (float) $product['sale_price'];
        }

        return (float) $product['price'];
    }

    /**
     * Check stock availability
     */
    public function isInStock(int $productId, ?int $variationId = null, int $quantity = 1): bool
    {
        if ($variationId) {
            $db = \Config\Database::connect();
            $variation = $db->table('product_variations')
                           ->where('id', $variationId)
                           ->get()
                           ->getRowArray();

            if (!$variation) {
                return false;
            }

            return $variation['stock'] >= $quantity;
        }

        $product = $this->find($productId);

        if (!$product || !$product['manage_stock']) {
            return true;
        }

        if ($product['allow_backorder']) {
            return true;
        }

        return $product['stock'] >= $quantity;
    }

    /**
     * Decrement stock
     */
    public function decrementStock(int $productId, int $quantity, ?int $variationId = null): bool
    {
        $db = \Config\Database::connect();

        if ($variationId) {
            $db->table('product_variations')
               ->where('id', $variationId)
               ->set('stock', 'stock - ' . $quantity, false)
               ->update();
        }

        return $this->set('stock', 'stock - ' . $quantity, false)
                    ->where('id', $productId)
                    ->update();
    }

    /**
     * Increment views
     */
    public function incrementViews(int $productId): bool
    {
        return $this->set('views', 'views + 1', false)
                    ->where('id', $productId)
                    ->update();
    }

    /**
     * Increment sales count
     */
    public function incrementSales(int $productId, int $quantity = 1): bool
    {
        return $this->set('sales_count', 'sales_count + ' . $quantity, false)
                    ->where('id', $productId)
                    ->update();
    }

    /**
     * Update rating
     */
    public function updateRating(int $productId): bool
    {
        $db = \Config\Database::connect();

        $stats = $db->table('product_reviews')
                    ->select('AVG(rating) as avg_rating, COUNT(*) as count')
                    ->where('product_id', $productId)
                    ->where('status', 'approved')
                    ->get()
                    ->getRowArray();

        return $this->update($productId, [
            'rating_average' => round($stats['avg_rating'] ?? 0, 2),
            'rating_count' => $stats['count'] ?? 0,
        ]);
    }
}
