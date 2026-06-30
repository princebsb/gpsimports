<?php

namespace App\Services;

use Config\Database;

class CommissionService
{
    protected $db;
    protected $cache = [];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Determina o tipo de produto baseado no nome e categoria
     */
    public function detectProductType(string $productName, ?string $categoryName = null): string
    {
        $name = mb_strtolower($productName);
        $category = mb_strtolower($categoryName ?? '');

        // Apple Products - ordem importa (mais especifico primeiro)
        if (strpos($name, 'iphone 17') !== false || strpos($name, 'iphone17') !== false) {
            return 'apple_iphone17';
        }
        if (strpos($name, 'iphone 16') !== false || strpos($name, 'iphone16') !== false) {
            return 'apple_iphone16';
        }
        if (strpos($name, 'macbook') !== false || strpos($name, 'mac book') !== false) {
            return 'apple_macbook';
        }
        if (strpos($name, 'ipad') !== false) {
            return 'apple_ipad';
        }
        if (strpos($name, 'airpod') !== false) {
            return 'apple_airpods';
        }
        // iPhone antigo, CPO, SWAP
        if (preg_match('/iphone\s*(15|14|13|12|11|se|xr|xs|x|8|7|6)/i', $name) ||
            strpos($name, 'cpo') !== false ||
            strpos($name, 'swap') !== false) {
            return 'apple_iphone_antigo';
        }

        // Celulares (Samsung, Motorola, Xiaomi, etc) - Exclui eletrodomesticos
        $isElectrodomestico = preg_match('/(fritadeira|air\s*fryer|cafeteira|cafe|coffee|chaleira|kettle|liquidificador|blender|panela|fogao|cooker|jarra|dispenser|aspirador|vacuum|purificador|humidifier|balanca|escova\s*de\s*dente)/i', $name);

        if (!$isElectrodomestico && (
            strpos($category, 'celular') !== false ||
            strpos($category, 'smartphone') !== false ||
            strpos($category, 'telefon') !== false ||
            preg_match('/(galaxy\s*s|galaxy\s*a|galaxy\s*z|moto\s*g|moto\s*e|redmi\s*note|redmi\s*\d|poco\s*[a-z]|realme\s*\d|oppo\s*[a-z])/i', $name))) {
            return 'celulares';
        }

        // Perfumes
        if (strpos($category, 'perfum') !== false ||
            strpos($category, 'fragranc') !== false ||
            strpos($name, 'perfum') !== false ||
            strpos($name, 'cologne') !== false ||
            strpos($name, 'eau de') !== false ||
            strpos($name, 'toilette') !== false ||
            strpos($name, 'parfum') !== false) {
            return 'perfumes';
        }

        // Cosmeticos
        if (strpos($category, 'cosmetic') !== false ||
            strpos($category, 'maquiagem') !== false ||
            strpos($category, 'beleza') !== false ||
            strpos($category, 'skin') !== false ||
            preg_match('/(shampoo|condicion|creme|lotion|mascara|batom|base|foundation|blush|lipstick|serum|hidratante)/i', $name)) {
            return 'cosmeticos';
        }

        // Eletronicos (padrao para outros produtos eletronicos)
        if (strpos($category, 'eletronic') !== false ||
            strpos($category, 'eletronico') !== false ||
            strpos($category, 'audio') !== false ||
            strpos($category, 'video') !== false ||
            strpos($category, 'game') !== false ||
            strpos($category, 'comput') !== false ||
            preg_match('/(playstation|ps5|ps4|xbox|nintendo|switch|monitor|tv|television|speaker|headphone|fone|tablet|notebook|laptop|drone|camera|gopro)/i', $name)) {
            return 'eletronicos';
        }

        return 'outros';
    }

    /**
     * Busca a comissao para um tipo de produto e preco
     */
    public function getCommission(string $productType, float $priceUsd): ?array
    {
        $cacheKey = "{$productType}_{$priceUsd}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $commission = $this->db->table('commissions')
            ->where('product_type', $productType)
            ->where('price_min_usd <=', $priceUsd)
            ->groupStart()
                ->where('price_max_usd >=', $priceUsd)
                ->orWhere('price_max_usd IS NULL')
            ->groupEnd()
            ->where('active', 1)
            ->get()
            ->getRowArray();

        // Se nao encontrou, tenta o tipo "outros"
        if (!$commission && $productType !== 'outros') {
            $commission = $this->getCommission('outros', $priceUsd);
        }

        $this->cache[$cacheKey] = $commission;
        return $commission;
    }

