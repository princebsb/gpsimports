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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Minha Conta</h2>
                <span class="text-muted">Ola, <?= esc($customer['name'] ?? session()->get('customer_name')) ?>!</span>
            </div>

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

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Total de Pedidos</h6>
                                    <h3 class="mb-0"><?= $total_orders ?? 0 ?></h3>
                                </div>
                                <i class="bi bi-bag-check fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Total Gasto</h6>
                                    <h3 class="mb-0">R$ <?= number_format($total_spent ?? 0, 2, ',', '.') ?></h3>
                                </div>
                                <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-dark opacity-75 mb-1">Cashback</h6>
                                    <h3 class="mb-0">R$ <?= number_format($cashback_balance ?? 0, 2, ',', '.') ?></h3>
                                </div>
                                <i class="bi bi-coin fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <a href="<?= base_url('minha-conta/pedidos') ?>" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-box-seam fs-2 text-primary mb-2"></i>
                            <p class="mb-0">Meus Pedidos</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= base_url('minha-conta/enderecos') ?>" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-geo-alt fs-2 text-primary mb-2"></i>
                            <p class="mb-0">Enderecos</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= base_url('minha-conta/favoritos') ?>" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-heart fs-2 text-primary mb-2"></i>
                            <p class="mb-0">Favoritos <span class="badge bg-secondary"><?= $wishlist_count ?? 0 ?></span></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="<?= base_url('minha-conta/dados') ?>" class="card text-decoration-none h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-gear fs-2 text-primary mb-2"></i>
                            <p class="mb-0">Meus Dados</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pedidos Recentes</h5>
                    <a href="<?= base_url('minha-conta/pedidos') ?>" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?= esc($order['order_number']) ?></strong>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
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
                                            <td class="text-end">R$ <?= number_format($order['total'], 2, ',', '.') ?></td>
                                            <td class="text-end">
                                                <a href="<?= base_url('minha-conta/pedidos/' . $order['order_number']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x fs-1 text-muted"></i>
                            <p class="text-muted mt-3">Voce ainda nao fez nenhum pedido.</p>
                            <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                                <i class="bi bi-bag-plus me-2"></i>Comecar a Comprar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
