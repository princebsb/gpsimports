<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Movimentacoes de Estoque</h1>
    <a href="<?= base_url('admin/estoque') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Qtd</th>
                        <th>Antes</th>
                        <th>Depois</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movements)): ?>
                        <?php foreach ($movements as $mov): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                                <td><?= esc($mov['product_name']) ?></td>
                                <td>
                                    <?php if ($mov['type'] === 'add'): ?>
                                        <span class="badge bg-success">Entrada</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Saida</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $mov['quantity'] ?></td>
                                <td><?= $mov['stock_before'] ?></td>
                                <td><?= $mov['stock_after'] ?></td>
                                <td><?= esc($mov['reason'] ?? '-') ?></td>
                                <td><?= esc($mov['user_name'] ?? 'Sistema') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Nenhuma movimentacao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
