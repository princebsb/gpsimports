<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Newsletter</h1>
    <a href="<?= base_url('admin/marketing/newsletter/exportar') ?>" class="btn btn-outline-primary">
        <i class="bi bi-download me-1"></i>Exportar CSV
    </a>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Data de Inscrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subscribers)): ?>
                        <?php foreach ($subscribers as $sub): ?>
                            <tr>
                                <td><?= esc($sub['email']) ?></td>
                                <td>
                                    <?php if ($sub['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Cancelado</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($sub['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope fs-1"></i>
                                <p class="mt-2">Nenhum inscrito na newsletter</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
