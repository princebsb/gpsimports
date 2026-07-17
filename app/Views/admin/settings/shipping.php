<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Configurações de Frete</h1>
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
            <a href="<?= base_url('admin/configuracoes/pagamento') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-credit-card me-2"></i>Pagamento
            </a>
            <a href="<?= base_url('admin/configuracoes/frete') ?>" class="list-group-item list-group-item-action active">
                <i class="bi bi-truck me-2"></i>Frete
            </a>
            <a href="<?= base_url('admin/configuracoes/email') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-envelope me-2"></i>Email
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <form action="<?= base_url('admin/configuracoes/frete') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Configurações Gerais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CEP de Origem</label>
                            <input type="text" name="shipping_origin_zipcode" class="form-control" value="<?= esc($settings['shipping_origin_zipcode'] ?? '') ?>" placeholder="00000-000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dias de Manuseio</label>
                            <input type="number" name="shipping_handling_days" class="form-control" value="<?= $settings['shipping_handling_days'] ?? 1 ?>">
                            <small class="text-muted">Dias adicionais para preparação do pedido</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Frete Grátis</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="free_shipping_enabled" class="form-check-input" id="freeShipping" value="1" <?= ($settings['free_shipping_enabled'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="freeShipping">Habilitar Frete Grátis</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor Mínimo para Frete Grátis</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="free_shipping_min_value" class="form-control" step="0.01" value="<?= $settings['free_shipping_min_value'] ?? 299 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Correios</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" name="correios_enabled" class="form-check-input" id="correiosEnabled" value="1" <?= ($settings['correios_enabled'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="correiosEnabled">Usar Correios</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="correios_pac_enabled" class="form-check-input" id="pacEnabled" value="1" <?= ($settings['correios_pac_enabled'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="pacEnabled">PAC</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="correios_sedex_enabled" class="form-check-input" id="sedexEnabled" value="1" <?= ($settings['correios_sedex_enabled'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sedexEnabled">SEDEX</label>
                            </div>
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
