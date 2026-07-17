<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Gestão de Estoque</h1>
    <div class="btn-group">
        <a href="<?= base_url('admin/estoque/alertas') ?>" class="btn btn-outline-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>Alertas
        </a>
        <a href="<?= base_url('admin/estoque/movimentacoes') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Histórico
        </a>
    </div>
</div>

<div class="table-card mb-4">
    <div class="card-body p-3">
        <div class="btn-group">
            <a href="<?= base_url('admin/estoque') ?>" class="btn btn-<?= $filter === 'all' ? 'primary' : 'outline-primary' ?>">Todos</a>
            <a href="<?= base_url('admin/estoque?filter=low') ?>" class="btn btn-<?= $filter === 'low' ? 'warning' : 'outline-warning' ?>">Estoque Baixo</a>
            <a href="<?= base_url('admin/estoque?filter=out') ?>" class="btn btn-<?= $filter === 'out' ? 'danger' : 'outline-danger' ?>">Esgotados</a>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>SKU</th>
                        <th>Categoria</th>
                        <th style="width: 120px;">Estoque</th>
                        <th style="width: 200px;">Ajustar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><strong><?= esc($product['name']) ?></strong></td>
                                <td><code><?= esc($product['sku']) ?></code></td>
                                <td><?= esc($product['category_name'] ?? '-') ?></td>
                                <td>
                                    <?php if ($product['stock'] <= 0): ?>
                                        <span class="badge bg-danger fs-6"><?= $product['stock'] ?></span>
                                    <?php elseif ($product['stock'] <= 5): ?>
                                        <span class="badge bg-warning fs-6"><?= $product['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success fs-6"><?= $product['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-danger" onclick="adjustStock(<?= $product['id'] ?>, 'remove')">-</button>
                                        <input type="number" class="form-control text-center" id="qty_<?= $product['id'] ?>" value="1" min="1" style="max-width: 60px;">
                                        <button class="btn btn-outline-success" onclick="adjustStock(<?= $product['id'] ?>, 'add')">+</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhum produto encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function adjustStock(productId, type) {
    const qty = document.getElementById('qty_' + productId).value;

    fetch('<?= base_url('admin/estoque/ajustar') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&type=${type}&quantity=${qty}&reason=Ajuste manual&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message);
        }
    });
}
</script>
<?= $this->endSection() ?>
