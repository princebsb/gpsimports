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
            <h2 class="mb-4">Meus Dados</h2>

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

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('minha-conta/dados') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required value="<?= esc($customer['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required value="<?= esc($customer['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control" value="<?= esc($customer['cpf'] ?? '') ?>" maxlength="14" readonly>
                                <small class="text-muted">CPF nao pode ser alterado</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="<?= esc($customer['phone'] ?? '') ?>" maxlength="15">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Celular</label>
                                <input type="text" name="mobile" id="mobile" class="form-control" value="<?= esc($customer['mobile'] ?? '') ?>" maxlength="15">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= esc($customer['birth_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Genero</label>
                                <select name="gender" class="form-select">
                                    <option value="">Prefiro nao informar</option>
                                    <option value="M" <?= ($customer['gender'] ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= ($customer['gender'] ?? '') === 'F' ? 'selected' : '' ?>>Feminino</option>
                                    <option value="O" <?= ($customer['gender'] ?? '') === 'O' ? 'selected' : '' ?>>Outro</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('minha-conta/senha') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-shield-lock me-2"></i>Alterar Senha
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Salvar Alteracoes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informacoes da Conta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <small class="text-muted">Membro desde</small>
                            <p class="mb-0 fw-bold"><?= date('d/m/Y', strtotime($customer['created_at'] ?? 'now')) ?></p>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <small class="text-muted">Ultimo acesso</small>
                            <p class="mb-0 fw-bold"><?= $customer['last_login_at'] ? date('d/m/Y H:i', strtotime($customer['last_login_at'])) : 'Nunca' ?></p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <?php if (($customer['status'] ?? '') === 'active'): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= esc($customer['status'] ?? 'Desconhecido') ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Phone masks
    ['phone', 'mobile'].forEach(function(id) {
        document.getElementById(id)?.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
                value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            }
            this.value = value;
        });
    });

    // CPF mask
    document.getElementById('cpf')?.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        this.value = value;
    });
</script>
<?= $this->endSection() ?>
