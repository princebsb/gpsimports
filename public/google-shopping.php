<?php
/**
 * Google Shopping Feed - GPS Imports
 * URL: https://gpsimports.com.br/google-shopping.php
 */

// Carregar configuracoes do CodeIgniter
require_once __DIR__ . '/../vendor/autoload.php';

// Configuracao do banco de dados
$dbHost = 'localhost';
$dbName = 'u699148595_gpsimports';
$dbUser = 'u699148595_gpsimports';
$dbPass = ''; // Preencher com a senha

// Tentar carregar do .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    $dbHost = $env['database.default.hostname'] ?? $dbHost;
    $dbName = $env['database.default.database'] ?? $dbName;
    $dbUser = $env['database.default.username'] ?? $dbUser;
    $dbPass = $env['database.default.password'] ?? $dbPass;
}

// Configuracoes da loja
$storeUrl = 'https://gpsimports.com.br';
$storeName = 'GPS Imports';

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    die('Erro de conexao com o banco de dados');
}

// Buscar produtos ativos com estoque
$sql = "
    SELECT
        p.id,
        p.sku,
        p.name,
        p.slug,
        p.short_description,
        p.description,
        p.price,
        p.sale_price,
        p.stock,
        p.weight,
        p.featured_image,
        p.gtin,
        p.mpn,
        c.name as category_name,
        b.name as brand_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.status = 'active'
    AND p.price > 0
    AND (p.stock > 0 OR p.manage_stock = 0)
    ORDER BY p.id DESC
";

$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gerar XML
header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
    <title><?= htmlspecialchars($storeName) ?></title>
    <link><?= $storeUrl ?></link>
    <description>Produtos importados de alta qualidade - <?= htmlspecialchars($storeName) ?></description>
<?php foreach ($products as $product): ?>
<?php
    // Preco atual
    $currentPrice = ($product['sale_price'] && $product['sale_price'] < $product['price'])
        ? $product['sale_price']
        : $product['price'];

    // URL do produto
    $productUrl = $storeUrl . '/produto/' . $product['slug'];

    // Imagem
    $imageUrl = '';
    if (!empty($product['featured_image'])) {
        if (strpos($product['featured_image'], 'http') === 0) {
            $imageUrl = $product['featured_image'];
        } else {
            $imageUrl = $storeUrl . '/uploads/products/' . $product['featured_image'];
        }
    }

    // Descricao limpa
    $description = strip_tags($product['short_description'] ?: $product['description'] ?: $product['name']);
    $description = preg_replace('/\s+/', ' ', $description);
    $description = trim(substr($description, 0, 5000));

    // Disponibilidade
    $availability = ($product['stock'] > 0) ? 'in_stock' : 'out_of_stock';

    // Condicao
    $condition = 'new';
?>
    <item>
        <g:id><?= htmlspecialchars($product['sku'] ?: $product['id']) ?></g:id>
        <g:title><?= htmlspecialchars(substr($product['name'], 0, 150)) ?></g:title>
        <g:description><?= htmlspecialchars($description) ?></g:description>
        <g:link><?= htmlspecialchars($productUrl) ?></g:link>
<?php if ($imageUrl): ?>
        <g:image_link><?= htmlspecialchars($imageUrl) ?></g:image_link>
<?php endif; ?>
        <g:availability><?= $availability ?></g:availability>
        <g:price><?= number_format($product['price'], 2, '.', '') ?> BRL</g:price>
<?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
        <g:sale_price><?= number_format($product['sale_price'], 2, '.', '') ?> BRL</g:sale_price>
<?php endif; ?>
<?php if (!empty($product['brand_name'])): ?>
        <g:brand><?= htmlspecialchars($product['brand_name']) ?></g:brand>
<?php else: ?>
        <g:brand><?= htmlspecialchars($storeName) ?></g:brand>
<?php endif; ?>
<?php if (!empty($product['gtin'])): ?>
        <g:gtin><?= htmlspecialchars($product['gtin']) ?></g:gtin>
<?php endif; ?>
<?php if (!empty($product['mpn'])): ?>
        <g:mpn><?= htmlspecialchars($product['mpn']) ?></g:mpn>
<?php endif; ?>
<?php if (empty($product['gtin']) && empty($product['mpn'])): ?>
        <g:identifier_exists>false</g:identifier_exists>
<?php endif; ?>
        <g:condition><?= $condition ?></g:condition>
<?php if (!empty($product['category_name'])): ?>
        <g:product_type><?= htmlspecialchars($product['category_name']) ?></g:product_type>
<?php endif; ?>
        <g:google_product_category>Electronics</g:google_product_category>
<?php if ($product['weight'] > 0): ?>
        <g:shipping_weight><?= number_format($product['weight'], 2, '.', '') ?> kg</g:shipping_weight>
<?php endif; ?>
    </item>
<?php endforeach; ?>
</channel>
</rss>
