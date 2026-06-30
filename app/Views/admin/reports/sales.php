<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Relatorio de Vendas</h1>
    <a href="<?= base_url('admin/relatorios') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="table-card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Data Inicial</label>
                <input type="date" name="start" class="form-control" value="<?= $startDate ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data Final</label>
                <input type="date" name="end" class="form-control" value="<?= $endDate ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="table-card h-100">
            <div class="card-body text-center">
                <h6 class="text-muted">Total de Vendas</h6>
                <h2 class="text-primary"><?= $totals['orders'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-card h-100">
            <div class="card-body text-center">
                <h6 class="text-muted">Receita Total</h6>
                <h2 class="text-success">R$ <?= number_format($totals['revenue'] ?? 0, 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Pedidos</th>
                        <th>Receita</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                <td><?= $sale['total_orders'] ?></td>
                                <td>R$ <?= number_format($sale['total_revenue'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhuma venda no periodo</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
