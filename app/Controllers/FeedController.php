<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FeedController extends BaseController
{
    /**
     * Feed XML para Google Merchant Center
     */
    public function googleMerchant()
    {
        // Cache de 6 horas
        $cacheName = 'google_merchant_feed';
        $cache = \Config\Services::cache();

        $xml = $cache->get($cacheName);

        if ($xml === null) {
            $xml = $this->generateGoogleMerchantFeed();
            $cache->save($cacheName, $xml, 21600); // 6 horas
        }

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=utf-8')
            ->setBody($xml);
    }

    /**
     * Limpar cache do feed
     */
    public function clearCache()
    {
        $cache = \Config\Services::cache();
        $cache->delete('google_merchant_feed');

        return redirect()->back()->with('success', 'Cache do feed limpo com sucesso!');
    }

    /**
     * Gerar XML do feed
     */
    protected function generateGoogleMerchantFeed(): string
    {
        $productModel = model('ProductModel');
        $baseUrl = base_url();
        $storeName = setting('store_name') ?? 'GPS Imports';

        // Buscar produtos ativos com estoque
        $products = $productModel
            ->select('products.*, categories.name as category_name, brands.name as brand_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('brands', 'brands.id = products.brand_id', 'left')
            ->where('products.status', 'active')
            ->where('products.deleted_at', null)
            ->findAll();

        // Iniciar XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '  <title>' . $this->escape($storeName) . '</title>' . "\n";
        $xml .= '  <link>' . $baseUrl . '</link>' . "\n";
        $xml .= '  <description>Produtos ' . $this->escape($storeName) . '</description>' . "\n";

        foreach ($products as $product) {
            // Pular produtos sem imagem
            $imageUrl = $this->getProductImageUrl($product, $baseUrl);
            if (empty($imageUrl)) {
                continue;
            }

            // Preco
            $price = (float) ($product['promotional_price'] > 0 ? $product['promotional_price'] : $product['price']);
            if ($price <= 0) {
                continue;
            }

            // Disponibilidade
            $stock = (int) ($product['stock'] ?? 0);
            $availability = $stock > 0 ? 'in_stock' : 'out_of_stock';

            // Descricao limpa
            $description = $this->cleanDescription($product['description'] ?? $product['name']);

            $xml .= '  <item>' . "\n";
            $xml .= '    <g:id>' . $this->escape($product['id']) . '</g:id>' . "\n";
            $xml .= '    <g:title>' . $this->escape($this->truncate($product['name'], 150)) . '</g:title>' . "\n";
            $xml .= '    <g:description>' . $this->escape($this->truncate($description, 5000)) . '</g:description>' . "\n";
            $xml .= '    <g:link>' . $baseUrl . 'produto/' . $this->escape($product['slug']) . '</g:link>' . "\n";
            $xml .= '    <g:image_link>' . $this->escape($imageUrl) . '</g:image_link>' . "\n";
            $xml .= '    <g:price>' . number_format($price, 2, '.', '') . ' BRL</g:price>' . "\n";
            $xml .= '    <g:availability>' . $availability . '</g:availability>' . "\n";
            $xml .= '    <g:condition>new</g:condition>' . "\n";

            // Marca
            if (!empty($product['brand_name'])) {
                $xml .= '    <g:brand>' . $this->escape($product['brand_name']) . '</g:brand>' . "\n";
            }

            // Categoria Google
            if (!empty($product['category_name'])) {
                $xml .= '    <g:product_type>' . $this->escape($product['category_name']) . '</g:product_type>' . "\n";
            }

            // SKU como MPN
            if (!empty($product['sku'])) {
                $xml .= '    <g:mpn>' . $this->escape($product['sku']) . '</g:mpn>' . "\n";
            }

            // GTIN/EAN se tiver
            if (!empty($product['ean']) || !empty($product['gtin'])) {
                $gtin = $product['gtin'] ?? $product['ean'];
                $xml .= '    <g:gtin>' . $this->escape($gtin) . '</g:gtin>' . "\n";
            } else {
                $xml .= '    <g:identifier_exists>no</g:identifier_exists>' . "\n";
            }

            // Peso para frete
            if (!empty($product['weight']) && $product['weight'] > 0) {
                $xml .= '    <g:shipping_weight>' . number_format($product['weight'], 2, '.', '') . ' kg</g:shipping_weight>' . "\n";
            }

            $xml .= '  </item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    /**
     * Obter URL da imagem do produto
     */
    protected function getProductImageUrl(array $product, string $baseUrl): string
    {
        // Imagem principal
        if (!empty($product['featured_image'])) {
            $img = $product['featured_image'];

            // Se ja e URL completa
            if (strpos($img, 'http') === 0) {
                return $img;
            }

            // Caminho local
            return $baseUrl . 'uploads/products/' . $img;
        }

        // Tentar buscar da galeria
        if (!empty($product['images'])) {
            $images = is_string($product['images']) ? json_decode($product['images'], true) : $product['images'];
            if (!empty($images) && is_array($images)) {
                $firstImage = $images[0];
                if (strpos($firstImage, 'http') === 0) {
                    return $firstImage;
                }
                return $baseUrl . 'uploads/products/' . $firstImage;
            }
        }

        return '';
    }

    /**
     * Limpar descricao HTML
     */
    protected function cleanDescription(string $text): string
    {
        // Remover tags HTML
        $text = strip_tags($text);
        // Remover quebras de linha extras
        $text = preg_replace('/\s+/', ' ', $text);
        // Remover caracteres especiais problemáticos
        $text = preg_replace('/[^\p{L}\p{N}\s.,!?;:\-()%$R]/u', '', $text);
        return trim($text);
    }

    /**
     * Truncar texto
     */
    protected function truncate(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length - 3) . '...';
    }

    /**
     * Escapar para XML
     */
    protected function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
