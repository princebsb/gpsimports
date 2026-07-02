<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<!-- Banners Slider -->
<?php if (!empty($banners)): ?>
    <section class="py-4">
        <div class="container">
            <div class="swiper banner-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($banners as $banner): ?>
                        <div class="swiper-slide">
                            <a href="<?= $banner['link'] ?? '#' ?>" class="banner-slide d-block">
                                <?php
                                $bannerPath = FCPATH . 'uploads/banners/' . ($banner['image'] ?? '');
                                if (!empty($banner['image']) && file_exists($bannerPath)):
                                ?>
                                    <img src="<?= base_url('uploads/banners/' . $banner['image']) ?>" alt="<?= esc($banner['title']) ?>">
                                <?php else: ?>
                                    <img src="https://placehold.co/1200x400/3b82f6/ffffff?text=<?= urlencode($banner['title'] ?? 'Banner') ?>" alt="<?= esc($banner['title']) ?>" class="w-100 rounded">
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Categories -->
<?php if (!empty($categories)): ?>
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Categorias</h2>
            <div class="swiper categories-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($categories as $category): ?>
                        <div class="swiper-slide">
                            <a href="<?= base_url('categoria/' . $category['slug']) ?>" class="category-card d-block text-decoration-none text-center">
                                <?php
                                $catImagePath = FCPATH . 'uploads/categories/' . ($category['image'] ?? '');
                                if (!empty($category['image']) && file_exists($catImagePath)):
                                ?>
                                    <img src="<?= base_url('uploads/categories/' . $category['image']) ?>" alt="<?= esc($category['name']) ?>" class="rounded-circle mb-2" style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="category-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px;">
                                        <i class="bi bi-grid fs-4"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="fw-medium text-dark"><?= esc($category['name']) ?></div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Featured Products -->
<?php if (!empty($featuredProducts)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Destaques</h2>
                <a href="<?= base_url('produtos?destaque=1') ?>" class="btn btn-outline-primary btn-sm">Ver Todos</a>
            </div>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <?= view('front/partials/product-card', ['product' => $product]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Info Banners -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="bi bi-box-seam fs-1 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Envio Rápido</h6>
                        <small class="text-muted">Para todo o Brasil</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="bi bi-shield-check fs-1 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Compra Segura</h6>
                        <small class="text-muted">Pagamento via Mercado Pago</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center p-3 bg-light rounded">
                    <i class="bi bi-credit-card fs-1 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Parcele em até 6x</h6>
                        <small class="text-muted">Sem armazenar dados do cartão</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- On Sale Products -->
<?php if (!empty($saleProducts)): ?>
    <section class="py-5 bg-danger bg-opacity-10">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <i class="bi bi-fire text-danger me-2"></i>Promocoes
                </h2>
                <a href="<?= base_url('promocoes') ?>" class="btn btn-danger btn-sm">Ver Todas</a>
            </div>
            <div class="swiper products-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($saleProducts as $product): ?>
                        <div class="swiper-slide">
                            <?= view('front/partials/product-card', ['product' => $product]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- New Arrivals -->
<?php if (!empty($newProducts)): ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <i class="bi bi-stars text-warning me-2"></i>Lancamentos
                </h2>
                <a href="<?= base_url('lancamentos') ?>" class="btn btn-outline-primary btn-sm">Ver Todos</a>
            </div>
            <div class="row g-4">
                <?php foreach ($newProducts as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <?= view('front/partials/product-card', ['product' => $product]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Brands -->
<?php if (!empty($brands)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Marcas</h2>
            <div class="swiper brands-swiper">
                <div class="swiper-wrapper align-items-center">
                    <?php foreach ($brands as $brand): ?>
                        <div class="swiper-slide text-center">
                            <a href="<?= base_url('marca/' . $brand['slug']) ?>">
                                <?php if ($brand['logo']): ?>
                                    <img src="<?= base_url('uploads/brands/' . $brand['logo']) ?>" alt="<?= esc($brand['name']) ?>" style="max-height: 50px; filter: grayscale(100%); opacity: 0.6; transition: all 0.3s;" onmouseover="this.style.filter='none';this.style.opacity='1';" onmouseout="this.style.filter='grayscale(100%)';this.style.opacity='0.6';">
                                <?php else: ?>
                                    <span class="fw-bold text-muted"><?= esc($brand['name']) ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Newsletter -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h3 class="mb-3">Receba nossas ofertas</h3>
                <p class="mb-4">Cadastre-se e receba promocoes exclusivas no seu e-mail</p>
                <form action="<?= base_url('newsletter') ?>" method="post" class="newsletter-form">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Seu melhor e-mail" required>
                        <button type="submit" class="btn btn-dark btn-lg">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Banner Swiper
    new Swiper('.banner-swiper', {
        loop: true,
        autoplay: {
            delay: 5000,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    // Categories Swiper
    new Swiper('.categories-swiper', {
        slidesPerView: 2,
        spaceBetween: 15,
        breakpoints: {
            576: { slidesPerView: 3 },
            768: { slidesPerView: 4 },
            992: { slidesPerView: 6 },
        },
    });

    // Products Swiper
    new Swiper('.products-swiper', {
        slidesPerView: 2,
        spaceBetween: 15,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            576: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            992: { slidesPerView: 4 },
        },
    });

    // Brands Swiper
    new Swiper('.brands-swiper', {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 3000,
        },
        breakpoints: {
            576: { slidesPerView: 4 },
            768: { slidesPerView: 5 },
            992: { slidesPerView: 6 },
        },
    });
</script>
<?= $this->endSection() ?>
