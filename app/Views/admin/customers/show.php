<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= esc($customer['name']) ?></h1>
    <a href="<?= base_url('admin/clientes') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dados do Cliente</h5>
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= esc($customer['name']) ?></p>
                <p><strong>Email:</strong> <?= esc($customer['email']) ?></p>
                <p><strong>Telefone:</strong> <?= esc($customer['phone'] ?? '-') ?></p>
                <p><strong>CPF:</strong> <?= esc($customer['cpf'] ?? '-') ?></p>
                <p><strong>Status:</strong>
                    <?php if ($customer['status'] === 'active'): ?>
                        <span class="badge bg-success">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inativo</span>
                    <?php endif; ?>
                </p>
                <p><strong>Cadastro:</strong> <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?></p>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">Endereços</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($addresses)): ?>
                    <?php foreach ($addresses as $address): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <strong><?= esc($address['name']) ?></strong>
                            <?php if ($address['is_default']): ?>
                                <span class="badge bg-primary">Principal</span>
                            <?php endif; ?>
                            <br>
                            <small class="text-muted">
                                <?= esc($address['street']) ?>, <?= esc($address['number']) ?>
                                <?= $address['complement'] ? ' - ' . esc($address['complement']) : '' ?><br>
                                <?= esc($address['neighborhood']) ?> - <?= esc($address['city']) ?>/<?= esc($address['state']) ?><br>
                                CEP: <?= esc($address['zipcode']) ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhum endereço cadastrado</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">Pedidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= $order['id'] ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                        <td>R$ <?= number_format($order['total'], 2, ',', '.') ?></td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'cancelled' => 'danger',
                                                'refunded' => 'secondary',
                                            ];
                                            $color = $statusColors[$order['payment_status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= ucfirst($order['payment_status']) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/pedidos/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Nenhum pedido</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
