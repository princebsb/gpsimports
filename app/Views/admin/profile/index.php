<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Meu Perfil</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">Dados Pessoais</h5>
            </div>
            <div class="card-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger"><?= session('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('admin/perfil') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" name="name" id="name" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" value="<?= old('name', $user['name']) ?>" required>
                        <?php if (session('errors.name')): ?>
                            <div class="invalid-feedback"><?= session('errors.name') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" name="email" id="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" value="<?= old('email', $user['email']) ?>" required>
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback"><?= session('errors.email') ?></div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Alterar Senha</h6>
                    <p class="text-muted small">Deixe em branco para manter a senha atual.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input type="password" name="password" id="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>">
                                <?php if (session('errors.password')): ?>
                                    <div class="invalid-feedback"><?= session('errors.password') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Senha</label>
                                <input type="password" name="password_confirm" id="password_confirm" class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>">
                                <?php if (session('errors.password_confirm')): ?>
                                    <div class="invalid-feedback"><?= session('errors.password_confirm') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Salvar Alteracoes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">Informacoes da Conta</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                </div>

                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">ID</td>
                        <td class="text-end">#<?= $user['id'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Funcao</td>
                        <td class="text-end">
                            <?php
                            $roles = ['admin' => 'Administrador', 'manager' => 'Gerente', 'operator' => 'Operador'];
                            echo $roles[$user['role'] ?? 'admin'] ?? 'Administrador';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cadastro</td>
                        <td class="text-end"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ultimo Acesso</td>
                        <td class="text-end"><?= !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
