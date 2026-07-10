<?= $this->extend('layouts/front') ?>

<?= $this->section('styles') ?>
<style>
    .checkout-step {
        display: none;
    }
    .checkout-step.active {
        display: block;
    }
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }
    .step-indicator .step {
        display: flex;
        align-items: center;
        color: #94a3b8;
    }
    .step-indicator .step.active {
        color: var(--primary-color);
    }
    .step-indicator .step.completed {
        color: #22c55e;
    }
    .step-indicator .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        margin-right: 0.5rem;
        font-weight: 600;
    }
    .step-indicator .step.active .step-number {
        background: var(--primary-color);
        color: #fff;
    }
    .step-indicator .step.completed .step-number {
        background: #22c55e;
        color: #fff;
    }
    .step-indicator .step-line {
        width: 60px;
        height: 2px;
        background: #e2e8f0;
        margin: 0 1rem;
    }
    .address-card {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .address-card:hover, .address-card.selected {
        border-color: var(--primary-color);
        background: rgba(37, 99, 235, 0.05);
    }
    .payment-info {
        background: linear-gradient(135deg, #00b1ea 0%, #009ee3 100%);
        border-radius: 0.5rem;
        padding: 1.5rem;
        color: #fff;
    }
    .payment-methods-list {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .payment-methods-list img {
        height: 24px;
        background: #fff;
        border-radius: 4px;
        padding: 2px 4px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <h1 class="h3 mb-4">Finalizar Compra</h1>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step1Indicator">
            <span class="step-number">1</span>
            <span>Endereco</span>
        </div>
        <div class="step-line"></div>
        <div class="step" id="step2Indicator">
            <span class="step-number">2</span>
            <span>Confirmacao</span>
        </div>
    </div>

    <div class="row">
        <!-- Checkout Steps -->
        <div class="col-lg-8">
            <form id="checkoutForm">
                <?= csrf_field() ?>

                <!-- Step 1: Address -->
                <div class="checkout-step active" id="step1">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Endereco de Entrega</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($addresses)): ?>
                                <div class="row g-3 mb-3">
                                    <?php foreach ($addresses as $address): ?>
                                        <div class="col-md-6">
                                            <div class="address-card <?= $address['is_default'] ? 'selected' : '' ?>" onclick="selectAddress(<?= $address['id'] ?>, this)" data-zipcode="<?= esc($address['zipcode']) ?>">
                                                <input type="radio" name="address_id" value="<?= $address['id'] ?>" class="d-none" <?= $address['is_default'] ? 'checked' : '' ?>>
                                                <div class="d-flex justify-content-between">
                                                    <strong><?= esc($address['label'] ?? 'Endereco') ?></strong>
                                                    <?php if ($address['is_default']): ?>
                                                        <span class="badge bg-primary">Padrao</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small mt-2">
                                                    <?= esc($address['street']) ?>, <?= esc($address['number']) ?>
                                                    <?php if ($address['complement']): ?>
                                                        - <?= esc($address['complement']) ?>
                                                    <?php endif; ?>
                                                    <br>
                                                    <?= esc($address['neighborhood']) ?> - <?= esc($address['city']) ?>/<?= esc($address['state']) ?>
                                                    <br>
                                                    CEP: <?= esc($address['zipcode']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($addresses)): ?>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                                    <i class="bi bi-plus me-1"></i>Novo Endereco
                                </button>
                            <?php endif; ?>

                            <div class="collapse <?= empty($addresses) ? 'show' : '' ?> mt-3" id="newAddressForm">
                                <input type="hidden" name="use_new_address" id="useNewAddress" value="<?= empty($addresses) ? '1' : '0' ?>">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">CEP <span class="text-danger">*</span></label>
                                        <input type="text" name="new_zipcode" id="newZipcode" class="form-control" maxlength="9" placeholder="00000-000">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Rua <span class="text-danger">*</span></label>
                                        <input type="text" name="new_street" id="newStreet" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Numero <span class="text-danger">*</span></label>
                                        <input type="text" name="new_number" id="newNumber" class="form-control">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Complemento</label>
                                        <input type="text" name="new_complement" id="newComplement" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bairro <span class="text-danger">*</span></label>
                                        <input type="text" name="new_neighborhood" id="newNeighborhood" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Cidade <span class="text-danger">*</span></label>
                                        <input type="text" name="new_city" id="newCity" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select name="new_state" id="newState" class="form-select">
                                            <option value="">UF</option>
                                            <?php foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf): ?>
                                                <option value="<?= $uf ?>"><?= $uf ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Metodo de Envio</h5>
                        </div>
                        <div class="card-body">
                            <div id="shippingMethods">
                                <?php if (!empty($cart['shipping_options'])): ?>
                                    <?php foreach ($cart['shipping_options'] as $option): ?>
                                        <div class="form-check border rounded p-3 mb-2">
                                            <input type="radio" name="shipping_method" value="<?= $option['code'] ?>"
                                                   class="form-check-input" id="ship_<?= $option['code'] ?>"
                                                   <?= ($cart['shipping_method'] ?? '') === $option['code'] ? 'checked' : '' ?>>
                                            <label class="form-check-label w-100" for="ship_<?= $option['code'] ?>">
                                                <div class="d-flex justify-content-between">
                                                    <span><?= esc($option['name']) ?></span>
                                                    <strong>R$ <?= number_format($option['price'], 2, ',', '.') ?></strong>
                                                </div>
                                                <small class="text-muted"><?= $option['deadline'] ?> dias uteis +3 dias úteis (importação)</small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted"><?= empty($addresses) ? 'Preencha o CEP para ver as opcoes de frete.' : 'Selecione um endereco para ver as opcoes de frete.' ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('carrinho') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Voltar ao Carrinho
                        </a>
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)">
                            Continuar<i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Confirmation -->
                <div class="checkout-step" id="step2">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Revisao do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <h6><i class="bi bi-geo-alt me-1"></i>Endereco de Entrega</h6>
                                    <div id="reviewAddress" class="text-muted small"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6><i class="bi bi-truck me-1"></i>Metodo de Envio</h6>
                                    <div id="reviewShipping" class="text-muted small"></div>
                                </div>
                            </div>

                            <hr>

                            <h6>Itens do Pedido</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($cart['items'] as $item): ?>
                                            <tr>
                                                <td style="width: 60px;">
                                                    <?php
                                                    $itemImg = $item['image'] ?? '';
                                                    $imgUrl = (strpos($itemImg, 'http') === 0) ? $itemImg : ($itemImg ? base_url('uploads/products/thumbs/' . $itemImg) : 'https://placehold.co/50x50/e9ecef/495057?text=P');
                                                    ?>
                                                    <img src="<?= esc($imgUrl) ?>" class="rounded" style="width: 50px;" onerror="this.src='https://placehold.co/50x50/e9ecef/495057?text=P'">
                                                </td>
                                                <td>
                                                    <?= esc($item['name']) ?>
                                                    <small class="text-muted d-block">Qtd: <?= $item['quantity'] ?></small>
                                                </td>
                                                <td class="text-end">R$ <?= number_format(($item['subtotal'] ?? ($item['price'] * $item['quantity'])), 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="payment-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <svg viewBox="0 0 48 48" style="height: 32px; width: 32px;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="24" cy="24" r="24" fill="white"/>
                                <path d="M24 8C15.164 8 8 15.164 8 24s7.164 16 16 16 16-7.164 16-16S32.836 8 24 8zm0 28c-6.627 0-12-5.373-12-12S17.373 12 24 12s12 5.373 12 12-5.373 12-12 12z" fill="#009EE3"/>
                                <path d="M24 14c-5.514 0-10 4.486-10 10s4.486 10 10 10 10-4.486 10-10-4.486-10-10-10zm4.5 11h-3.5v3.5c0 .552-.448 1-1 1s-1-.448-1-1V25h-3.5c-.552 0-1-.448-1-1s.448-1 1-1H23v-3.5c0-.552.448-1 1-1s1 .448 1 1V23h3.5c.552 0 1 .448 1 1s-.448 1-1 1z" fill="#009EE3"/>
                            </svg>
                            <span class="ms-3 h5 mb-0">Mercado Pago - Pagamento Seguro</span>
                        </div>
                        <p class="mb-3">Escolha como deseja pagar:</p>

                        <?php
                        $pixDiscount = (float) (setting('pix_discount') ?? 5);
                        $pixTotal = $cart['total'] * (1 - $pixDiscount / 100);
                        ?>

                        <!-- Opcao PIX com desconto -->
                        <div class="card mb-3 border-success">
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="radio" name="payment_method" id="payPix" value="pix" class="form-check-input" checked>
                                    <label for="payPix" class="form-check-label w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-qr-code text-success me-2 fs-4"></i>
                                                <strong>PIX</strong>
                                                <span class="badge bg-success ms-2"><?= $pixDiscount ?>% OFF</span>
                                                <br><small class="text-muted">Aprovação instantânea</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-success fs-5" id="pricePixTotal">R$ <?= number_format($pixTotal, 2, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Opcao Cartao -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="radio" name="payment_method" id="payCard" value="card" class="form-check-input">
                                    <label for="payCard" class="form-check-label w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-credit-card text-primary me-2 fs-4"></i>
                                                <strong>Cartão de Crédito</strong>
                                                <br><small class="text-muted">Em até 12x (juros a partir de 2x)</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="fs-5" id="priceCardTotal">R$ <?= number_format($cart['total'], 2, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Opcao Boleto -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="radio" name="payment_method" id="payBoleto" value="boleto" class="form-check-input">
                                    <label for="payBoleto" class="form-check-label w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-upc text-secondary me-2 fs-4"></i>
                                                <strong>Boleto Bancário</strong>
                                                <br><small class="text-muted">Aprovação em até 2 dias úteis</small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="fs-5" id="priceBoletoTotal">R$ <?= number_format($cart['total'], 2, ',', '.') ?></strong>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-shield-lock me-1"></i>
                            <strong>Pagamento 100% Seguro:</strong> Todo o processo é realizado pelo Mercado Pago.
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="agree_terms" id="agreeTerms" class="form-check-input" required>
                        <label for="agreeTerms" class="form-check-label">
                            Li e aceito os <a href="<?= base_url('termos-uso') ?>" target="_blank">Termos de Compra</a>
                        </label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left me-1"></i>Voltar
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="bi bi-lock me-1"></i>Finalizar Pedido
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header">
                    <h5 class="mb-0">Resumo</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?= count($cart['items']) ?> itens)</span>
                        <span>R$ <?= number_format($cart['subtotal'], 2, ',', '.') ?></span>
                    </div>

                    <?php if ($cart['discount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Desconto</span>
                            <span>-R$ <?= number_format($cart['discount'], 2, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete</span>
                        <span id="summaryShipping">
                            <?= isset($cart['shipping_cost']) ? 'R$ ' . number_format($cart['shipping_cost'], 2, ',', '.') : '-' ?>
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <strong class="h5 mb-0">Total</strong>
                        <strong class="h5 mb-0 text-primary" id="summaryTotal">R$ <?= number_format($cart['total'], 2, ',', '.') ?></strong>
                    </div>

                    <div class="mt-3 small text-muted text-center">
                        <i class="bi bi-shield-check me-1"></i>Compra 100% segura
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Formatar moeda brasileira
    function formatMoney(value) {
        return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    let currentStep = 1;
    let currentShippingPrice = <?= $cart['shipping_cost'] ?? 0 ?>;

    function goToStep(step) {
        // Validate current step before proceeding
        if (step > currentStep) {
            if (!validateStep(currentStep)) return;
        }

        document.querySelectorAll('.checkout-step').forEach(el => el.classList.remove('active'));
        document.getElementById('step' + step).classList.add('active');

        // Update indicators
        for (let i = 1; i <= 2; i++) {
            const indicator = document.getElementById('step' + i + 'Indicator');
            indicator.classList.remove('active', 'completed');
            if (i < step) indicator.classList.add('completed');
            if (i === step) indicator.classList.add('active');
        }

        // Update review on step 2
        if (step === 2) {
            updateReview();
        }

        currentStep = step;
        window.scrollTo(0, 0);
    }

    function validateStep(step) {
        if (step === 1) {
            const addressSelected = document.querySelector('input[name="address_id"]:checked');
            const useNewAddress = document.getElementById('useNewAddress').value === '1';
            const shippingSelected = document.querySelector('input[name="shipping_method"]:checked');

            if (!addressSelected && !useNewAddress) {
                toastr.error('Selecione um endereco de entrega');
                return false;
            }

            // Validate new address fields
            if (useNewAddress) {
                const zipcode = document.getElementById('newZipcode').value;
                const street = document.getElementById('newStreet').value;
                const number = document.getElementById('newNumber').value;
                const neighborhood = document.getElementById('newNeighborhood').value;
                const city = document.getElementById('newCity').value;
                const state = document.getElementById('newState').value;

                if (!zipcode || !street || !number || !neighborhood || !city || !state) {
                    toastr.error('Preencha todos os campos obrigatorios do endereco');
                    return false;
                }
            }

            if (!shippingSelected) {
                toastr.error('Selecione um metodo de envio');
                return false;
            }
        }

        return true;
    }

    function selectAddress(id, element) {
        document.querySelectorAll('.address-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        element.querySelector('input').checked = true;

        // Desmarcar novo endereco
        document.getElementById('useNewAddress').value = '0';

        // Fechar formulario de novo endereco
        const newAddressCollapse = bootstrap.Collapse.getInstance(document.getElementById('newAddressForm'));
        if (newAddressCollapse) {
            newAddressCollapse.hide();
        }

        // Calculate shipping
        const zipcode = element.dataset.zipcode;
        if (zipcode) {
            calculateShipping(zipcode);
        }
    }

    function calculateShipping(zipcode) {
        const shippingDiv = document.getElementById('shippingMethods');
        shippingDiv.innerHTML = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm me-2"></span>Calculando frete...</div>';

        fetch('<?= base_url('carrinho/calcular-frete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `zipcode=${zipcode.replace(/\D/g, '')}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.options && data.options.length > 0) {
                let html = '';
                data.options.forEach((option, index) => {
                    const checked = index === 0 ? 'checked' : '';
                    const price = formatMoney(option.price);
                    html += `
                        <div class="form-check border rounded p-3 mb-2">
                            <input type="radio" name="shipping_method" value="${option.code}"
                                   class="form-check-input" id="ship_${option.code}"
                                   data-price="${option.price}" data-name="${option.name}" ${checked}
                                   onchange="selectShipping('${option.code}', ${option.price}, '${option.name}')">
                            <label class="form-check-label w-100" for="ship_${option.code}">
                                <div class="d-flex justify-content-between">
                                    <span>${option.name}</span>
                                    <strong>${price}</strong>
                                </div>
                                <small class="text-muted">${option.deadline} dias uteis +3 dias úteis (importação)</small>
                            </label>
                        </div>
                    `;
                });
                shippingDiv.innerHTML = html;

                // Auto-select first option
                if (data.options.length > 0) {
                    selectShipping(data.options[0].code, data.options[0].price, data.options[0].name);
                }
            } else {
                shippingDiv.innerHTML = '<div class="alert alert-warning mb-0">Nao foi possivel calcular o frete. Tente novamente.</div>';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            shippingDiv.innerHTML = '<div class="alert alert-danger mb-0">Erro ao calcular frete.</div>';
        });
    }

    const pixDiscountPercent = <?= $pixDiscount ?>;

    function selectShipping(code, price, name) {
        currentShippingPrice = price;

        // Update shipping cost display
        document.getElementById('summaryShipping').textContent = formatMoney(price);

        // Update total
        const subtotal = <?= $cart['subtotal'] ?? 0 ?>;
        const discount = <?= $cart['discount'] ?? 0 ?>;
        let total = subtotal - discount + price;

        document.getElementById('summaryTotal').textContent = formatMoney(total);

        // Update payment options prices
        const pixTotal = total * (1 - pixDiscountPercent / 100);
        document.getElementById('pricePixTotal').textContent = formatMoney(pixTotal);
        document.getElementById('priceCardTotal').textContent = formatMoney(total);
        document.getElementById('priceBoletoTotal').textContent = formatMoney(total);

        // Save shipping selection
        fetch('<?= base_url('carrinho/selecionar-frete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `shipping_method=${code}&shipping_price=${price}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
        });
    }

    function updateReview() {
        // Address
        const useNewAddress = document.getElementById('useNewAddress').value === '1';
        const reviewAddressEl = document.getElementById('reviewAddress');

        if (useNewAddress) {
            const street = document.getElementById('newStreet').value;
            const number = document.getElementById('newNumber').value;
            const complement = document.getElementById('newComplement').value;
            const neighborhood = document.getElementById('newNeighborhood').value;
            const city = document.getElementById('newCity').value;
            const state = document.getElementById('newState').value;
            const zipcode = document.getElementById('newZipcode').value;

            reviewAddressEl.innerHTML = `
                ${street}, ${number}${complement ? ' - ' + complement : ''}<br>
                ${neighborhood} - ${city}/${state}<br>
                CEP: ${zipcode}
            `;
        } else {
            const addressInput = document.querySelector('input[name="address_id"]:checked');
            if (addressInput) {
                const card = addressInput.closest('.address-card');
                reviewAddressEl.innerHTML = card.querySelector('.small').innerHTML;
            }
        }

        // Shipping
        const shippingInput = document.querySelector('input[name="shipping_method"]:checked');
        if (shippingInput) {
            const name = shippingInput.dataset.name || shippingInput.closest('label').querySelector('span').textContent;
            const price = formatMoney(shippingInput.dataset.price || currentShippingPrice);
            document.getElementById('reviewShipping').innerHTML = `${name}<br><strong>${price}</strong>`;
        }
    }

    // Form submission
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validar se um metodo de envio foi selecionado
        const shippingSelected = document.querySelector('input[name="shipping_method"]:checked');
        if (!shippingSelected) {
            toastr.error('Selecione um metodo de envio antes de finalizar');
            goToStep(1);
            return;
        }

        if (!document.getElementById('agreeTerms').checked) {
            toastr.error('Voce precisa aceitar os termos');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

        const formData = new FormData(this);

        try {
            const response = await fetch('<?= base_url('checkout/processar') ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.redirect) {
                // Redirecionar para o Mercado Pago
                window.location.href = data.redirect;
            } else {
                toastr.error(data.message || 'Erro ao processar. Tente novamente.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-lock me-1"></i>Ir para Pagamento';
            }
        } catch (error) {
            toastr.error('Erro ao processar. Tente novamente.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-lock me-1"></i>Ir para Pagamento';
        }
    });

    // Mask CEP do novo endereco
    document.getElementById('newZipcode')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').replace(/(\d{5})(\d)/, '$1-$2').substring(0, 9);
    });

    // Auto-complete e calcular frete do novo endereco
    document.getElementById('newZipcode')?.addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            // Auto-complete endereco
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('newStreet').value = data.logradouro || '';
                        document.getElementById('newNeighborhood').value = data.bairro || '';
                        document.getElementById('newCity').value = data.localidade || '';
                        document.getElementById('newState').value = data.uf || '';
                        document.getElementById('newNumber').focus();
                    }
                });

            // Calcular frete
            calculateShipping(cep);
        }
    });

    // Quando abre o formulario de novo endereco
    document.getElementById('newAddressForm')?.addEventListener('show.bs.collapse', function() {
        document.getElementById('useNewAddress').value = '1';
        document.querySelectorAll('.address-card').forEach(el => {
            el.classList.remove('selected');
            el.querySelector('input').checked = false;
        });
        document.getElementById('shippingMethods').innerHTML = '<p class="text-muted">Preencha o CEP para ver as opcoes de frete.</p>';
    });

    // Select default address on load and calculate shipping
    const defaultAddress = document.querySelector('.address-card.selected');
    if (defaultAddress) {
        const zipcode = defaultAddress.dataset.zipcode;
        if (zipcode) {
            calculateShipping(zipcode);
        }
    }
</script>
<?= $this->endSection() ?>
