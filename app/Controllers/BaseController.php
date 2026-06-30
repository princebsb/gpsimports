<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * Helpers to be loaded automatically
     *
     * @var list<string>
     */
    protected $helpers = ['settings', 'form', 'url'];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');

        // Load global data for views
        $this->loadGlobalViewData();
    }

    /**
     * Load data that should be available in all views
     */
    protected function loadGlobalViewData(): void
    {
        $categoryModel = model('CategoryModel');
        $cartService = service('cart');

        // Menu categories (only parent categories with is_menu = 1)
        $allMenuCategories = $categoryModel->where('status', 'active')
                                            ->where('is_menu', 1)
                                            ->where('parent_id IS NULL')
                                            ->orderBy('sort_order')
                                            ->findAll();

        // Add subcategories and filter categories without products
        $db = \Config\Database::connect();
        $menuCategories = [];

        foreach ($allMenuCategories as $category) {
            // Get subcategories with products
            $children = $db->table('categories c')
                           ->select('c.*')
                           ->join('products p', 'p.category_id = c.id AND p.status = "active"', 'inner')
                           ->where('c.status', 'active')
                           ->where('c.parent_id', $category['id'])
                           ->groupBy('c.id')
                           ->orderBy('c.name')
                           ->get()
                           ->getResultArray();

            // Count products directly in this category
            $directProducts = $db->table('products')
                                 ->where('category_id', $category['id'])
                                 ->where('status', 'active')
                                 ->countAllResults();

            // Only include if has children with products OR has direct products
            if (!empty($children) || $directProducts > 0) {
                $category['children'] = $children;
                $menuCategories[] = $category;
            }
        }

        // Cart count
        $cartCount = 0;
        try {
            $cart = $cartService->getCurrentCart();
            $cartCount = array_sum(array_column($cart['items'] ?? [], 'quantity'));
        } catch (\Exception $e) {
            // Cart not available yet
        }

        // Set global view data
        $renderer = service('renderer');
        $renderer->setVar('menuCategories', $menuCategories);
        $renderer->setVar('cartCount', $cartCount);
    }
}
