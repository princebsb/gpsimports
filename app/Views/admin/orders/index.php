<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Pedidos</h1>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/pedidos/exportar') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>Exportar
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value text-warning"><?= $stats['pending'] ?? 0 ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value text-info"><?= $stats['processing'] ?? 0 ?></div>
            <div class="stat-label">Processando</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value text-primary"><?= $stats['shipped'] ?? 0 ?></div>
            <div class="stat-label">Enviados</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-value text-success"><?= $stats['delivered'] ?? 0 ?></div>
            <div class="stat-label">Entregues</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="table-card mb-4">
    <div class="card-body p-3">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Pedido ou cliente..." value="<?= esc($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos Status</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pendente</option>
                    <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processando</option>
                    <option value="shipped" <?= ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Enviado</option>
                    <option value="delivered" <?= ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select">
                    <option value="">Todos Pagamentos</option>
                    <option value="pending" <?= ($filters['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Aguardando</option>
                    <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Pago</option>
                    <option value="refunded" <?= ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="De" value="<?= $filters['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="Até" value="<?= $filters['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Itens</th>
                        <th>Total</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong>#<?= $order['order_number'] ?></strong>
                                </td>
                                <td>
                                    <div><?= esc($order['customer_name']) ?></div>
                                    <small class="text-muted"><?= esc($order['customer_email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $order['items_count'] ?? 0 ?></span>
                                </td>
                                <td>
                                    <strong>R$ <?= number_format($order['total'], 2, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $paymentClass = match($order['payment_status']) {
                                        'paid', 'approved' => 'bg-success',
                                        'pending', 'in_process' => 'bg-warning',
                                        'refunded' => 'bg-info',
                                        'failed', 'rejected' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    $paymentLabel = match($order['payment_status']) {
                                        'paid', 'approved' => 'Pago',
                                        'pending', 'in_process' => 'Aguardando',
                                        'refunded' => 'Reembolsado',
                                        'failed', 'rejected' => 'Falhou',
                                        'processing' => 'Processando',
                                        default => ucfirst($order['payment_status'] ?? 'Pendente')
                                    };
                                    ?>
                                    <span class="badge <?= $paymentClass ?>"><?= $paymentLabel ?></span>
                                    <br>
                                    <small class="text-muted">
                                        <?php
                                        echo match($order['payment_method'] ?? '') {
                                            'credit_card' => 'Cartão Crédito',
                                            'debit_card' => 'Cartão Débito',
                                            'pix' => 'PIX',
                                            'boleto' => 'Boleto',
                                            'checkout_pro' => 'Mercado Pago',
                                            'account_money' => 'Mercado Pago',
                                            default => ucfirst($order['payment_method'] ?? '-')
                                        };
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($order['status']) {
                                        'pending' => 'badge-pending',
                                        'paid' => 'badge-processing',
                                        'processing' => 'badge-processing',
                                        'shipped' => 'badge-shipped',
                                        'delivered' => 'badge-delivered',
                                        'cancelled' => 'badge-cancelled',
                                        default => 'bg-secondary'
                                    };
                                    $statusLabel = match($order['status']) {
                                        'pending' => 'Pendente',
                                        'paid' => 'Pago',
                                        'processing' => 'Em Preparação',
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregue',
                                        'cancelled' => 'Cancelado',
                                        default => ucfirst($order['status'] ?? 'Pendente')
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <div><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/pedidos/' . $order['id']) ?>" class="btn btn-outline-primary" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                            <span class="visually-hidden">Opções</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="<?= base_url('admin/pedidos/' . $order['id']) ?>"><i class="bi bi-eye me-2"></i>Ver Detalhes</a></li>
                                            <li><a class="dropdown-item" href="<?= base_url('admin/pedidos/' . $order['id'] . '/imprimir') ?>" target="_blank"><i class="bi bi-printer me-2"></i>Imprimir</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <?php if ($order['status'] === 'processing'): ?>
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'shipped')"><i class="bi bi-truck me-2"></i>Marcar Enviado</a></li>
                                            <?php endif; ?>
                                            <?php if ($order['status'] === 'shipped'): ?>
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'delivered')"><i class="bi bi-check-circle me-2"></i>Marcar Entregue</a></li>
                                            <?php endif; ?>
                                            <?php if (!in_array($order['status'], ['cancelled', 'delivered'])): ?>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'cancelled')"><i class="bi bi-x-circle me-2"></i>Cancelar</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Nenhum pedido encontrado</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                <div class="text-muted small">
                    Mostrando <?= count($orders) ?> de <?= $pager->getTotal() ?> pedidos
                </div>
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateStatus(orderId, status) {
        const statusLabels = {
            'shipped': 'enviado',
            'delivered': 'entregue',
            'cancelled': 'cancelado'
        };

        Swal.fire({
            title: 'Confirmar',
            text: `Marcar pedido como ${statusLabels[status]}?`,
            icon: status === 'cancelled' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'cancelled' ? '#dc3545' : '#2563eb',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('admin/pedidos/') ?>${orderId}/status/${status}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>
