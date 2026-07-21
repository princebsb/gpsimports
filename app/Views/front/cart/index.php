<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Carrinho de Compras</h1>

    <?php if (!empty($cart['items'])): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 100px;">Produto</th>
                                        <th></th>
                                        <th class="text-center" style="width: 150px;">Quantidade</th>
                                        <th class="text-end" style="width: 120px;">Preco</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart['items'] as $item): ?>
                                        <tr class="border-bottom" id="cart-item-<?= $item['id'] ?>">
                                            <td>
                                                <?php
                                                $itemImage = $item['image'] ?? '';
                                                $itemImageUrl = '';
                                                if (!empty($itemImage)) {
                                                    if (strpos($itemImage, 'http') === 0) {
                                                        $itemImageUrl = $itemImage;
                                                    } else {
                                                        $itemImageUrl = base_url('uploads/products/thumbs/' . $itemImage);
                                                    }
                                                } else {
                                                    $itemImageUrl = 'https://placehold.co/80x80/e9ecef/495057?text=Produto';
                                                }
                                                ?>
                                                <img src="<?= esc($itemImageUrl) ?>"
                                                     alt="<?= esc($item['name']) ?>"
                                                     class="rounded"
                                                     style="width: 80px; height: 80px; object-fit: cover;"
                                                     onerror="this.src='https://placehold.co/80x80/e9ecef/495057?text=Produto'">
                                            </td>
                                            <td>
                                                <a href="<?= base_url('produto/' . $item['slug']) ?>" class="text-dark text-decoration-none fw-medium">
                                                    <?= esc($item['name']) ?>
                                                </a>
                                                <?php if (!empty($item['variation_name'])): ?>
                                                    <div class="small text-muted"><?= esc($item['variation_name']) ?></div>
                                                <?php endif; ?>
                                                <div class="small text-muted">
                                                    SKU: <?= esc($item['sku'] ?? '-') ?>
                                                    <?php if (!empty($item['fonte'])): ?>
                                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.65rem;" title="<?= esc($item['fonte']) ?>">
                                                            <?= $item['fonte'] === 'megaeletronicos' ? 'ME' : ($item['fonte'] === 'atacadoconnect' ? 'AC' : strtoupper(substr($item['fonte'], 0, 2))) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm justify-content-center" style="width: 120px; margin: 0 auto;">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?= $item['id'] ?>, -1, <?= $item['price'] ?>)">-</button>
                                                    <input type="text" class="form-control text-center" id="qty-<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" readonly>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity(<?= $item['id'] ?>, 1, <?= $item['price'] ?>)">+</button>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <strong id="item-total-<?= $item['id'] ?>">R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?></strong>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-link text-danger p-0" onclick="removeItem(<?= $item['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="<?= base_url('produtos') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Continuar Comprando
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <!-- Coupon -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Cupom de Desconto</h6>
                        <?php if (!empty($cart['coupon'])): ?>
                            <div class="d-flex justify-content-between align-items-center bg-success bg-opacity-10 p-2 rounded">
                                <span class="text-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    <?= esc($cart['coupon']['code']) ?>
                                </span>
                                <button type="button" class="btn btn-link text-danger p-0" onclick="removeCoupon()">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        <?php else: ?>
                            <form id="couponForm">
                                <div class="input-group">
                                    <input type="text" name="coupon_code" id="couponCode" class="form-control" placeholder="Digite o cupom">
                                    <button type="submit" class="btn btn-outline-primary">Aplicar</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Aviso de peso -->
                <?php
                $pesoTotal = 0;
                foreach ($cart['items'] as $item) {
                    $pesoTotal += (float)($item['weight'] ?? 0.3) * $item['quantity'];
                }
                $pesoExcedido = $pesoTotal > 30;
                ?>

                <!-- Shipping -->
                <div class="card mb-3">
                    <div class="card-body" id="shippingCard">
                        <h6 class="card-title d-flex justify-content-between align-items-center">
                            Calcular Frete
                            <small class="text-muted fw-normal" id="pesoDisplay">
                                <i class="bi bi-box-seam me-1"></i><?= number_format($pesoTotal, 2, ',', '.') ?> kg
                            </small>
                        </h6>
                        <?php if ($pesoExcedido): ?>
                        <div class="alert alert-danger small mb-0 weight-warning">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Peso total: <?= number_format($pesoTotal, 2, ',', '.') ?> kg</strong><br>
                            O limite dos Correios é 30kg por envio.<br>
                            Entre em contato para cotação especial.
                        </div>
                        <?php else: ?>
                        <form id="shippingForm">
                            <div class="input-group">
                                <input type="text" name="zipcode" id="zipcode" class="form-control" placeholder="CEP" maxlength="9" value="<?= $cart['shipping_zipcode'] ?? '' ?>">
                                <button type="submit" class="btn btn-outline-primary">Calcular</button>
                            </div>
                        </form>
                        <div id="shippingOptions" class="mt-3">
                            <?php if (!empty($cart['shipping_options'])): ?>
                                <?php
                                $cartSubtotal = (float)($cart['subtotal'] ?? 0);
                                $limitePAC = 4477.36;
                                $limiteSEDEX = 38057.59;
                                ?>
                                <?php foreach ($cart['shipping_options'] as $option): ?>
                                    <?php
                                    $isPAC = stripos($option['name'], 'PAC') !== false;
                                    $isSEDEX = stripos($option['name'], 'SEDEX') !== false;
                                    $limiteServico = $isPAC ? $limitePAC : ($isSEDEX ? $limiteSEDEX : 0);
                                    $excedeLimite = $limiteServico > 0 && $cartSubtotal > $limiteServico;
                                    ?>
                                    <div class="form-check border rounded p-3 mb-2 <?= ($cart['shipping_method'] ?? '') === $option['code'] ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                        <input type="radio" name="shipping_method" value="<?= $option['code'] ?>"
                                               class="form-check-input" id="shipping_<?= $option['code'] ?>"
                                               <?= ($cart['shipping_method'] ?? '') === $option['code'] ? 'checked' : '' ?>
                                               onchange="selectShipping('<?= $option['code'] ?>', <?= $option['price'] ?>)">
                                        <label class="form-check-label w-100" for="shipping_<?= $option['code'] ?>">
                                            <div class="d-flex justify-content-between">
                                                <span><?= esc($option['name']) ?></span>
                                                <strong>R$ <?= number_format($option['price'], 2, ',', '.') ?></strong>
                                            </div>
                                            <small class="text-muted"><?= $option['deadline'] ?> dias úteis +3 dias úteis (importação)</small>
                                            <?php if ($excedeLimite): ?>
                                                <div class="text-warning small mt-1">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                    Seguro: cobertura máxima de R$ <?= number_format($limiteServico, 2, ',', '.') ?>, conforme limite dos Correios.
                                                </div>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Resumo do Pedido</h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal">R$ <?= number_format($cart['subtotal'], 2, ',', '.') ?></span>
                        </div>

                        <?php if (($cart['discount'] ?? 0) > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto</span>
                                <span id="discount">-R$ <?= number_format($cart['discount'] ?? 0, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete</span>
                            <span id="shipping">
                                <?php if (isset($cart['shipping_cost'])): ?>
                                    R$ <?= number_format($cart['shipping_cost'], 2, ',', '.') ?>
                                <?php else: ?>
                                    Calcular
                                <?php endif; ?>
                            </span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <strong class="h5 mb-0">Total</strong>
                            <strong class="h5 mb-0 text-primary" id="total">R$ <?= number_format($cart['total'], 2, ',', '.') ?></strong>
                        </div>

                        <?php
                        $pixDiscount = get_pix_discount($cart['total']);
                        $pixTotal = $cart['total'] * (1 - $pixDiscount / 100);
                        ?>
                        <div class="d-flex justify-content-between mb-3 text-success">
                            <span><i class="bi bi-qr-code me-1"></i>No PIX</span>
                            <strong id="pix-total">R$ <?= number_format($pixTotal, 2, ',', '.') ?></strong>
                            <span class="badge bg-success" id="pix-badge"><?= $pixDiscount ?>% OFF</span>
                        </div>

                        <?php
                        // Verificar se todos os produtos são da fonte atacadoconnect
                        $allAC = true;
                        $subtotalUSD = 0;
                        foreach ($cart['items'] as $item) {
                            if (($item['fonte'] ?? '') !== 'atacadoconnect') {
                                $allAC = false;
                                break;
                            }
                            $subtotalUSD += (float)($item['preco_usd'] ?? 0) * $item['quantity'];
                        }

                        $subtotal = (float)($cart['subtotal'] ?? 0);

                        // Se todos AC, mínimo é 100 USD convertido para BRL, senão R$ 500
                        if ($allAC && !empty($cart['items'])) {
                            $exchangeService = new \App\Services\ExchangeRateService();
                            $cotacao = $exchangeService->getRate();
                            $minUSD = 100.00;
                            $minSubtotal = $minUSD * $cotacao; // Converte para reais
                            $falta = $minSubtotal - $subtotal;
                            $canCheckout = $subtotal >= $minSubtotal;
                        } else {
                            $minSubtotal = 500.00;
                            $falta = $minSubtotal - $subtotal;
                            $canCheckout = $subtotal >= $minSubtotal;
                        }

                        // Bloquear checkout se peso excedido
                        if ($pesoExcedido) {
                            $canCheckout = false;
                        }
                        ?>

                        <div id="checkoutBtnContainer">
                            <?php if ($pesoExcedido): ?>
                                <div class="alert alert-danger small mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    <strong>Peso excede o limite de 30kg</strong><br>
                                    Entre em contato para cotação especial.
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                                    </button>
                                </div>
                            <?php elseif (!$canCheckout): ?>
                                <div class="alert alert-warning small mb-3" id="minValueAlert">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Valor mínimo em produtos: R$ <?= number_format($minSubtotal, 2, ',', '.') ?></strong>
                                    <br>Falta <strong>R$ <?= number_format($falta, 2, ',', '.') ?></strong> para finalizar.
                                    <br><small class="text-muted">(sem contar o frete)</small>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="d-grid">
                                    <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-lg">
                                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>Compra 100% segura
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">Seu carrinho esta vazio</h4>
            <p class="text-muted">Adicione produtos para continuar comprando</p>
            <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                <i class="bi bi-bag me-1"></i>Ver Produtos
            </a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Headers para requisicoes AJAX
    const ajaxHeaders = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    };

    // Formatar moeda brasileira
    function formatMoney(value) {
        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    // Update quantity
    function updateQuantity(itemId, delta, price) {
        const qtyInput = document.getElementById('qty-' + itemId);
        const currentQty = parseInt(qtyInput.value);
        const newQty = currentQty + delta;

        if (newQty < 1) {
            removeItem(itemId);
            return;
        }

        fetch('<?= base_url('carrinho/atualizar') ?>', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `item_id=${itemId}&quantity=${newQty}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                qtyInput.value = newQty;
                document.getElementById('item-total-' + itemId).textContent = formatMoney(price * newQty);
                document.getElementById('cartCount').textContent = data.cart_count;
                updateTotals(data);

                // Atualizar peso e status do carrinho
                if (data.peso_total !== undefined) {
                    updateWeightStatus(data.peso_total, data.peso_excedido);
                }

                // Atualizar opções de frete se recebidas e peso não excedido
                if (!data.peso_excedido && data.shipping_options && data.shipping_options.length > 0) {
                    updateShippingOptions(data.shipping_options, data.subtotal);
                }

                toastr.success('Quantidade atualizada');
            } else {
                toastr.error(data.message);
            }
        });
    }

    // Atualizar status do peso
    function updateWeightStatus(pesoTotal, pesoExcedido) {
        const shippingCard = document.getElementById('shippingCard');
        if (!shippingCard) return;

        // Atualizar display do peso
        const pesoDisplay = document.getElementById('pesoDisplay');
        if (pesoDisplay) {
            pesoDisplay.innerHTML = `<i class="bi bi-box-seam me-1"></i>${pesoTotal.toFixed(2).replace('.', ',')} kg`;
            pesoDisplay.className = pesoExcedido ? 'text-danger fw-normal' : 'text-muted fw-normal';
        }

        const shippingForm = document.getElementById('shippingForm');
        const shippingOptions = document.getElementById('shippingOptions');

        if (pesoExcedido) {
            // Esconder form de frete e mostrar aviso de peso
            if (shippingForm) shippingForm.style.display = 'none';
            if (shippingOptions) shippingOptions.style.display = 'none';

            // Remover aviso antigo se existir
            const oldWarning = shippingCard.querySelector('.weight-warning');
            if (oldWarning) oldWarning.remove();

            // Adicionar novo aviso
            const warningHtml = `
                <div class="alert alert-danger small mb-0 weight-warning">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Peso total: ${pesoTotal.toFixed(2).replace('.', ',')} kg</strong><br>
                    O limite dos Correios é 30kg por envio.<br>
                    Entre em contato para cotação especial.
                </div>
            `;
            const cardTitle = shippingCard.querySelector('.card-title');
            if (cardTitle) {
                cardTitle.insertAdjacentHTML('afterend', warningHtml);
            }

            // Atualizar botão de checkout
            updateCheckoutForWeight(true, pesoTotal);
        } else {
            // Remover aviso de peso se existir
            const oldWarning = shippingCard.querySelector('.weight-warning');
            if (oldWarning) oldWarning.remove();

            // Mostrar form de frete
            if (shippingForm) shippingForm.style.display = '';
            if (shippingOptions) shippingOptions.style.display = '';

            // Restaurar botão de checkout (baseado no valor mínimo)
            updateCheckoutForWeight(false, pesoTotal);
        }
    }

    // Atualizar botão de checkout baseado no peso
    function updateCheckoutForWeight(pesoExcedido, pesoTotal) {
        const btnContainer = document.getElementById('checkoutBtnContainer');
        if (!btnContainer) return;

        if (pesoExcedido) {
            btnContainer.innerHTML = `
                <div class="alert alert-danger small mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <strong>Peso excede o limite de 30kg</strong><br>
                    Peso atual: ${pesoTotal.toFixed(2).replace('.', ',')} kg<br>
                    Entre em contato para cotação especial.
                </div>
                <div class="d-grid">
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                    </button>
                </div>
            `;
        } else {
            // Restaurar baseado no valor mínimo
            const subtotalText = document.getElementById('subtotal')?.textContent || '0';
            const subtotal = parseFloat(subtotalText.replace(/[R$\s.]/g, '').replace(',', '.')) || 0;
            updateMinValueAlert(subtotal);
        }
    }

    // Limites de seguro dos Correios
    const LIMITE_PAC = 4477.36;
    const LIMITE_SEDEX = 38057.59;

    // Atualizar opções de frete
    function updateShippingOptions(options, subtotal = null) {
        const optionsDiv = document.getElementById('shippingOptions');
        if (!optionsDiv) return;

        // Pegar subtotal atual se não foi passado
        if (subtotal === null || subtotal === undefined) {
            const subtotalText = document.getElementById('subtotal')?.textContent || '0';
            // Remove "R$", espaços e pontos de milhar, troca vírgula por ponto
            subtotal = parseFloat(subtotalText.replace(/[R$\s.]/g, '').replace(',', '.')) || 0;
        }

        let html = '';
        options.forEach((option, index) => {
            const checked = index === 0 ? 'checked' : '';
            const isPAC = option.name.toUpperCase().includes('PAC');
            const isSEDEX = option.name.toUpperCase().includes('SEDEX');
            const limiteServico = isPAC ? LIMITE_PAC : (isSEDEX ? LIMITE_SEDEX : 0);
            const excedeLimite = limiteServico > 0 && subtotal > limiteServico;

            let avisoSeguro = '';
            if (excedeLimite) {
                avisoSeguro = `
                    <div class="text-warning small mt-1">
                        <i class="bi bi-exclamation-triangle"></i>
                        Seguro: cobertura máxima de ${formatMoney(limiteServico)}, conforme limite dos Correios.
                    </div>
                `;
            }

            html += `
                <div class="form-check border rounded p-3 mb-2 shipping-option ${index === 0 ? 'border-primary bg-primary bg-opacity-10' : ''}" style="cursor: pointer;">
                    <input type="radio" name="shipping_method" value="${option.code}"
                           class="form-check-input" id="shipping_${option.code}" ${checked}
                           onchange="selectShipping('${option.code}', ${option.price})">
                    <label class="form-check-label w-100" for="shipping_${option.code}" style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <span><strong>${option.name}</strong> ${option.company ? '- ' + option.company : ''}</span>
                            <strong class="text-primary">${formatMoney(option.price)}</strong>
                        </div>
                        <small class="text-muted">${option.deadline} dias úteis +3 dias úteis (importação)</small>
                        ${avisoSeguro}
                    </label>
                </div>
            `;
        });
        optionsDiv.innerHTML = html;

        // Auto-selecionar primeira opção
        if (options.length > 0) {
            selectShipping(options[0].code, options[0].price);
        }
    }

    // Remove item
    function removeItem(itemId) {
        Swal.fire({
            title: 'Remover item?',
            text: 'Deseja remover este item do carrinho?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('carrinho/remover') ?>', {
                    method: 'POST',
                    headers: ajaxHeaders,
                    body: `item_id=${itemId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover linha com animacao
                        const row = document.getElementById('cart-item-' + itemId);
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            row.remove();

                            // Se carrinho vazio, mostrar mensagem
                            if (data.cart_count === 0) {
                                document.querySelector('.container.py-4').innerHTML = `
                                    <h1 class="h3 mb-4">Carrinho de Compras</h1>
                                    <div class="text-center py-5">
                                        <i class="bi bi-cart-x display-1 text-muted"></i>
                                        <h4 class="mt-3">Seu carrinho esta vazio</h4>
                                        <p class="text-muted">Adicione produtos para continuar comprando</p>
                                        <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                                            <i class="bi bi-bag me-1"></i>Ver Produtos
                                        </a>
                                    </div>
                                `;
                                return;
                            }
                        }, 300);

                        document.getElementById('cartCount').textContent = data.cart_count;
                        updateTotals(data);

                        // Atualizar peso e status do carrinho
                        if (data.peso_total !== undefined) {
                            updateWeightStatus(data.peso_total, data.peso_excedido);
                        }

                        // Atualizar opções de frete se recebidas e peso não excedido
                        if (!data.peso_excedido && data.shipping_options && data.shipping_options.length > 0) {
                            updateShippingOptions(data.shipping_options, data.subtotal);
                        }

                        toastr.success('Item removido');
                    } else {
                        toastr.error(data.message || 'Erro ao remover item');
                    }
                })
                .catch(() => toastr.error('Erro de conexao'));
            }
        });
    }

    // Apply coupon
    document.getElementById('couponForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('couponCode').value;
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('<?= base_url('carrinho/cupom') ?>', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `coupon_code=${code}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Aplicar';
            if (data.success) {
                toastr.success(data.message || 'Cupom aplicado!');
                setTimeout(() => location.reload(), 500);
            } else {
                toastr.error(data.message);
            }
        });
    });

    // Remove coupon
    function removeCoupon() {
        fetch('<?= base_url('carrinho/remover-cupom') ?>', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Cupom removido');
                setTimeout(() => location.reload(), 500);
            }
        });
    }

    // Calculate shipping
    document.getElementById('shippingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const zipcode = document.getElementById('zipcode').value;
        const optionsDiv = document.getElementById('shippingOptions');

        optionsDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Calculando frete...</div>';

        fetch('<?= base_url('carrinho/calcular-frete') ?>', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `zipcode=${zipcode}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.options.length > 0) {
                updateShippingOptions(data.options);
            } else {
                optionsDiv.innerHTML = `<div class="alert alert-warning small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>${data.message || 'Nenhuma opção de frete disponivel'}</div>`;
            }
        })
        .catch(() => {
            optionsDiv.innerHTML = '<div class="alert alert-danger small mb-0">Erro ao calcular frete</div>';
        });
    });

    // Select shipping method
    function selectShipping(code, price) {
        fetch('<?= base_url('carrinho/selecionar-frete') ?>', {
            method: 'POST',
            headers: ajaxHeaders,
            body: `shipping_method=${code}&shipping_price=${price}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('shipping').textContent = formatMoney(price);
                document.getElementById('total').textContent = formatMoney(data.total);
                // Atualizar preco PIX
                const pixTotalEl = document.getElementById('pix-total');
                if (pixTotalEl) {
                    const pixTotal = data.total * (1 - PIX_DISCOUNT / 100);
                    pixTotalEl.textContent = formatMoney(pixTotal);
                }
                toastr.success('Frete selecionado');
            }
        });
    }

    // CEP mask
    document.getElementById('zipcode')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').replace(/(\d{5})(\d)/, '$1-$2').substring(0, 9);
    });

    const MIN_SUBTOTAL = 500.00;

    const PIX_DISCOUNT_NORMAL = <?= (float) (setting('pix_discount') ?? 5) ?>;
    const PIX_DISCOUNT_HIGH = <?= (float) (setting('pix_discount_high_value') ?? 3) ?>;
    const PIX_THRESHOLD = <?= (float) (setting('pix_discount_threshold') ?? 5000) ?>;

    function getPixDiscount(value) {
        return value > PIX_THRESHOLD ? PIX_DISCOUNT_HIGH : PIX_DISCOUNT_NORMAL;
    }

    function updateTotals(data) {
        const subtotal = data.subtotal || data.cart?.subtotal || 0;
        const total = data.total || data.cart?.total || 0;

        document.getElementById('subtotal').textContent = formatMoney(subtotal);
        document.getElementById('total').textContent = formatMoney(total);

        // Atualizar preco PIX com desconto dinamico
        const pixTotalEl = document.getElementById('pix-total');
        const pixBadge = document.getElementById('pix-badge');
        if (pixTotalEl) {
            const pixDiscount = getPixDiscount(total);
            const pixTotal = total * (1 - pixDiscount / 100);
            pixTotalEl.textContent = formatMoney(pixTotal);
            if (pixBadge) {
                pixBadge.textContent = pixDiscount + '% OFF';
            }
        }

        // Atualizar desconto se existir
        const discountEl = document.getElementById('discount');
        if (discountEl && data.cart?.discount) {
            discountEl.textContent = '-' + formatMoney(data.cart.discount);
        }

        // Atualizar alerta de valor minimo
        updateMinValueAlert(subtotal);
    }

    function updateMinValueAlert(subtotal) {
        const btnContainer = document.getElementById('checkoutBtnContainer');
        if (!btnContainer) return;

        const falta = MIN_SUBTOTAL - subtotal;

        if (subtotal < MIN_SUBTOTAL) {
            btnContainer.innerHTML = `
                <div class="alert alert-warning small mb-3" id="minValueAlert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Valor minimo em produtos: R$ ${MIN_SUBTOTAL.toFixed(2).replace('.', ',')}</strong>
                    <br>Falta <strong>${formatMoney(falta)}</strong> para finalizar.
                    <br><small class="text-muted">(sem contar o frete)</small>
                </div>
                <div class="d-grid">
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                    </button>
                </div>
            `;
        } else {
            btnContainer.innerHTML = `
                <div class="d-grid">
                    <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-lock me-1"></i>Finalizar Compra
                    </a>
                </div>
            `;
        }
    }
</script>
<?= $this->endSection() ?>
