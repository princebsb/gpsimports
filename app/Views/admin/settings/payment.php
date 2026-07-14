<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Configuracoes de Pagamento</h1>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="list-group">
            <a href="<?= base_url('admin/configuracoes') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-gear me-2"></i>Geral
            </a>
            <a href="<?= base_url('admin/configuracoes/loja') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-shop me-2"></i>Loja
            </a>
            <a href="<?= base_url('admin/configuracoes/pagamento') ?>" class="list-group-item list-group-item-action active">
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
        <form action="<?= base_url('admin/configuracoes/pagamento') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Mercado Pago</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <input type="text" name="mp_public_key" class="form-control" value="<?= esc($settings['mp_public_key'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Access Token</label>
                        <input type="password" name="mp_access_token" class="form-control" value="<?= esc($settings['mp_access_token'] ?? '') ?>">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="mp_sandbox" class="form-check-input" id="mpSandbox" value="1" <?= ($settings['mp_sandbox'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mpSandbox">Modo Sandbox (Teste)</label>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">PIX</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="pix_enabled" class="form-check-input" id="pixEnabled" value="1" <?= ($settings['pix_enabled'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="pixEnabled">PIX Habilitado</label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Desconto diferenciado por faixa de valor do produto
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Desconto PIX - Ate R$ 5.000 (%)</label>
                            <input type="number" name="pix_discount" class="form-control" step="0.1" value="<?= $settings['pix_discount'] ?? 5 ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Desconto PIX - Acima de R$ 5.000 (%)</label>
                            <input type="number" name="pix_discount_high_value" class="form-control" step="0.1" value="<?= $settings['pix_discount_high_value'] ?? 3 ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Limite para desconto maior (R$)</label>
                            <input type="number" name="pix_discount_threshold" class="form-control" step="0.01" value="<?= $settings['pix_discount_threshold'] ?? 5000 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Cartao de Credito</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="credit_card_enabled" class="form-check-input" id="ccEnabled" value="1" <?= ($settings['credit_card_enabled'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ccEnabled">Cartao de Credito Habilitado</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maximo de Parcelas</label>
                            <input type="number" name="credit_card_max_installments" class="form-control" value="<?= $settings['credit_card_max_installments'] ?? 12 ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor Minimo da Parcela</label>
                            <input type="number" name="credit_card_min_installment" class="form-control" step="0.01" value="<?= $settings['credit_card_min_installment'] ?? 10 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Boleto</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="boleto_enabled" class="form-check-input" id="boletoEnabled" value="1" <?= ($settings['boleto_enabled'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="boletoEnabled">Boleto Habilitado</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desconto Boleto (%)</label>
                            <input type="number" name="boleto_discount" class="form-control" step="0.1" value="<?= $settings['boleto_discount'] ?? 0 ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dias para Vencimento</label>
                            <input type="number" name="boleto_days" class="form-control" value="<?= $settings['boleto_days'] ?? 3 ?>">
                        </div>
                    </div>
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
