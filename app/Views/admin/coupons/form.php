<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= !empty($coupon) ? 'Editar Cupom' : 'Novo Cupom' ?></h1>
    <a href="<?= base_url('admin/cupons') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<form action="<?= !empty($coupon) ? base_url('admin/cupons/atualizar/' . $coupon['id']) : base_url('admin/cupons/salvar') ?>" method="post">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Cupom</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código do Cupom <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase" required
                                   value="<?= esc($coupon['code'] ?? old('code')) ?>"
                                   placeholder="Ex: DESCONTO10">
                            <small class="text-muted">Código que o cliente vai digitar</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control"
                                   value="<?= esc($coupon['description'] ?? old('description')) ?>"
                                   placeholder="Ex: Cupom de boas-vindas">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Desconto <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="couponType" required>
                                <option value="percentage" <?= ($coupon['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Porcentagem (%)</option>
                                <option value="fixed" <?= ($coupon['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Valor Fixo (R$)</option>
                                <option value="free_shipping" <?= ($coupon['type'] ?? '') === 'free_shipping' ? 'selected' : '' ?>>Frete Grátis</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Valor <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="valuePrefix">%</span>
                                <input type="number" name="value" class="form-control" step="0.01" min="0" required
                                       value="<?= $coupon['value'] ?? old('value') ?? '0' ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Desconto Máximo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="max_discount" class="form-control" step="0.01" min="0"
                                       value="<?= $coupon['max_discount'] ?? old('max_discount') ?>">
                            </div>
                            <small class="text-muted">Limite para cupons de porcentagem</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Restrições</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor Mínimo do Pedido</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" name="min_order_value" class="form-control" step="0.01" min="0"
                                       value="<?= $coupon['min_order_value'] ?? old('min_order_value') ?? '0' ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aplicar em</label>
                            <select name="applies_to" class="form-select">
                                <option value="all" <?= ($coupon['applies_to'] ?? '') === 'all' ? 'selected' : '' ?>>Todos os Produtos</option>
                                <option value="products" <?= ($coupon['applies_to'] ?? '') === 'products' ? 'selected' : '' ?>>Produtos Específicos</option>
                                <option value="categories" <?= ($coupon['applies_to'] ?? '') === 'categories' ? 'selected' : '' ?>>Categorias Específicas</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" name="exclude_sale_items" class="form-check-input" id="excludeSale" value="1"
                               <?= ($coupon['exclude_sale_items'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="excludeSale">Excluir produtos em promoção</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="first_purchase_only" class="form-check-input" id="firstPurchase" value="1"
                               <?= ($coupon['first_purchase_only'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="firstPurchase">Apenas primeira compra</label>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Limite de Uso</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Limite Total de Uso</label>
                            <input type="number" name="usage_limit" class="form-control" min="0"
                                   value="<?= $coupon['usage_limit'] ?? old('usage_limit') ?>">
                            <small class="text-muted">Deixe vazio para uso ilimitado</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Limite por Cliente</label>
                            <input type="number" name="usage_limit_per_user" class="form-control" min="0"
                                   value="<?= $coupon['usage_limit_per_user'] ?? old('usage_limit_per_user') ?? '1' ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Publicação</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($coupon['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($coupon['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Início</label>
                        <input type="datetime-local" name="starts_at" class="form-control"
                               value="<?= isset($coupon['starts_at']) ? date('Y-m-d\TH:i', strtotime($coupon['starts_at'])) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data de Expiração</label>
                        <input type="datetime-local" name="expires_at" class="form-control"
                               value="<?= isset($coupon['expires_at']) ? date('Y-m-d\TH:i', strtotime($coupon['expires_at'])) : '' ?>">
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i><?= !empty($coupon) ? 'Atualizar' : 'Criar' ?> Cupom
                    </button>
                </div>
            </div>

            <?php if (!empty($coupon)): ?>
                <div class="table-card">
                    <div class="card-body">
                        <h6 class="mb-3">Estatísticas</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Vezes Usado:</span>
                            <strong><?= $coupon['usage_count'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Criado em:</span>
                            <strong><?= date('d/m/Y', strtotime($coupon['created_at'])) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('couponType').addEventListener('change', function() {
        const prefix = document.getElementById('valuePrefix');
        if (this.value === 'percentage') {
            prefix.textContent = '%';
        } else if (this.value === 'fixed') {
            prefix.textContent = 'R$';
        } else {
            prefix.textContent = '-';
        }
    });

    // Trigger on load
    document.getElementById('couponType').dispatchEvent(new Event('change'));
</script>
<?= $this->endSection() ?>
