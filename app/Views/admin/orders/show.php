<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Pedido #<?= $order['order_number'] ?></h1>
        <small class="text-muted">Criado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/pedidos/imprimir/' . $order['id']) ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Imprimir
        </a>
        <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Itens do Pedido</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preco</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= base_url('uploads/products/thumbs/' . $item['image']) ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= esc($item['name'] ?? $item['product_name'] ?? 'Produto') ?></strong>
                                                <?php if (!empty($item['variation_name']) || !empty($item['attributes'])): ?>
                                                    <br><small class="text-muted"><?= esc($item['variation_name'] ?? $item['attributes'] ?? '') ?></small>
                                                <?php endif; ?>
                                                <br><small class="text-muted">SKU: <?= esc($item['sku'] ?? '-') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end">R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                    <td class="text-end">R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end">R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></td>
                            </tr>
                            <?php if ($order['discount'] > 0): ?>
                                <tr class="text-success">
                                    <td colspan="3" class="text-end">
                                        Desconto
                                        <?php if ($order['coupon_code']): ?>
                                            (<?= esc($order['coupon_code']) ?>)
                                        <?php endif; ?>:
                                    </td>
                                    <td class="text-end">-R$ <?= number_format($order['discount'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-end">Frete (<?= esc($order['shipping_method']) ?>):</td>
                                <td class="text-end">R$ <?= number_format($order['shipping_cost'], 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="fs-5">R$ <?= number_format($order['total'], 2, ',', '.') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Historico</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (!empty($order['status_history'])): ?>
                    <?php foreach ($order['status_history'] as $history): ?>
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-icon me-3">
                                <?php
                                $icon = match($history['status'] ?? 'pending') {
                                    'pending' => 'clock',
                                    'processing', 'paid' => 'gear',
                                    'shipped' => 'truck',
                                    'delivered' => 'check-circle',
                                    'cancelled' => 'x-circle',
                                    default => 'circle'
                                };
                                $color = match($history['status'] ?? 'pending') {
                                    'pending' => 'warning',
                                    'processing', 'paid' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $color ?> rounded-circle p-2">
                                    <i class="bi bi-<?= $icon ?>"></i>
                                </span>
                            </div>
                            <div class="timeline-content">
                                <div class="fw-bold"><?= esc($history['status_label'] ?? ucfirst($history['status'] ?? 'Status')) ?></div>
                                <?php if (!empty($history['notes']) || !empty($history['comment'])): ?>
                                    <div class="text-muted small"><?= esc($history['notes'] ?? $history['comment'] ?? '') ?></div>
                                <?php endif; ?>
                                <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum historico disponivel.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Status -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Status do Pedido</h5>
            </div>
            <div class="card-body">
                <?php
                $statusClass = match($order['status']) {
                    'pending' => 'badge-pending',
                    'processing' => 'badge-processing',
                    'shipped' => 'badge-shipped',
                    'delivered' => 'badge-delivered',
                    'cancelled' => 'badge-cancelled',
                    default => 'bg-secondary'
                };
                $statusLabel = match($order['status']) {
                    'pending' => 'Pendente',
                    'processing' => 'Processando',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregue',
                    'cancelled' => 'Cancelado',
                    default => $order['status']
                };
                ?>
                <div class="text-center mb-3">
                    <span class="badge <?= $statusClass ?> fs-6 px-3 py-2"><?= $statusLabel ?></span>
                </div>

                <?php if (!in_array($order['status'], ['cancelled', 'delivered'])): ?>
                    <form action="<?= base_url('admin/pedidos/' . $order['id'] . '/status') ?>" method="post" id="statusForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Alterar Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processando</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Enviado</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observacao</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Atualizar Status</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cliente</h5>
            </div>
            <div class="card-body">
                <?php $customer = $order['customer'] ?? []; ?>
                <div class="mb-3">
                    <strong><?= esc($customer['name'] ?? 'N/A') ?></strong>
                    <br>
                    <a href="mailto:<?= esc($customer['email'] ?? '') ?>"><?= esc($customer['email'] ?? 'N/A') ?></a>
                    <br>
                    <?php if (!empty($customer['phone']) || !empty($customer['mobile'])): ?>
                        <a href="tel:<?= esc($customer['phone'] ?? $customer['mobile'] ?? '') ?>"><?= esc($customer['phone'] ?? $customer['mobile'] ?? '') ?></a>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('admin/clientes/' . $order['customer_id']) ?>" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-person me-1"></i>Ver Cliente
                </a>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Endereco de Entrega</h5>
            </div>
            <div class="card-body">
                <address class="mb-0">
                    <?= esc($order['shipping_name'] ?? '') ?><br>
                    <?= esc($order['shipping_street'] ?? $order['shipping_address'] ?? '') ?>, <?= esc($order['shipping_number'] ?? '') ?>
                    <?php if (!empty($order['shipping_complement'])): ?>
                        <br><?= esc($order['shipping_complement']) ?>
                    <?php endif; ?>
                    <br><?= esc($order['shipping_neighborhood'] ?? '') ?>
                    <br><?= esc($order['shipping_city'] ?? '') ?> - <?= esc($order['shipping_state'] ?? '') ?>
                    <br>CEP: <?= esc($order['shipping_zipcode'] ?? '') ?>
                </address>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pagamento</h5>
            </div>
            <div class="card-body">
                <?php
                $paymentStatus = $order['payment_status'] ?? 'pending';
                $paymentMethod = $order['payment_method'] ?? 'N/A';
                $paymentClass = match($paymentStatus) {
                    'paid', 'approved' => 'bg-success',
                    'pending' => 'bg-warning',
                    'refunded' => 'bg-info',
                    'failed', 'rejected' => 'bg-danger',
                    default => 'bg-secondary'
                };
                $paymentLabel = match($paymentStatus) {
                    'paid', 'approved' => 'Pago',
                    'pending' => 'Aguardando',
                    'refunded' => 'Reembolsado',
                    'failed', 'rejected' => 'Falhou',
                    default => ucfirst($paymentStatus)
                };
                $methodLabel = match($paymentMethod) {
                    'credit_card' => 'Cartao de Credito',
                    'debit_card' => 'Cartao de Debito',
                    'pix' => 'PIX',
                    'boleto' => 'Boleto',
                    default => ucfirst($paymentMethod)
                };
                ?>
                <div class="mb-2">
                    <span class="badge <?= $paymentClass ?>"><?= $paymentLabel ?></span>
                </div>
                <div><strong>Metodo:</strong> <?= $methodLabel ?></div>
                <?php if (($order['installments'] ?? 1) > 1): ?>
                    <div><strong>Parcelas:</strong> <?= $order['installments'] ?>x</div>
                <?php endif; ?>
                <?php if (!empty($order['payment_id']) || !empty($order['payments'])): ?>
                    <?php $payment = $order['payments'][0] ?? null; ?>
                    <?php if ($payment): ?>
                        <div class="small text-muted mt-2">ID: <?= esc($payment['transaction_id'] ?? $payment['id'] ?? '') ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shipping Tracking -->
        <?php if (($order['status'] ?? '') === 'shipped' || !empty($order['tracking_code'])): ?>
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Rastreamento</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($order['tracking_code'])): ?>
                        <div class="mb-2">
                            <strong>Codigo:</strong>
                            <code><?= esc($order['tracking_code']) ?></code>
                        </div>
                        <a href="https://www.linkcorreios.com.br/?id=<?= esc($order['tracking_code']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Rastrear
                        </a>
                    <?php else: ?>
                        <form action="<?= base_url('admin/pedidos/rastreio/' . $order['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Codigo de Rastreio</label>
                                <input type="text" name="tracking_code" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Adicionar Rastreio</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
