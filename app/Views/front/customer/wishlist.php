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
            <h2 class="mb-4">Meus Favoritos</h2>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($wishlist)): ?>
                <div class="row">
                    <?php foreach ($wishlist as $item): ?>
                        <div class="col-md-4 col-6 mb-4" id="wishlist-item-<?= $item['product_id'] ?>">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <a href="<?= base_url('produto/' . ($item['slug'] ?? $item['product_id'])) ?>">
                                        <img src="<?= base_url('uploads/products/' . ($item['featured_image'] ?? 'default.jpg')) ?>"
                                             alt="<?= esc($item['name'] ?? 'Produto') ?>"
                                             class="card-img-top"
                                             style="height: 200px; object-fit: cover;">
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            onclick="removeFromWishlist(<?= $item['product_id'] ?>)"
                                            title="Remover dos favoritos">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="<?= base_url('produto/' . ($item['slug'] ?? $item['product_id'])) ?>" class="text-decoration-none text-dark">
                                            <?= esc($item['name'] ?? 'Produto') ?>
                                        </a>
                                    </h6>
                                    <?php if (!empty($item['sale_price']) && $item['sale_price'] < $item['price']): ?>
                                        <p class="card-text mb-1">
                                            <small class="text-muted text-decoration-line-through">R$ <?= number_format($item['price'], 2, ',', '.') ?></small>
                                        </p>
                                        <p class="card-text text-primary fw-bold">R$ <?= number_format($item['sale_price'], 2, ',', '.') ?></p>
                                    <?php else: ?>
                                        <p class="card-text text-primary fw-bold">R$ <?= number_format($item['price'] ?? 0, 2, ',', '.') ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <button type="button"
                                            class="btn btn-primary btn-sm w-100"
                                            onclick="addToCart(<?= $item['product_id'] ?>)">
                                        <i class="bi bi-cart-plus me-1"></i>Adicionar ao Carrinho
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-heart fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Voce ainda nao tem produtos favoritos.</p>
                        <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                            <i class="bi bi-bag me-2"></i>Explorar Produtos
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function removeFromWishlist(productId) {
        Swal.fire({
            title: 'Remover dos favoritos?',
            text: 'O produto sera removido da sua lista de favoritos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('minha-conta/favoritos/remover') ?>/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`wishlist-item-${productId}`).remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Removido!',
                            text: 'Produto removido dos favoritos.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Check if wishlist is empty
                        if (document.querySelectorAll('[id^="wishlist-item-"]').length === 0) {
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Nao foi possivel remover o produto.'
                    });
                });
            }
        });
    }

    function addToCart(productId) {
        fetch('<?= base_url('carrinho/adicionar') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}&quantity=1&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Adicionado!',
                    text: 'Produto adicionado ao carrinho.',
                    showCancelButton: true,
                    confirmButtonText: 'Ver Carrinho',
                    cancelButtonText: 'Continuar Comprando',
                    confirmButtonColor: '#0d6efd'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('carrinho') ?>';
                    }
                });

                // Update cart count
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count || 0;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: data.message || 'Nao foi possivel adicionar o produto.'
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao adicionar produto ao carrinho.'
            });
        });
    }
</script>
<?= $this->endSection() ?>
