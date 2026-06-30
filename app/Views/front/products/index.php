<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="<?= base_url('produtos') ?>">
                        <!-- Categories -->
                        <div class="mb-4">
                            <h6>Categorias</h6>
                            <select name="categoria" id="categoryFilter" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Brands -->
                        <div class="mb-4">
                            <h6>Marcas</h6>
                            <select name="marca" id="brandFilter" class="form-select form-select-sm">
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
                        <a href="<?= base_url('produtos') ?>" class="btn btn-outline-secondary w-100 mt-2">Limpar</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0 text-muted">
                    <?php
                    $total = $pager ? $pager->getTotal() : count($products);
                    echo number_format($total, 0, ',', '.') . ' produto(s) encontrado(s)';
                    ?>
                <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                    <option value="<?= base_url('produtos?ordenar=newest') ?>" <?= ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="<?= base_url('produtos?ordenar=price_asc') ?>" <?= ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Menor preco</option>
                    <option value="<?= base_url('produtos?ordenar=price_desc') ?>" <?= ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Maior preco</option>
                    <option value="<?= base_url('produtos?ordenar=bestseller') ?>" <?= ($filters['sort'] ?? '') == 'bestseller' ? 'selected' : '' ?>>Mais vendidos</option>
                </select>
            </div>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Nenhum produto encontrado com os filtros selecionados.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4">
                            <?= view('front/partials/product-card', ['product' => $product]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($pager && $pager->getPageCount() > 1): ?>
                    <div class="pagination-wrapper mt-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <div class="pagination-info text-muted small">
                                Pagina <?= $pager->getCurrentPage() ?> de <?= $pager->getPageCount() ?>
                                <span class="d-none d-md-inline">
                                    (<?= number_format($pager->getTotal(), 0, ',', '.') ?> produtos)
                                </span>
                            </div>
                            <?= $pager->links() ?>
                            <div class="pagination-goto d-none d-md-flex align-items-center gap-2">
                                <label class="text-muted small mb-0">Ir para:</label>
                                <input type="number"
                                       class="form-control form-control-sm"
                                       style="width: 70px;"
                                       min="1"
                                       max="<?= $pager->getPageCount() ?>"
                                       value="<?= $pager->getCurrentPage() ?>"
                                       onkeypress="if(event.key==='Enter'){goToPage(this.value, <?= $pager->getPageCount() ?>)}">
                            </div>
                        </div>
                    </div>
                    <script>
                    function goToPage(page, maxPage) {
                        page = parseInt(page);
                        if (page >= 1 && page <= maxPage) {
                            const url = new URL(window.location.href);
                            url.searchParams.set('page', page);
                            window.location.href = url.toString();
                        }
                    }
                    </script>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categoryFilter');
    const brandSelect = document.getElementById('brandFilter');
    const currentBrandId = '<?= $filters['brand_id'] ?? '' ?>';

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;

        // Mostrar loading
        brandSelect.disabled = true;
        brandSelect.innerHTML = '<option value="">Carregando...</option>';

        // Buscar marcas da categoria
        fetch('<?= base_url('produtos/marcas-por-categoria') ?>?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                brandSelect.disabled = false;
                let html = '<option value="">Todas</option>';

                if (data.success && data.brands.length > 0) {
                    data.brands.forEach(brand => {
                        html += `<option value="${brand.id}">${brand.name}</option>`;
                    });
                }

                brandSelect.innerHTML = html;
            })
            .catch(() => {
                brandSelect.disabled = false;
                brandSelect.innerHTML = '<option value="">Todas</option>';
            });
    });
});
</script>
<?= $this->endSection() ?>
