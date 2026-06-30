<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Alertas de Estoque</h1>
    <a href="<?= base_url('admin/estoque') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="table-card">
    <div class="card-body">
        <?php if (!empty($products)): ?>
            <div class="table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>SKU</th>
                            <th>Categoria</th>
                            <th>Estoque Atual</th>
                            <th>Alerta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><strong><?= esc($product['name']) ?></strong></td>
                                <td><code><?= esc($product['sku']) ?></code></td>
                                <td><?= esc($product['category_name'] ?? '-') ?></td>
                                <td>
                                    <?php if ($product['stock'] <= 0): ?>
                                        <span class="badge bg-danger fs-6"><?= $product['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning fs-6"><?= $product['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $product['stock_alert'] ?? 5 ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <p class="mt-2 text-muted">Nenhum produto com estoque baixo</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
