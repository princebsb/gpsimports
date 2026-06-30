<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h3 mb-4 text-center">Rastrear Pedido</h1>

            <!-- Formulario de busca -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="get" action="<?= base_url('rastrear-pedido') ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label for="pedido" class="form-label">Numero do Pedido</label>
                                <input type="text"
                                       name="pedido"
                                       id="pedido"
                                       class="form-control form-control-lg"
                                       placeholder="Ex: ORD-20240115-XXXXX"
                                       value="<?= esc($orderNumber ?? '') ?>"
                                       required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i><?= esc($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($order): ?>
                <?php
                $statusColors = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'paid' => 'primary',
                    'shipped' => 'info',
                    'delivered' => 'success',
                    'cancelled' => 'danger',
                    'refunded' => 'secondary',
                ];
                $statusNames = [
                    'pending' => 'Pendente',
                    'processing' => 'Processando',
                    'paid' => 'Pago',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregue',
                    'cancelled' => 'Cancelado',
                    'refunded' => 'Reembolsado',
                ];
                $color = $statusColors[$order['status']] ?? 'secondary';
                $statusName = $statusNames[$order['status']] ?? $order['status'];
                ?>

                <!-- Status do Pedido -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pedido #<?= esc($order['order_number']) ?></h5>
                            <span class="badge bg-<?= $color ?> fs-6"><?= $statusName ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Data do Pedido</small>
                                <p class="mb-0 fw-bold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Total</small>
                                <p class="mb-0 fw-bold text-primary">R$ <?= number_format($order['total'], 2, ',', '.') ?></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Itens</small>
                                <p class="mb-0 fw-bold"><?= $order['items_count'] ?> produto(s)</p>
                            </div>
                        </div>

                        <?php if (!empty($order['tracking_code'])): ?>
                            <div class="alert alert-info mb-0 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-truck me-2"></i>
                                        <strong>Codigo de Rastreio:</strong> <?= esc($order['tracking_code']) ?>
                                    </div>
                                    <a href="https://www.melhorrastreio.com.br/app/correios/<?= esc($order['tracking_code']) ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Rastrear
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Timeline do Status -->
                <?php if (!empty($order['status_history'])): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historico do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($order['status_history'] as $index => $history): ?>
                                    <div class="timeline-item <?= $index === 0 ? 'active' : '' ?>">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <p class="mb-0 fw-bold">
                                                <?= $statusNames[$history['status']] ?? $history['status'] ?>
                                            </p>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($history['created_at'])) ?>
                                            </small>
                                            <?php if (!empty($history['notes'])): ?>
                                                <p class="mb-0 mt-1 text-muted small"><?= esc($history['notes']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Itens do Pedido -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bag me-2"></i>Itens do Pedido</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($order['items'])): ?>
                            <?php foreach ($order['items'] as $item): ?>
                                <?php
                                $itemImage = $item['image'] ?? '';
                                if (empty($itemImage)) {
                                    $itemImageUrl = 'https://placehold.co/60x60/e9ecef/495057?text=P';
                                } elseif (strpos($itemImage, 'http') === 0) {
                                    $itemImageUrl = $itemImage;
                                } else {
                                    $itemImageUrl = base_url('uploads/products/thumbs/' . $itemImage);
                                }
                                ?>
                                <div class="d-flex p-3 border-bottom">
                                    <img src="<?= esc($itemImageUrl) ?>"
                                         alt="<?= esc($item['name']) ?>"
                                         class="rounded me-3"
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         onerror="this.src='https://placehold.co/60x60/e9ecef/495057?text=P'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                                        <small class="text-muted">Qtd: <?= $item['quantity'] ?></small>
                                    </div>
                                    <div class="text-end">
                                        <strong>R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Endereco de Entrega -->
                <?php if (!empty($order['shipping_street'])): ?>
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Endereco de Entrega</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">
                                <?= esc($order['shipping_street']) ?>, <?= esc($order['shipping_number']) ?>
                                <?php if (!empty($order['shipping_complement'])): ?>
                                    - <?= esc($order['shipping_complement']) ?>
                                <?php endif; ?>
                                <br>
                                <?= esc($order['shipping_neighborhood']) ?><br>
                                <?= esc($order['shipping_city']) ?> - <?= esc($order['shipping_state']) ?><br>
                                CEP: <?= esc($order['shipping_zipcode']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif (!$orderNumber): ?>
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="text-muted mt-3">Digite o numero do seu pedido para rastrear</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-marker {
    position: absolute;
    left: -26px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #e9ecef;
    border: 2px solid #fff;
}
.timeline-item.active .timeline-marker {
    background: var(--bs-primary);
}
</style>

<?= $this->endSection() ?>
