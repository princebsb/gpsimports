<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================================================
// FRONTEND ROUTES
// ============================================================================

// Sitemap e Robots
$routes->get('sitemap.xml', '\App\Controllers\SitemapController::index');
$routes->get('sitemap', '\App\Controllers\SitemapController::index');
$routes->get('robots.txt', '\App\Controllers\SitemapController::robots');

// Feed Google Merchant Center
$routes->get('feed/google-merchant.xml', '\App\Controllers\FeedController::googleMerchant');
$routes->get('feed/google', '\App\Controllers\FeedController::googleMerchant');
$routes->get('products.xml', '\App\Controllers\FeedController::googleMerchant');

$routes->group('', ['namespace' => 'App\Controllers\Front'], static function ($routes) {
    // Home
    $routes->get('/', 'HomeController::index');

    // Products
    $routes->get('produtos', 'ProductController::index');
    $routes->get('produto/(:segment)', 'ProductController::show/$1');
    $routes->get('categoria/(:segment)', 'CategoryController::show/$1');
    $routes->get('marca/(:segment)', 'ProductController::byBrand/$1');
    $routes->get('promocoes', 'ProductController::promotions');
    $routes->get('lancamentos', 'ProductController::newArrivals');
    $routes->get('busca', 'SearchController::index');
    $routes->get('busca/autocomplete', 'SearchController::autocomplete');
    $routes->post('produto/calcular-frete', 'ProductController::calculateShipping');
    $routes->get('produtos/marcas-por-categoria', 'ProductController::brandsByCategory');

    // Tracking
    $routes->get('rastrear-pedido', 'TrackingController::index');

    // Cart
    $routes->get('carrinho', 'CartController::index');
    $routes->get('carrinho/mini', 'CartController::mini');
    $routes->post('carrinho/adicionar', 'CartController::add');
    $routes->post('carrinho/atualizar', 'CartController::update');
    $routes->post('carrinho/remover', 'CartController::remove');
    $routes->post('carrinho/cupom', 'CartController::applyCoupon');
    $routes->post('carrinho/remover-cupom', 'CartController::removeCoupon');
    $routes->post('carrinho/calcular-frete', 'CartController::calculateShipping');
    $routes->post('carrinho/selecionar-frete', 'CartController::selectShipping');

    // Checkout
    $routes->get('checkout', 'CheckoutController::index');
    $routes->post('checkout/processar', 'CheckoutController::process');
    $routes->get('checkout/sucesso/(:segment)', 'CheckoutController::success/$1');
    $routes->get('checkout/falha/(:segment)', 'CheckoutController::failure/$1');
    $routes->get('checkout/pendente/(:segment)', 'CheckoutController::pending/$1');

    // Customer Auth
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('cadastro', 'AuthController::register');
    $routes->post('cadastro', 'AuthController::attemptRegister');
    $routes->get('sair', 'AuthController::logout');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('esqueci-senha', 'AuthController::forgotPassword');
    $routes->post('esqueci-senha', 'AuthController::sendResetLink');
    $routes->get('redefinir-senha/(:segment)', 'AuthController::resetPassword/$1');
    $routes->post('redefinir-senha', 'AuthController::attemptReset');

    // Customer Area
    $routes->group('minha-conta', ['filter' => 'auth'], static function ($routes) {
        $routes->get('/', 'CustomerController::dashboard');
        $routes->get('pedidos', 'CustomerController::orders');
        $routes->get('pedidos/(:segment)', 'CustomerController::orderDetail/$1');
        $routes->get('enderecos', 'CustomerController::addresses');
        $routes->post('enderecos/salvar', 'CustomerController::saveAddress');
        $routes->post('enderecos/excluir/(:num)', 'CustomerController::deleteAddress/$1');
        $routes->get('dados', 'CustomerController::profile');
        $routes->post('dados', 'CustomerController::updateProfile');
        $routes->get('senha', 'CustomerController::password');
        $routes->post('senha', 'CustomerController::updatePassword');
        $routes->get('favoritos', 'CustomerController::wishlist');
        $routes->post('favoritos/adicionar/(:num)', 'CustomerController::addToWishlist/$1');
        $routes->post('favoritos/remover/(:num)', 'CustomerController::removeFromWishlist/$1');

        // LGPD
        $routes->get('exportar-dados', 'CustomerController::exportData');
        $routes->post('excluir-conta', 'CustomerController::deleteAccount');
    });

    // Newsletter
    $routes->post('newsletter/assinar', 'HomeController::subscribeNewsletter');

    // Static Pages
    $routes->get('sobre', 'HomeController::about');
    $routes->get('contato', 'HomeController::contact');
    $routes->post('contato', 'HomeController::sendContact');
    $routes->get('politica-privacidade', 'HomeController::privacy');
    $routes->get('termos-uso', 'HomeController::terms');
    $routes->get('trocas-devolucoes', 'HomeController::returns');
    $routes->get('como-comprar', 'HomeController::howToBuy');
    $routes->get('formas-pagamento', 'HomeController::paymentMethods');
    $routes->get('frete-entrega', 'HomeController::shipping');
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // Admin Auth
    $routes->get('login', 'DashboardController::login');
    $routes->post('login', 'DashboardController::attemptLogin');
    $routes->get('logout', 'DashboardController::logout');

    // Protected Admin Routes
    $routes->group('', ['filter' => 'admin'], static function ($routes) {
        // Dashboard
        $routes->get('/', 'DashboardController::index');
        $routes->get('dashboard', 'DashboardController::index');

        // Profile
        $routes->get('perfil', 'DashboardController::profile');
        $routes->post('perfil', 'DashboardController::updateProfile');

        // Products
        $routes->get('produtos', 'ProductController::index');
        $routes->get('produtos/criar', 'ProductController::create');
        $routes->get('produtos/novo', 'ProductController::create');
        $routes->post('produtos/salvar', 'ProductController::store');
        $routes->get('produtos/editar/(:num)', 'ProductController::edit/$1');
        $routes->get('produtos/(:num)/editar', 'ProductController::edit/$1');
        $routes->post('produtos/atualizar/(:num)', 'ProductController::update/$1');
        $routes->post('produtos/(:num)/atualizar', 'ProductController::update/$1');
        $routes->post('produtos/excluir/(:num)', 'ProductController::delete/$1');
        $routes->post('produtos/(:num)/excluir', 'ProductController::delete/$1');
        $routes->post('produtos/(:num)/duplicar', 'ProductController::duplicate/$1');
        $routes->post('produtos/status/(:num)', 'ProductController::toggleStatus/$1');
        $routes->post('produtos/duplicar/(:num)', 'ProductController::duplicate/$1');
        $routes->get('produtos/exportar', 'ProductController::export');
        $routes->post('produtos/importar', 'ProductController::import');
        $routes->post('produtos/upload-imagem', 'ProductController::uploadImage');
        $routes->post('produtos/remover-imagem/(:num)', 'ProductController::removeImage/$1');
        $routes->post('produtos/ordenar-imagens', 'ProductController::sortImages');

        // Categories
        $routes->get('categorias', 'CategoryController::index');
        $routes->get('categorias/criar', 'CategoryController::create');
        $routes->post('categorias/salvar', 'CategoryController::store');
        $routes->get('categorias/editar/(:num)', 'CategoryController::edit/$1');
        $routes->post('categorias/atualizar/(:num)', 'CategoryController::update/$1');
        $routes->post('categorias/excluir/(:num)', 'CategoryController::delete/$1');
        $routes->post('categorias/ordenar', 'CategoryController::sort');

        // Brands
        $routes->get('marcas', 'CategoryController::brands');
        $routes->get('marcas/criar', 'CategoryController::createBrand');
        $routes->post('marcas/salvar', 'CategoryController::storeBrand');
        $routes->get('marcas/editar/(:num)', 'CategoryController::editBrand/$1');
        $routes->post('marcas/atualizar/(:num)', 'CategoryController::updateBrand/$1');
        $routes->post('marcas/excluir/(:num)', 'CategoryController::deleteBrand/$1');

        // Orders
        $routes->get('pedidos', 'OrderController::index');
        $routes->get('pedidos/(:num)', 'OrderController::show/$1');
        $routes->post('pedidos/status/(:num)', 'OrderController::updateStatus/$1');
        $routes->post('pedidos/(:num)/status', 'OrderController::updateStatus/$1');
        $routes->get('pedidos/(:num)/status/(:segment)', 'OrderController::changeStatus/$1/$2');
        $routes->post('pedidos/rastreio/(:num)', 'OrderController::addTracking/$1');
        $routes->get('pedidos/imprimir/(:num)', 'OrderController::print/$1');
        $routes->get('pedidos/exportar', 'OrderController::export');

        // Melhor Envio - Etiquetas
        $routes->get('pedidos/(:num)/cotar-frete', 'OrderController::quotarFrete/$1');
        $routes->post('pedidos/(:num)/gerar-etiqueta', 'OrderController::gerarEtiqueta/$1');
        $routes->get('pedidos/(:num)/imprimir-etiqueta', 'OrderController::imprimirEtiqueta/$1');
        $routes->get('pedidos/(:num)/rastrear-etiqueta', 'OrderController::rastrearEtiqueta/$1');

        // Melhor Envio - Creditos
        $routes->post('melhor-envio/adicionar-credito', 'OrderController::adicionarCreditoME');

        // Customers
        $routes->get('clientes', 'CustomerController::index');
        $routes->get('clientes/(:num)', 'CustomerController::show/$1');
        $routes->post('clientes/status/(:num)', 'CustomerController::toggleStatus/$1');
        $routes->get('clientes/exportar', 'CustomerController::export');

        // Stock
        $routes->get('estoque', 'StockController::index');
        $routes->post('estoque/ajustar', 'StockController::adjust');
        $routes->get('estoque/movimentacoes', 'StockController::movements');
        $routes->get('estoque/alertas', 'StockController::alerts');

        // Coupons
        $routes->get('cupons', 'CouponController::index');
        $routes->get('cupons/criar', 'CouponController::create');
        $routes->post('cupons/salvar', 'CouponController::store');
        $routes->get('cupons/editar/(:num)', 'CouponController::edit/$1');
        $routes->post('cupons/atualizar/(:num)', 'CouponController::update/$1');
        $routes->post('cupons/excluir/(:num)', 'CouponController::delete/$1');

        // Banners
        $routes->get('banners', 'BannerController::index');
        $routes->get('banners/criar', 'BannerController::create');
        $routes->post('banners/salvar', 'BannerController::store');
        $routes->get('banners/editar/(:num)', 'BannerController::edit/$1');
        $routes->post('banners/atualizar/(:num)', 'BannerController::update/$1');
        $routes->post('banners/excluir/(:num)', 'BannerController::delete/$1');
        $routes->post('banners/ordenar', 'BannerController::sort');

        // Reports
        $routes->get('relatorios', 'ReportController::index');
        $routes->get('relatorios/vendas', 'ReportController::sales');
        $routes->get('relatorios/produtos', 'ReportController::products');
        $routes->get('relatorios/clientes', 'ReportController::customers');
        $routes->get('relatorios/exportar/(:segment)', 'ReportController::export/$1');

        // Settings
        $routes->get('configuracoes', 'SettingsController::index');
        $routes->post('configuracoes/salvar', 'SettingsController::save');
        $routes->get('configuracoes/loja', 'SettingsController::store');
        $routes->post('configuracoes/loja', 'SettingsController::saveStore');
        $routes->get('configuracoes/pagamento', 'SettingsController::payment');
        $routes->post('configuracoes/pagamento', 'SettingsController::savePayment');
        $routes->get('configuracoes/frete', 'SettingsController::shipping');
        $routes->post('configuracoes/frete', 'SettingsController::saveShipping');
        $routes->get('configuracoes/email', 'SettingsController::email');
        $routes->post('configuracoes/email', 'SettingsController::saveEmail');
        $routes->post('configuracoes/email/testar', 'SettingsController::testEmail');

        // Users (Admin)
        $routes->get('usuarios', 'UserController::index');
        $routes->get('usuarios/criar', 'UserController::create');
        $routes->post('usuarios/salvar', 'UserController::store');
        $routes->get('usuarios/editar/(:num)', 'UserController::edit/$1');
        $routes->post('usuarios/atualizar/(:num)', 'UserController::update/$1');
        $routes->post('usuarios/excluir/(:num)', 'UserController::delete/$1');

        // Marketing
        $routes->get('marketing/newsletter', 'MarketingController::newsletter');
        $routes->get('marketing/newsletter/exportar', 'MarketingController::exportNewsletter');
        $routes->get('newsletter', 'MarketingController::newsletter');
        $routes->get('newsletter/exportar', 'MarketingController::exportNewsletter');
    });
});

