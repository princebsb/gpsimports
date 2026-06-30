<div class="card">
    <div class="card-body">
        <div class="text-center mb-3">
            <div class="avatar-circle bg-primary text-white mx-auto mb-2" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <?= strtoupper(substr(session()->get('customer_name') ?? 'U', 0, 1)) ?>
            </div>
            <h6 class="mb-0"><?= esc(session()->get('customer_name')) ?></h6>
            <small class="text-muted"><?= esc(session()->get('customer_email')) ?></small>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a class="nav-link <?= uri_string() === 'minha-conta' ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta') ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link <?= str_contains(uri_string(), 'minha-conta/pedidos') ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta/pedidos') ?>">
                <i class="bi bi-box-seam me-2"></i>Meus Pedidos
            </a>
            <a class="nav-link <?= str_contains(uri_string(), 'minha-conta/enderecos') ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta/enderecos') ?>">
                <i class="bi bi-geo-alt me-2"></i>Enderecos
            </a>
            <a class="nav-link <?= str_contains(uri_string(), 'minha-conta/favoritos') ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta/favoritos') ?>">
                <i class="bi bi-heart me-2"></i>Favoritos
            </a>
            <a class="nav-link <?= str_contains(uri_string(), 'minha-conta/dados') ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta/dados') ?>">
                <i class="bi bi-person me-2"></i>Meus Dados
            </a>
            <a class="nav-link <?= str_contains(uri_string(), 'minha-conta/senha') ? 'active fw-bold' : '' ?>" href="<?= base_url('minha-conta/senha') ?>">
                <i class="bi bi-shield-lock me-2"></i>Alterar Senha
            </a>
            <hr>
            <a class="nav-link text-danger" href="<?= base_url('sair') ?>">
                <i class="bi bi-box-arrow-right me-2"></i>Sair
            </a>
        </nav>
    </div>
</div>

<style>
.nav-link {
    color: #333;
    padding: 0.5rem 0;
    border-radius: 0.25rem;
    transition: all 0.2s;
}
.nav-link:hover {
    background-color: #f8f9fa;
    padding-left: 0.5rem;
}
.nav-link.active {
    color: #0d6efd;
    background-color: #e7f1ff;
    padding-left: 0.5rem;
}
</style>
