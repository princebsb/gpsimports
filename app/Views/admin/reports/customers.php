<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Melhores Clientes</h1>
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
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Pedidos</th>
                        <th>Total Gasto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php $i = 1; foreach ($customers as $customer): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $i++ ?></span></td>
                                <td><strong><?= esc($customer['name']) ?></strong></td>
                                <td><?= esc($customer['email']) ?></td>
                                <td><?= $customer['total_orders'] ?></td>
                                <td>R$ <?= number_format($customer['total_spent'], 2, ',', '.') ?></td>
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
