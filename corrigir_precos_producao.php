<?php
/**
 * CORREÇÃO DE PREÇOS - PRODUÇÃO
 *
 * Este script corrige os preços errados no banco de produção.
 * Execute via CLI no servidor: php corrigir_precos_producao.php
 *
 * IMPORTANTE: Remova este arquivo após executar!
 */

// Verificar se está rodando via CLI ou com token de segurança
$token = $_GET['token'] ?? ($argv[1] ?? '');
$expectedToken = 'GPS2026_CORRECAO_' . date('Ymd');

if (php_sapi_name() !== 'cli' && $token !== $expectedToken) {
    die('Acesso negado. Use token: ' . $expectedToken);
}

// Credenciais de produção
$host = 'localhost';
$database = 'u699148595_gpsimports';
$username = 'u699148595_gpsimports';
$password = 'Gpsimports@2026';
$port = 3306;

$db = new mysqli($host, $username, $password, $database, $port);

if ($db->connect_error) {
    die('Erro de conexão: ' . $db->connect_error);
}

$db->set_charset('utf8mb4');

echo "=== CORREÇÃO DE PREÇOS - PRODUÇÃO ===\n";
echo "Data: " . date('Y-m-d H:i:s') . "\n\n";

// Array com todas as correções
$correcoes = [
    // Primeira rodada - baseado em USD da fonte
    ['id' => 1837, 'price' => 1716.00, 'cost' => 1320.00, 'desc' => 'HD Seagate'],
    ['id' => 2401, 'price' => 356.79, 'cost' => 274.45, 'desc' => 'Fone QCY T13'],
    ['id' => 2680, 'price' => 1859.00, 'cost' => 1430.00, 'desc' => 'Controle PowerA'],
    ['id' => 5742, 'price' => 877.80, 'cost' => 731.50, 'desc' => 'Moto G15'],
    ['id' => 6265, 'price' => 3181.75, 'cost' => 2447.50, 'desc' => 'Garmin Forerunner'],
    ['id' => 6321, 'price' => 6935.50, 'cost' => 5335.00, 'desc' => 'Garmin Fenix 8'],
    ['id' => 6329, 'price' => 965.25, 'cost' => 742.50, 'desc' => 'Xiaomi Watch S5'],
    ['id' => 6382, 'price' => 1115.40, 'cost' => 929.50, 'desc' => 'Carregador Motorola'],
    ['id' => 6918, 'price' => 89.38, 'cost' => 68.75, 'desc' => 'Secador Prosper'],
    ['id' => 6924, 'price' => 106.54, 'cost' => 81.95, 'desc' => 'Secador Aiwa'],
    ['id' => 6932, 'price' => 89.38, 'cost' => 68.75, 'desc' => 'Secador Prosper'],
    ['id' => 6946, 'price' => 314.60, 'cost' => 242.00, 'desc' => 'Secador Taiff Black'],
    ['id' => 6949, 'price' => 39.33, 'cost' => 30.25, 'desc' => 'Secador Krab'],
    ['id' => 6950, 'price' => 765.05, 'cost' => 588.50, 'desc' => 'Secador Taiff Unique'],
    ['id' => 6975, 'price' => 164.45, 'cost' => 126.50, 'desc' => 'Panela MOX'],
    ['id' => 7062, 'price' => 464.75, 'cost' => 357.50, 'desc' => 'Garrafa Stanley'],
    ['id' => 7590, 'price' => 2369.40, 'cost' => 1974.50, 'desc' => 'Blackview BL9000'],
    ['id' => 7597, 'price' => 1312.03, 'cost' => 1009.25, 'desc' => 'Roteador TP-Link'],
    ['id' => 7615, 'price' => 1082.40, 'cost' => 902.00, 'desc' => 'Redmi Note 14'],
    ['id' => 7616, 'price' => 1221.00, 'cost' => 1017.50, 'desc' => 'Redmi Note 14 5G'],
    ['id' => 7617, 'price' => 1221.00, 'cost' => 1017.50, 'desc' => 'Redmi Note 14 5G'],
    ['id' => 7626, 'price' => 772.20, 'cost' => 643.50, 'desc' => 'Moto G35 5G'],
    ['id' => 7630, 'price' => 693.00, 'cost' => 577.50, 'desc' => 'Moto G05'],
    ['id' => 7640, 'price' => 679.80, 'cost' => 566.50, 'desc' => 'Redmi A5'],
    ['id' => 7642, 'price' => 508.20, 'cost' => 423.50, 'desc' => 'Moto E15'],
    ['id' => 7645, 'price' => 547.80, 'cost' => 456.50, 'desc' => 'POCO C71'],
    ['id' => 7647, 'price' => 943.80, 'cost' => 786.50, 'desc' => 'Moto G15'],
    ['id' => 7648, 'price' => 1518.00, 'cost' => 1265.00, 'desc' => 'realme 14T'],
    ['id' => 7652, 'price' => 2508.00, 'cost' => 2090.00, 'desc' => 'Galaxy A56'],
    ['id' => 7653, 'price' => 2508.00, 'cost' => 2090.00, 'desc' => 'Galaxy A56'],
    ['id' => 7660, 'price' => 534.60, 'cost' => 445.50, 'desc' => 'Moto E15'],
    ['id' => 7664, 'price' => 8243.40, 'cost' => 6869.50, 'desc' => 'ASUS VivoBook'],
    ['id' => 7737, 'price' => 13120.80, 'cost' => 10934.00, 'desc' => 'MacBook Pro 2025'],
    ['id' => 7747, 'price' => 4481.40, 'cost' => 3734.50, 'desc' => 'HP Notebook'],
    ['id' => 7750, 'price' => 4270.20, 'cost' => 3558.50, 'desc' => 'HP OmniBook'],
    ['id' => 7755, 'price' => 10263.00, 'cost' => 8552.50, 'desc' => 'MacBook Air 2026'],
    ['id' => 7769, 'price' => 15312.00, 'cost' => 12760.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7771, 'price' => 23034.00, 'cost' => 19195.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7772, 'price' => 19206.00, 'cost' => 16005.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7773, 'price' => 23034.00, 'cost' => 19195.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7774, 'price' => 16170.00, 'cost' => 13475.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7775, 'price' => 16170.00, 'cost' => 13475.00, 'desc' => 'MacBook Pro 2026'],
    ['id' => 7835, 'price' => 114.40, 'cost' => 88.00, 'desc' => 'JBL TUNE500'],
    ['id' => 7836, 'price' => 114.40, 'cost' => 88.00, 'desc' => 'JBL TUNE500'],

    // Segunda rodada - produtos adicionais
    ['id' => 7622, 'price' => 924.00, 'cost' => 770.00, 'desc' => 'realme C75'],
    ['id' => 7628, 'price' => 1219.36, 'cost' => 1016.13, 'desc' => 'Redmi Note 14S'],
    ['id' => 7629, 'price' => 1219.36, 'cost' => 1016.13, 'desc' => 'Redmi Note 14S'],
    ['id' => 7633, 'price' => 1518.00, 'cost' => 1265.00, 'desc' => 'Redmi Note 14 Pro'],
    ['id' => 7623, 'price' => 877.80, 'cost' => 731.50, 'desc' => 'Moto G15'],
    ['id' => 7624, 'price' => 877.80, 'cost' => 731.50, 'desc' => 'Moto G15'],
    ['id' => 7635, 'price' => 877.80, 'cost' => 731.50, 'desc' => 'Moto G15'],
    ['id' => 7636, 'price' => 943.80, 'cost' => 786.50, 'desc' => 'Moto G15 8GB'],
    ['id' => 7637, 'price' => 1056.00, 'cost' => 880.00, 'desc' => 'Moto G15 256GB'],
    ['id' => 7638, 'price' => 1056.00, 'cost' => 880.00, 'desc' => 'Moto G15 256GB'],
    ['id' => 7639, 'price' => 943.80, 'cost' => 786.50, 'desc' => 'Moto G15 8GB'],
    ['id' => 7646, 'price' => 877.80, 'cost' => 731.50, 'desc' => 'Moto G15'],
    ['id' => 7594, 'price' => 679.80, 'cost' => 566.50, 'desc' => 'Redmi 14C'],
    ['id' => 7618, 'price' => 1221.00, 'cost' => 1017.50, 'desc' => 'Redmi Note 14 5G'],
    ['id' => 7634, 'price' => 1219.36, 'cost' => 1016.13, 'desc' => 'Redmi Note 14S'],
    ['id' => 7632, 'price' => 1980.00, 'cost' => 1650.00, 'desc' => 'Redmi Note 14 Pro+'],
    ['id' => 7625, 'price' => 2310.00, 'cost' => 1925.00, 'desc' => 'POCO X7 Pro'],
    ['id' => 7644, 'price' => 2970.00, 'cost' => 2475.00, 'desc' => 'POCO F7 Pro'],
    ['id' => 7651, 'price' => 3960.00, 'cost' => 3300.00, 'desc' => 'Motorola Edge 60'],
    ['id' => 7659, 'price' => 924.00, 'cost' => 770.00, 'desc' => 'realme C75'],
    ['id' => 7724, 'price' => 2310.00, 'cost' => 1925.00, 'desc' => 'Notebook Audisat'],
];

