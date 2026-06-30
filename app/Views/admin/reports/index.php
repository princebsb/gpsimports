<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Relatorios</h1>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <a href="<?= base_url('admin/relatorios/vendas') ?>" class="text-decoration-none">
            <div class="table-card h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-graph-up text-primary" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Vendas</h4>
                    <p class="text-muted mb-0">Relatorio de vendas por periodo</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?= base_url('admin/relatorios/produtos') ?>" class="text-decoration-none">
            <div class="table-card h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-box-seam text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Produtos</h4>
                    <p class="text-muted mb-0">Produtos mais vendidos</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?= base_url('admin/relatorios/clientes') ?>" class="text-decoration-none">
            <div class="table-card h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-dark">Clientes</h4>
                    <p class="text-muted mb-0">Melhores clientes</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