// ============================================================================
// MELHOR ENVIO ROUTES
// ============================================================================

$routes->group('melhor-envio', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->get('autorizar', 'MelhorEnvioController::authorize');
    $routes->get('callback', 'MelhorEnvioController::callback');
    $routes->get('desconectar', 'MelhorEnvioController::disconnect');
});

// ============================================================================
// WEBHOOK ROUTES
// ============================================================================

$routes->group('webhook', ['namespace' => 'App\Controllers'], static function ($routes) {
    $routes->post('mercadopago', 'WebhookController::mercadopago');
    $routes->get('mercadopago', 'WebhookController::mercadopago'); // MP pode enviar GET tambem
});

// ============================================================================
// API ROUTES
// ============================================================================

$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], static function ($routes) {
    // Auth
    $routes->post('auth/login', 'AuthController::login');
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/refresh', 'AuthController::refresh');
    $routes->post('auth/forgot-password', 'AuthController::forgotPassword');

    // Public
    $routes->get('products', 'ProductController::index');
    $routes->get('products/(:segment)', 'ProductController::show/$1');
    $routes->get('products/category/(:segment)', 'ProductController::byCategory/$1');
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/(:segment)', 'CategoryController::show/$1');
    $routes->get('search', 'ProductController::search');

    // Protected
    $routes->group('', ['filter' => 'apiAuth'], static function ($routes) {
        // Cart
        $routes->get('cart', 'CartController::index');
        $routes->post('cart/add', 'CartController::add');
        $routes->put('cart/update', 'CartController::update');
        $routes->delete('cart/remove/(:num)', 'CartController::remove/$1');
        $routes->post('cart/coupon', 'CartController::applyCoupon');
        $routes->delete('cart/coupon', 'CartController::removeCoupon');
        $routes->post('cart/shipping', 'CartController::calculateShipping');

        // Orders
        $routes->get('orders', 'OrderController::index');
        $routes->get('orders/(:segment)', 'OrderController::show/$1');
        $routes->post('orders', 'OrderController::store');

        // Customer
        $routes->get('customer/profile', 'CustomerController::profile');
        $routes->put('customer/profile', 'CustomerController::updateProfile');
        $routes->get('customer/addresses', 'CustomerController::addresses');
        $routes->post('customer/addresses', 'CustomerController::addAddress');
        $routes->put('customer/addresses/(:num)', 'CustomerController::updateAddress/$1');
        $routes->delete('customer/addresses/(:num)', 'CustomerController::deleteAddress/$1');

        // Wishlist
        $routes->get('wishlist', 'CustomerController::wishlist');
        $routes->post('wishlist/(:num)', 'CustomerController::addToWishlist/$1');
        $routes->delete('wishlist/(:num)', 'CustomerController::removeFromWishlist/$1');

        // Stock
        $routes->get('stock/(:num)', 'StockController::check/$1');
    });

    // Webhooks
    $routes->post('webhooks/mercadopago', 'OrderController::mercadoPagoWebhook');
    $routes->post('webhooks/pagseguro', 'OrderController::pagseguroWebhook');
});
