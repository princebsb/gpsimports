<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ImportProducts extends BaseCommand
{
    protected $group = 'Import';
    protected $name = 'import:products';
    protected $description = 'Importa produtos do banco produtos_paraguai';
    protected $usage = 'import:products [options]';
    protected $options = [
        '--limit' => 'Limite de produtos a importar (default: todos)',
        '--offset' => 'Offset para paginação (default: 0)',
        '--dry-run' => 'Simula a importação sem salvar',
        '--update' => 'Atualiza produtos existentes (por SKU)',
    ];

    // Mapeamento de categoria_slug para category_id
    protected array $categoryMap = [];

    // Mapeamento de marcas
    protected array $brandMap = [];

    // Palavras-chave para detectar marca no nome
    protected array $brandKeywords = [
        'apple' => 'Apple',
        'iphone' => 'Apple',
        'ipad' => 'Apple',
        'macbook' => 'Apple',
        'airpods' => 'Apple',
        'samsung' => 'Samsung',
        'galaxy' => 'Samsung',
        'xiaomi' => 'Xiaomi',
        'redmi' => 'Xiaomi',
        'poco' => 'Xiaomi',
        'sony' => 'Sony',
        'playstation' => 'Sony',
        'lg' => 'LG',
        'jbl' => 'JBL',
        'logitech' => 'Logitech',
        'razer' => 'Razer',
        'microsoft' => 'Microsoft',
        'xbox' => 'Microsoft',
        'surface' => 'Microsoft',
        'google' => 'Google',
        'pixel' => 'Google',
        'bose' => 'Bose',
        'anker' => 'Anker',
        'dji' => 'DJI',
        'gopro' => 'GoPro',
        'canon' => 'Canon',
        'nikon' => 'Nikon',
        'huawei' => 'Huawei',
        'motorola' => 'Motorola',
        'asus' => 'ASUS',
        'dell' => 'Dell',
        'lenovo' => 'Lenovo',
        'hp' => 'HP',
        'acer' => 'Acer',
        'nintendo' => 'Nintendo',
        'garmin' => 'Garmin',
        'fitbit' => 'Fitbit',
        'marshall' => 'Marshall',
        'harman' => 'Harman Kardon',
        'beats' => 'Beats',
        'sennheiser' => 'Sennheiser',
        'audio-technica' => 'Audio-Technica',
        'bang' => 'Bang & Olufsen',
        'sonos' => 'Sonos',
    ];

    // Mapeamento de categoria_slug para categoria do sistema
    protected array $slugToCategoryMap = [
        // Celulares e Tablets
        'apple' => 'Celulares',
        'celulares' => 'Celulares',
        'smartphones' => 'Celulares',
        'iphone' => 'Celulares',
        'tablets' => 'Tablets',
        'ipad' => 'Tablets',

        // Informática
        'notebooks' => 'Notebooks',
        'laptop' => 'Notebooks',
        'macbook' => 'Notebooks',
        'computadores' => 'Computadores',
        'desktop' => 'Computadores',
        'monitores' => 'Monitores',
        'perifericos' => 'Perifericos',
        'teclados' => 'Perifericos',
        'mouse' => 'Perifericos',

        // Audio
        'fones' => 'Fones de Ouvido',
        'headphones' => 'Fones de Ouvido',
        'earbuds' => 'Fones de Ouvido',
        'airpods' => 'Fones de Ouvido',
        'caixas-de-som' => 'Caixas de Som',
        'speakers' => 'Caixas de Som',
        'audio' => 'Audio',
        'soundbar' => 'Soundbars',

        // Games
        'games' => 'Consoles',
        'consoles' => 'Consoles',
        'playstation' => 'Consoles',
        'xbox' => 'Consoles',
        'nintendo' => 'Consoles',
        'jogos' => 'Jogos',
        'controles' => 'Controles',

        // Wearables
        'smartwatch' => 'Smartwatches',
        'relogios' => 'Smartwatches',
        'watch' => 'Smartwatches',
        'pulseiras' => 'Pulseiras Inteligentes',

        // Câmeras
        'cameras' => 'Cameras',
        'fotografia' => 'Cameras',
        'drones' => 'Drones',
        'action-cam' => 'Cameras de Acao',
        'gopro' => 'Cameras de Acao',

        // Acessórios
        'acessorios' => 'Acessorios',
        'capas' => 'Capas e Peliculas',
        'carregadores' => 'Carregadores',
        'cabos' => 'Cabos',
        'power-bank' => 'Power Banks',

        // Casa Inteligente
        'smart-home' => 'Casa Inteligente',
        'alexa' => 'Assistentes Virtuais',
        'google-home' => 'Assistentes Virtuais',

        // TV e Video
        'tv' => 'TVs',
        'televisores' => 'TVs',
        'projetores' => 'Projetores',
        'streaming' => 'Streaming',
    ];

    public function run(array $params)
    {
        $limit = $params['limit'] ?? null;
        $offset = $params['offset'] ?? 0;
        $dryRun = CLI::getOption('dry-run') !== null;
        $update = CLI::getOption('update') !== null;

        CLI::write('==============================================', 'cyan');
        CLI::write('  IMPORTAÇÃO DE PRODUTOS - produtos_paraguai  ', 'cyan');
        CLI::write('==============================================', 'cyan');
        CLI::newLine();

        if ($dryRun) {
            CLI::write('*** MODO DRY-RUN - Nenhum dado será salvo ***', 'yellow');
            CLI::newLine();
        }

        // Carregar mapeamentos
        $this->loadMappings();

        // Conectar ao banco de origem
        $sourceDb = $this->getSourceDb();

        // Contar total
        $totalQuery = $sourceDb->query("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1 AND disponivel = 1");
        $total = $totalQuery->getRow()->total;

        CLI::write("Total de produtos ativos: {$total}", 'green');
        CLI::newLine();

        // Query de seleção
        $sql = "SELECT * FROM produtos WHERE ativo = 1 AND disponivel = 1 ORDER BY id";
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $products = $sourceDb->query($sql)->getResultArray();
        $count = count($products);

        CLI::write("Processando {$count} produtos...", 'white');
        CLI::newLine();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $productModel = model('ProductModel');
        $db = \Config\Database::connect();

        foreach ($products as $index => $product) {
            $progress = $index + 1;
            CLI::showProgress($progress, $count);

            try {
                $data = $this->mapProduct($product);

                if (!$data) {
                    $skipped++;
                    continue;
                }

                // Verificar se já existe pelo SKU
                $existing = $productModel->where('sku', $data['sku'])->first();

                if ($existing) {
                    if ($update) {
                        if (!$dryRun) {
                            $productModel->update($existing['id'], $data);
                        }
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    if (!$dryRun) {
                        $productId = $productModel->insert($data);

                        // Associar categoria
                        if ($productId && $data['category_id']) {
                            $db->table('product_categories')->insert([
                                'product_id' => $productId,
                                'category_id' => $data['category_id'],
                            ]);
                        }
                    }
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors++;
                log_message('error', "Erro ao importar produto {$product['id']}: " . $e->getMessage());
            }
        }

        CLI::showProgress($count, $count);
        CLI::newLine(2);

        // Resumo
        CLI::write('=== RESUMO DA IMPORTAÇÃO ===', 'cyan');
        CLI::write("Importados: {$imported}", 'green');
        CLI::write("Atualizados: {$updated}", 'yellow');
        CLI::write("Ignorados: {$skipped}", 'white');
        CLI::write("Erros: {$errors}", 'red');
        CLI::newLine();

        if ($dryRun) {
            CLI::write('*** Nenhum dado foi salvo (dry-run) ***', 'yellow');
        }
    }

    protected function getSourceDb()
    {
        return \Config\Database::connect([
            'DSN'      => '',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'produtos_paraguai',
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8mb4',
            'DBCollat' => 'utf8mb4_unicode_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3800,
        ]);
    }

    protected function loadMappings()
    {
        $db = \Config\Database::connect();

        // Carregar categorias
        $categories = $db->table('categories')->get()->getResultArray();
        foreach ($categories as $cat) {
            $this->categoryMap[strtolower($cat['slug'])] = $cat['id'];
            $this->categoryMap[strtolower($cat['name'])] = $cat['id'];
        }

        // Carregar marcas
        $brands = $db->table('brands')->get()->getResultArray();
        foreach ($brands as $brand) {
            $this->brandMap[strtolower($brand['slug'])] = $brand['id'];
            $this->brandMap[strtolower($brand['name'])] = $brand['id'];
        }

        CLI::write("Carregadas " . count($categories) . " categorias e " . count($brands) . " marcas", 'white');
    }

    protected function mapProduct(array $source): ?array
    {
        // Ignorar produtos sem nome ou preço
        if (empty($source['nome']) || empty($source['preco_brl']) || $source['preco_brl'] <= 0) {
            return null;
        }

        $name = trim($source['nome']);
        $slug = $this->generateSlug($name);
        $sku = $source['codigo_produto'] ?: 'IMP-' . $source['id'];

        // Detectar marca
        $brandId = $this->detectBrand($name);

        // Mapear categoria
        $categoryId = $this->mapCategory($source['categoria_slug'], $source['categoria_pai'], $source['categoria_avo']);

        // Calcular preço de venda (margem de 30% sobre o custo)
        $costPrice = (float) $source['preco_brl'];
        $salePrice = round($costPrice * 1.30, 2); // 30% de margem

        // Preparar descrição
        $description = $source['descricao'] ?: '';
        if (!empty($source['especificacoes'])) {
            $description .= "\n\n<h4>Especificações</h4>\n" . $source['especificacoes'];
        }

        // Tags
        $tags = [];
        if (!empty($source['fonte'])) {
            $tags[] = $source['fonte'];
        }
        if (!empty($source['categoria_slug'])) {
            $tags[] = $source['categoria_slug'];
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'sku' => $sku,
            'codigo_produto' => $source['codigo_produto'] ?? null,
            'description' => $description,
            'short_description' => mb_substr(strip_tags($description), 0, 300),
            'price' => $salePrice,
            'sale_price' => null,
            'cost_price' => $costPrice,
            'preco_usd' => (float) ($source['preco_usd'] ?? 0),
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'featured_image' => $source['imagem_url'] ?: null,
            'weight' => 0.5, // Peso padrão
            'width' => 20,   // Dimensões padrão
            'height' => 10,
            'length' => 30,
            'stock' => 10,   // Estoque inicial
            'stock_min' => 2,
            'manage_stock' => 1,
            'allow_backorder' => 0,
            'has_variations' => 0,
            'tags' => implode(',', $tags),
            'is_featured' => 0,
            'is_new' => 1,
            'is_bestseller' => 0,
            'views' => 0,
            'sales_count' => 0,
            'rating_average' => 0,
            'rating_count' => 0,
            'sort_order' => 0,
            'status' => 'active',
        ];
    }

    protected function detectBrand(string $name): ?int
    {
        $nameLower = strtolower($name);

        foreach ($this->brandKeywords as $keyword => $brandName) {
            if (strpos($nameLower, $keyword) !== false) {
                $brandSlug = strtolower($brandName);
                if (isset($this->brandMap[$brandSlug])) {
                    return $this->brandMap[$brandSlug];
                }
            }
        }

        return null;
    }

    protected function mapCategory(?string $slug, ?string $parent, ?string $grandparent): ?int
    {
        // Tentar mapear pela slug
        if ($slug) {
            $slugLower = strtolower($slug);

            // Verificar mapeamento direto
            if (isset($this->slugToCategoryMap[$slugLower])) {
                $catName = strtolower($this->slugToCategoryMap[$slugLower]);
                if (isset($this->categoryMap[$catName])) {
                    return $this->categoryMap[$catName];
                }
            }

            // Verificar se existe categoria com esse slug
            if (isset($this->categoryMap[$slugLower])) {
                return $this->categoryMap[$slugLower];
            }
        }

        // Tentar pela categoria pai
        if ($parent) {
            $parentLower = strtolower($parent);
            if (isset($this->categoryMap[$parentLower])) {
                return $this->categoryMap[$parentLower];
            }
        }

        // Categoria padrão: Eletrônicos
        return $this->categoryMap['eletronicos'] ?? 1;
    }

    protected function generateSlug(string $name): string
    {
        $slug = mb_strtolower($name);

        // Remover acentos
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);

        // Substituir caracteres especiais por hífen
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remover hífens duplicados e das extremidades
        $slug = trim(preg_replace('/-+/', '-', $slug), '-');

        // Limitar tamanho
        $slug = substr($slug, 0, 180);

        // Adicionar sufixo único para evitar duplicatas
        $slug .= '-' . substr(md5($name . time()), 0, 6);

        return $slug;
    }
}
