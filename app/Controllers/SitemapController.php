<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SitemapController extends BaseController
{
    public function index()
    {
        $baseUrl = base_url();

        // Produtos
        $productModel = model('ProductModel');
        $products = $productModel->where('status', 'active')
                                 ->select('slug, updated_at')
                                 ->findAll();

        // Categorias
        $categoryModel = model('CategoryModel');
        $categories = $categoryModel->where('status', 'active')
                                    ->select('slug, updated_at')
                                    ->findAll();

        // Marcas
        $brandModel = model('BrandModel');
        $brands = [];
        if ($brandModel) {
            $brands = $brandModel->where('status', 'active')
                                 ->select('slug, updated_at')
                                 ->findAll() ?? [];
        }

        // Gerar XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Paginas estaticas
        $staticPages = [
            ['url' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => 'produtos', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => 'promocoes', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => 'lancamentos', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => 'sobre', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => 'contato', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => 'politica-privacidade', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => 'termos-uso', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => 'trocas-devolucoes', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $xml .= $this->buildUrlTag($baseUrl . $page['url'], date('Y-m-d'), $page['changefreq'], $page['priority']);
        }

        // Categorias
        foreach ($categories as $category) {
            $lastmod = $category['updated_at'] ?? date('Y-m-d');
            if ($lastmod) {
                $lastmod = date('Y-m-d', strtotime($lastmod));
            }
            $xml .= $this->buildUrlTag($baseUrl . 'categoria/' . $category['slug'], $lastmod, 'weekly', '0.8');
        }

        // Marcas
        foreach ($brands as $brand) {
            $lastmod = $brand['updated_at'] ?? date('Y-m-d');
            if ($lastmod) {
                $lastmod = date('Y-m-d', strtotime($lastmod));
            }
            $xml .= $this->buildUrlTag($baseUrl . 'marca/' . $brand['slug'], $lastmod, 'weekly', '0.7');
        }

        // Produtos
        foreach ($products as $product) {
            $lastmod = $product['updated_at'] ?? date('Y-m-d');
            if ($lastmod) {
                $lastmod = date('Y-m-d', strtotime($lastmod));
            }
            $xml .= $this->buildUrlTag($baseUrl . 'produto/' . $product['slug'], $lastmod, 'weekly', '0.8');
        }

        $xml .= '</urlset>';

        return $this->response
            ->setHeader('Content-Type', 'application/xml; charset=utf-8')
            ->setBody($xml);
    }

    protected function buildUrlTag(string $url, string $lastmod, string $changefreq, string $priority): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Robots.txt
     */
    public function robots()
    {
        $baseUrl = base_url();

        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /carrinho\n";
        $robots .= "Disallow: /checkout\n";
        $robots .= "Disallow: /minha-conta/\n";
        $robots .= "Disallow: /login\n";
        $robots .= "Disallow: /cadastro\n";
        $robots .= "\n";
        $robots .= "Sitemap: {$baseUrl}sitemap.xml\n";

        return $this->response
            ->setHeader('Content-Type', 'text/plain; charset=utf-8')
            ->setBody($robots);
    }
}
