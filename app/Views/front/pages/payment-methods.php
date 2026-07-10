<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="h3 mb-4">Formas de Pagamento</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-credit-card text-primary me-2"></i>
                        Cartão de Crédito
                    </h5>
                    <p class="card-text">
                        Parcele suas compras em até <strong>12x</strong> no cartão de crédito (juros a partir de 2x).
                    </p>
                    <p class="mb-2"><strong>Bandeiras aceitas:</strong></p>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-light text-dark px-3 py-2">Visa</span>
                        <span class="badge bg-light text-dark px-3 py-2">Mastercard</span>
                        <span class="badge bg-light text-dark px-3 py-2">Elo</span>
                        <span class="badge bg-light text-dark px-3 py-2">American Express</span>
                        <span class="badge bg-light text-dark px-3 py-2">Hipercard</span>
                    </div>
                    <p class="small text-muted mb-0">
                        * Parcelamento disponível para compras acima de R$ 60,00 (mínimo de R$ 10,00 por parcela)
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-qr-code text-success me-2"></i>
                        PIX
                    </h5>
                    <p class="card-text">
                        Pague com PIX e tenha <strong>aprovação instantânea</strong> do seu pedido.
                    </p>
                    <ul>
                        <li>Pagamento à vista</li>
                        <li>QR Code gerado na finalização</li>
                        <li>Aprovação em segundos</li>
                        <li>Disponível 24 horas</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-upc text-secondary me-2"></i>
                        Boleto Bancário
                    </h5>
                    <p class="card-text">
                        Pague à vista com boleto bancário.
                    </p>
                    <ul>
                        <li>Vencimento em 3 dias úteis</li>
                        <li>Compensação em até 2 dias úteis após o pagamento</li>
                        <li>Pode ser pago em qualquer banco, lotérica ou internet banking</li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-info">
                <h5 class="alert-heading">
                    <i class="bi bi-shield-lock me-2"></i>
                    Pagamento 100% Seguro
                </h5>
                <p class="mb-0">
                    Todos os pagamentos são processados pelo <strong>Mercado Pago</strong>.
                    Não armazenamos dados do seu cartão de crédito. Suas informações estão protegidas
                    com a mais alta tecnologia de segurança.
                </p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Dúvidas sobre Pagamento?</h5>
                    <p class="card-text">Entre em contato conosco:</p>
                    <p class="mb-2">
                        <i class="bi bi-whatsapp text-success me-2"></i>
                        <a href="https://wa.me/<?= setting('store_whatsapp') ?>"><?= format_phone(setting('store_whatsapp') ?? '') ?></a>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:<?= setting('store_email') ?>"><?= setting('store_email') ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
