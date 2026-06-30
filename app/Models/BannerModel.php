<?php

namespace App\Models;

use CodeIgniter\Model;

class BannerModel extends Model
{
    protected $table = 'banners';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title',
        'subtitle',
        'image',
        'image_mobile',
        'link',
        'button_text',
        'position',
        'text_color',
        'text_position',
        'sort_order',
        'starts_at',
        'expires_at',
        'status',
        'clicks',
        'views',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get active banners by position
     */
    public function getByPosition(string $position, int $limit = 10): array
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('position', $position)
                    ->where('status', 'active')
                    ->groupStart()
                    ->where('starts_at IS NULL')
                    ->orWhere('starts_at <=', $now)
                    ->groupEnd()
                    ->groupStart()
                    ->where('expires_at IS NULL')
                    ->orWhere('expires_at >=', $now)
                    ->groupEnd()
                    ->orderBy('sort_order')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get home slider banners
     */
    public function getHomeSlider(): array
    {
        return $this->getByPosition('home_slider');
    }

    /**
     * Increment views
     */
    public function incrementViews(int $bannerId): bool
    {
        return $this->set('views', 'views + 1', false)
                    ->where('id', $bannerId)
                    ->update();
    }

    /**
     * Increment clicks
     */
    public function incrementClicks(int $bannerId): bool
    {
        return $this->set('clicks', 'clicks + 1', false)
                    ->where('id', $bannerId)
                    ->update();
    }

    /**
     * Get banner positions
     */
    public function getPositions(): array
    {
        return [
            'home_slider' => 'Slider Principal',
            'home_banner_1' => 'Banner Home 1',
            'home_banner_2' => 'Banner Home 2',
            'category_top' => 'Topo Categoria',
            'product_sidebar' => 'Sidebar Produto',
            'cart_top' => 'Topo Carrinho',
            'checkout_top' => 'Topo Checkout',
            'popup' => 'Popup',
        ];
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(array $bannerIds): bool
    {
        foreach ($bannerIds as $order => $id) {
            $this->update($id, ['sort_order' => $order]);
        }

        return true;
    }
}
