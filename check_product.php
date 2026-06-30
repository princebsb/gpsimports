<?php
require_once 'vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

// Buscar produto pelo SKU
$product = $db->table('products')
    ->select('id, sku, name, price, cost_price, category_id')
    ->where('sku', '1268508')
    ->get()
    ->getRowArray();

if (!$product) {
    echo "Produto nao encontrado!\n";
    exit;
}

echo "=== PRODUTO ===\n";
echo "ID: {$product['id']}\n";
echo "SKU: {$product['sku']}\n";
echo "Nome: {$product['name']}\n";
echo "Preco atual: R$ " . number_format($product['price'], 2, ',', '.') . "\n";
echo "Custo (USD): $" . number_format($product['cost_price'], 2) . "\n";
echo "\n";

// Calcular preco correto
$service = new \App\Services\CommissionService();

// Buscar categoria
$category = null;
if ($product['category_id']) {
    $cat = $db->table('categories')
        ->select('name')
        ->where('id', $product['category_id'])
        ->get()
        ->getRowArray();
    $category = $cat['name'] ?? null;
}

echo "Categoria: {$category}\n";

$productType = $service->detectProductType($product['name'], $category);
echo "Tipo detectado: {$productType}\n\n";

$calc = $service->calculateFinalPrice((float)$product['cost_price'], $productType);

echo "=== CALCULO ===\n";
echo "Custo USD: $" . number_format($calc['cost_usd'], 2) . "\n";
echo "Taxa cambio: " . $calc['exchange_rate'] . "\n";
echo "Custo BRL: R$ " . number_format($calc['cost_brl'], 2, ',', '.') . "\n";
echo "Comissao: " . $calc['commission_rate'] . "%\n";
echo "Taxa plataforma: " . $calc['platform_fee'] . "%\n";
echo "Margem total: " . $calc['total_margin'] . "%\n";
echo "Preco final calculado: R$ " . number_format($calc['final_price_brl'], 2, ',', '.') . "\n";
echo "Lucro: R$ " . number_format($calc['profit_brl'], 2, ',', '.') . "\n";
