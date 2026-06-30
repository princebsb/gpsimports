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
            <h2 class="mb-4">Alterar Senha</h2>

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

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('minha-conta/senha') ?>" method="post" id="passwordForm">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Senha Atual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('current_password', 'currentIcon')">
                                    <i class="bi bi-eye" id="currentIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nova Senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('new_password', 'newIcon')">
                                    <i class="bi bi-eye" id="newIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimo 8 caracteres</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirmar Nova Senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', 'confirmIcon')">
                                    <i class="bi bi-eye" id="confirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('minha-conta/dados') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check me-2"></i>Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Dicas de Seguranca</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Use pelo menos 8 caracteres</li>
                        <li>Combine letras maiusculas e minusculas</li>
                        <li>Inclua numeros e caracteres especiais</li>
                        <li>Evite informacoes pessoais como nome ou data de nascimento</li>
                        <li>Nao use a mesma senha em outros sites</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Form validation
    document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Senha muito curta',
                text: 'A nova senha deve ter no minimo 8 caracteres.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Senhas diferentes',
                text: 'A nova senha e a confirmacao nao conferem.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        return true;
    });
</script>
<?= $this->endSection() ?>
