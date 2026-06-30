<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Configuracoes da Loja</h1>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="list-group">
            <a href="<?= base_url('admin/configuracoes') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-gear me-2"></i>Geral
            </a>
            <a href="<?= base_url('admin/configuracoes/loja') ?>" class="list-group-item list-group-item-action active">
                <i class="bi bi-shop me-2"></i>Loja
            </a>
            <a href="<?= base_url('admin/configuracoes/pagamento') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-credit-card me-2"></i>Pagamento
            </a>
            <a href="<?= base_url('admin/configuracoes/frete') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-truck me-2"></i>Frete
            </a>
            <a href="<?= base_url('admin/configuracoes/email') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-envelope me-2"></i>Email
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <form action="<?= base_url('admin/configuracoes/loja') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Dados da Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nome da Loja</label>
                            <input type="text" name="store_name" class="form-control" value="<?= esc($settings['store_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="store_cnpj" class="form-control" value="<?= esc($settings['store_cnpj'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="store_email" class="form-control" value="<?= esc($settings['store_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="store_phone" class="form-control" value="<?= esc($settings['store_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" name="store_whatsapp" class="form-control" value="<?= esc($settings['store_whatsapp'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Endereco</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Endereco Completo</label>
                        <textarea name="store_address" class="form-control" rows="2"><?= esc($settings['store_address'] ?? '') ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="store_city" class="form-control" value="<?= esc($settings['store_city'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="store_state" class="form-control" value="<?= esc($settings['store_state'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="store_zipcode" class="form-control" value="<?= esc($settings['store_zipcode'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Horario de Funcionamento</h5>
                </div>
                <div class="card-body">
                    <textarea name="store_hours" class="form-control" rows="3" placeholder="Segunda a Sexta: 9h as 18h&#10;Sabado: 9h as 13h"><?= esc($settings['store_hours'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
