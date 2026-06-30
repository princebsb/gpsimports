<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <?= $this->include('front/customer/_sidebar') ?>
        </div>

        <!-- Content -->
        <div class="col-lg-9">
            <h2 class="mb-4">Meus Pedidos</h2>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($orders)): ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Itens</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?= esc($order['order_number']) ?></strong>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td><?= $order['items_count'] ?? '-' ?> itens</td>
                                        <td>
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
                                            $name = $statusNames[$order['status']] ?? $order['status'];
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $name ?></span>
                                        </td>
                                        <td class="text-end">
                                            <strong>R$ <?= number_format($order['total'], 2, ',', '.') ?></strong>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('minha-conta/pedidos/' . $order['order_number']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-bag-x fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Voce ainda nao fez nenhum pedido.</p>
                        <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                            <i class="bi bi-bag-plus me-2"></i>Comecar a Comprar
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