    /**
     * Calcula o preco final com comissao e taxa da plataforma
     *
     * @param float $costUsd Preco de custo em USD
     * @param string $productType Tipo do produto
     * @param float|null $exchangeRate Taxa de cambio USD -> BRL (null = busca automatica)
     * @return array ['final_price_brl', 'commission_rate', 'platform_fee', 'is_blocked', 'breakdown']
     */
    public function calculateFinalPrice(float $costUsd, string $productType, ?float $exchangeRate = null): array
    {
        // Taxa de cambio padrao (pode ser buscada de API ou configuracao)
        if ($exchangeRate === null) {
            $settingRow = $this->db->table('settings')->where('key', 'usd_exchange_rate')->get()->getRowArray();
            $exchangeRate = (float) ($settingRow['value'] ?? 5.50);
        }

        $commission = $this->getCommission($productType, $costUsd);

        if (!$commission) {
            // Fallback: 20% comissao + 15% plataforma
            $commission = [
                'commission_rate' => 20.00,
                'platform_fee' => 15.00,
                'is_blocked' => 0,
            ];
        }

        $commissionRate = (float) $commission['commission_rate'];
        $platformFee = (float) $commission['platform_fee'];
        $isBlocked = (int) ($commission['is_blocked'] ?? 0);

        // Calculo:
        // Preco final = Custo em BRL × (1 + comissao%) × (1 + plataforma%)
        $costBrl = $costUsd * $exchangeRate;

        // Aplicar comissao e taxa da plataforma
        $commissionMultiplier = 1 + ($commissionRate / 100);
        $platformMultiplier = 1 + ($platformFee / 100);
        $totalMargin = ($commissionRate + $platformFee);

        $finalPriceBrl = $costBrl * $commissionMultiplier * $platformMultiplier;

        // Arredondar para cima em .90 ou .99
        $finalPriceBrl = $this->roundPrice($finalPriceBrl);

        return [
            'cost_usd' => $costUsd,
            'cost_brl' => round($costBrl, 2),
            'exchange_rate' => $exchangeRate,
            'product_type' => $productType,
            'commission_rate' => $commissionRate,
            'platform_fee' => $platformFee,
            'total_margin' => round($totalMargin * 100, 2),
            'final_price_brl' => $finalPriceBrl,
            'is_blocked' => $isBlocked,
            'profit_brl' => round($finalPriceBrl - $costBrl, 2),
        ];
    }

    /**
     * Arredonda preco para .90 ou .99
     */
    protected function roundPrice(float $price): float
    {
        $intPart = floor($price);
        $decPart = $price - $intPart;

        if ($decPart <= 0.45) {
            return $intPart + 0.90;
        } elseif ($decPart <= 0.95) {
            return $intPart + 0.99;
        } else {
            return $intPart + 1.90;
        }
    }

    /**
     * Atualiza o preco de um produto baseado no custo e comissoes
     */
    public function updateProductPrice(int $productId): bool
    {
        $product = $this->db->table('products')
            ->select('id, name, price, cost_price, category_id')
            ->where('id', $productId)
            ->get()
            ->getRowArray();

        if (!$product) {
            return false;
        }

        // Busca categoria
        $category = null;
        if ($product['category_id']) {
            $cat = $this->db->table('categories')
                ->select('name')
                ->where('id', $product['category_id'])
                ->get()
                ->getRowArray();
            $category = $cat['name'] ?? null;
        }

        // Preco de custo em USD (assumindo que cost_price esta em USD)
        $costUsd = (float) ($product['cost_price'] ?? $product['price']);

        // Se o preco de custo for 0, nao atualiza
        if ($costUsd <= 0) {
            return false;
        }

        // Detecta tipo do produto
        $productType = $this->detectProductType($product['name'], $category);

        // Calcula preco final
        $calculation = $this->calculateFinalPrice($costUsd, $productType);

        // Atualiza o produto
        $this->db->table('products')
            ->where('id', $productId)
            ->update([
                'price' => $calculation['final_price_brl'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return true;
    }

    /**
     * Retorna todas as comissoes ativas
     */
    public function getAllCommissions(): array
    {
        return $this->db->table('commissions')
            ->where('active', 1)
            ->orderBy('product_type')
            ->orderBy('price_min_usd')
            ->get()
            ->getResultArray();
    }
}
