<?php
/**
 * Verificar e corrigir preços - PRODUÇÃO
 */

header('Content-Type: text/plain; charset=utf-8');

$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? 'check';

if ($token !== 'GPS2026_CHECK_20260804') {
    die('Token invalido. Use: ?token=GPS2026_CHECK_20260804');
}

$db = new mysqli('localhost', 'u699148595_gpsimports', 'Gpsimports@2026', 'u699148595_gpsimports', 3306);
$db->set_charset('utf8mb4');

if ($db->connect_error) {
    die('Erro de conexão: ' . $db->connect_error);
}

if ($action === 'fix') {
    echo "=== CORRIGINDO PREÇOS RESTANTES ===\n\n";

    // Buscar produtos com preço < R$ 100 que são claramente erros
    $result = $db->query("
        SELECT id, sku, name, price, cost_price
        FROM products
        WHERE price > 0 AND price < 100
        AND (name LIKE '%Celular%' OR name LIKE '%Smartphone%')
        AND name NOT LIKE '%Nokia%'
        AND name NOT LIKE '%Samsung SM-B%'
        AND name NOT LIKE '%LG B220%'
        AND deleted_at IS NULL AND status = 'active'
    ");

    $CAMBIO = 5.50;
    $updates = [];

    while ($row = $result->fetch_assoc()) {
        // Estimar preço baseado no nome
        $nome = strtolower($row['name']);
        $precoEstimado = 0;

        if (strpos($nome, 'poco f7') !== false) {
            $precoEstimado = 2970; // POCO F7 Pro
        } elseif (strpos($nome, 'poco x7') !== false) {
            $precoEstimado = 2310; // POCO X7 Pro
        } elseif (strpos($nome, 'poco c') !== false) {
            $precoEstimado = 548; // POCO C71
        } elseif (strpos($nome, 'redmi note 14 pro+') !== false || strpos($nome, 'redmi note 14 pro plus') !== false) {
            $precoEstimado = 1980;
        } elseif (strpos($nome, 'redmi note 14 pro') !== false) {
            $precoEstimado = 1518;
        } elseif (strpos($nome, 'redmi note 14s') !== false) {
            $precoEstimado = 1219;
        } elseif (strpos($nome, 'redmi note 14 5g') !== false) {
            $precoEstimado = 1221;
        } elseif (strpos($nome, 'redmi note 14') !== false) {
            $precoEstimado = 1082;
        } elseif (strpos($nome, 'redmi 14c') !== false) {
            $precoEstimado = 680;
        } elseif (strpos($nome, 'redmi a5') !== false || strpos($nome, 'redmi a3') !== false) {
            $precoEstimado = 680;
        } elseif (strpos($nome, 'motorola edge') !== false) {
            $precoEstimado = 3960;
        } elseif (strpos($nome, 'moto g35') !== false) {
            $precoEstimado = 772;
        } elseif (strpos($nome, 'moto g15') !== false) {
            $precoEstimado = 878;
        } elseif (strpos($nome, 'moto g05') !== false || strpos($nome, 'moto e15') !== false) {
            $precoEstimado = 534;
        } elseif (strpos($nome, 'realme c75') !== false) {
            $precoEstimado = 924;
        } elseif (strpos($nome, 'realme 14t') !== false) {
            $precoEstimado = 1518;
        } elseif (strpos($nome, 'galaxy a56') !== false) {
            $precoEstimado = 2508;
        } elseif (strpos($nome, 'galaxy a55') !== false) {
            $precoEstimado = 2200;
        } elseif (strpos($nome, 'galaxy a35') !== false) {
            $precoEstimado = 1650;
        } elseif (strpos($nome, 'galaxy a25') !== false) {
            $precoEstimado = 1320;
        } elseif (strpos($nome, 'galaxy a15') !== false) {
            $precoEstimado = 990;
        } else {
            // Preço genérico baseado no custo atual * 100 (assumindo que o custo era o USD)
            $custoOriginal = $row['cost_price'] ?? $row['price'];
            if ($custoOriginal > 0 && $custoOriginal < 20) {
                $precoEstimado = round($custoOriginal * $CAMBIO * 1.20 * 10, 2);
            }
        }

        if ($precoEstimado > 0) {
            $custoEstimado = round($precoEstimado / 1.20, 2);
            $updates[] = [
                'id' => $row['id'],
                'sku' => $row['sku'],
                'nome' => mb_substr($row['name'], 0, 50),
                'preco_atual' => $row['price'],
                'preco_novo' => $precoEstimado,
                'custo_novo' => $custoEstimado,
            ];
        }
    }

    if (count($updates) > 0) {
        $db->begin_transaction();
        $updated = 0;

        foreach ($updates as $u) {
            $stmt = $db->prepare("UPDATE products SET price = ?, cost_price = ? WHERE id = ?");
            $stmt->bind_param('ddi', $u['preco_novo'], $u['custo_novo'], $u['id']);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo "OK: ID {$u['id']} | R$ " . number_format($u['preco_atual'], 2) . " -> R$ " . number_format($u['preco_novo'], 2) . " | {$u['nome']}\n";
                $updated++;
            }
            $stmt->close();
        }

        $db->commit();
        echo "\n>>> {$updated} produtos atualizados!\n";
    } else {
        echo "Nenhum produto para corrigir.\n";
    }

} else {
    echo "=== VERIFICAÇÃO DE PREÇOS - PRODUÇÃO ===\n";
    echo "Data: " . date('Y-m-d H:i:s') . "\n\n";

    // Contar produtos com preço < R$ 100
    $result = $db->query("
        SELECT COUNT(*) as total
        FROM products
        WHERE price > 0 AND price < 100
        AND (name LIKE '%Celular%' OR name LIKE '%Smartphone%')
        AND name NOT LIKE '%Nokia%'
        AND name NOT LIKE '%Samsung SM-B%'
        AND name NOT LIKE '%LG B220%'
        AND deleted_at IS NULL AND status = 'active'
    ");
    $row = $result->fetch_assoc();
    echo "Celulares com preço < R$ 100 (excluindo básicos): {$row['total']}\n\n";

    // Listar
    $result = $db->query("
        SELECT id, sku, name, price
        FROM products
        WHERE price > 0 AND price < 100
        AND (name LIKE '%Celular%' OR name LIKE '%Smartphone%')
        AND name NOT LIKE '%Nokia%'
        AND name NOT LIKE '%Samsung SM-B%'
        AND name NOT LIKE '%LG B220%'
        AND deleted_at IS NULL AND status = 'active'
        ORDER BY price ASC
        LIMIT 50
    ");

    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | R$ " . number_format($row['price'], 2, ',', '.') . " | {$row['name']}\n";
    }

    echo "\n\nPara corrigir, use: ?token=GPS2026_CHECK_20260804&action=fix\n";
}

$db->close();
