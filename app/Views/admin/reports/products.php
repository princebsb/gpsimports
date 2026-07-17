<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Produtos Mais Vendidos</h1>
    <a href="<?= base_url('admin/relatorios') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>SKU</th>
                        <th>Quantidade Vendida</th>
                        <th>Receita</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php $i = 1; foreach ($products as $product): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $i++ ?></span></td>
                                <td><strong><?= esc($product['name']) ?></strong></td>
                                <td><code><?= esc($product['sku']) ?></code></td>
                                <td><?= $product['total_sold'] ?></td>
                                <td>R$ <?= number_format($product['total_revenue'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum dado disponível</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
