<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Produtos</h1>
    <a href="<?= base_url('admin/produtos/novo') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Produto
    </a>
</div>

<!-- Filters -->
<div class="table-card mb-4">
    <div class="card-body p-3">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Buscar produtos..." value="<?= esc($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">Todas Categorias</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($filters['category'] ?? '') == $id ? 'selected' : '' ?>>
                            <?= esc($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="brand" class="form-select">
                    <option value="">Todas Marcas</option>
                    <?php foreach ($brands as $id => $name): ?>
                        <option value="<?= $id ?>" <?= ($filters['brand'] ?? '') == $id ? 'selected' : '' ?>>
                            <?= esc($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Todos Status</option>
                    <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover" id="productsTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">Imagem</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preco</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th style="width: 150px;">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php
                                    $img = $product['featured_image'] ?? '';
                                    if ($img):
                                        $imgUrl = (strpos($img, 'http') === 0) ? $img : base_url('uploads/products/thumbs/' . $img);
                                    ?>
                                        <img src="<?= esc($imgUrl) ?>"
                                             alt="<?= esc($product['name']) ?>"
                                             class="rounded"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             onerror="this.src='https://placehold.co/50x50/e9ecef/495057?text=P'">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= esc($product['name']) ?></strong>
                                        <br>
                                        <small class="text-muted">SKU: <?= esc($product['sku']) ?></small>
                                        <?php if (!empty($product['fonte'])): ?>
                                            <br><span class="badge bg-info badge-sm"><?= esc($product['fonte']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($product['url_origem'])): ?>
                                            <a href="<?= esc($product['url_origem']) ?>" target="_blank" class="ms-1" title="Ver produto original">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= esc($product['category_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $price = (float) ($product['price'] ?? 0);
                                    $salePrice = (float) ($product['sale_price'] ?? 0);
                                    $precoUsd = (float) ($product['preco_usd'] ?? 0);
                                    $costPrice = (float) ($product['cost_price'] ?? 0);
                                    ?>
                                    <?php if ($salePrice > 0 && $salePrice < $price): ?>
                                        <span class="text-decoration-line-through text-muted small">R$ <?= number_format($price, 2, ',', '.') ?></span>
                                        <br>
                                        <strong class="text-success">R$ <?= number_format($salePrice, 2, ',', '.') ?></strong>
                                    <?php elseif ($price > 0): ?>
                                        <strong>R$ <?= number_format($price, 2, ',', '.') ?></strong>
                                    <?php elseif ($precoUsd > 0): ?>
                                        <strong class="text-primary">US$ <?= number_format($precoUsd, 2) ?></strong>
                                        <br><small class="text-warning">Sem preço BRL</small>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Sem preço</span>
                                    <?php endif; ?>
                                    <?php if ($price > 0 && $precoUsd > 0): ?>
                                        <br><small class="text-primary">US$ <?= number_format($precoUsd, 2) ?></small>
                                    <?php endif; ?>
                                    <?php if ($costPrice > 0): ?>
                                        <br><small class="text-danger">Custo: R$ <?= number_format($costPrice, 2, ',', '.') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $stockAlert = $product['stock_alert'] ?? 5; ?>
                                    <?php if (($product['stock'] ?? 0) <= 0): ?>
                                        <span class="badge bg-danger">Esgotado</span>
                                    <?php elseif (($product['stock'] ?? 0) <= $stockAlert): ?>
                                        <span class="badge bg-warning"><?= $product['stock'] ?? 0 ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $product['stock'] ?? 0 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= ucfirst($product['status'] ?? 'Inativo') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php
                                        $msgWpp = "👋 Olá! Sou o *Sérgio*, já compro produtos no Paraguai há algum tempo.%0A%0A";
                                        $msgWpp .= "🚚 Tenho pessoal que *retira mercadoria pra mim* toda *Terça*, *Quinta* e *Sábado*.%0A%0A";
                                        $msgWpp .= "💵 Eles *pagam e retiram as notas* nesses dias.%0A%0A";
                                        $msgWpp .= "*Quero comprar com vocês!*%0A%0A";
                                        $msgWpp .= "Segue o produto:%0A%0A";
                                        $msgWpp .= "• " . urlencode($product['name']) . "%0A";
                                        $msgWpp .= "  SKU: " . urlencode($product['sku'] ?? '-') . "%0A%0A";
                                        $msgWpp .= "Favor gerar a NOTA.%0A%0AObrigado. 🙏";
                                        ?>
                                        <a href="https://web.whatsapp.com/send/?phone=595982897556&text=<?= $msgWpp ?>" target="_blank" class="btn btn-outline-success" title="WhatsApp Fornecedor">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                        <a href="<?= base_url('admin/produtos/' . $product['id'] . '/editar') ?>" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/produtos/' . $product['id'] . '/duplicar') ?>" class="btn btn-outline-secondary" title="Duplicar">
                                            <i class="bi bi-copy"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Excluir" onclick="confirmDelete('<?= base_url('admin/produtos/' . $product['id'] . '/excluir') ?>')">
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
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">Nenhum produto encontrado</p>
                                    <a href="<?= base_url('admin/produtos/novo') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus me-1"></i>Adicionar Produto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                <div class="text-muted small">
                    Mostrando <?= count($products) ?> de <?= $pager->getTotal() ?> produtos
                </div>
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
