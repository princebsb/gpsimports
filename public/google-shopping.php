<?php
/**
 * Google Shopping Feed - GPS Imports
 * URL: https://gpsimports.com.br/google-shopping.php
 */

// Definir o path base
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Carregar o autoload do Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Carregar o bootstrap do CodeIgniter
$paths = require dirname(__DIR__) . '/app/Config/Paths.php';

// Boot do sistema
require_once $paths->systemDirectory . '/Boot.php';

// Carregar configuracoes do banco
$db = \Config\Database::connect();

// Configuracoes da loja
$storeUrl = rtrim(base_url(), '/');
$storeName = 'GPS Imports';

// Buscar produtos ativos com estoque
$builder = $db->table('products p');
$builder->select('
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
');
$builder->join('categories c', 'p.category_id = c.id', 'left');
$builder->join('brands b', 'p.brand_id = b.id', 'left');
$builder->where('p.status', 'active');
$builder->where('p.price >', 0);
$builder->groupStart();
$builder->where('p.stock >', 0);
$builder->orWhere('p.manage_stock', 0);
$builder->groupEnd();
$builder->orderBy('p.id', 'DESC');

$products = $builder->get()->getResultArray();

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
