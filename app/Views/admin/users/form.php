<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= isset($user) ? 'Editar Usuário' : 'Novo Usuário' ?></h1>
    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="<?= isset($user) ? base_url('admin/usuarios/atualizar/' . $user['id']) : base_url('admin/usuarios/salvar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= esc($user['name'] ?? old('name')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required value="<?= esc($user['email'] ?? old('email')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha <?= isset($user) ? '' : '<span class="text-danger">*</span>' ?></label>
                        <input type="password" name="password" class="form-control" <?= isset($user) ? '' : 'required' ?>>
                        <?php if (isset($user)): ?>
                            <small class="text-muted">Deixe em branco para manter a senha atual</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Função</label>
                        <select name="role" class="form-select">
                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                            <option value="manager" <?= ($user['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Gerente</option>
                            <option value="operator" <?= ($user['role'] ?? '') === 'operator' ? 'selected' : '' ?>>Operador</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($user['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