// Iniciar transação
$db->begin_transaction();

$updated = 0;
$errors = 0;

foreach ($correcoes as $c) {
    $stmt = $db->prepare("UPDATE products SET price = ?, cost_price = ? WHERE id = ?");
    $stmt->bind_param('ddi', $c['price'], $c['cost'], $c['id']);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "OK: ID {$c['id']} - {$c['desc']} -> R$ " . number_format($c['price'], 2, ',', '.') . "\n";
            $updated++;
        } else {
            echo "SKIP: ID {$c['id']} - Não encontrado ou já atualizado\n";
        }
    } else {
        echo "ERRO: ID {$c['id']} - {$db->error}\n";
        $errors++;
    }
    $stmt->close();
}

if ($errors > 0) {
    $db->rollback();
    echo "\n>>> ROLLBACK - Erros encontrados!\n";
} else {
    $db->commit();
    echo "\n>>> COMMIT - {$updated} produtos atualizados com sucesso!\n";
}

// Verificar resultado
echo "\n=== VERIFICAÇÃO ===\n";
$result = $db->query("
    SELECT COUNT(*) as total
    FROM products
    WHERE price < 100
    AND (name LIKE '%Celular%' OR name LIKE '%Notebook%' OR name LIKE '%iPhone%')
    AND deleted_at IS NULL
");
$row = $result->fetch_assoc();
echo "Celulares/Notebooks com preço < R$ 100: {$row['total']}\n";

$db->close();

echo "\n=== FIM ===\n";
echo "IMPORTANTE: Remova este arquivo após executar!\n";
