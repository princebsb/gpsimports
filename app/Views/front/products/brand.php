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
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Nenhum produto encontrado desta marca.
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
