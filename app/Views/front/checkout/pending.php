<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-clock-history text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-3">Aguardando Pagamento</h2>
                    <p class="text-muted mb-4">
                        Seu pedido <strong>#<?= esc($order['order_number']) ?></strong> foi criado com sucesso!
                        <br>Estamos aguardando a confirmacao do pagamento.
                    </p>

                    <?php if ($order['payment_method'] === 'pix'): ?>
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="bi bi-qr-code me-2"></i>Pague com PIX</h5>

                                <?php if (!empty($payment['pix_copy_paste'])): ?>
                                    <!-- QR Code gerado via JavaScript -->
                                    <div class="mb-3">
                                        <div id="qrcode" class="d-inline-block p-3 bg-white rounded"></div>
                                    </div>

                                    <p class="text-muted small mb-3">
                                        Escaneie o QR Code acima com o app do seu banco
                                    </p>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Ou copie o codigo PIX:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="pixCode" value="<?= esc($payment['pix_copy_paste']) ?>" readonly>
                                            <button class="btn btn-primary" type="button" onclick="copyPixCode()">
                                                <i class="bi bi-clipboard"></i> Copiar
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($payment['pix_expiration'])): ?>
                                    <div class="alert alert-warning py-2 mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Este codigo expira em: <strong><?= date('d/m/Y H:i', strtotime($payment['pix_expiration'])) ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($order['payment_method'] === 'boleto'): ?>
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="mb-3"><i class="bi bi-upc me-2"></i>Boleto Bancario</h5>

                                <?php if (!empty($payment['boleto_barcode'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Linha Digitavel:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="boletoCode" value="<?= esc($payment['boleto_barcode']) ?>" readonly>
                                            <button class="btn btn-primary" type="button" onclick="copyBoletoCode()">
                                                <i class="bi bi-clipboard"></i> Copiar
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($payment['boleto_url'])): ?>
                                    <a href="<?= esc($payment['boleto_url']) ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-file-pdf me-2"></i>Visualizar Boleto
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($payment['boleto_expiration'])): ?>
                                    <p class="text-muted small mt-3">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Vencimento: <?= date('d/m/Y', strtotime($payment['boleto_expiration'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Total do Pedido</small>
                                <h4 class="mb-0 text-primary">R$ <?= number_format($order['total'], 2, ',', '.') ?></h4>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Status</small>
                                <h4 class="mb-0 text-warning">Aguardando</h4>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="<?= base_url('minha-conta/pedidos/' . $order['order_number']) ?>" class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>Ver Detalhes do Pedido
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Voltar para a Loja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- QRCode.js Library - usando qrcodejs que é mais confiável -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs2@0.0.2/qrcode.min.js"></script>

<script>
    <?php if ($order['payment_method'] === 'pix' && !empty($payment['pix_copy_paste'])): ?>
    // Gerar QR Code
    document.addEventListener('DOMContentLoaded', function() {
        const pixCode = <?= json_encode($payment['pix_copy_paste']) ?>;
        const qrcodeContainer = document.getElementById('qrcode');

        if (qrcodeContainer && pixCode) {
            try {
                // Limpar container
                qrcodeContainer.innerHTML = '';

                // Criar QR Code usando qrcodejs2
                new QRCode(qrcodeContainer, {
                    text: pixCode,
                    width: 200,
                    height: 200,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch (error) {
                console.error('Erro ao gerar QR Code:', error);
                qrcodeContainer.innerHTML = '<div class="alert alert-warning py-2">Nao foi possivel gerar o QR Code. Use o codigo PIX abaixo.</div>';
            }
        }
    });
    <?php endif; ?>

    function copyPixCode() {
        const input = document.getElementById('pixCode');
        input.select();
        input.setSelectionRange(0, 99999); // Para mobile

        navigator.clipboard.writeText(input.value).then(function() {
            toastr.success('Codigo PIX copiado!');
        }).catch(function() {
            // Fallback para navegadores antigos
            document.execCommand('copy');
            toastr.success('Codigo PIX copiado!');
        });
    }

    function copyBoletoCode() {
        const input = document.getElementById('boletoCode');
        input.select();
        input.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(input.value).then(function() {
            toastr.success('Linha digitavel copiada!');
        }).catch(function() {
            document.execCommand('copy');
            toastr.success('Linha digitavel copiada!');
        });
    }
</script>
<?= $this->endSection() ?>
