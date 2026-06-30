<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-3">Pagamento Confirmado!</h2>
                    <p class="text-muted mb-4">
                        Seu pedido <strong>#<?= esc($order['order_number']) ?></strong> foi confirmado com sucesso!
                        <br>Voce recebera um e-mail com os detalhes do pedido.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Numero do Pedido</small>
                                <h5 class="mb-0">#<?= esc($order['order_number']) ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Total</small>
                                <h5 class="mb-0 text-primary">R$ <?= number_format($order['total'], 2, ',', '.') ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Status</small>
                                <h5 class="mb-0 text-success">Aprovado</h5>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($order['items'])): ?>
                    <div class="card bg-light mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-bag me-2"></i>Itens do Pedido</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th class="text-center">Qtd</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <?= esc($item['name']) ?>
                                                <?php if (!empty($item['attributes'])): ?>
                                                    <br><small class="text-muted"><?= esc($item['attributes']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-end">R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-truck me-2"></i>Endereco de Entrega</h6>
                            <p class="mb-0">
                                <?= esc($order['shipping_street']) ?>, <?= esc($order['shipping_number']) ?>
                                <?php if (!empty($order['shipping_complement'])): ?>
                                    - <?= esc($order['shipping_complement']) ?>
                                <?php endif; ?>
                                <br>
                                <?= esc($order['shipping_neighborhood']) ?> - <?= esc($order['shipping_city']) ?>/<?= esc($order['shipping_state']) ?>
                                <br>
                                CEP: <?= esc($order['shipping_zipcode']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Seu pedido sera processado e enviado em breve. Voce pode acompanhar o status na sua area de cliente.
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= base_url('minha-conta/pedidos/' . $order['order_number']) ?>" class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>Ver Detalhes do Pedido
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
