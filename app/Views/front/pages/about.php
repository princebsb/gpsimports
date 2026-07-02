<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="h3 mb-4">Sobre Nós</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Quem Somos</h5>
                    <p class="card-text">
                        A <strong><?= setting('store_name') ?? 'GPS Imports' ?></strong> é uma empresa especializada
                        em produtos importados de alta qualidade. Nossa missão é trazer até você os melhores
                        produtos do mercado com preços competitivos e atendimento de excelência.
                    </p>
                    <p class="card-text">
                        Trabalhamos com as melhores marcas e garantimos a procedência de todos os nossos produtos,
                        oferecendo segurança e confiança em cada compra.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Nossa Missão</h5>
                    <p class="card-text">
                        Proporcionar a melhor experiência de compra online, com produtos de qualidade,
                        preços justos e um atendimento que supera expectativas.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Nossos Valores</h5>
                    <ul>
                        <li><strong>Qualidade:</strong> Trabalhamos apenas com produtos de procedência garantida</li>
                        <li><strong>Transparência:</strong> Preços claros, sem surpresas</li>
                        <li><strong>Atendimento:</strong> Suporte humanizado e eficiente</li>
                        <li><strong>Segurança:</strong> Pagamentos 100% seguros via Mercado Pago</li>
                        <li><strong>Agilidade:</strong> Envio rápido para todo o Brasil</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Por que escolher a <?= setting('store_name') ?? 'GPS Imports' ?>?</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-shield-check text-success fs-3 me-3"></i>
                                <div>
                                    <strong>Compra Segura</strong>
                                    <p class="small text-muted mb-0">Pagamento via Mercado Pago</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-truck text-primary fs-3 me-3"></i>
                                <div>
                                    <strong>Entrega Rápida</strong>
                                    <p class="small text-muted mb-0">Para todo o Brasil</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-credit-card text-info fs-3 me-3"></i>
                                <div>
                                    <strong>Parcele em até 6x</strong>
                                    <p class="small text-muted mb-0">No cartão de crédito</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-headset text-warning fs-3 me-3"></i>
                                <div>
                                    <strong>Suporte</strong>
                                    <p class="small text-muted mb-0">Atendimento humanizado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Dados da Empresa</h5>
                    <p class="card-text">
                        <strong><?= setting('store_razao_social') ?></strong><br>
                        CNPJ: <?= setting('store_cnpj') ?>
                    </p>
                    <hr>
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
                    <h5 class="card-title">Entre em Contato</h5>
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
