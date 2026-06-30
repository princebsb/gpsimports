<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <?php foreach ($categoryBreadcrumb as $item): ?>
                <?php if ($item['id'] == $category['id']): ?>
                    <li class="breadcrumb-item active"><?= esc($item['name']) ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?= base_url('categoria/' . $item['slug']) ?>"><?= esc($item['name']) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <!-- Subcategories -->
            <?php if (!empty($subcategories)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Subcategorias</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($subcategories as $sub): ?>
                            <li class="list-group-item">
                                <a href="<?= base_url('categoria/' . $sub['slug']) ?>" class="text-decoration-none">
                                    <?= esc($sub['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('categoria/' . $category['slug']) ?>">
                        <!-- Brands -->
                        <div class="mb-4">
                            <h6>Marcas</h6>
                            <select name="marca" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' ?>>
                                        <?= esc($brand['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6>Preco</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="preco_min" class="form-control form-control-sm" placeholder="Min" value="<?= $filters['min_price'] ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="preco_max" class="form-control form-control-sm" placeholder="Max" value="<?= $filters['max_price'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- On Sale -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="promocao" value="1" class="form-check-input" id="onSale" <?= !empty($filters['on_sale']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="onSale">Em promocao</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0"><?= esc($category['name']) ?></h1>
                <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                    <option value="<?= base_url('categoria/' . $category['slug'] . '?ordenar=newest') ?>" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="<?= base_url('categoria/' . $category['slug'] . '?ordenar=price_asc') ?>" <?= ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Menor preco</option>
                    <option value="<?= base_url('categoria/' . $category['slug'] . '?ordenar=price_desc') ?>" <?= ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Maior preco</option>
                    <option value="<?= base_url('categoria/' . $category['slug'] . '?ordenar=bestseller') ?>" <?= ($filters['sort'] ?? '') == 'bestseller' ? 'selected' : '' ?>>Mais vendidos</option>
                </select>
            </div>

            <?php if (!empty($category['description'])): ?>
                <p class="text-muted mb-4"><?= esc($category['description']) ?></p>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Nenhum produto encontrado nesta categoria.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4">
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
    </div>
</div>

<?= $this->endSection() ?>
