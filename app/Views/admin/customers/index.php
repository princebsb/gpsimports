<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Clientes</h1>
    <a href="<?= base_url('admin/clientes/exportar') ?>" class="btn btn-outline-primary">
        <i class="bi bi-download me-1"></i>Exportar CSV
    </a>
</div>

<div class="table-card mb-4">
    <div class="card-body p-3">
        <form method="get" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email ou telefone..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Pedidos</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><strong><?= esc($customer['name']) ?></strong></td>
                                <td><?= esc($customer['email']) ?></td>
                                <td><?= esc($customer['phone'] ?? '-') ?></td>
                                <td><?= $customer['orders_count'] ?? 0 ?></td>
                                <td>
                                    <?php if ($customer['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/clientes/' . $customer['id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1"></i>
                                <p class="mt-2">Nenhum cliente encontrado</p>
                            </td>
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
