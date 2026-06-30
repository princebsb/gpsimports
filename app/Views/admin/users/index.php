<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Usuarios do Sistema</h1>
    <a href="<?= base_url('admin/usuarios/criar') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Usuario
    </a>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Funcao</th>
                        <th>Status</th>
                        <th>Ultimo Acesso</th>
                        <th style="width: 120px;">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong><?= esc($user['name']) ?></strong></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                    $roles = ['admin' => 'Administrador', 'manager' => 'Gerente', 'operator' => 'Operador'];
                                    $userRole = $user['role'] ?? 'admin';
                                    echo $roles[$userRole] ?? ucfirst($userRole);
                                    ?>
                                </td>
                                <td>
                                    <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($user['last_login']) ? date('d/m/Y H:i', strtotime($user['last_login'])) : '-' ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/usuarios/editar/' . $user['id']) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != session()->get('admin_id')): ?>
                                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('<?= base_url('admin/usuarios/excluir/' . $user['id']) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Nenhum usuario encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
