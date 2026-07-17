<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Dashboard</h1>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" id="periodFilter" style="width: auto;">
            <option value="today">Hoje</option>
            <option value="week" selected>Esta Semana</option>
            <option value="month">Este Mês</option>
            <option value="year">Este Ano</option>
        </select>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">R$ <?= number_format($stats['revenue'] ?? 0, 2, ',', '.') ?></div>
                    <div class="stat-label">Faturamento</div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <?php if (isset($stats['revenue_change'])): ?>
                <div class="stat-change mt-2 <?= $stats['revenue_change'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    <i class="bi bi-<?= $stats['revenue_change'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= abs($stats['revenue_change']) ?>% vs período anterior
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?= $stats['orders'] ?? 0 ?></div>
                    <div class="stat-label">Pedidos</div>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
            <?php if (isset($stats['pending_orders']) && $stats['pending_orders'] > 0): ?>
                <div class="stat-change mt-2 text-warning">
                    <i class="bi bi-clock"></i>
                    <?= $stats['pending_orders'] ?> aguardando
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value"><?= $stats['customers'] ?? 0 ?></div>
                    <div class="stat-label">Clientes</div>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <?php if (isset($stats['new_customers'])): ?>
                <div class="stat-change mt-2 text-success">
                    <i class="bi bi-person-plus"></i>
                    <?= $stats['new_customers'] ?> novos
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-value">R$ <?= number_format($stats['average_ticket'] ?? 0, 2, ',', '.') ?></div>
                    <div class="stat-label">Ticket Médio</div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Pedidos Recentes</h5>
                <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentOrders)): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?= $order['order_number'] ?></strong>
                                        </td>
                                        <td><?= esc($order['customer_name']) ?></td>
                                        <td>R$ <?= number_format($order['total'], 2, ',', '.') ?></td>
                                        <td>
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
                                            <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                        </td>
                                        <td><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/pedidos/' . $order['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Nenhum pedido encontrado
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Widgets -->
    <div class="col-lg-4">
        <!-- Low Stock Alert -->
        <div class="table-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Estoque Baixo</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($lowStockProducts)): ?>
                        <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2" style="max-width: 180px;">
                                    <small><?= esc($product['name']) ?></small>
                                </div>
                                <span class="badge bg-danger"><?= $product['stock'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted">
                            <small>Nenhum produto com estoque baixo</small>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Top Products -->
        <div class="table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="bi bi-star text-warning me-2"></i>Mais Vendidos</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($topProducts)): ?>
                        <?php foreach (array_slice($topProducts, 0, 5) as $index => $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2"><?= $index + 1 ?></span>
                                    <div class="text-truncate" style="max-width: 150px;">
                                        <small><?= esc($product['name']) ?></small>
                                    </div>
                                </div>
                                <small class="text-muted"><?= $product['sold'] ?> vendas</small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted">
                            <small>Sem dados de vendas</small>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="row mt-4">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">Vendas dos Últimos 30 Dias</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const salesData = <?= json_encode($chartData ?? []) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.labels || [],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: salesData.values || [],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }

    // Period filter
    document.getElementById('periodFilter')?.addEventListener('change', function() {
        window.location.href = '<?= base_url('admin') ?>?period=' + this.value;
    });
</script>
<?= $this->endSection() ?>
