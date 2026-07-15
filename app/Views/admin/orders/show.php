<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Pedido #<?= $order['order_number'] ?></h1>
        <small class="text-muted">Criado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/pedidos/imprimir/' . $order['id']) ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Imprimir
        </a>
        <a href="<?= base_url('admin/pedidos') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Itens do Pedido</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preco</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $itemImg = $item['image'] ?? $item['product_image'] ?? '';
                                            if (!empty($itemImg)):
                                                $imgUrl = (strpos($itemImg, 'http') === 0) ? $itemImg : base_url('uploads/products/thumbs/' . $itemImg);
                                            ?>
                                                <img src="<?= esc($imgUrl) ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://placehold.co/50x50/e9ecef/495057?text=P'">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= esc($item['name'] ?? $item['product_name'] ?? 'Produto') ?></strong>
                                                <?php if (!empty($item['variation_name']) || !empty($item['attributes'])): ?>
                                                    <br><small class="text-muted"><?= esc($item['variation_name'] ?? $item['attributes'] ?? '') ?></small>
                                                <?php endif; ?>
                                                <br><small class="text-muted">SKU: <?= esc($item['sku'] ?? '-') ?></small>
                                                <?php if (!empty($item['fonte'])): ?>
                                                    <br><span class="badge bg-info badge-sm"><?= esc($item['fonte']) ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($item['url_origem'])): ?>
                                                    <a href="<?= esc($item['url_origem']) ?>" target="_blank" class="ms-1" title="Ver produto original">
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end">R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                    <td class="text-end">R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end">R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></td>
                            </tr>
                            <?php if ($order['discount'] > 0): ?>
                                <tr class="text-success">
                                    <td colspan="3" class="text-end">
                                        Desconto
                                        <?php if ($order['coupon_code']): ?>
                                            (<?= esc($order['coupon_code']) ?>)
                                        <?php endif; ?>:
                                    </td>
                                    <td class="text-end">-R$ <?= number_format($order['discount'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="3" class="text-end">Frete (<?= esc($order['shipping_method']) ?>):</td>
                                <td class="text-end">R$ <?= number_format($order['shipping_cost'], 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong class="fs-5">R$ <?= number_format($order['total'], 2, ',', '.') ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Historico</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (!empty($order['status_history'])): ?>
                    <?php foreach ($order['status_history'] as $history): ?>
                        <div class="timeline-item d-flex mb-3">
                            <div class="timeline-icon me-3">
                                <?php
                                $icon = match($history['status'] ?? 'pending') {
                                    'pending' => 'clock',
                                    'processing', 'paid' => 'gear',
                                    'shipped' => 'truck',
                                    'delivered' => 'check-circle',
                                    'cancelled' => 'x-circle',
                                    default => 'circle'
                                };
                                $color = match($history['status'] ?? 'pending') {
                                    'pending' => 'warning',
                                    'processing', 'paid' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                $historyStatusLabel = match($history['status'] ?? 'pending') {
                                    'pending' => 'Pendente',
                                    'paid' => 'Pago',
                                    'processing' => 'Em Preparacao',
                                    'shipped' => 'Enviado',
                                    'delivered' => 'Entregue',
                                    'cancelled' => 'Cancelado',
                                    'refunded' => 'Reembolsado',
                                    default => ucfirst($history['status'] ?? 'Pendente')
                                };
                                ?>
                                <span class="badge bg-<?= $color ?> rounded-circle p-2">
                                    <i class="bi bi-<?= $icon ?>"></i>
                                </span>
                            </div>
                            <div class="timeline-content">
                                <div class="fw-bold">
                                    <?= esc($history['status_label'] ?? $historyStatusLabel) ?>
                                    <?php if (!empty($history['notify_customer'])): ?>
                                        <span class="badge bg-info badge-sm ms-1" title="Cliente notificado por email">
                                            <i class="bi bi-envelope-check"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($history['notes']) || !empty($history['comment'])): ?>
                                    <div class="text-muted small"><?= esc($history['notes'] ?? $history['comment'] ?? '') ?></div>
                                <?php endif; ?>
                                <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhum historico disponivel.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Status -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Status do Pedido</h5>
            </div>
            <div class="card-body">
                <?php
                $statusClass = match($order['status']) {
                    'pending' => 'badge-pending',
                    'paid' => 'badge-processing',
                    'processing' => 'badge-processing',
                    'shipped' => 'badge-shipped',
                    'delivered' => 'badge-delivered',
                    'cancelled' => 'badge-cancelled',
                    default => 'bg-secondary'
                };
                $statusLabel = match($order['status']) {
                    'pending' => 'Pendente',
                    'paid' => 'Pago',
                    'processing' => 'Em Preparacao',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregue',
                    'cancelled' => 'Cancelado',
                    default => ucfirst($order['status'] ?? 'Pendente')
                };
                ?>
                <div class="text-center mb-3">
                    <span class="badge <?= $statusClass ?> fs-6 px-3 py-2"><?= $statusLabel ?></span>
                </div>

                <?php if (!in_array($order['status'], ['cancelled', 'delivered'])): ?>
                    <form action="<?= base_url('admin/pedidos/' . $order['id'] . '/status') ?>" method="post" id="statusForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Alterar Status</label>
                            <select name="status" class="form-select" id="selectStatus">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Pago</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Em Preparacao</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Enviado</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observacao</label>
                            <textarea name="comment" class="form-control" rows="2" placeholder="Observacao interna (opcional)"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notify_customer" value="1" id="notifyCustomer" checked>
                                <label class="form-check-label" for="notifyCustomer">
                                    <i class="bi bi-envelope me-1"></i>Notificar cliente por email
                                </label>
                            </div>
                            <small class="text-muted">O cliente recebera um email informando a mudanca de status.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-1"></i>Atualizar Status
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cliente</h5>
            </div>
            <div class="card-body">
                <?php $customer = $order['customer'] ?? []; ?>
                <div class="mb-3">
                    <strong><?= esc($customer['name'] ?? 'N/A') ?></strong>
                    <br>
                    <a href="mailto:<?= esc($customer['email'] ?? '') ?>"><?= esc($customer['email'] ?? 'N/A') ?></a>
                    <br>
                    <?php if (!empty($customer['phone']) || !empty($customer['mobile'])): ?>
                        <a href="tel:<?= esc($customer['phone'] ?? $customer['mobile'] ?? '') ?>"><?= esc($customer['phone'] ?? $customer['mobile'] ?? '') ?></a>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('admin/clientes/' . $order['customer_id']) ?>" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-person me-1"></i>Ver Cliente
                </a>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Endereco de Entrega</h5>
            </div>
            <div class="card-body">
                <address class="mb-0">
                    <?= esc($order['shipping_name'] ?? '') ?><br>
                    <?= esc($order['shipping_street'] ?? $order['shipping_address'] ?? '') ?>, <?= esc($order['shipping_number'] ?? '') ?>
                    <?php if (!empty($order['shipping_complement'])): ?>
                        <br><?= esc($order['shipping_complement']) ?>
                    <?php endif; ?>
                    <br><?= esc($order['shipping_neighborhood'] ?? '') ?>
                    <br><?= esc($order['shipping_city'] ?? '') ?> - <?= esc($order['shipping_state'] ?? '') ?>
                    <br>CEP: <?= esc($order['shipping_zipcode'] ?? '') ?>
                </address>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pagamento</h5>
            </div>
            <div class="card-body">
                <?php
                $paymentStatus = $order['payment_status'] ?? 'pending';
                $paymentMethod = $order['payment_method'] ?? 'N/A';
                $paymentClass = match($paymentStatus) {
                    'paid', 'approved' => 'bg-success',
                    'pending' => 'bg-warning',
                    'refunded' => 'bg-info',
                    'failed', 'rejected' => 'bg-danger',
                    default => 'bg-secondary'
                };
                $paymentLabel = match($paymentStatus) {
                    'paid', 'approved' => 'Pago',
                    'pending' => 'Aguardando',
                    'refunded' => 'Reembolsado',
                    'failed', 'rejected' => 'Falhou',
                    default => ucfirst($paymentStatus)
                };
                $methodLabel = match($paymentMethod) {
                    'credit_card' => 'Cartao de Credito',
                    'debit_card' => 'Cartao de Debito',
                    'pix' => 'PIX',
                    'boleto' => 'Boleto',
                    'checkout_pro' => 'Mercado Pago',
                    'account_money' => 'Saldo Mercado Pago',
                    default => ucfirst($paymentMethod)
                };
                ?>
                <div class="mb-2">
                    <span class="badge <?= $paymentClass ?>"><?= $paymentLabel ?></span>
                </div>
                <div><strong>Metodo:</strong> <?= $methodLabel ?></div>
                <?php if (($order['installments'] ?? 1) > 1): ?>
                    <div><strong>Parcelas:</strong> <?= $order['installments'] ?>x</div>
                <?php endif; ?>
                <?php if (!empty($order['payment_id']) || !empty($order['payments'])): ?>
                    <?php $payment = $order['payments'][0] ?? null; ?>
                    <?php if ($payment): ?>
                        <div class="small text-muted mt-2">ID: <?= esc($payment['transaction_id'] ?? $payment['id'] ?? '') ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fornecedor -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-truck me-1"></i>Fornecedor</h5>
            </div>
            <div class="card-body">
                <?php
                // Montar lista de produtos
                $produtosList = "";
                foreach ($order['items'] as $item) {
                    $produtosList .= "• " . ($item['name'] ?? 'Produto') . "%0A";
                    $produtosList .= "  Qtd: " . $item['quantity'] . "%0A";
                    $produtosList .= "  SKU: " . ($item['sku'] ?? '-') . "%0A%0A";
                }

                $msgWhatsApp = "👋 Olá! Sou o *Sérgio*, já compro produtos no Paraguai há algum tempo.%0A%0A";
                $msgWhatsApp .= "🚚 Tenho pessoal que *retira mercadoria pra mim* toda *Terça*, *Quinta* e *Sábado*.%0A%0A";
                $msgWhatsApp .= "💵 Eles *pagam e retiram as notas* nesses dias.%0A%0A";
                $msgWhatsApp .= "*Quero comprar com vocês!*%0A%0A";
                $msgWhatsApp .= "Segue o produto:%0A%0A";
                $msgWhatsApp .= $produtosList;
                $msgWhatsApp .= "Favor gerar a NOTA.%0A%0A";
                $msgWhatsApp .= "Obrigado. 🙏";
                ?>
                <a href="https://web.whatsapp.com/send/?phone=595982897556&text=<?= $msgWhatsApp ?>" target="_blank" class="btn btn-success w-100">
                    <i class="bi bi-whatsapp me-1"></i>WhatsApp Fornecedor
                </a>
                <small class="text-muted d-block mt-2 text-center">+595 982 897 556</small>
            </div>
        </div>

        <!-- Melhor Envio - Gerar Etiqueta -->
        <?php if (in_array($order['status'], ['paid', 'processing']) && empty($order['tracking_code'])): ?>
        <div class="table-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box-seam me-1"></i>Gerar Etiqueta</h5>
            </div>
            <div class="card-body">
                <?php if ($meBalance !== null): ?>
                    <div class="alert alert-<?= $meBalance >= 30 ? 'success' : 'warning' ?> py-2 mb-3">
                        <i class="bi bi-wallet2 me-1"></i>
                        <strong>Saldo Melhor Envio:</strong> R$ <?= number_format($meBalance, 2, ',', '.') ?>
                        <?php if ($meBalance < 30): ?>
                            <br><small class="text-muted">Saldo baixo! Adicione creditos para gerar etiquetas.</small>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary py-2 mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <small>Nao foi possivel obter saldo. <a href="<?= base_url('melhor-envio/autorizar') ?>">Verificar conexao</a></small>
                    </div>
                <?php endif; ?>
                <?php
                // Calcular peso e dimensoes dos itens do pedido
                $totalWeight = 0;
                $maxHeight = 2;
                $maxWidth = 11;
                $maxLength = 16;

                foreach ($order['items'] as $item) {
                    $qty = (int) ($item['quantity'] ?? 1);
                    $itemWeight = (float) ($item['product_weight'] ?? 0.3);
                    $totalWeight += $itemWeight * $qty;

                    // Pegar maiores dimensoes (para calcular volume da caixa)
                    $itemHeight = (int) ($item['product_height'] ?? 2);
                    $itemWidth = (int) ($item['product_width'] ?? 11);
                    $itemLength = (int) ($item['product_length'] ?? 16);

                    $maxHeight = max($maxHeight, $itemHeight);
                    $maxWidth = max($maxWidth, $itemWidth);
                    $maxLength = max($maxLength, $itemLength);
                }

                // Minimos para embalagem
                $totalWeight = max(0.1, round($totalWeight, 2));
                $maxHeight = max(2, $maxHeight);
                $maxWidth = max(11, $maxWidth);
                $maxLength = max(16, $maxLength);
                ?>
                <p class="small text-muted mb-3">Gere a etiqueta de envio pelo Melhor Envio.</p>

                <form action="<?= base_url('admin/pedidos/' . $order['id'] . '/gerar-etiqueta') ?>" method="post" id="formEtiqueta">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Transportadora</label>
                        <select name="service_id" class="form-select" required id="selectTransportadora">
                            <option value="">Selecione...</option>
                            <option value="1">Correios PAC</option>
                            <option value="2">Correios SEDEX</option>
                            <option value="3">Jadlog .Package</option>
                            <option value="4">Jadlog .Com</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Peso Total (kg)</label>
                        <input type="number" name="weight" class="form-control" step="0.01" min="0.1" value="<?= $totalWeight ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label">Altura (cm)</label>
                            <input type="number" name="height" class="form-control" min="1" value="<?= $maxHeight ?>" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Largura (cm)</label>
                            <input type="number" name="width" class="form-control" min="1" value="<?= $maxWidth ?>" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Compr. (cm)</label>
                            <input type="number" name="length" class="form-control" min="1" value="<?= $maxLength ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnGerarEtiqueta">
                        <i class="bi bi-printer me-1"></i>Gerar Etiqueta
                    </button>
                </form>

                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="cotarFrete()" id="btnCotarFrete">
                        <i class="bi bi-calculator me-1"></i>Cotar Frete
                    </button>
                </div>

                <!-- Resultado da Cotacao -->
                <div id="resultadoCotacao" class="mt-3" style="display: none;">
                    <div class="small text-muted mb-2"><i class="bi bi-truck me-1"></i>Opcoes de Frete:</div>
                    <div id="listaCotacoes"></div>
                </div>

                <!-- Adicionar Creditos -->
                <div class="mt-3 pt-3 border-top">
                    <p class="small text-muted mb-2"><i class="bi bi-plus-circle me-1"></i>Adicionar Creditos</p>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">R$</span>
                        <input type="number" id="valorCredito" class="form-control" value="50" min="10" max="50000" step="10">
                        <button type="button" class="btn btn-success" onclick="adicionarCredito()">
                            <i class="bi bi-plus"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Etiqueta Gerada -->
        <?php if (!empty($order['me_label_id'])): ?>
        <div class="table-card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle me-1"></i>Etiqueta Gerada</h5>
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>ID:</strong> <?= esc($order['me_label_id']) ?></p>
                <?php if (!empty($order['tracking_code'])): ?>
                <p class="small mb-2"><strong>Rastreio:</strong> <code><?= esc($order['tracking_code']) ?></code></p>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/pedidos/' . $order['id'] . '/imprimir-etiqueta') ?>" target="_blank" class="btn btn-success">
                        <i class="bi bi-printer me-1"></i>Imprimir Etiqueta
                    </a>
                    <a href="<?= base_url('admin/pedidos/' . $order['id'] . '/rastrear-etiqueta') ?>" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-geo-alt me-1"></i>Rastrear
                    </a>
                    <?php if (!empty($order['tracking_code']) && !empty($order['shipping_phone'])): ?>
                    <?php
                        $customerName = $order['shipping_name'] ?? $order['customer']['name'] ?? 'Cliente';
                        $firstName = explode(' ', trim($customerName))[0];
                        $trackingUrl = 'https://www.melhorrastreio.com.br/rastreio/' . $order['tracking_code'];
                        $whatsappMsg = "Olá {$firstName}! 😊\n\n";
                        $whatsappMsg .= "Ótima notícia! Seu pedido #{$order['order_number']} foi enviado e já está a caminho! 📦\n\n";
                        $whatsappMsg .= "🔍 *Código de Rastreio:*\n{$order['tracking_code']}\n\n";
                        $whatsappMsg .= "📍 *Acompanhe aqui:*\n{$trackingUrl}\n\n";
                        $whatsappMsg .= "Qualquer dúvida, estamos à disposição!\n\n";
                        $whatsappMsg .= "Att, GPS Imports 🚀";
                        $phone = preg_replace('/\D/', '', $order['shipping_phone']);
                        if (strlen($phone) <= 11) {
                            $phone = '55' . $phone;
                        }
                        $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($whatsappMsg);
                    ?>
                    <a href="<?= $whatsappUrl ?>" target="_blank" class="btn btn-success" style="background-color: #25D366; border-color: #25D366;">
                        <i class="bi bi-whatsapp me-1"></i>Enviar WhatsApp
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Shipping Tracking -->
        <?php if (($order['status'] ?? '') === 'shipped' || !empty($order['tracking_code'])): ?>
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Rastreamento</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($order['tracking_code'])): ?>
                        <div class="mb-3">
                            <strong>Codigo:</strong>
                            <code class="fs-5"><?= esc($order['tracking_code']) ?></code>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="https://www.melhorrastreio.com.br/rastreio/<?= esc($order['tracking_code']) ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-geo-alt me-1"></i>Rastrear Pedido
                            </a>
                            <?php if (!empty($order['shipping_phone'])): ?>
                            <?php
                                $customerName = $order['shipping_name'] ?? $order['customer']['name'] ?? 'Cliente';
                                $firstName = explode(' ', trim($customerName))[0];
                                $trackingUrl = 'https://www.melhorrastreio.com.br/rastreio/' . $order['tracking_code'];
                                $whatsappMsg = "Olá {$firstName}! 😊\n\n";
                                $whatsappMsg .= "Ótima notícia! Seu pedido #{$order['order_number']} foi enviado e já está a caminho! 📦\n\n";
                                $whatsappMsg .= "🔍 *Código de Rastreio:*\n{$order['tracking_code']}\n\n";
                                $whatsappMsg .= "📍 *Acompanhe aqui:*\n{$trackingUrl}\n\n";
                                $whatsappMsg .= "Qualquer dúvida, estamos à disposição!\n\n";
                                $whatsappMsg .= "Att, GPS Imports 🚀";
                                $phone = preg_replace('/\D/', '', $order['shipping_phone']);
                                if (strlen($phone) <= 11) {
                                    $phone = '55' . $phone;
                                }
                                $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($whatsappMsg);
                            ?>
                            <a href="<?= $whatsappUrl ?>" target="_blank" class="btn btn-success" style="background-color: #25D366; border-color: #25D366;">
                                <i class="bi bi-whatsapp me-1"></i>Enviar WhatsApp
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form action="<?= base_url('admin/pedidos/rastreio/' . $order['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Codigo de Rastreio</label>
                                <input type="text" name="tracking_code" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Adicionar Rastreio</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal PIX -->
<div class="modal fade" id="modalPix" tabindex="-1" aria-labelledby="modalPixLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalPixLabel"><i class="bi bi-qr-code me-2"></i>PIX - Melhor Envio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center">
                <div id="pixLoading" class="py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Gerando PIX...</p>
                </div>
                <div id="pixContent" style="display: none;">
                    <p class="mb-3"><strong>Valor:</strong> <span id="pixValor" class="fs-4 text-success">R$ 0,00</span></p>
                    <div id="pixQrCodeContainer" class="mb-3">
                        <canvas id="qrCanvasOrder"></canvas>
                    </div>
                    <p class="small text-muted mb-2">Ou copie o codigo PIX:</p>
                    <div class="input-group mb-3">
                        <input type="text" id="pixCode" class="form-control form-control-sm" readonly>
                        <button class="btn btn-outline-success" type="button" onclick="copiarPix()">
                            <i class="bi bi-clipboard"></i> Copiar
                        </button>
                    </div>
                    <div id="pixCopied" class="alert alert-success py-2" style="display: none;">
                        <i class="bi bi-check-circle me-1"></i>Codigo copiado!
                    </div>
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Apos o pagamento, aguarde alguns segundos e recarregue a pagina para atualizar o saldo.
                    </div>
                </div>
                <div id="pixError" style="display: none;">
                    <div class="alert alert-danger py-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <span id="pixErrorMsg">Erro ao gerar PIX</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Atualizar Saldo
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<script>
let pixModal = null;

function adicionarCredito() {
    const valor = document.getElementById('valorCredito').value;

    if (valor < 10 || valor > 50000) {
        alert('Valor deve ser entre R$ 10,00 e R$ 50.000,00');
        return;
    }

    // Mostrar modal
    pixModal = new bootstrap.Modal(document.getElementById('modalPix'));
    document.getElementById('pixLoading').style.display = 'block';
    document.getElementById('pixContent').style.display = 'none';
    document.getElementById('pixError').style.display = 'none';
    pixModal.show();

    fetch('<?= base_url('admin/melhor-envio/adicionar-credito') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify({ valor: parseFloat(valor), metodo: 'pix' })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Resposta PIX:', data);
        document.getElementById('pixLoading').style.display = 'none';

        if (data.success) {
            const pixCode = data.pix_code || data.digitable || '';
            const pixLink = data.link || '';

            if (pixCode) {
                // Mostrar QR code e codigo
                document.getElementById('pixValor').textContent = 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
                document.getElementById('pixCode').value = pixCode;

                // Gerar QR code via QRious
                new QRious({
                    element: document.getElementById('qrCanvasOrder'),
                    value: pixCode,
                    size: 250,
                    level: 'L'
                });

                document.getElementById('pixContent').style.display = 'block';
            } else if (pixLink) {
                // Se nao tem codigo PIX mas tem link, redirecionar
                window.open(pixLink, '_blank');
                pixModal.hide();
                alert('Link de pagamento aberto em nova aba.');
            } else {
                document.getElementById('pixErrorMsg').textContent = 'Resposta invalida da API';
                document.getElementById('pixError').style.display = 'block';
            }
        } else {
            document.getElementById('pixErrorMsg').textContent = data.message || 'Erro ao gerar PIX';
            document.getElementById('pixError').style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('pixLoading').style.display = 'none';
        document.getElementById('pixErrorMsg').textContent = 'Erro na requisicao: ' + error;
        document.getElementById('pixError').style.display = 'block';
    });
}

function copiarPix() {
    const pixCode = document.getElementById('pixCode');
    pixCode.select();
    pixCode.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(pixCode.value).then(() => {
        document.getElementById('pixCopied').style.display = 'block';
        setTimeout(() => {
            document.getElementById('pixCopied').style.display = 'none';
        }, 3000);
    }).catch(() => {
        document.execCommand('copy');
        document.getElementById('pixCopied').style.display = 'block';
        setTimeout(() => {
            document.getElementById('pixCopied').style.display = 'none';
        }, 3000);
    });
}

function cotarFrete() {
    const btn = document.getElementById('btnCotarFrete');
    const resultado = document.getElementById('resultadoCotacao');
    const lista = document.getElementById('listaCotacoes');

    // Pegar dimensoes do formulario
    const form = document.getElementById('formEtiqueta');
    const weight = form ? form.querySelector('[name="weight"]').value : 0.5;
    const height = form ? form.querySelector('[name="height"]').value : 10;
    const width = form ? form.querySelector('[name="width"]').value : 15;
    const length = form ? form.querySelector('[name="length"]').value : 20;

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Cotando...';

    const url = `<?= base_url('admin/pedidos/' . $order['id'] . '/cotar-frete') ?>?weight=${weight}&height=${height}&width=${width}&length=${length}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-calculator me-1"></i>Cotar Frete';

            const saldo = data.balance || 0;

            if (data.success && data.quotes && data.quotes.length > 0) {
                let html = `<div class="alert alert-info py-2 mb-2">
                    <i class="bi bi-wallet2 me-1"></i>Saldo: <strong>R$ ${saldo.toFixed(2).replace('.', ',')}</strong>
                </div>`;
                data.quotes.forEach(quote => {
                    const falta = quote.price > saldo ? (quote.price - saldo) : 0;
                    const statusClass = falta > 0 ? 'text-danger' : 'text-success';
                    const statusIcon = falta > 0 ? 'x-circle' : 'check-circle';

                    html += `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>${quote.name}</strong>
                                <br><small class="text-muted">${quote.company} - ${quote.deadline} dias</small>
                            </div>
                            <div class="text-end">
                                <strong>R$ ${quote.price.toFixed(2).replace('.', ',')}</strong>
                                <br><small class="${statusClass}">
                                    <i class="bi bi-${statusIcon}"></i>
                                    ${falta > 0 ? 'Falta R$ ' + falta.toFixed(2).replace('.', ',') : 'OK'}
                                </small>
                            </div>
                        </div>
                    `;
                });
                lista.innerHTML = html;
                resultado.style.display = 'block';
            } else {
                lista.innerHTML = '<div class="alert alert-warning py-2">Nenhuma cotacao disponivel.</div>';
                resultado.style.display = 'block';
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-calculator me-1"></i>Cotar Frete';
            alert('Erro ao cotar frete: ' + error);
        });
}
</script>
<?= $this->endSection() ?>
