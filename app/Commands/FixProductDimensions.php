<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixProductDimensions extends BaseCommand
{
    protected $group       = 'Products';
    protected $name        = 'products:fix-dimensions';
    protected $description = 'Corrige pesos e dimensoes de produtos baseado no tipo/categoria';

    // Pesos e dimensoes padrão por tipo de produto (baseados em dados reais)
    protected $productDefaults = [
        // Celulares e Smartphones
        'celular|smartphone|iphone|galaxy|redmi|poco|motorola|nokia' => [
            'weight' => 0.200, 'width' => 8, 'height' => 16, 'length' => 1
        ],
        // Tablets
        'tablet|ipad' => [
            'weight' => 0.500, 'width' => 25, 'height' => 18, 'length' => 1
        ],
        // Notebooks/Laptops
        'notebook|laptop|macbook' => [
            'weight' => 2.000, 'width' => 36, 'height' => 25, 'length' => 2
        ],
        // Fones de Ouvido
        'fone de ouvido|earphone|headphone|airpods|buds|earbuds|headset' => [
            'weight' => 0.150, 'width' => 10, 'height' => 10, 'length' => 5
        ],
        // Smartwatch/Relógios
        'smartwatch|relogio|watch|band|mi band|galaxy watch|apple watch' => [
            'weight' => 0.100, 'width' => 10, 'height' => 10, 'length' => 3
        ],
        // Caixas de Som/Speakers
        'caixa de som|speaker|soundbar|soundbox|jbl|bose' => [
            'weight' => 0.800, 'width' => 15, 'height' => 20, 'length' => 10
        ],
        // Luminárias
        'luminaria|lampada|led desk|desk lamp|abajur' => [
            'weight' => 1.000, 'width' => 15, 'height' => 45, 'length' => 15
        ],
        // Carregadores
        'carregador|charger|fonte|adaptador de energia' => [
            'weight' => 0.150, 'width' => 8, 'height' => 8, 'length' => 3
        ],
        // Cabos
        'cabo|cable|usb|lightning|type-c|hdmi' => [
            'weight' => 0.050, 'width' => 15, 'height' => 10, 'length' => 2
        ],
        // Power Banks
        'powerbank|power bank|bateria portatil|bateria externa' => [
            'weight' => 0.300, 'width' => 15, 'height' => 8, 'length' => 2
        ],
        // Capinhas/Cases
        'capinha|capa|case|cover|protetor' => [
            'weight' => 0.050, 'width' => 10, 'height' => 18, 'length' => 1
        ],
        // Películas
        'pelicula|tela|screen protector|vidro temperado' => [
            'weight' => 0.030, 'width' => 10, 'height' => 18, 'length' => 1
        ],
        // Cartuchos/Toners
        'cartucho|toner|refil' => [
            'weight' => 0.150, 'width' => 12, 'height' => 12, 'length' => 5
        ],
        // Impressoras pequenas
        'impressora termica|mini printer' => [
            'weight' => 1.500, 'width' => 20, 'height' => 15, 'length' => 15
        ],
        // Impressoras médias
        'impressora jato|impressora multifuncional|impressora a jato' => [
            'weight' => 5.000, 'width' => 45, 'height' => 20, 'length' => 35
        ],
        // Impressoras matriciais/grandes
        'impressora matricial|impressora laser' => [
            'weight' => 8.000, 'width' => 50, 'height' => 30, 'length' => 40
        ],
        // Teclados
        'teclado|keyboard' => [
            'weight' => 0.500, 'width' => 45, 'height' => 15, 'length' => 3
        ],
        // Mouses
        'mouse|rato' => [
            'weight' => 0.100, 'width' => 12, 'height' => 7, 'length' => 4
        ],
        // Webcams
        'webcam|camera web' => [
            'weight' => 0.150, 'width' => 10, 'height' => 8, 'length' => 5
        ],
        // Monitores
        'monitor|tela|display' => [
            'weight' => 4.000, 'width' => 60, 'height' => 40, 'length' => 10
        ],
        // TVs
        'tv |televisor|smart tv|television' => [
            'weight' => 8.000, 'width' => 100, 'height' => 60, 'length' => 10
        ],
        // Câmeras
        'camera|gopro|action cam|webcam' => [
            'weight' => 0.300, 'width' => 12, 'height' => 8, 'length' => 5
        ],
        // Drones
        'drone|dji|quadcopter' => [
            'weight' => 0.500, 'width' => 25, 'height' => 25, 'length' => 10
        ],
        // Perfumes (pequenos)
        'perfume|colonia|eau de|fragrance|toilette|parfum' => [
            'weight' => 0.250, 'width' => 8, 'height' => 12, 'length' => 5
        ],
        // Cosméticos
        'cosmetico|maquiagem|batom|base|rimel|sombra|blush' => [
            'weight' => 0.100, 'width' => 8, 'height' => 10, 'length' => 3
        ],
        // Cremes/Hidratantes
        'creme|hidratante|loção|serum|protetor solar' => [
            'weight' => 0.200, 'width' => 8, 'height' => 15, 'length' => 5
        ],
        // HD/SSD
        'hd externo|ssd|disco rigido|hard disk|pendrive|pen drive' => [
            'weight' => 0.150, 'width' => 12, 'height' => 8, 'length' => 2
        ],
        // Cartões de memória
        'cartao de memoria|micro sd|sd card|memory card' => [
            'weight' => 0.010, 'width' => 5, 'height' => 5, 'length' => 1
        ],
        // Controles/Joysticks
        'controle|joystick|gamepad|controller' => [
            'weight' => 0.250, 'width' => 18, 'height' => 12, 'length' => 6
        ],
        // Consoles
        'playstation|xbox|nintendo switch|console' => [
            'weight' => 3.500, 'width' => 35, 'height' => 25, 'length' => 10
        ],
        // Roteadores
        'roteador|router|repetidor|extensor|wifi|wi-fi' => [
            'weight' => 0.400, 'width' => 20, 'height' => 15, 'length' => 5
        ],
        // Suportes/Tripés
        'suporte|tripe|stand|holder|apoio' => [
            'weight' => 0.500, 'width' => 20, 'height' => 30, 'length' => 10
        ],
        // Ring Light
        'ring light|luz anel|iluminador' => [
            'weight' => 0.800, 'width' => 30, 'height' => 35, 'length' => 10
        ],
        // Microfones
        'microfone|microphone|mic' => [
            'weight' => 0.300, 'width' => 10, 'height' => 20, 'length' => 8
        ],
        // Estabilizadores/Gimbals
        'estabilizador|gimbal|stabilizer' => [
            'weight' => 0.600, 'width' => 20, 'height' => 30, 'length' => 10
        ],
        // Aspiradores
        'aspirador|vacuum|robo aspi' => [
            'weight' => 3.000, 'width' => 35, 'height' => 35, 'length' => 10
        ],
        // Ventiladores
        'ventilador|fan' => [
            'weight' => 2.500, 'width' => 40, 'height' => 50, 'length' => 20
        ],
        // Default para produtos pequenos não identificados
        'default' => [
            'weight' => 0.300, 'width' => 15, 'height' => 15, 'length' => 5
        ],
    ];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $dryRun = in_array('--dry-run', $params);
        $verbose = in_array('-v', $params) || in_array('--verbose', $params);

        if ($dryRun) {
            CLI::write('Modo simulacao - nenhuma alteracao sera feita', 'yellow');
        }

        // Buscar todos os produtos
        $products = $db->table('products')
            ->select('id, sku, name, weight, width, height, length')
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        $total = count($products);
        $updated = 0;
        $suspicious = 0;

        CLI::write("Analisando {$total} produtos...", 'white');
        CLI::newLine();

        foreach ($products as $index => $product) {
            $name = strtolower($product['name']);
            $currentWeight = (float) $product['weight'];
            $defaults = $this->getDefaultsForProduct($name);

            // Verificar se o peso parece incorreto
            $expectedWeight = $defaults['weight'];
            $tolerance = 3; // Tolerância de 3x para mais ou menos

            $isSuspicious = false;
            $reason = '';

            // Peso muito diferente do esperado
            if ($currentWeight > 0 && ($currentWeight > $expectedWeight * $tolerance || $currentWeight < $expectedWeight / $tolerance)) {
                $isSuspicious = true;
                $reason = "Peso {$currentWeight}kg muito diferente do esperado {$expectedWeight}kg";
            }

            // Peso zerado ou muito baixo
            if ($currentWeight <= 0.01) {
                $isSuspicious = true;
                $reason = "Peso zerado ou muito baixo";
            }

            if ($isSuspicious) {
                $suspicious++;

                if ($verbose) {
                    CLI::write("SKU: {$product['sku']} - {$product['name']}", 'yellow');
                    CLI::write("  Motivo: {$reason}", 'yellow');
                    CLI::write("  Atual: {$currentWeight}kg {$product['width']}x{$product['height']}x{$product['length']}cm", 'red');
                    CLI::write("  Sugerido: {$defaults['weight']}kg {$defaults['width']}x{$defaults['height']}x{$defaults['length']}cm", 'green');
                    CLI::newLine();
                }

                if (!$dryRun) {
                    $db->table('products')
                        ->where('id', $product['id'])
                        ->update([
                            'weight' => $defaults['weight'],
                            'width' => $defaults['width'],
                            'height' => $defaults['height'],
                            'length' => $defaults['length'],
                        ]);
                    $updated++;
                }
            }

            // Progress
            if (($index + 1) % 100 === 0) {
                CLI::showProgress($index + 1, $total);
            }
        }

        CLI::showProgress($total, $total);
        CLI::newLine(2);

        CLI::write("Resultado:", 'white');
        CLI::write("  Total de produtos: {$total}", 'white');
        CLI::write("  Produtos suspeitos: {$suspicious}", 'yellow');

        if (!$dryRun) {
            CLI::write("  Produtos atualizados: {$updated}", 'green');
        } else {
            CLI::write("  (Modo simulacao - use sem --dry-run para aplicar)", 'yellow');
        }
    }

    protected function getDefaultsForProduct(string $name): array
    {
        foreach ($this->productDefaults as $pattern => $defaults) {
            if ($pattern === 'default') continue;

            $patterns = explode('|', $pattern);
            foreach ($patterns as $p) {
                if (stripos($name, trim($p)) !== false) {
                    return $defaults;
                }
            }
        }

        return $this->productDefaults['default'];
    }
}
