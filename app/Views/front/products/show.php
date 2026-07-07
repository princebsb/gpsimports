<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<?php
// Schema Markup (JSON-LD) para SEO
$schemaPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['sale_price'] : $product['price'];
$schemaImage = $product['featured_image'] ?? '';
if (!empty($schemaImage) && strpos($schemaImage, 'http') !== 0) {
    $schemaImage = base_url('uploads/products/' . $schemaImage);
}
$schemaAvailability = ($product['stock'] ?? 0) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
$schemaCondition = 'https://schema.org/NewCondition';
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= esc($product['name'], 'js') ?>",
    "description": "<?= esc(strip_tags($product['description'] ?? $product['short_description'] ?? $product['name']), 'js') ?>",
    "sku": "<?= esc($product['sku'] ?? $product['id'], 'js') ?>",
    "image": "<?= esc($schemaImage, 'js') ?>",
    "url": "<?= current_url() ?>",
    <?php if (!empty($product['brand_name'])): ?>
    "brand": {
        "@type": "Brand",
        "name": "<?= esc($product['brand_name'], 'js') ?>"
    },
    <?php endif; ?>
    <?php if (!empty($product['gtin']) || !empty($product['ean'])): ?>
    "gtin13": "<?= esc($product['gtin'] ?? $product['ean'], 'js') ?>",
    <?php endif; ?>
    "offers": {
        "@type": "Offer",
        "url": "<?= current_url() ?>",
        "priceCurrency": "BRL",
        "price": "<?= number_format($schemaPrice, 2, '.', '') ?>",
        "priceValidUntil": "<?= date('Y-12-31') ?>",
        "availability": "<?= $schemaAvailability ?>",
        "itemCondition": "<?= $schemaCondition ?>",
        "seller": {
            "@type": "Organization",
            "name": "<?= esc(setting('store_name') ?? 'GPS Imports', 'js') ?>"
        }
    }
    <?php if (($product['rating'] ?? 0) > 0): ?>
    ,"aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?= number_format($product['rating'], 1, '.', '') ?>",
        "reviewCount": "<?= (int) ($product['reviews_count'] ?? 1) ?>",
        "bestRating": "5",
        "worstRating": "1"
    }
    <?php endif; ?>
}
</script>

