<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $meta_description ?? setting('meta_description') ?? 'Loja online com os melhores produtos' ?>">
    <meta name="keywords" content="<?= $meta_keywords ?? setting('meta_keywords') ?? '' ?>">

    <title><?= $title ?? '' ?><?= $title ? ' - ' : '' ?><?= setting('store_name') ?? 'GPS Imports' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $title ?? setting('store_name') ?>">
    <meta property="og:description" content="<?= $meta_description ?? setting('meta_description') ?? '' ?>">
    <meta property="og:image" content="<?= $og_image ?? base_url('assets/images/og-image.jpg') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:type" content="website">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Swiper -->
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: <?= setting('primary_color') ?? '#2563eb' ?>;
            --secondary-color: <?= setting('secondary_color') ?? '#1e293b' ?>;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1e293b;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
        }

        a:hover {
            color: var(--secondary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        /* Top Bar */
        .top-bar {
            background: var(--secondary-color);
            color: #fff;
            font-size: 0.875rem;
            padding: 0.5rem 0;
        }

        .top-bar a {
            color: #cbd5e1;
        }

        .top-bar a:hover {
            color: #fff;
        }

        /* Header */
        .main-header {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            max-height: 50px;
        }

        .search-form {
            max-width: 500px;
        }

        .search-form .form-control {
            border-radius: 0.5rem 0 0 0.5rem;
            border-right: 0;
        }

        .search-form .btn {
            border-radius: 0 0.5rem 0.5rem 0;
        }

        .header-icons .nav-link {
            color: var(--secondary-color);
            padding: 0.5rem 1rem;
        }

        .header-icons .nav-link:hover {
            color: var(--primary-color);
        }

        .cart-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--primary-color);
            color: #fff;
            font-size: 0.7rem;
            padding: 0.15rem 0.4rem;
            border-radius: 50%;
        }

        /* Navigation */
        .main-nav {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .main-nav .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1rem;
        }

        .main-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
        }

        /* Product Card */
        .product-card {
            background: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }

        .product-card:hover {
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .product-card .product-image {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
        }

        .product-card .product-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-card .product-badges {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .product-card .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .product-card .product-actions {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .product-card:hover .product-actions {
            opacity: 1;
        }

        .product-card .product-actions .btn {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .product-card .product-info {
            padding: 1rem;
        }

        .product-card .product-category {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .product-card .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin: 0.25rem 0 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .product-price {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .product-card .current-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-card .old-price {
            font-size: 0.875rem;
            color: #94a3b8;
            text-decoration: line-through;
        }

        .product-card .installments {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Section Title */
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            margin-top: 0.5rem;
        }

        /* Banner */
        .banner-slide {
            border-radius: 1rem;
            overflow: hidden;
        }

        .banner-slide img {
            width: 100%;
            height: auto;
        }

        /* Categories */
        .category-card {
            text-align: center;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            transition: all 0.3s;
        }

        .category-card:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .category-card:hover .category-icon {
            background: #fff;
            color: var(--primary-color);
        }

        .category-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.5rem;
            transition: all 0.3s;
        }

        /* Footer */
        .main-footer {
            background: var(--secondary-color);
            color: #cbd5e1;
            padding: 3rem 0 1rem;
        }

        .main-footer h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .main-footer a {
            color: #cbd5e1;
        }

        .main-footer a:hover {
            color: #fff;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
        }

        .payment-icons img {
            height: 24px;
            margin-right: 0.5rem;
        }

        /* Breadcrumb */
        .breadcrumb-wrapper {
            background: #f8fafc;
            padding: 0.75rem 0;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        /* WhatsApp Button */
        .whatsapp-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #25D366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            z-index: 999;
            transition: transform 0.3s;
        }

        .whatsapp-button:hover {
            transform: scale(1.1);
            color: #fff;
        }

        /* Search Autocomplete */
        .autocomplete-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 0 0 0.5rem 0.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }

        .autocomplete-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .autocomplete-item:hover {
            background: #f8fafc;
        }

        .autocomplete-item img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 0.25rem;
            margin-right: 0.75rem;
        }

        /* Mobile Menu */
        @media (max-width: 991.98px) {
            .search-form {
                width: 100%;
                max-width: none;
            }

            .header-icons {
                flex-wrap: nowrap;
            }

            .header-icons .nav-link {
                padding: 0.5rem;
            }
        }

        /* Cart Offcanvas */
        #cartOffcanvas {
            width: 380px;
        }

        @media (max-width: 575.98px) {
            #cartOffcanvas {
                width: 100%;
            }
        }

        .cart-offcanvas-item {
            display: flex;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .cart-offcanvas-item:last-child {
            border-bottom: none;
        }

        .cart-offcanvas-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .cart-offcanvas-item .item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-offcanvas-item .item-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--secondary-color);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cart-offcanvas-item .item-price {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .cart-offcanvas-item .item-qty {
            font-size: 0.8rem;
            color: #64748b;
        }

        .cart-offcanvas-item .btn-remove {
            color: #94a3b8;
            padding: 0.25rem;
            line-height: 1;
        }

        .cart-offcanvas-item .btn-remove:hover {
            color: #ef4444;
        }

        /* Dropdown Submenu */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu > .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            display: none;
        }

        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-submenu > a.dropdown-toggle::after {
            float: right;
            margin-top: 0.5rem;
            border-left: 0.3em solid;
            border-top: 0.3em solid transparent;
            border-bottom: 0.3em solid transparent;
            border-right: 0;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6">
                    <i class="bi bi-headset me-1"></i> Atendimento: Seg a Sex 9h as 18h
                </div>
                <div class="col-6 text-end">
                    <a href="<?= base_url('rastrear-pedido') ?>" class="me-3"><i class="bi bi-geo-alt me-1"></i>Rastrear Pedido</a>
                    <a href="https://wa.me/<?= setting('store_whatsapp') ?>" target="_blank"><i class="bi bi-whatsapp me-1"></i><?= format_phone(setting('store_whatsapp') ?? '') ?: '(11) 99999-9999' ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header py-3">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-6 col-lg-2">
                    <a href="<?= base_url() ?>" class="logo">
                        <?php if (setting('logo')): ?>
                            <img src="<?= base_url('uploads/settings/' . setting('logo')) ?>" alt="<?= setting('store_name') ?>">
                        <?php else: ?>
                            <h4 class="mb-0 fw-bold text-primary"><?= setting('store_name') ?? 'GPS Imports' ?></h4>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Search -->
                <div class="col-lg-6 d-none d-lg-block">
                    <form action="<?= base_url('busca') ?>" method="get" class="search-form position-relative" id="searchForm">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="O que voce procura?" id="searchInput" autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="autocomplete-results" id="autocompleteResults"></div>
                    </form>
                </div>

                <!-- Icons -->
                <div class="col-6 col-lg-4">
                    <ul class="nav header-icons justify-content-end">
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="bi bi-search fs-5"></i>
                            </a>
                        </li>
                        <?php if (session()->get('customer_logged_in')): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-person fs-5"></i>
                                    <span class="d-none d-lg-inline ms-1">Ola, <?= esc(session()->get('customer_first_name')) ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= base_url('minha-conta') ?>"><i class="bi bi-speedometer2 me-2"></i>Minha Conta</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('minha-conta/pedidos') ?>"><i class="bi bi-box me-2"></i>Meus Pedidos</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('minha-conta/favoritos') ?>"><i class="bi bi-heart me-2"></i>Favoritos</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('login') ?>">
                                    <i class="bi bi-person fs-5"></i>
                                    <span class="d-none d-lg-inline ms-1">Entrar</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?= base_url('carrinho') ?>" id="cartLink">
                                <i class="bi bi-cart3 fs-5"></i>
                                <span class="cart-badge" id="cartCount"><?= $cartCount ?? 0 ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav d-none d-lg-block">
        <div class="container">
            <ul class="nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-list me-1"></i>Categorias
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach (($menuCategories ?? []) as $category): ?>
                            <?php if (!empty($category['children'])): ?>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="<?= base_url('categoria/' . $category['slug']) ?>">
                                        <?= esc($category['name']) ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($category['children'] as $child): ?>
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('categoria/' . $child['slug']) ?>">
                                                    <?= esc($child['name']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('categoria/' . $category['slug']) ?>">
                                        <?= esc($category['name']) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('produtos') ?>">Todos os Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('promocoes') ?>">Promocoes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('lancamentos') ?>">Lancamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('contato') ?>">Contato</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <nav class="navbar navbar-expand-lg d-lg-none bg-white border-bottom">
        <div class="container">
            <button class="navbar-toggler border-0 ps-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="bi bi-list fs-4"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Offcanvas -->
    <div class="offcanvas offcanvas-start" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url() ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('produtos') ?>">Todos os Produtos</a>
                </li>
                <?php foreach (($categories ?? []) as $category): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('categoria/' . $category['slug']) ?>"><?= esc($category['name']) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('promocoes') ?>">Promocoes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('contato') ?>">Contato</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Breadcrumb -->
    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
        <div class="breadcrumb-wrapper">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                        <?php foreach ($breadcrumb as $item): ?>
                            <?php
                            // Support both 'label'/'url' and 'name'/'slug' formats
                            $label = $item['label'] ?? $item['name'] ?? '';
                            $url = $item['url'] ?? (isset($item['slug']) ? base_url('categoria/' . $item['slug']) : null);
                            $isActive = $item['active'] ?? (!$url);
                            ?>
                            <?php if ($url && !$isActive): ?>
                                <li class="breadcrumb-item"><a href="<?= $url ?>"><?= esc($label) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?= esc($label) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>
        </div>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><?= setting('store_name') ?? 'GPS Imports' ?></h5>
                    <p><?= setting('about_short') ?? 'Sua loja online de confianca.' ?></p>
                    <div class="social-icons">
                        <?php if (setting('facebook')): ?>
                            <a href="<?= setting('facebook') ?>" target="_blank" class="me-2"><i class="bi bi-facebook fs-5"></i></a>
                        <?php endif; ?>
                        <?php if (setting('instagram')): ?>
                            <a href="<?= setting('instagram') ?>" target="_blank" class="me-2"><i class="bi bi-instagram fs-5"></i></a>
                        <?php endif; ?>
                        <?php if (setting('youtube')): ?>
                            <a href="<?= setting('youtube') ?>" target="_blank" class="me-2"><i class="bi bi-youtube fs-5"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-6 col-lg-2 mb-4">
                    <h5>Institucional</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('sobre') ?>">Sobre Nos</a></li>
                        <li><a href="<?= base_url('contato') ?>">Contato</a></li>
                        <li><a href="<?= base_url('politica-privacidade') ?>">Privacidade</a></li>
                        <li><a href="<?= base_url('termos-uso') ?>">Termos de Uso</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2 mb-4">
                    <h5>Ajuda</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('como-comprar') ?>">Como Comprar</a></li>
                        <li><a href="<?= base_url('formas-pagamento') ?>">Pagamento</a></li>
                        <li><a href="<?= base_url('frete-entrega') ?>">Frete e Entrega</a></li>
                        <li><a href="<?= base_url('trocas-devolucoes') ?>">Trocas</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contato</h5>
                    <ul class="list-unstyled">
                        <?php if (setting('store_email')): ?>
                            <li><i class="bi bi-envelope me-2"></i><?= setting('store_email') ?></li>
                        <?php endif; ?>
                        <?php if (setting('store_whatsapp')): ?>
                            <li><i class="bi bi-whatsapp me-2"></i>
                                <a href="https://wa.me/<?= setting('store_whatsapp') ?>" target="_blank" class="text-decoration-none">
                                    <?= format_phone(setting('store_whatsapp') ?? '') ?: setting('store_whatsapp') ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if (setting('store_address')): ?>
                        <p class="small mt-3 mb-1">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?= setting('store_address') ?><br>
                            <?= setting('store_neighborhood') ?> - <?= setting('store_city') ?>/<?= setting('store_state') ?><br>
                            CEP: <?= setting('store_zipcode') ?>
                        </p>
                    <?php endif; ?>
                    <p class="small mt-3 mb-0">
                        <strong><?= setting('store_razao_social') ?></strong><br>
                        CNPJ: <?= setting('store_cnpj') ?>
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <small>&copy; <?= date('Y') ?> <?= setting('store_name') ?? 'GPS Imports' ?>. Todos os direitos reservados.</small>
                    </div>
                    <div class="col-lg-6 text-lg-end">
                        <div class="payment-icons">
                            <img src="<?= base_url('assets/images/payments/visa.svg') ?>" alt="Visa">
                            <img src="<?= base_url('assets/images/payments/mastercard.svg') ?>" alt="Mastercard">
                            <img src="<?= base_url('assets/images/payments/pix.svg') ?>" alt="PIX">
                            <img src="<?= base_url('assets/images/payments/boleto.svg') ?>" alt="Boleto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <?php if (setting('store_whatsapp')): ?>
        <a href="https://wa.me/<?= setting('store_whatsapp') ?>" target="_blank" class="whatsapp-button">
            <i class="bi bi-whatsapp"></i>
        </a>
    <?php endif; ?>

    <!-- Cart Offcanvas (Mini Cart) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="cartOffcanvasLabel">
                <i class="bi bi-cart-check me-2"></i>Carrinho
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <!-- Added Item Notification -->
            <div class="alert alert-success m-3 mb-0 d-none" id="cartAddedAlert">
                <i class="bi bi-check-circle me-2"></i>
                <span id="cartAddedMessage">Produto adicionado ao carrinho!</span>
            </div>

            <!-- Cart Items -->
            <div class="flex-grow-1 overflow-auto p-3" id="cartOffcanvasItems">
                <div class="text-center py-4" id="cartOffcanvasEmpty">
                    <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Seu carrinho esta vazio</p>
                </div>
                <div id="cartOffcanvasContent" class="d-none">
                    <!-- Items will be loaded here via JS -->
                </div>
            </div>

            <!-- Cart Footer -->
            <div class="border-top p-3 bg-light" id="cartOffcanvasFooter">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <strong class="fs-5 text-primary" id="cartOffcanvasSubtotal">R$ 0,00</strong>
                </div>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('carrinho') ?>" class="btn btn-primary">
                        <i class="bi bi-cart3 me-2"></i>Ver Carrinho
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                        <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Modal (Mobile) -->
    <div class="modal fade" id="searchModal">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('busca') ?>" method="get">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="O que voce procura?" autofocus>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toastr config
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };

        // Search Autocomplete
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            const term = $(this).val();

            if (term.length < 2) {
                $('#autocompleteResults').hide();
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get('<?= base_url('busca/autocomplete') ?>', { q: term }, function(data) {
                    if (data.length > 0) {
                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="<?= base_url('produto/') ?>${item.slug}" class="autocomplete-item">
                                    <img src="${item.image}" alt="${item.name}">
                                    <div>
                                        <div class="fw-medium">${item.name}</div>
                                        <div class="text-primary">${item.price_formatted}</div>
                                    </div>
                                </a>
                            `;
                        });
                        $('#autocompleteResults').html(html).show();
                    } else {
                        $('#autocompleteResults').hide();
                    }
                });
            }, 300);
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#searchForm').length) {
                $('#autocompleteResults').hide();
            }
        });

        // Cart Offcanvas Instance
        let cartOffcanvas = null;
        document.addEventListener('DOMContentLoaded', function() {
            cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
        });

        // Add to Cart
        function addToCart(productId, quantity = 1, variationId = null) {
            $.ajax({
                url: '<?= base_url('carrinho/adicionar') ?>',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    product_id: productId,
                    quantity: quantity,
                    variation_id: variationId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#cartCount').text(response.cart_count);

                        // Show added message
                        $('#cartAddedMessage').text(response.message || 'Produto adicionado ao carrinho!');
                        $('#cartAddedAlert').removeClass('d-none');

                        // Load cart items and open offcanvas
                        loadCartOffcanvas();
                        cartOffcanvas.show();

                        // Hide alert after 3 seconds
                        setTimeout(function() {
                            $('#cartAddedAlert').addClass('d-none');
                        }, 3000);
                    } else {
                        toastr.error(response.message || 'Erro ao adicionar produto');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao adicionar ao carrinho:', error, xhr.responseText);
                    toastr.error('Erro ao adicionar produto ao carrinho');
                }
            });
        }

        // Load Cart Offcanvas Content
        function loadCartOffcanvas() {
            $.ajax({
                url: '<?= base_url('carrinho/mini') ?>',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success && response.items && response.items.length > 0) {
                        let html = '';
                        response.items.forEach(function(item) {
                            html += `
                                <div class="cart-offcanvas-item">
                                    <img src="${item.image}" alt="${item.name}">
                                    <div class="item-info">
                                        <div class="item-name">${item.name}</div>
                                        ${item.attributes ? '<small class="text-muted">' + item.attributes + '</small>' : ''}
                                        <div class="item-price">R$ ${item.price_formatted}</div>
                                        <div class="item-qty">Qtd: ${item.quantity}</div>
                                    </div>
                                    <button type="button" class="btn btn-link btn-remove" onclick="removeFromCartOffcanvas(${item.product_id}, ${item.variation_id || 'null'})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `;
                        });

                        $('#cartOffcanvasContent').html(html).removeClass('d-none');
                        $('#cartOffcanvasEmpty').addClass('d-none');
                        $('#cartOffcanvasSubtotal').text('R$ ' + response.subtotal_formatted);
                        $('#cartOffcanvasFooter').show();
                    } else {
                        $('#cartOffcanvasContent').addClass('d-none').html('');
                        $('#cartOffcanvasEmpty').removeClass('d-none');
                        $('#cartOffcanvasSubtotal').text('R$ 0,00');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar mini carrinho:', error);
                    $('#cartOffcanvasContent').addClass('d-none').html('');
                    $('#cartOffcanvasEmpty').removeClass('d-none');
                }
            });
        }

        // Remove from Cart (Offcanvas)
        function removeFromCartOffcanvas(productId, variationId) {
            $.ajax({
                url: '<?= base_url('carrinho/remover') ?>',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    product_id: productId,
                    variation_id: variationId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#cartCount').text(response.cart_count);
                        loadCartOffcanvas();
                        toastr.success('Produto removido');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao remover do carrinho:', error);
                }
            });
        }

        // Wishlist Toggle
        function toggleWishlist(productId, btn) {
            <?php if (!session()->get('customer_logged_in')): ?>
                window.location.href = '<?= base_url('login') ?>';
                return;
            <?php endif; ?>

            $.post('<?= base_url('minha-conta/favoritos/') ?>' + productId, {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(response) {
                if (response.in_wishlist) {
                    $(btn).find('i').removeClass('bi-heart').addClass('bi-heart-fill text-danger');
                    toastr.success('Adicionado aos favoritos');
                } else {
                    $(btn).find('i').removeClass('bi-heart-fill text-danger').addClass('bi-heart');
                    toastr.success('Removido dos favoritos');
                }
            });
        }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
