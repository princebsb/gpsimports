<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Cupons de Desconto</h1>
    <a href="<?= base_url('admin/cupons/criar') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Cupom
    </a>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Uso</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th style="width: 120px;">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($coupons)): ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td>
                                    <code class="fs-6"><?= esc($coupon['code']) ?></code>
                                    <?php if ($coupon['description']): ?>
                                        <br><small class="text-muted"><?= esc($coupon['description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $types = [
                                        'percentage' => '<span class="badge bg-info">Porcentagem</span>',
                                        'fixed' => '<span class="badge bg-primary">Valor Fixo</span>',
                                        'free_shipping' => '<span class="badge bg-success">Frete Gratis</span>',
                                    ];
                                    echo $types[$coupon['type']] ?? $coupon['type'];
                                    ?>
                                </td>
                                <td>
                                    <?php if ($coupon['type'] === 'percentage'): ?>
                                        <?= number_format($coupon['value'], 0) ?>%
                                    <?php elseif ($coupon['type'] === 'fixed'): ?>
                                        R$ <?= number_format($coupon['value'], 2, ',', '.') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                    <?php if ($coupon['max_discount']): ?>
                                        <br><small class="text-muted">Max: R$ <?= number_format($coupon['max_discount'], 2, ',', '.') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $coupon['usage_count'] ?? 0 ?>
                                    <?php if ($coupon['usage_limit']): ?>
                                        / <?= $coupon['usage_limit'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['starts_at']): ?>
                                        <small>De: <?= date('d/m/Y', strtotime($coupon['starts_at'])) ?></small><br>
                                    <?php endif; ?>
                                    <?php if ($coupon['expires_at']): ?>
                                        <small>Ate: <?= date('d/m/Y', strtotime($coupon['expires_at'])) ?></small>
                                        <?php if (strtotime($coupon['expires_at']) < time()): ?>
                                            <span class="badge bg-danger">Expirado</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="text-muted">Sem limite</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($coupon['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/cupons/editar/' . $coupon['id']) ?>" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Excluir" onclick="confirmDelete('<?= base_url('admin/cupons/excluir/' . $coupon['id']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-ticket-perforated fs-1"></i>
                                    <p class="mt-2">Nenhum cupom cadastrado</p>
                                    <a href="<?= base_url('admin/cupons/criar') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus me-1"></i>Criar Cupom
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
