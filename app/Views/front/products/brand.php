<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('produtos') ?>">Produtos</a></li>
            <li class="breadcrumb-item active"><?= esc($brand['name']) ?></li>
        </ol>
    </nav>

    <!-- Brand Header -->
    <div class="d-flex align-items-center mb-4">
        <?php if (!empty($brand['logo'])): ?>
            <img src="<?= base_url('uploads/brands/' . $brand['logo']) ?>" alt="<?= esc($brand['name']) ?>" style="max-height: 60px;" class="me-3">
        <?php endif; ?>
        <div>
            <h1 class="h3 mb-1"><?= esc($brand['name']) ?></h1>
            <?php if (!empty($brand['description'])): ?>
                <p class="text-muted mb-0"><?= esc($brand['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sorting -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="mb-0 text-muted"><?= count($products) ?> produto(s)</p>
        <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
            <option value="<?= base_url('marca/' . $brand['slug'] . '?ordenar=newest') ?>" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mais recentes</option>
            <option value="<?= base_url('marca/' . $brand['slug'] . '?ordenar=price_asc') ?>" <?= ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Menor preco</option>
            <option value="<?= base_url('marca/' . $brand['slug'] . '?ordenar=price_desc') ?>" <?= ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Maior preco</option>
            <option value="<?= base_url('marca/' . $brand['slug'] . '?ordenar=bestseller') ?>" <?= ($filters['sort'] ?? '') == 'bestseller' ? 'selected' : '' ?>>Mais vendidos</option>
        </select>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
            <h5>Nenhum produto encontrado</h5>
            <p class="text-muted">Nenhum produto encontrado desta marca.</p>
            <a href="<?= base_url('produtos') ?>" class="btn btn-outline-secondary">Ver todos os produtos</a>

            <!-- Mensagem de contato -->
            <div class="mt-4 p-4 bg-light rounded">
                <h6 class="mb-3"><i class="bi bi-headset me-2"></i>Nao encontrou o que procura?</h6>
                <p class="text-muted mb-3">Entre em contato conosco que nos achamos o produto para voce!</p>
                <?php if (setting('store_whatsapp')): ?>
                    <a href="https://wa.me/<?= setting('store_whatsapp') ?>?text=<?= urlencode('Olá! Estou procurando um produto da marca ' . $brand['name'] . ' que não encontrei no site.') ?>" target="_blank" class="btn btn-success">
                        <i class="bi bi-whatsapp me-2"></i>Falar no WhatsApp
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('contato') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-2"></i>Entrar em contato
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <?= view('front/partials/product-card', ['product' => $product]) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pager): ?>
            <div class="d-flex justify-content-center mt-4">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
