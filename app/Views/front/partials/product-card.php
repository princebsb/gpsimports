<div class="product-card h-100">
    <div class="product-image">
        <a href="<?= base_url('produto/' . $product['slug']) ?>">
            <?php
            $featuredImage = $product['featured_image'] ?? '';
            $imageUrl = '';

            if (!empty($featuredImage)) {
                // Verificar se é URL externa (http/https)
                if (strpos($featuredImage, 'http') === 0) {
                    $imageUrl = $featuredImage;
                } else {
                    // Imagem local
                    $imagePath = FCPATH . 'uploads/products/' . $featuredImage;
                    if (file_exists($imagePath)) {
                        $imageUrl = base_url('uploads/products/' . $featuredImage);
                    }
                }
            }

            if ($imageUrl):
            ?>
                <img src="<?= esc($imageUrl) ?>" alt="<?= esc($product['name']) ?>" loading="lazy" onerror="this.src='https://placehold.co/400x400/e9ecef/495057?text=Sem+Imagem'">
            <?php else: ?>
                <img src="https://placehold.co/400x400/e9ecef/495057?text=<?= urlencode(substr($product['name'], 0, 20)) ?>" alt="<?= esc($product['name']) ?>">
            <?php endif; ?>
        </a>

        <!-- Badges -->
        <div class="product-badges">
            <?php if ($product['is_new']): ?>
                <span class="badge bg-success">Novo</span>
            <?php endif; ?>
            <?php if ($product['price'] > 0 && $product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                <?php $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
                <span class="badge bg-danger">-<?= $discount ?>%</span>
            <?php endif; ?>
            <?php if ($product['stock'] <= 0): ?>
                <span class="badge bg-secondary">Esgotado</span>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="product-actions">
            <button type="button" class="btn btn-light" onclick="toggleWishlist(<?= $product['id'] ?>, this)" title="Favoritar">
                <i class="bi bi-heart<?= ($product['in_wishlist'] ?? false) ? '-fill text-danger' : '' ?>"></i>
            </button>
            <a href="<?= base_url('produto/' . $product['slug']) ?>" class="btn btn-light" title="Ver Detalhes">
                <i class="bi bi-eye"></i>
            </a>
        </div>
    </div>

    <div class="product-info">
        <?php if (!empty($product['category_name'])): ?>
            <div class="product-category"><?= esc($product['category_name']) ?></div>
        <?php endif; ?>

        <h3 class="product-title">
            <a href="<?= base_url('produto/' . $product['slug']) ?>" class="text-decoration-none text-dark">
                <?= esc($product['name']) ?>
            </a>
        </h3>

        <div class="product-price">
            <?php
            $currentPrice = $product['current_price'] ?? ($product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['sale_price'] : $product['price']);
            ?>
            <?php if ($currentPrice > 0): ?>
                <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                    <span class="old-price">R$ <?= number_format($product['price'], 2, ',', '.') ?></span>
                <?php endif; ?>
                <span class="current-price">R$ <?= number_format($currentPrice, 2, ',', '.') ?></span>
            <?php else: ?>
                <span class="current-price text-warning">Consulte</span>
            <?php endif; ?>
        </div>

        <?php if ($currentPrice > 0): ?>
            <?php
            $pixDiscount = get_pix_discount($currentPrice);
            $pixPrice = $currentPrice * (1 - $pixDiscount / 100);
            ?>
            <?php $parcelasSemJuros = (int) (setting('installments_no_interest') ?? 1); ?>
            <div class="pix-price text-success small mb-1">
                <i class="bi bi-qr-code"></i>
                <strong>R$ <?= number_format($pixPrice, 2, ',', '.') ?></strong> no PIX
            </div>
            <div class="installments small">
                <i class="bi bi-credit-card"></i>
                <?= $parcelasSemJuros ?>x de R$ <?= number_format($currentPrice / $parcelasSemJuros, 2, ',', '.') ?> s/ juros
            </div>
        <?php endif; ?>

        <?php if ($product['stock'] > 0): ?>
            <button type="button" class="btn btn-primary btn-sm w-100 mt-2" onclick="addToCart(<?= $product['id'] ?>)">
                <i class="bi bi-cart-plus me-1"></i>Comprar
            </button>
        <?php else: ?>
            <button type="button" class="btn btn-secondary btn-sm w-100 mt-2" disabled>
                Indisponivel
            </button>
        <?php endif; ?>
    </div>
</div>
