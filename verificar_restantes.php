<?php
/**
 * Verificar produtos restantes com preço errado - PRODUÇÃO
 */

$token = $_GET['token'] ?? '';
if ($token !== 'GPS2026_VERIFICAR_20260804') {
    die('Token invalido');
}

$db = new mysqli('localhost', 'u699148595_gpsimports', 'Gpsimports@2026', 'u699148595_gpsimports', 3306);
$db->set_charset('utf8mb4');

echo "<pre>";
echo "=== PRODUTOS COM PREÇO < R$ 100 ===\n\n";

$result = $db->query("
    SELECT id, sku, name, price, cost_price, url_origem
    FROM products
    WHERE price > 0 AND price < 100
    AND (name LIKE '%Celular%' OR name LIKE '%Notebook%' OR name LIKE '%iPhone%'
         OR name LIKE '%Galaxy%' OR name LIKE '%MacBook%' OR name LIKE '%iPad%'
         OR name LIKE '%Tablet%' OR name LIKE '%Watch%' OR name LIKE '%Relógio%')
    AND deleted_at IS NULL AND status = 'active'
    ORDER BY price ASC
    LIMIT 50
");

while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']} | R$ " . number_format($row['price'], 2, ',', '.') . " | SKU: {$row['sku']}\n";
    echo "   " . mb_substr($row['name'], 0, 70) . "\n";
    echo "   URL: " . ($row['url_origem'] ?: 'SEM URL') . "\n\n";
}

$db->close();
echo "</pre>";
