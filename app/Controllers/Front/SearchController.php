<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class SearchController extends BaseController
{
    public function index()
    {
        $term = $this->request->getGet('q');

        if (empty($term)) {
            return redirect()->to('/produtos');
        }

        $filters = [
            'search' => $term,
            'sort' => $this->request->getGet('ordenar') ?? 'relevance',
        ];

        $productService = service('product');
        $result = $productService->getFilteredProducts($filters, 20);

        // Log search
        $this->logSearch($term, count($result['products']));

        return view('front/search/index', [
            'title' => 'Busca: ' . $term,
            'term' => $term,
            'products' => $result['products'],
            'pager' => $result['pager'],
            'filters' => $filters,
        ]);
    }

    public function autocomplete()
    {
        $term = $this->request->getGet('q');

        if (empty($term) || strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $products = service('product')->search($term, 8);

        $results = array_map(function ($product) {
            // Verificar se a imagem é URL externa ou local
            $image = '';
            if (!empty($product['featured_image'])) {
                if (strpos($product['featured_image'], 'http') === 0) {
                    $image = $product['featured_image'];
                } else {
                    $image = base_url('uploads/products/' . $product['featured_image']);
                }
            } else {
                $image = 'https://placehold.co/80x80/e9ecef/495057?text=Sem+Imagem';
            }

            return [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'price' => $product['current_price'],
                'price_formatted' => 'R$ ' . number_format($product['current_price'] ?? 0, 2, ',', '.'),
                'image' => $image,
            ];
        }, $products);

        return $this->response->setJSON($results);
    }

    protected function logSearch(string $term, int $resultsCount): void
    {
        $db = \Config\Database::connect();

        // Log individual search
        $db->table('search_history')->insert([
            'term' => $term,
            'results_count' => $resultsCount,
            'customer_id' => session()->get('customer_id'),
            'session_id' => session()->session_id,
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Update popular searches
        $existing = $db->table('popular_searches')
            ->where('term', strtolower($term))
            ->get()
            ->getRowArray();

        if ($existing) {
            $db->table('popular_searches')
                ->where('id', $existing['id'])
                ->update([
                    'search_count' => $existing['search_count'] + 1,
                    'last_searched_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $db->table('popular_searches')->insert([
                'term' => strtolower($term),
                'search_count' => 1,
                'last_searched_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
