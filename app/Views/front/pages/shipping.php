<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="h3 mb-4">Frete e Entrega</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-truck text-primary me-2"></i>
                        Entrega em Todo o Brasil
                    </h5>
                    <p class="card-text">
                        Enviamos para todas as regiões do país através dos Correios e transportadoras parceiras.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-calculator text-success me-2"></i>
                        Calcule o Frete
                    </h5>
                    <p class="card-text">
                        Para calcular o valor e prazo de entrega:
                    </p>
                    <ol>
                        <li>Adicione os produtos desejados ao carrinho</li>
                        <li>Na página do carrinho, digite seu CEP</li>
                        <li>Escolha a opção de frete que preferir</li>
                    </ol>
                    <p class="text-muted small">
                        O valor do frete é calculado com base no peso, dimensões dos produtos e CEP de destino.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-clock text-warning me-2"></i>
                        Prazos de Entrega
                    </h5>
                    <p class="card-text">
                        Os prazos são contados em <strong>dias úteis</strong> a partir da confirmação do pagamento:
                    </p>
                    <ul>
                        <li><strong>Cartão de Crédito/PIX:</strong> Processamento imediato</li>
                        <li><strong>Boleto:</strong> Até 2 dias úteis após o pagamento</li>
                    </ul>
                    <p class="text-muted small">
                        * Os prazos informados são estimativas das transportadoras e podem variar de acordo com a região.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-geo-alt text-danger me-2"></i>
                        Rastreamento
                    </h5>
                    <p class="card-text">
                        Após o envio, você receberá o código de rastreamento por e-mail.
                        Acompanhe sua entrega em:
                    </p>
                    <ul>
                        <li><a href="<?= base_url('minha-conta/pedidos') ?>">Minha Conta > Meus Pedidos</a></li>
                        <li><a href="<?= base_url('rastrear-pedido') ?>">Rastrear Pedido</a></li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-box-seam text-info me-2"></i>
                        Recebimento
                    </h5>
                    <p class="card-text">
                        No momento da entrega:
                    </p>
                    <ul>
                        <li>Confira se a embalagem está intacta antes de assinar</li>
                        <li>Em caso de avaria externa, recuse o pedido e entre em contato conosco</li>
                        <li>Verifique se todos os itens estão corretos</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Nosso Endereço</h5>
                    <p class="card-text small">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?= setting('store_address') ?><br>
                        <?= setting('store_neighborhood') ?> - <?= setting('store_city') ?>/<?= setting('store_state') ?><br>
                        CEP: <?= setting('store_zipcode') ?>
                    </p>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Dúvidas sobre Entrega?</h5>
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