<div class="container py-4">
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <div class="position-relative" style="padding-top: 100%; background: #f8fafc; border-radius: 0.5rem; overflow: hidden;">
                        <?php
                        $featuredImage = $product['featured_image'] ?? '';
                        $productImageUrl = '';

                        if (!empty($featuredImage)) {
                            // Verificar se é URL externa (http/https)
                            if (strpos($featuredImage, 'http') === 0) {
                                $productImageUrl = $featuredImage;
                            } else {
                                // Imagem local
                                $productImagePath = FCPATH . 'uploads/products/' . $featuredImage;
                                if (file_exists($productImagePath)) {
                                    $productImageUrl = base_url('uploads/products/' . $featuredImage);
                                }
                            }
                        }

                        if (empty($productImageUrl)) {
                            $productImageUrl = 'https://placehold.co/600x600/e9ecef/495057?text=' . urlencode(substr($product['name'], 0, 30));
                        }
                        ?>
                        <img src="<?= esc($productImageUrl) ?>"
                             onerror="this.src='https://placehold.co/600x600/e9ecef/495057?text=Sem+Imagem'"
                             alt="<?= esc($product['name']) ?>"
                             id="mainImage"
                             class="position-absolute top-0 start-0 w-100 h-100"
                             style="object-fit: contain;">

                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-3 fs-6">-<?= $discount ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Thumbnails -->
                <?php
                // Coletar todas as imagens (da tabela product_images ou do images_json)
                $allImages = [];

                // Adicionar imagem principal primeiro
                if (!empty($productImageUrl)) {
                    $allImages[] = $productImageUrl;
                }

                // Imagens da tabela product_images
                if (!empty($product['images'])) {
                    foreach ($product['images'] as $image) {
                        $imgUrl = base_url('uploads/products/' . $image['filename']);
                        if (!in_array($imgUrl, $allImages)) {
                            $allImages[] = $imgUrl;
                        }
                    }
                }

                // Imagens do images_json (URLs externas)
                if (!empty($product['images_json'])) {
                    $jsonImages = json_decode($product['images_json'], true);
                    if (is_array($jsonImages)) {
                        foreach ($jsonImages as $imgUrl) {
                            if (!empty($imgUrl) && !in_array($imgUrl, $allImages)) {
                                $allImages[] = $imgUrl;
                            }
                        }
                    }
                }
                ?>

                <?php if (count($allImages) > 1): ?>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <?php foreach ($allImages as $index => $imgUrl): ?>
                            <img src="<?= esc($imgUrl) ?>"
                                 alt="<?= esc($product['name']) ?> - Imagem <?= $index + 1 ?>"
                                 class="img-thumbnail <?= $index === 0 ? 'border-primary' : '' ?>"
                                 style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                                 onerror="this.style.display='none'"
                                 onclick="document.getElementById('mainImage').src='<?= esc($imgUrl) ?>'; document.querySelectorAll('.img-thumbnail').forEach(el => el.classList.remove('border-primary')); this.classList.add('border-primary');">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <!-- Brand -->
            <?php if (!empty($product['brand_name'])): ?>
                <a href="<?= base_url('marca/' . $product['brand_slug']) ?>" class="text-muted text-decoration-none small">
                    <?= esc($product['brand_name']) ?>
                </a>
            <?php endif; ?>

            <h1 class="h3 mt-2"><?= esc($product['name']) ?></h1>

            <!-- Rating -->
            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= ($product['rating'] ?? 0) ? '-fill' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="text-muted small">(<?= $product['reviews_count'] ?? 0 ?> avaliacoes)</span>
                <span class="text-muted small ms-3">SKU: <?= esc($product['sku']) ?></span>
            </div>

            <!-- Price -->
            <div class="mb-4">
                <?php
                $currentPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['sale_price'] : $product['price'];
                $pixDiscount = (float) (setting('pix_discount') ?? 5);
                $pixPrice = $currentPrice * (1 - $pixDiscount / 100);
                ?>
                <?php if ($currentPrice > 0): ?>
                    <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                        <div class="text-muted text-decoration-line-through">
                            De: R$ <?= number_format($product['price'], 2, ',', '.') ?>
                        </div>
                    <?php endif; ?>
                    <div class="h2 text-primary mb-1">R$ <?= number_format($currentPrice, 2, ',', '.') ?></div>
                    <div class="text-success mb-2">
                        <i class="bi bi-qr-code me-1"></i>
                        <strong>R$ <?= number_format($pixPrice, 2, ',', '.') ?></strong> no PIX
                        <span class="badge bg-success ms-1"><?= $pixDiscount ?>% OFF</span>
                    </div>
                    <?php
                    $parcelasSemJuros = (int) (setting('installments_no_interest') ?? 3);
                    $parcelasMax = (int) (setting('installments_max') ?? 12);
                    // Taxas de juros do Mercado Pago por parcela (CFT)
                    $taxasPorParcela = [
                        4 => 11.36,
                        5 => 14.31,
                        6 => 14.32,
                        7 => 16.72,
                        8 => 16.73,
                        9 => 19.69,
                        10 => 20.65,
                        11 => 20.66,
                        12 => 22.11,
                    ];
                    ?>
                    <div class="mb-2">
                        <i class="bi bi-credit-card me-1 text-primary"></i>
                        <strong><?= $parcelasSemJuros ?>x</strong> de <strong>R$ <?= number_format($currentPrice / $parcelasSemJuros, 2, ',', '.') ?></strong> sem juros
                    </div>
                    <div>
                        <a href="#" class="text-primary small text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalParcelas">
                            <i class="bi bi-list-ul me-1"></i>Ver todas as parcelas
                        </a>
                    </div>

                    <!-- Modal Parcelas -->
                    <div class="modal fade" id="modalParcelas" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-credit-card me-2"></i>Parcelas no Cartão
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <table class="table table-hover mb-0">
                                        <tbody>
                                            <?php for ($i = 1; $i <= $parcelasMax; $i++): ?>
                                                <?php
                                                $semJuros = $i <= $parcelasSemJuros;
                                                if ($semJuros) {
                                                    $valorParcela = $currentPrice / $i;
                                                    $valorTotal = $currentPrice;
                                                    $taxa = 0;
                                                } else {
                                                    // Usar taxa especifica da parcela
                                                    $taxa = $taxasPorParcela[$i] ?? 0;
                                                    $valorTotal = $currentPrice * (1 + $taxa / 100);
                                                    $valorParcela = $valorTotal / $i;
                                                }
                                                ?>
                                                <tr>
                                                    <td class="ps-3">
                                                        <strong><?= $i ?>x</strong> de
                                                        <strong>R$ <?= number_format($valorParcela, 2, ',', '.') ?></strong>
                                                        <?php if ($semJuros): ?>
                                                            <span class="text-success">sem juros</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end pe-3 text-muted small">
                                                        R$ <?= number_format($valorTotal, 2, ',', '.') ?>
                                                    </td>
                                                </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer bg-light">
                                    <small class="text-muted">
                                        <i class="bi bi-shield-check me-1"></i>
                                        Pagamento seguro via Mercado Pago
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="h2 text-warning mb-1">Consulte</div>
                    <div class="text-muted small">
                        <i class="bi bi-whatsapp me-1"></i>Entre em contato para saber o preco
                    </div>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <?php if ($product['short_description']): ?>
                <p class="text-muted mb-4"><?= esc($product['short_description']) ?></p>
            <?php endif; ?>

            <!-- Variations -->
            <?php if (!empty($product['variations'])): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold">Opcao:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($product['variations'] as $variation): ?>
                            <button type="button"
                                    class="btn btn-outline-secondary variation-btn <?= $variation['stock'] <= 0 ? 'disabled' : '' ?>"
                                    data-id="<?= $variation['id'] ?>"
                                    data-price="<?= $variation['price'] ?? $currentPrice ?>"
                                    data-stock="<?= $variation['stock'] ?>">
                                <?= esc($variation['name']) ?>
                                <?php if ($variation['stock'] <= 0): ?>
                                    <small class="text-danger">(Esgotado)</small>
                                <?php endif; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Stock Status -->
            <div class="mb-4">
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Em Estoque</span>
                    <?php if ($product['stock'] <= 5): ?>
                        <span class="badge bg-warning ms-2">Ultimas <?= $product['stock'] ?> unidades!</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Esgotado</span>
                <?php endif; ?>
            </div>

            <!-- Add to Cart -->
            <?php if ($product['stock'] > 0): ?>
                <form id="addToCartForm" class="mb-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="variation_id" id="variationId" value="">

                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label class="form-label mb-0">Quantidade:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 130px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(-1)">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="<?= ($product['max_per_order'] ?? 0) ?: ($product['stock'] ?? 99) ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus me-2"></i>Adicionar ao Carrinho
                        </button>
                        <a href="<?= base_url('checkout') ?>" class="btn btn-success btn-lg buy-now-btn" style="display: none;">
                            <i class="bi bi-lightning-fill me-2"></i>Comprar Agora
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-secondary">
                    <i class="bi bi-bell me-2"></i>
                    Este produto esta esgotado. Cadastre-se para ser avisado quando estiver disponivel.
                </div>
                <form action="<?= base_url('avisar-disponibilidade') ?>" method="post" class="mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Seu e-mail" required>
                        <button type="submit" class="btn btn-primary">Avisar-me</button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Wishlist & Share -->
            <div class="d-flex gap-3 mb-4">
                <button type="button" class="btn btn-outline-secondary" onclick="toggleWishlist(<?= $product['id'] ?>, this)">
                    <i class="bi bi-heart<?= ($product['in_wishlist'] ?? false) ? '-fill text-danger' : '' ?> me-1"></i>
                    Favoritar
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="bi bi-share me-1"></i>Compartilhar
                </button>
            </div>

            <!-- Shipping Calculator -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-truck me-2"></i>Calcular Frete</h6>
                    <form id="shippingForm" class="row g-2">
                        <div class="col">
                            <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder="Digite seu CEP" maxlength="9">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-primary">Calcular</button>
                        </div>
                    </form>
                    <div id="shippingResults" class="mt-3"></div>
                    <a href="https://buscacepinter.correios.com.br/" target="_blank" class="small text-muted">Nao sei meu CEP</a>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-credit-card me-2"></i>Formas de Pagamento</h6>
                    <img src="<?= base_url('assets/images/pagamento_mp.png') ?>" alt="Formas de Pagamento - Mercado Pago" class="img-fluid" style="max-width: 200px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <?php
    // Processar especificacoes do JSON
    $especificacoesJson = [];
    if (!empty($product['especificacoes'])) {
        if (is_string($product['especificacoes'])) {
            $especificacoesJson = json_decode($product['especificacoes'], true) ?? [];
        } else {
            $especificacoesJson = $product['especificacoes'];
        }
    }
    $hasSpecs = !empty($product['specifications']) || !empty($especificacoesJson);
    ?>
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Descricao</button>
                </li>
                <?php if ($hasSpecs): ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specs">Especificacoes</button>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">
                        Avaliacoes (<?= $product['reviews_count'] ?? 0 ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4 bg-white border border-top-0 rounded-bottom">
                <!-- Description -->
                <div class="tab-pane fade show active" id="description">
                    <?= $product['description'] ?>
                </div>

                <!-- Specifications -->
                <?php if ($hasSpecs): ?>
                    <div class="tab-pane fade" id="specs">
                        <h5 class="mb-4 text-primary fw-bold">Especificacoes Tecnicas</h5>
                        <div class="table-responsive">
                            <table class="table table-specs mb-0">
                                <tbody>
                                    <?php if (!empty($especificacoesJson)): ?>
                                        <?php $rowIndex = 0; foreach ($especificacoesJson as $key => $value): ?>
                                            <tr class="<?= $rowIndex % 2 === 0 ? 'bg-light' : '' ?>">
                                                <td class="spec-label fw-semibold text-dark"><?= esc($key) ?></td>
                                                <td class="spec-value"><?= esc($value) ?></td>
                                            </tr>
                                        <?php $rowIndex++; endforeach; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($product['specifications'])): ?>
                                        <?php foreach ($product['specifications'] as $spec): ?>
                                            <tr class="<?= $rowIndex % 2 === 0 ? 'bg-light' : '' ?>">
                                                <td class="spec-label fw-semibold text-dark"><?= esc($spec['name']) ?></td>
                                                <td class="spec-value"><?= esc($spec['value']) ?></td>
                                            </tr>
                                        <?php $rowIndex++; endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Reviews -->
                <div class="tab-pane fade" id="reviews">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= esc($review['customer_name']) ?></strong>
                                        <div class="text-warning small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                                </div>
                                <p class="mt-2 mb-0"><?= esc($review['comment']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Este produto ainda nao possui avaliacoes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="mt-5">
            <h2 class="section-title">Produtos Relacionados</h2>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="col-6 col-md-3">
                        <?= view('front/partials/product-card', ['product' => $related]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compartilhar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="d-flex justify-content-center gap-3">
                    <a href="https://wa.me/?text=<?= urlencode($product['name'] . ' - ' . current_url()) ?>" target="_blank" class="btn btn-success btn-lg">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="btn btn-primary btn-lg">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($product['name']) ?>" target="_blank" class="btn btn-info btn-lg">
                        <i class="bi bi-twitter"></i>
                    </a>
                </div>
                <hr>
                <div class="input-group">
                    <input type="text" class="form-control" value="<?= current_url() ?>" id="shareUrl" readonly>
                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('shareUrl').value); toastr.success('Link copiado!');">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Tabela de Especificacoes Tecnicas */
.table-specs {
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}

.table-specs tr:first-child td {
    border-top: none;
}

.table-specs td {
    padding: 14px 20px;
    border-top: 1px solid #e2e8f0;
    vertical-align: middle;
}

.table-specs .spec-label {
    width: 280px;
    background-color: transparent;
    color: #1e3a5f;
    font-weight: 600;
    border-right: 1px solid #e2e8f0;
}

.table-specs .spec-value {
    color: #475569;
}

.table-specs tr.bg-light {
    background-color: #f8fafc !important;
}

.table-specs tr:hover {
    background-color: #f1f5f9 !important;
}

@media (max-width: 576px) {
    .table-specs .spec-label {
        width: 140px;
        padding: 10px 12px;
        font-size: 0.875rem;
    }

    .table-specs .spec-value {
        padding: 10px 12px;
        font-size: 0.875rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Quantity buttons
    function changeQty(delta) {
        const input = document.getElementById('quantity');
        const newVal = parseInt(input.value) + delta;
        if (newVal >= 1 && newVal <= parseInt(input.max)) {
            input.value = newVal;
        }
    }

    // Variation selection
    document.querySelectorAll('.variation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.classList.contains('disabled')) return;

            document.querySelectorAll('.variation-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');

            document.getElementById('variationId').value = this.dataset.id;

            // Update max quantity
            document.getElementById('quantity').max = this.dataset.stock;
        });
    });

    // Add to Cart
    document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('carrinho/adicionar') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cartCount').textContent = data.cart_count;
                document.querySelector('.buy-now-btn').style.display = 'block';
                toastr.success(data.message);
            } else {
                toastr.error(data.message);
            }
        });
    });

    // Shipping calculation
    document.getElementById('shippingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const zipcode = document.getElementById('zipcode').value;
        const quantity = document.getElementById('quantity')?.value || 1;
        const resultsDiv = document.getElementById('shippingResults');

        resultsDiv.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Calculando...';

        fetch('<?= base_url('produto/calcular-frete') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `zipcode=${zipcode}&product_id=<?= $product['id'] ?>&quantity=${quantity}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                data.options.forEach(option => {
                    const company = option.company ? `<small class="text-muted">${option.company}</small>` : '';
                    const priceHtml = `<span class="fw-bold">${parseFloat(option.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</span>`;
                    html += `
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <div class="fw-medium">${option.name}</div>
                                ${company}
                                <small class="text-muted d-block">${option.deadline} dias uteis</small>
                            </div>
                            <div class="text-end">
                                ${priceHtml}
                            </div>
                        </div>
                    `;
                });
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = `<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>${data.message}</div>`;
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Erro ao calcular frete.</div>';
        });
    });

    // CEP mask
    document.getElementById('zipcode')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').replace(/(\d{5})(\d)/, '$1-$2').substring(0, 9);
    });
</script>
<?= $this->endSection() ?>
