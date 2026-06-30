<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('products')->truncate();
        $this->db->table('product_categories')->truncate();

        $products = $this->getProducts();

        foreach ($products as $product) {
            $categories = $product['categories'] ?? [];
            unset($product['categories']);

            $product['created_at'] = date('Y-m-d H:i:s');
            $product['updated_at'] = date('Y-m-d H:i:s');

            $this->db->table('products')->insert($product);
            $productId = $this->db->insertID();

            foreach ($categories as $catId) {
                $this->db->table('product_categories')->insert([
                    'product_id' => $productId,
                    'category_id' => $catId,
                ]);
            }
        }

        echo "Products seeded successfully (" . count($products) . " products).\n";
    }

    private function getProducts(): array
    {
        return [
            // Smartphones
            ['name' => 'iPhone 15 Pro Max 256GB', 'slug' => 'iphone-15-pro-max-256gb', 'sku' => 'APL-IP15PM-256', 'brand_id' => 1, 'price' => 9999.00, 'sale_price' => 8999.00, 'stock' => 25, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'O iPhone mais poderoso. Chip A17 Pro, camera de 48MP e tela Super Retina XDR.', 'categories' => [1, 10]],
            ['name' => 'iPhone 15 128GB', 'slug' => 'iphone-15-128gb', 'sku' => 'APL-IP15-128', 'brand_id' => 1, 'price' => 6499.00, 'sale_price' => null, 'stock' => 40, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'iPhone 15 com Dynamic Island e camera de 48MP.', 'categories' => [1, 10]],
            ['name' => 'Samsung Galaxy S24 Ultra 512GB', 'slug' => 'samsung-galaxy-s24-ultra-512gb', 'sku' => 'SAM-S24U-512', 'brand_id' => 2, 'price' => 8999.00, 'sale_price' => 7999.00, 'stock' => 30, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Galaxy AI integrado, S Pen e camera de 200MP.', 'categories' => [1, 10]],
            ['name' => 'Samsung Galaxy S24 256GB', 'slug' => 'samsung-galaxy-s24-256gb', 'sku' => 'SAM-S24-256', 'brand_id' => 2, 'price' => 5499.00, 'sale_price' => null, 'stock' => 50, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Galaxy S24 com Galaxy AI e tela Dynamic AMOLED 2X.', 'categories' => [1, 10]],
            ['name' => 'Xiaomi 14 Ultra 512GB', 'slug' => 'xiaomi-14-ultra-512gb', 'sku' => 'XIA-14U-512', 'brand_id' => 3, 'price' => 6999.00, 'sale_price' => 5999.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Camera Leica quad de 50MP e Snapdragon 8 Gen 3.', 'categories' => [1, 10]],
            ['name' => 'Xiaomi Redmi Note 13 Pro 256GB', 'slug' => 'xiaomi-redmi-note-13-pro-256gb', 'sku' => 'XIA-RN13P-256', 'brand_id' => 3, 'price' => 1899.00, 'sale_price' => 1699.00, 'stock' => 80, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Camera de 200MP e carregamento rapido de 67W.', 'categories' => [1, 10]],
            ['name' => 'Motorola Edge 50 Pro 256GB', 'slug' => 'motorola-edge-50-pro-256gb', 'sku' => 'MOT-E50P-256', 'brand_id' => 18, 'price' => 3499.00, 'sale_price' => null, 'stock' => 35, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Tela pOLED 144Hz e camera de 50MP com OIS.', 'categories' => [1, 10]],

            // Tablets
            ['name' => 'iPad Pro 12.9" M2 256GB', 'slug' => 'ipad-pro-129-m2-256gb', 'sku' => 'APL-IPDP129-256', 'brand_id' => 1, 'price' => 11999.00, 'sale_price' => 10999.00, 'stock' => 15, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'iPad Pro com chip M2 e tela Liquid Retina XDR.', 'categories' => [1, 11]],
            ['name' => 'iPad Air M2 256GB', 'slug' => 'ipad-air-m2-256gb', 'sku' => 'APL-IPDA-256', 'brand_id' => 1, 'price' => 7499.00, 'sale_price' => null, 'stock' => 25, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'iPad Air com chip M2 e tela de 10.9".', 'categories' => [1, 11]],
            ['name' => 'Samsung Galaxy Tab S9 Ultra 512GB', 'slug' => 'samsung-galaxy-tab-s9-ultra-512gb', 'sku' => 'SAM-TS9U-512', 'brand_id' => 2, 'price' => 9999.00, 'sale_price' => 8499.00, 'stock' => 12, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Tela Super AMOLED de 14.6" e S Pen inclusa.', 'categories' => [1, 11]],
            ['name' => 'Xiaomi Pad 6 Pro 256GB', 'slug' => 'xiaomi-pad-6-pro-256gb', 'sku' => 'XIA-PAD6P-256', 'brand_id' => 3, 'price' => 2999.00, 'sale_price' => 2499.00, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Tablet com Snapdragon 8+ Gen 1 e tela de 11".', 'categories' => [1, 11]],

            // Notebooks
            ['name' => 'MacBook Pro 14" M3 Pro 512GB', 'slug' => 'macbook-pro-14-m3-pro-512gb', 'sku' => 'APL-MBP14-M3P', 'brand_id' => 1, 'price' => 19999.00, 'sale_price' => 17999.00, 'stock' => 10, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'MacBook Pro com chip M3 Pro e tela Liquid Retina XDR.', 'categories' => [2, 20]],
            ['name' => 'MacBook Air 15" M3 512GB', 'slug' => 'macbook-air-15-m3-512gb', 'sku' => 'APL-MBA15-M3', 'brand_id' => 1, 'price' => 14999.00, 'sale_price' => null, 'stock' => 18, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'MacBook Air fino e leve com tela de 15.3".', 'categories' => [2, 20]],
            ['name' => 'Dell XPS 15 i7 32GB 1TB', 'slug' => 'dell-xps-15-i7-32gb-1tb', 'sku' => 'DEL-XPS15-I7', 'brand_id' => 20, 'price' => 12999.00, 'sale_price' => 11499.00, 'stock' => 8, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Notebook premium com tela OLED 3.5K.', 'categories' => [2, 20]],
            ['name' => 'ASUS ROG Zephyrus G16 RTX 4070', 'slug' => 'asus-rog-zephyrus-g16-rtx4070', 'sku' => 'ASU-ROGG16-4070', 'brand_id' => 19, 'price' => 14999.00, 'sale_price' => null, 'stock' => 6, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Notebook gamer com Intel Core i9 e RTX 4070.', 'categories' => [2, 20]],

            // Monitores
            ['name' => 'Samsung Odyssey G9 49" OLED', 'slug' => 'samsung-odyssey-g9-49-oled', 'sku' => 'SAM-ODG9-49', 'brand_id' => 2, 'price' => 12999.00, 'sale_price' => 10999.00, 'stock' => 5, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Monitor curvo ultrawide OLED 240Hz.', 'categories' => [2, 21]],
            ['name' => 'LG UltraGear 27" 4K 144Hz', 'slug' => 'lg-ultragear-27-4k-144hz', 'sku' => 'LG-UG27-4K', 'brand_id' => 5, 'price' => 3999.00, 'sale_price' => 3499.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Monitor gamer 4K com Nano IPS e 1ms.', 'categories' => [2, 21]],
            ['name' => 'Dell UltraSharp U2723QE 27" 4K', 'slug' => 'dell-ultrasharp-u2723qe-27-4k', 'sku' => 'DEL-U2723QE', 'brand_id' => 20, 'price' => 4499.00, 'sale_price' => null, 'stock' => 12, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Monitor profissional IPS Black com USB-C.', 'categories' => [2, 21]],

            // Teclados
            ['name' => 'Logitech MX Keys S', 'slug' => 'logitech-mx-keys-s', 'sku' => 'LOG-MXKEYSS', 'brand_id' => 7, 'price' => 799.00, 'sale_price' => null, 'stock' => 45, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Teclado wireless premium com iluminacao inteligente.', 'categories' => [2, 22]],
            ['name' => 'Razer BlackWidow V4 Pro', 'slug' => 'razer-blackwidow-v4-pro', 'sku' => 'RAZ-BWV4P', 'brand_id' => 8, 'price' => 1499.00, 'sale_price' => 1299.00, 'stock' => 25, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Teclado mecanico gamer com Command Dial.', 'categories' => [2, 22]],
            ['name' => 'Keychron Q1 Pro QMK', 'slug' => 'keychron-q1-pro-qmk', 'sku' => 'KEY-Q1P-QMK', 'brand_id' => 7, 'price' => 999.00, 'sale_price' => null, 'stock' => 18, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Teclado mecanico 75% com QMK/VIA.', 'categories' => [2, 22]],

            // Mouses
            ['name' => 'Logitech MX Master 3S', 'slug' => 'logitech-mx-master-3s', 'sku' => 'LOG-MXM3S', 'brand_id' => 7, 'price' => 699.00, 'sale_price' => 599.00, 'stock' => 50, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Mouse wireless premium com MagSpeed.', 'categories' => [2, 23]],
            ['name' => 'Razer DeathAdder V3 Pro', 'slug' => 'razer-deathadder-v3-pro', 'sku' => 'RAZ-DAV3P', 'brand_id' => 8, 'price' => 899.00, 'sale_price' => null, 'stock' => 35, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Mouse gamer wireless com sensor Focus Pro 30K.', 'categories' => [2, 23]],
            ['name' => 'Logitech G Pro X Superlight 2', 'slug' => 'logitech-g-pro-x-superlight-2', 'sku' => 'LOG-GPXSL2', 'brand_id' => 7, 'price' => 999.00, 'sale_price' => null, 'stock' => 28, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Mouse gamer wireless com apenas 60g.', 'categories' => [2, 23]],

            // Consoles
            ['name' => 'PlayStation 5 Standard', 'slug' => 'playstation-5-standard', 'sku' => 'SON-PS5-STD', 'brand_id' => 4, 'price' => 4499.00, 'sale_price' => 3999.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Console PlayStation 5 com leitor de disco.', 'categories' => [3, 30]],
            ['name' => 'PlayStation 5 Digital', 'slug' => 'playstation-5-digital', 'sku' => 'SON-PS5-DIG', 'brand_id' => 4, 'price' => 3999.00, 'sale_price' => 3499.00, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Console PlayStation 5 edicao digital.', 'categories' => [3, 30]],
            ['name' => 'Xbox Series X', 'slug' => 'xbox-series-x', 'sku' => 'MIC-XBSX', 'brand_id' => 9, 'price' => 4299.00, 'sale_price' => null, 'stock' => 18, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Console Xbox Series X 1TB.', 'categories' => [3, 30]],
            ['name' => 'Nintendo Switch OLED', 'slug' => 'nintendo-switch-oled', 'sku' => 'NIN-NSW-OLED', 'brand_id' => 23, 'price' => 2499.00, 'sale_price' => 2199.00, 'stock' => 40, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Nintendo Switch com tela OLED de 7".', 'categories' => [3, 30]],

            // Controles
            ['name' => 'DualSense Edge PS5', 'slug' => 'dualsense-edge-ps5', 'sku' => 'SON-DSE-PS5', 'brand_id' => 4, 'price' => 1499.00, 'sale_price' => null, 'stock' => 15, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Controle profissional customizavel para PS5.', 'categories' => [3, 31]],
            ['name' => 'Xbox Elite Series 2 Core', 'slug' => 'xbox-elite-series-2-core', 'sku' => 'MIC-XBE2C', 'brand_id' => 9, 'price' => 899.00, 'sale_price' => 799.00, 'stock' => 22, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Controle premium Xbox com gatilhos ajustaveis.', 'categories' => [3, 31]],
            ['name' => 'Nintendo Switch Pro Controller', 'slug' => 'nintendo-switch-pro-controller', 'sku' => 'NIN-SWPC', 'brand_id' => 23, 'price' => 449.00, 'sale_price' => null, 'stock' => 35, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Controle Pro para Nintendo Switch.', 'categories' => [3, 31]],

            // Fones de Ouvido
            ['name' => 'AirPods Pro 2', 'slug' => 'airpods-pro-2', 'sku' => 'APL-APP2', 'brand_id' => 1, 'price' => 1899.00, 'sale_price' => 1699.00, 'stock' => 60, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'AirPods Pro com ANC e Audio Espacial.', 'categories' => [4, 40]],
            ['name' => 'AirPods Max', 'slug' => 'airpods-max', 'sku' => 'APL-APM', 'brand_id' => 1, 'price' => 4999.00, 'sale_price' => 4499.00, 'stock' => 12, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Headphone premium Apple com ANC.', 'categories' => [4, 40]],
            ['name' => 'Sony WH-1000XM5', 'slug' => 'sony-wh-1000xm5', 'sku' => 'SON-WH1000XM5', 'brand_id' => 4, 'price' => 2499.00, 'sale_price' => 2199.00, 'stock' => 40, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Headphone wireless com melhor ANC do mercado.', 'categories' => [4, 40]],
            ['name' => 'Bose QuietComfort Ultra', 'slug' => 'bose-quietcomfort-ultra', 'sku' => 'BOS-QCU', 'brand_id' => 11, 'price' => 2899.00, 'sale_price' => null, 'stock' => 18, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Fone Bose com Immersive Audio.', 'categories' => [4, 40]],
            ['name' => 'Samsung Galaxy Buds2 Pro', 'slug' => 'samsung-galaxy-buds2-pro', 'sku' => 'SAM-GB2P', 'brand_id' => 2, 'price' => 1299.00, 'sale_price' => 999.00, 'stock' => 55, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Earbuds premium Samsung com ANC.', 'categories' => [4, 40]],

            // Caixas de Som
            ['name' => 'JBL Charge 5', 'slug' => 'jbl-charge-5', 'sku' => 'JBL-CHG5', 'brand_id' => 6, 'price' => 1099.00, 'sale_price' => 899.00, 'stock' => 45, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Caixa Bluetooth portatil com Power Bank.', 'categories' => [4, 41]],
            ['name' => 'JBL PartyBox 310', 'slug' => 'jbl-partybox-310', 'sku' => 'JBL-PB310', 'brand_id' => 6, 'price' => 3499.00, 'sale_price' => null, 'stock' => 8, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Caixa de som potente para festas.', 'categories' => [4, 41]],
            ['name' => 'Bose SoundLink Flex', 'slug' => 'bose-soundlink-flex', 'sku' => 'BOS-SLF', 'brand_id' => 11, 'price' => 999.00, 'sale_price' => null, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Caixa Bluetooth compacta e resistente.', 'categories' => [4, 41]],
            ['name' => 'Sonos Era 300', 'slug' => 'sonos-era-300', 'sku' => 'SON-ERA300', 'brand_id' => 11, 'price' => 3499.00, 'sale_price' => 2999.00, 'stock' => 10, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Caixa com audio espacial Dolby Atmos.', 'categories' => [4, 41]],

            // Smartwatches
            ['name' => 'Apple Watch Series 9 45mm', 'slug' => 'apple-watch-series-9-45mm', 'sku' => 'APL-AWS9-45', 'brand_id' => 1, 'price' => 4499.00, 'sale_price' => 3999.00, 'stock' => 35, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Apple Watch com chip S9 e Double Tap.', 'categories' => [7, 70]],
            ['name' => 'Apple Watch Ultra 2', 'slug' => 'apple-watch-ultra-2', 'sku' => 'APL-AWU2', 'brand_id' => 1, 'price' => 8999.00, 'sale_price' => null, 'stock' => 12, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Apple Watch para aventuras extremas.', 'categories' => [7, 70]],
            ['name' => 'Samsung Galaxy Watch 6 Classic', 'slug' => 'samsung-galaxy-watch-6-classic', 'sku' => 'SAM-GW6C', 'brand_id' => 2, 'price' => 2999.00, 'sale_price' => 2499.00, 'stock' => 28, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Smartwatch Samsung com bezel rotativo.', 'categories' => [7, 70]],
            ['name' => 'Xiaomi Watch S3', 'slug' => 'xiaomi-watch-s3', 'sku' => 'XIA-WS3', 'brand_id' => 3, 'price' => 999.00, 'sale_price' => null, 'stock' => 50, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Smartwatch com bezeis intercambiaveis.', 'categories' => [7, 70]],

            // Casa Inteligente
            ['name' => 'Google Nest Hub 2 Geracao', 'slug' => 'google-nest-hub-2-geracao', 'sku' => 'GOO-NH2G', 'brand_id' => 10, 'price' => 699.00, 'sale_price' => 599.00, 'stock' => 40, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Smart Display Google com monitoramento de sono.', 'categories' => [6, 63]],
            ['name' => 'Amazon Echo Dot 5 Geracao', 'slug' => 'amazon-echo-dot-5-geracao', 'sku' => 'AMZ-ED5G', 'brand_id' => 10, 'price' => 399.00, 'sale_price' => 299.00, 'stock' => 100, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Smart Speaker com Alexa.', 'categories' => [6, 63]],
            ['name' => 'Philips Hue Starter Kit', 'slug' => 'philips-hue-starter-kit', 'sku' => 'PHI-HUE-SK', 'brand_id' => 5, 'price' => 1299.00, 'sale_price' => null, 'stock' => 25, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Kit com 3 lampadas e Hub Hue Bridge.', 'categories' => [6, 60]],
            ['name' => 'Ring Video Doorbell Pro 2', 'slug' => 'ring-video-doorbell-pro-2', 'sku' => 'RIN-VDBP2', 'brand_id' => 10, 'price' => 1799.00, 'sale_price' => 1499.00, 'stock' => 18, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Campainha inteligente com video HD.', 'categories' => [6, 62]],

            // Drones
            ['name' => 'DJI Mini 4 Pro', 'slug' => 'dji-mini-4-pro', 'sku' => 'DJI-M4P', 'brand_id' => 13, 'price' => 6999.00, 'sale_price' => 5999.00, 'stock' => 10, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Drone compacto com camera 4K e sensor de obstaculos.', 'categories' => [8, 81]],
            ['name' => 'DJI Air 3', 'slug' => 'dji-air-3', 'sku' => 'DJI-AIR3', 'brand_id' => 13, 'price' => 8999.00, 'sale_price' => null, 'stock' => 8, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Drone com camera dupla e lente tele.', 'categories' => [8, 81]],
            ['name' => 'DJI Mavic 3 Pro', 'slug' => 'dji-mavic-3-pro', 'sku' => 'DJI-M3P', 'brand_id' => 13, 'price' => 15999.00, 'sale_price' => 13999.00, 'stock' => 5, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Drone profissional com camera Hasselblad.', 'categories' => [8, 81]],

            // Action Cameras
            ['name' => 'GoPro Hero 12 Black', 'slug' => 'gopro-hero-12-black', 'sku' => 'GOP-H12B', 'brand_id' => 14, 'price' => 3499.00, 'sale_price' => 2999.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Action camera 5.3K com estabilizacao HyperSmooth.', 'categories' => [8, 82]],
            ['name' => 'DJI Osmo Action 4', 'slug' => 'dji-osmo-action-4', 'sku' => 'DJI-OA4', 'brand_id' => 13, 'price' => 2999.00, 'sale_price' => null, 'stock' => 15, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Action camera 4K com sensor grande.', 'categories' => [8, 82]],
            ['name' => 'Insta360 X4', 'slug' => 'insta360-x4', 'sku' => 'INS-X4', 'brand_id' => 13, 'price' => 3999.00, 'sale_price' => null, 'stock' => 12, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Camera 360 8K com edicao por IA.', 'categories' => [8, 82]],

            // Power Banks
            ['name' => 'Anker PowerCore 26800', 'slug' => 'anker-powercore-26800', 'sku' => 'ANK-PC26800', 'brand_id' => 12, 'price' => 399.00, 'sale_price' => 349.00, 'stock' => 80, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Power bank de alta capacidade com PowerIQ.', 'categories' => [5, 53]],
            ['name' => 'Anker 737 Power Bank 24000mAh', 'slug' => 'anker-737-power-bank-24000mah', 'sku' => 'ANK-737-24K', 'brand_id' => 12, 'price' => 899.00, 'sale_price' => null, 'stock' => 35, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Power bank 140W para carregar notebooks.', 'categories' => [5, 53]],
            ['name' => 'Xiaomi Power Bank 3 20000mAh', 'slug' => 'xiaomi-power-bank-3-20000mah', 'sku' => 'XIA-PB3-20K', 'brand_id' => 3, 'price' => 199.00, 'sale_price' => 159.00, 'stock' => 120, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Power bank com carregamento rapido 18W.', 'categories' => [5, 53]],

            // Carregadores
            ['name' => 'Apple MagSafe Charger', 'slug' => 'apple-magsafe-charger', 'sku' => 'APL-MSC', 'brand_id' => 1, 'price' => 399.00, 'sale_price' => null, 'stock' => 70, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Carregador wireless magnetico para iPhone.', 'categories' => [5, 51]],
            ['name' => 'Anker Nano II 65W', 'slug' => 'anker-nano-ii-65w', 'sku' => 'ANK-N2-65W', 'brand_id' => 12, 'price' => 299.00, 'sale_price' => 249.00, 'stock' => 60, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Carregador GaN compacto para notebooks.', 'categories' => [5, 51]],
            ['name' => 'Samsung Super Fast Charger 45W', 'slug' => 'samsung-super-fast-charger-45w', 'sku' => 'SAM-SFC45', 'brand_id' => 2, 'price' => 249.00, 'sale_price' => null, 'stock' => 55, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Carregador super rapido Samsung.', 'categories' => [5, 51]],

            // Headsets Gamer
            ['name' => 'SteelSeries Arctis Nova Pro Wireless', 'slug' => 'steelseries-arctis-nova-pro-wireless', 'sku' => 'STE-ANPW', 'brand_id' => 25, 'price' => 2499.00, 'sale_price' => 2199.00, 'stock' => 15, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Headset gamer premium com ANC.', 'categories' => [3, 33]],
            ['name' => 'HyperX Cloud III Wireless', 'slug' => 'hyperx-cloud-iii-wireless', 'sku' => 'HPX-C3W', 'brand_id' => 24, 'price' => 899.00, 'sale_price' => 799.00, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Headset gamer wireless com 120h de bateria.', 'categories' => [3, 33]],
            ['name' => 'Razer BlackShark V2 Pro', 'slug' => 'razer-blackshark-v2-pro', 'sku' => 'RAZ-BSV2P', 'brand_id' => 8, 'price' => 1299.00, 'sale_price' => null, 'stock' => 22, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Headset esports com THX Spatial Audio.', 'categories' => [3, 33]],
            ['name' => 'Logitech G Pro X 2 Lightspeed', 'slug' => 'logitech-g-pro-x-2-lightspeed', 'sku' => 'LOG-GPX2LS', 'brand_id' => 7, 'price' => 1499.00, 'sale_price' => null, 'stock' => 18, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Headset pro com drivers de grafeno.', 'categories' => [3, 33]],

            // Webcams
            ['name' => 'Logitech Brio 4K Pro', 'slug' => 'logitech-brio-4k-pro', 'sku' => 'LOG-BRIO4K', 'brand_id' => 7, 'price' => 1299.00, 'sale_price' => 1099.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Webcam 4K HDR com RightLight 3.', 'categories' => [2, 24]],
            ['name' => 'Razer Kiyo Pro Ultra', 'slug' => 'razer-kiyo-pro-ultra', 'sku' => 'RAZ-KIYO-PU', 'brand_id' => 8, 'price' => 1999.00, 'sale_price' => null, 'stock' => 10, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Webcam 4K com sensor grande de 1/1.2".', 'categories' => [2, 24]],
            ['name' => 'Elgato Facecam Pro', 'slug' => 'elgato-facecam-pro', 'sku' => 'ELG-FCP', 'brand_id' => 7, 'price' => 1799.00, 'sale_price' => 1499.00, 'stock' => 12, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Webcam 4K60 para streaming profissional.', 'categories' => [2, 24]],

            // SSDs
            ['name' => 'Samsung 990 Pro 2TB NVMe', 'slug' => 'samsung-990-pro-2tb-nvme', 'sku' => 'SAM-990P-2TB', 'brand_id' => 2, 'price' => 1299.00, 'sale_price' => 1099.00, 'stock' => 35, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'SSD PCIe 4.0 com 7450MB/s de leitura.', 'categories' => [2, 25]],
            ['name' => 'WD Black SN850X 2TB', 'slug' => 'wd-black-sn850x-2tb', 'sku' => 'WD-SN850X-2TB', 'brand_id' => 20, 'price' => 1199.00, 'sale_price' => null, 'stock' => 28, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'SSD gamer NVMe com dissipador.', 'categories' => [2, 25]],
            ['name' => 'Samsung T7 Shield 2TB', 'slug' => 'samsung-t7-shield-2tb', 'sku' => 'SAM-T7S-2TB', 'brand_id' => 2, 'price' => 999.00, 'sale_price' => 849.00, 'stock' => 40, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'SSD externo resistente a agua e quedas.', 'categories' => [2, 25]],

            // Microfones
            ['name' => 'Blue Yeti X', 'slug' => 'blue-yeti-x', 'sku' => 'BLU-YETIX', 'brand_id' => 7, 'price' => 999.00, 'sale_price' => 849.00, 'stock' => 25, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Microfone USB profissional para streaming.', 'categories' => [4, 43]],
            ['name' => 'Shure MV7', 'slug' => 'shure-mv7', 'sku' => 'SHU-MV7', 'brand_id' => 11, 'price' => 1799.00, 'sale_price' => null, 'stock' => 15, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Microfone dinamico USB/XLR hibrido.', 'categories' => [4, 43]],
            ['name' => 'Elgato Wave:3', 'slug' => 'elgato-wave-3', 'sku' => 'ELG-W3', 'brand_id' => 7, 'price' => 999.00, 'sale_price' => null, 'stock' => 20, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Microfone condensador com mixer digital.', 'categories' => [4, 43]],
            ['name' => 'HyperX QuadCast S', 'slug' => 'hyperx-quadcast-s', 'sku' => 'HPX-QCS', 'brand_id' => 24, 'price' => 899.00, 'sale_price' => 749.00, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Microfone USB RGB para streamers.', 'categories' => [4, 43]],

            // TVs
            ['name' => 'LG OLED C3 65"', 'slug' => 'lg-oled-c3-65', 'sku' => 'LG-OLEDC3-65', 'brand_id' => 5, 'price' => 8999.00, 'sale_price' => 7499.00, 'stock' => 8, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'TV OLED 4K com processador a9 Gen6.', 'categories' => [1, 12]],
            ['name' => 'Samsung Neo QLED QN90C 65"', 'slug' => 'samsung-neo-qled-qn90c-65', 'sku' => 'SAM-QN90C-65', 'brand_id' => 2, 'price' => 7999.00, 'sale_price' => null, 'stock' => 10, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'TV Mini LED 4K com Neural Quantum Processor.', 'categories' => [1, 12]],
            ['name' => 'Sony Bravia XR A80L 55"', 'slug' => 'sony-bravia-xr-a80l-55', 'sku' => 'SON-A80L-55', 'brand_id' => 4, 'price' => 6999.00, 'sale_price' => 5999.00, 'stock' => 6, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'TV OLED 4K com Cognitive Processor XR.', 'categories' => [1, 12]],

            // Oculos VR
            ['name' => 'Meta Quest 3 512GB', 'slug' => 'meta-quest-3-512gb', 'sku' => 'MET-Q3-512', 'brand_id' => 9, 'price' => 3999.00, 'sale_price' => null, 'stock' => 15, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Headset VR standalone com Mixed Reality.', 'categories' => [7, 72]],
            ['name' => 'PlayStation VR2', 'slug' => 'playstation-vr2', 'sku' => 'SON-PSVR2', 'brand_id' => 4, 'price' => 4499.00, 'sale_price' => 3999.00, 'stock' => 12, 'status' => 'active', 'is_featured' => 1, 'is_new' => 0, 'short_description' => 'Headset VR para PS5 com eye tracking.', 'categories' => [7, 72]],

            // Extras para completar 100+
            ['name' => 'Apple Magic Keyboard com Touch ID', 'slug' => 'apple-magic-keyboard-touch-id', 'sku' => 'APL-MKTID', 'brand_id' => 1, 'price' => 1299.00, 'sale_price' => null, 'stock' => 30, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Teclado Apple wireless com Touch ID.', 'categories' => [2, 22]],
            ['name' => 'Apple Magic Mouse', 'slug' => 'apple-magic-mouse', 'sku' => 'APL-MM', 'brand_id' => 1, 'price' => 699.00, 'sale_price' => null, 'stock' => 40, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Mouse Apple com superficie Multi-Touch.', 'categories' => [2, 23]],
            ['name' => 'Apple Magic Trackpad', 'slug' => 'apple-magic-trackpad', 'sku' => 'APL-MTP', 'brand_id' => 1, 'price' => 999.00, 'sale_price' => null, 'stock' => 25, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Trackpad wireless com Force Touch.', 'categories' => [2, 23]],
            ['name' => 'Apple Pencil 2 Geracao', 'slug' => 'apple-pencil-2-geracao', 'sku' => 'APL-PEN2', 'brand_id' => 1, 'price' => 1099.00, 'sale_price' => null, 'stock' => 50, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Caneta stylus para iPad Pro e Air.', 'categories' => [5, 54]],
            ['name' => 'Samsung Galaxy Z Fold5 512GB', 'slug' => 'samsung-galaxy-z-fold5-512gb', 'sku' => 'SAM-ZF5-512', 'brand_id' => 2, 'price' => 10999.00, 'sale_price' => 9499.00, 'stock' => 10, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Smartphone dobravel Samsung premium.', 'categories' => [1, 10]],
            ['name' => 'Samsung Galaxy Z Flip5 256GB', 'slug' => 'samsung-galaxy-z-flip5-256gb', 'sku' => 'SAM-ZFL5-256', 'brand_id' => 2, 'price' => 6499.00, 'sale_price' => 5499.00, 'stock' => 20, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Smartphone flip compacto e estiloso.', 'categories' => [1, 10]],
            ['name' => 'Xiaomi Mi Band 8', 'slug' => 'xiaomi-mi-band-8', 'sku' => 'XIA-MB8', 'brand_id' => 3, 'price' => 299.00, 'sale_price' => 249.00, 'stock' => 150, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Pulseira fitness com tela AMOLED.', 'categories' => [7, 71]],
            ['name' => 'Canon EOS R6 Mark II', 'slug' => 'canon-eos-r6-mark-ii', 'sku' => 'CAN-R6M2', 'brand_id' => 15, 'price' => 18999.00, 'sale_price' => null, 'stock' => 5, 'status' => 'active', 'is_featured' => 0, 'is_new' => 1, 'short_description' => 'Camera mirrorless full-frame 24.2MP.', 'categories' => [8, 80]],
            ['name' => 'Sony A7 IV', 'slug' => 'sony-a7-iv', 'sku' => 'SON-A7IV', 'brand_id' => 4, 'price' => 17999.00, 'sale_price' => 15999.00, 'stock' => 6, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Camera mirrorless full-frame 33MP.', 'categories' => [8, 80]],
            ['name' => 'JBL Flip 6', 'slug' => 'jbl-flip-6', 'sku' => 'JBL-FLIP6', 'brand_id' => 6, 'price' => 699.00, 'sale_price' => 599.00, 'stock' => 70, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Caixa Bluetooth compacta IP67.', 'categories' => [4, 41]],
            ['name' => 'JBL Go 3', 'slug' => 'jbl-go-3', 'sku' => 'JBL-GO3', 'brand_id' => 6, 'price' => 249.00, 'sale_price' => 199.00, 'stock' => 120, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Caixa Bluetooth ultraportatil.', 'categories' => [4, 41]],
            ['name' => 'Sony WF-1000XM5', 'slug' => 'sony-wf-1000xm5', 'sku' => 'SON-WF1000XM5', 'brand_id' => 4, 'price' => 2299.00, 'sale_price' => null, 'stock' => 25, 'status' => 'active', 'is_featured' => 1, 'is_new' => 1, 'short_description' => 'Earbuds premium com ANC lider de mercado.', 'categories' => [4, 40]],
            ['name' => 'Xiaomi Buds 4 Pro', 'slug' => 'xiaomi-buds-4-pro', 'sku' => 'XIA-B4P', 'brand_id' => 3, 'price' => 899.00, 'sale_price' => 749.00, 'stock' => 45, 'status' => 'active', 'is_featured' => 0, 'is_new' => 0, 'short_description' => 'Earbuds com ANC e audio Hi-Fi.', 'categories' => [4, 40]],
        ];
    }
}
