<?php
/**
 * Google Shopping Feed - GPS Imports
 */

error_reporting(0);

// Ler configuracoes do .env
$envFile = dirname(__DIR__) . '/.env';
$dbHost = 'localhost';
$dbName = '';
$dbUser = '';
$dbPass = '';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");

        if ($key === 'database.default.hostname') $dbHost = $value;
        if ($key === 'database.default.database') $dbName = $value;
        if ($key === 'database.default.username') $dbUser = $value;
        if ($key === 'database.default.password') $dbPass = $value;
    }
}

// Configuracoes da loja
$storeUrl = 'https://gpsimports.com.br';
$storeName = 'GPS Imports';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    die('Erro de conexao');
}

// Buscar produtos
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

$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Gerar XML
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title><?= $storeName ?></title>
<link><?= $storeUrl ?></link>
<description>Produtos importados - <?= $storeName ?></description>
<?php foreach ($products as $p):
    $price = ($p['sale_price'] && $p['sale_price'] < $p['price']) ? $p['sale_price'] : $p['price'];
    $url = $storeUrl . '/produto/' . $p['slug'];
    $img = $p['featured_image'] ? (strpos($p['featured_image'], 'http') === 0 ? $p['featured_image'] : $storeUrl . '/uploads/products/' . $p['featured_image']) : '';
    $desc = trim(substr(strip_tags($p['short_description'] ?: $p['description'] ?: $p['name']), 0, 5000));
?>
<item>
<g:id><?= $p['sku'] ?: $p['id'] ?></g:id>
<g:title><![CDATA[<?= substr($p['name'], 0, 150) ?>]]></g:title>
<g:description><![CDATA[<?= $desc ?>]]></g:description>
<g:link><?= $url ?></g:link>
<?php if ($img): ?><g:image_link><?= $img ?></g:image_link><?php endif; ?>
<g:availability><?= $p['stock'] > 0 ? 'in_stock' : 'out_of_stock' ?></g:availability>
<g:price><?= number_format($p['price'], 2, '.', '') ?> BRL</g:price>
<?php if ($p['sale_price'] && $p['sale_price'] < $p['price']): ?>
<g:sale_price><?= number_format($p['sale_price'], 2, '.', '') ?> BRL</g:sale_price>
<?php endif; ?>
<g:brand><![CDATA[<?= $p['brand_name'] ?: $storeName ?>]]></g:brand>
<g:identifier_exists>false</g:identifier_exists>
<g:condition>new</g:condition>
<?php if ($p['category_name']): ?><g:product_type><![CDATA[<?= $p['category_name'] ?>]]></g:product_type><?php endif; ?>
<g:google_product_category>Electronics</g:google_product_category>
</item>
<?php endforeach; ?>
</channel>
</rss>
