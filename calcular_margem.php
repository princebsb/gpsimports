<?php
/**
 * Script para calcular margem de lucro dos produtos
 *
 * Calculo:
 * - Venda
 * - Custo do produto
 * - Comissao do importador (20% sobre o CUSTO)
 * - Mercado Pago (5% sobre a VENDA)
 * - Nota fiscal: 3% sobre a VENDA se >= R$ 2.000, senao R$ 30 fixo
 * - Frete (ignorado - varia por produto)
 *
 * Total de custos = Custo + Comissao + MP + NF
 * Lucro = Venda - Total de custos
 */

// Configuracoes do banco
$dbHost = 'localhost';
$dbName = 'gpsimports';
$dbUser = 'root';
$dbPass = '';
$dbPort = 3800;

// Taxas
$TAXA_COMISSAO_IMPORTADOR = 20.00; // % sobre o CUSTO
$TAXA_MERCADO_PAGO = 5.00;         // % sobre a VENDA
$TAXA_NOTA_FISCAL = 3.00;          // % sobre a VENDA
$COTACAO_DOLAR = 5.29;             // Cotação do dólar em reais

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Conectado ao banco de dados.\n";

    // Buscar todos os produtos ativos com custo definido
    $stmt = $pdo->query("
        SELECT p.id, p.sku, p.name, p.price, p.sale_price, p.cost_price, p.category_id, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active'
        AND p.cost_price > 0
        ORDER BY p.id ASC
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Encontrados " . count($products) . " produtos com custo definido.\n\n";

    // Preparar CSV
    $csvFile = fopen(__DIR__ . '/margem_lucro.csv', 'w');

    // BOM para Excel reconhecer UTF-8
    fwrite($csvFile, "\xEF\xBB\xBF");

    // Cabecalho
    fputcsv($csvFile, [
        'ID',
        'SKU',
        'Nome',
        'Categoria',
        'Venda (R$)',
        'Custo (USD)',
        'Custo (R$)',
        'Comissao Importador 20% (R$)',
        'Mercado Pago 5% (R$)',
        'Nota Fiscal 3% (R$)',
        'Total Custos (R$)',
        'Lucro (R$)',
        'Margem (%)',
    ], ';');

    $totalProdutos = 0;
    $totalLucro = 0;
    $produtosComPrejuizo = 0;

    foreach ($products as $product) {
        // Preco de venda (usa sale_price se existir, senao price)
        $venda = !empty($product['sale_price']) && $product['sale_price'] > 0
            ? (float) $product['sale_price']
            : (float) $product['price'];

        $custoUSD = (float) $product['cost_price'];
        $custo = $custoUSD * $COTACAO_DOLAR; // Converter USD para BRL

        // Pular se preco de venda for 0
        if ($venda <= 0) {
            continue;
        }

        // Calcular valores
        $comissaoImportador = $custo * ($TAXA_COMISSAO_IMPORTADOR / 100);  // 20% sobre CUSTO
        $mercadoPago = $venda * ($TAXA_MERCADO_PAGO / 100);                 // 5% sobre VENDA

        // Nota Fiscal: 3% se venda > R$ 2.000, senao R$ 30 fixo
        $notaFiscal = ($venda >= 2000) ? ($venda * 0.03) : 30.00;

        $totalCustos = $custo + $comissaoImportador + $mercadoPago + $notaFiscal;

        $lucro = $venda - $totalCustos;
        $margem = ($venda > 0) ? ($lucro / $venda) * 100 : 0;

        // Escrever no CSV
        fputcsv($csvFile, [
            $product['id'],
            $product['sku'],
            $product['name'],
            $product['category_name'] ?? '',
            number_format($venda, 2, ',', '.'),
            number_format($custoUSD, 2, ',', '.'),
            number_format($custo, 2, ',', '.'),
            number_format($comissaoImportador, 2, ',', '.'),
            number_format($mercadoPago, 2, ',', '.'),
            number_format($notaFiscal, 2, ',', '.'),
            number_format($totalCustos, 2, ',', '.'),
            number_format($lucro, 2, ',', '.'),
            number_format($margem, 2, ',', '.'),
        ], ';');

        $totalProdutos++;
        $totalLucro += $lucro;

        if ($lucro < 0) {
            $produtosComPrejuizo++;
        }
    }

    fclose($csvFile);

    echo "===========================================\n";
    echo "RESUMO\n";
    echo "===========================================\n";
    echo "Total de produtos analisados: $totalProdutos\n";
    echo "Lucro total estimado: R$ " . number_format($totalLucro, 2, ',', '.') . "\n";
    echo "Produtos com prejuizo: $produtosComPrejuizo\n";
    echo "===========================================\n";
    echo "\nArquivo salvo em: " . __DIR__ . "/margem_lucro.csv\n";

} catch (PDOException $e) {
    echo "Erro de conexao: " . $e->getMessage() . "\n";
    exit(1);
}
