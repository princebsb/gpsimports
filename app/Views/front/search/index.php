<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Search Header -->
    <div class="mb-4">
        <h1 class="h4">Resultados para: "<?= esc($term) ?>"</h1>
        <p class="text-muted mb-0"><?= count($products) ?> produto(s) encontrado(s)</p>
    </div>

    <!-- Sorting -->
    <div class="d-flex justify-content-end mb-4">
        <select class="form-select form-select-sm w-auto" onchange="window.location.href='<?= base_url('busca') ?>?q=<?= urlencode($term) ?>&ordenar=' + this.value">
            <option value="relevance" <?= ($filters['sort'] ?? '') == 'relevance' ? 'selected' : '' ?>>Mais relevantes</option>
            <option value="newest" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mais recentes</option>
            <option value="price_asc" <?= ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Menor preco</option>
            <option value="price_desc" <?= ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Maior preco</option>
        </select>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
            <h5>Nenhum produto encontrado</h5>
            <p class="text-muted">Tente buscar por outros termos ou navegue pelas categorias.</p>
            <a href="<?= base_url('produtos') ?>" class="btn btn-primary">Ver todos os produtos</a>
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
