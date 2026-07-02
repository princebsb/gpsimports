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

            <!-- LGPD - Exclusao de Conta -->
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-exclamation me-2"></i>Privacidade e Dados (LGPD)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        De acordo com a Lei Geral de Protecao de Dados (LGPD), voce tem o direito de solicitar a exclusao dos seus dados pessoais.
                    </p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#exportDataModal">
                                <i class="bi bi-download me-2"></i>Exportar Meus Dados
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash me-2"></i>Excluir Minha Conta
                            </button>
                        </div>
                    </div>

                    <p class="small text-muted mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Para mais informacoes, consulte nossa <a href="<?= base_url('politica-privacidade') ?>" target="_blank">Politica de Privacidade</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Exportar Dados -->
<div class="modal fade" id="exportDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-download me-2"></i>Exportar Meus Dados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Voce pode baixar todos os seus dados pessoais em formato JSON. Isso inclui:</p>
                <ul>
                    <li>Dados cadastrais (nome, email, telefone)</li>
                    <li>Enderecos salvos</li>
                    <li>Historico de pedidos</li>
                    <li>Lista de favoritos</li>
                </ul>
                <p class="text-muted small">O arquivo sera gerado e baixado automaticamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="<?= base_url('minha-conta/exportar-dados') ?>" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Baixar Dados
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir Conta -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Excluir Conta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('minha-conta/excluir-conta') ?>" method="post" id="deleteAccountForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong><i class="bi bi-exclamation-triangle me-2"></i>Atencao!</strong>
                        <p class="mb-0 mt-2">Esta acao e irreversivel. Ao excluir sua conta:</p>
                    </div>
                    <ul class="text-danger">
                        <li>Todos os seus dados pessoais serao removidos</li>
                        <li>Seu historico de pedidos sera anonimizado</li>
                        <li>Voce perdera acesso a sua conta permanentemente</li>
                        <li>Nao sera possivel recuperar os dados</li>
                    </ul>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Para confirmar, digite sua senha atual:</label>
                        <input type="password" name="password" class="form-control" required placeholder="Sua senha">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="confirm_delete" id="confirmDelete" class="form-check-input" required>
                        <label class="form-check-label text-danger" for="confirmDelete">
                            Entendo que esta acao e irreversivel e desejo excluir minha conta
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="btnDeleteAccount" disabled>
                        <i class="bi bi-trash me-2"></i>Excluir Permanentemente
                    </button>
                </div>
            </form>
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

    // Habilitar botao de exclusao apenas com checkbox marcado
    document.getElementById('confirmDelete')?.addEventListener('change', function() {
        document.getElementById('btnDeleteAccount').disabled = !this.checked;
    });

    // Confirmar exclusao
    document.getElementById('deleteAccountForm')?.addEventListener('submit', function(e) {
        if (!confirm('Tem certeza absoluta que deseja excluir sua conta? Esta acao NAO pode ser desfeita!')) {
            e.preventDefault();
        }
    });
</script>
<?= $this->endSection() ?>
