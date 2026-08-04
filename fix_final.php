<?php
header('Content-Type: text/plain; charset=utf-8');

$token = $_GET['token'] ?? '';
if ($token !== 'GPS2026_FIX_20260804') {
    die('Token invalido');
}

$db = new mysqli('localhost', 'u699148595_gpsimports', 'Gpsimports@2026', 'u699148595_gpsimports', 3306);
$db->set_charset('utf8mb4');

if ($db->connect_error) {
    die('Erro: ' . $db->connect_error);
}

echo "=== CORREÇÃO FINAL ===\n\n";

$correcoes = [
    ['id' => 6431, 'price' => 1115.40, 'cost' => 929.50, 'desc' => 'Compressor Xiaomi'],
    ['id' => 7463, 'price' => 4785.00, 'cost' => 3987.50, 'desc' => 'Babá Eletrônica Hubble'],
    ['id' => 7850, 'price' => 1852.95, 'cost' => 1544.13, 'desc' => 'Soundbar JBL BAR 500'],
    ['id' => 7862, 'price' => 1998.15, 'cost' => 1665.13, 'desc' => 'Mini System LG XBOOM'],
];

$db->begin_transaction();
$updated = 0;

foreach ($correcoes as $c) {
    $stmt = $db->prepare("UPDATE products SET price = ?, cost_price = ? WHERE id = ?");
    $stmt->bind_param('ddi', $c['price'], $c['cost'], $c['id']);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "OK: ID {$c['id']} - {$c['desc']} -> R$ " . number_format($c['price'], 2, ',', '.') . "\n";
        $updated++;
    }
    $stmt->close();
}

$db->commit();
echo "\n>>> {$updated} produtos atualizados!\n";

$db->close();
