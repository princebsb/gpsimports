<?php
header('Content-Type: text/plain; charset=utf-8');

$token = $_GET['token'] ?? '';
if ($token !== 'GPS2026_MACBOOKS') {
    die('Token invalido');
}

$db = new mysqli('localhost', 'u699148595_gpsimports', 'Gpsimports@2026', 'u699148595_gpsimports', 3306);
$db->set_charset('utf8mb4');

echo "=== CORREÇÃO MACBOOKS PRODUÇÃO ===\n\n";

$correcoes = [
    ['id' => 7758, 'price' => 9209.20, 'cost' => 8222.50],
    ['id' => 7763, 'price' => 11149.60, 'cost' => 9955.00],
    ['id' => 7768, 'price' => 11180.40, 'cost' => 9982.50],
    ['id' => 7799, 'price' => 7977.20, 'cost' => 7122.50],
    ['id' => 7760, 'price' => 9548.00, 'cost' => 8525.00],
    ['id' => 7756, 'price' => 11211.20, 'cost' => 10010.00],
];

$db->begin_transaction();
$updated = 0;

foreach ($correcoes as $c) {
    $stmt = $db->prepare("UPDATE products SET price = ?, cost_price = ? WHERE id = ?");
    $stmt->bind_param('ddi', $c['price'], $c['cost'], $c['id']);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "OK: ID {$c['id']} -> R$ " . number_format($c['price'], 2, ',', '.') . "\n";
        $updated++;
    }
    $stmt->close();
}

$db->commit();
echo "\n>>> {$updated} MacBooks atualizados!\n";

$db->close();
