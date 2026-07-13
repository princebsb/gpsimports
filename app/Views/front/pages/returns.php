<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="h3 mb-4">Trocas e Devoluções</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-arrow-repeat text-primary me-2"></i>
                        Direito de Arrependimento
                    </h5>
                    <p class="card-text">
                        De acordo com o <strong>Código de Defesa do Consumidor (Art. 49)</strong>, você tem até
                        <strong>7 dias corridos</strong> após o recebimento do produto para desistir da compra,
                        sem necessidade de justificativa.
                    </p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        O prazo de 7 dias é contado a partir da data de recebimento do produto.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-box-seam text-success me-2"></i>
                        Condições para Troca ou Devolução
                    </h5>
                    <p class="card-text">Para solicitar troca ou devolução, o produto deve:</p>
                    <ul>
                        <li>Estar na embalagem original, sem sinais de uso</li>
                        <li>Conter todos os acessórios e manuais</li>
                        <li>Não apresentar avarias causadas pelo cliente</li>
                        <li>Estar acompanhado da nota fiscal</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        Garantia
                    </h5>
                    <p class="card-text">
                        <strong>Garantia de 90 dias contra defeitos de fabricação</strong>, contados a partir do recebimento do produto pelo cliente.
                    </p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Não são cobertos pela garantia:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Danos por queda</li>
                            <li>Danos por água</li>
                            <li>Mau uso</li>
                            <li>Danos na tela</li>
                            <li>Carregador queimado</li>
                            <li>Produto aberto por terceiros</li>
                            <li>Lacre rompido</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Produto com Defeito de Fabricação
                    </h5>
                    <p class="card-text">
                        Se você recebeu um produto com defeito de fabricação:
                    </p>
                    <ul>
                        <li><strong>Até 7 dias:</strong> Troca imediata ou reembolso total</li>
                        <li><strong>Até 90 dias:</strong> Reparo ou troca (conforme disponibilidade)</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-list-check text-info me-2"></i>
                        Como Solicitar
                    </h5>
                    <ol>
                        <li>Entre em contato conosco via WhatsApp ou e-mail</li>
                        <li>Informe o número do pedido e o motivo da solicitação</li>
                        <li>Envie fotos do produto (se houver defeito)</li>
                        <li>Aguarde a análise e instruções de envio</li>
                        <li>Embale o produto com cuidado e envie para o endereço indicado</li>
                    </ol>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-cash-coin text-success me-2"></i>
                        Reembolso
                    </h5>
                    <p class="card-text">
                        Após o recebimento e análise do produto, o reembolso será processado:
                    </p>
                    <ul>
                        <li><strong>Cartão de Crédito:</strong> Estorno em até 2 faturas</li>
                        <li><strong>PIX/Boleto:</strong> Depósito em até 10 dias úteis</li>
                    </ul>
                    <p class="text-muted small">
                        * O prazo pode variar de acordo com a operadora do cartão.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-truck text-secondary me-2"></i>
                        Frete de Devolução
                    </h5>
                    <ul>
                        <li><strong>Arrependimento:</strong> Frete por conta do cliente</li>
                        <li><strong>Produto com defeito:</strong> Frete por nossa conta (coleta ou reembolso do valor)</li>
                        <li><strong>Produto errado enviado:</strong> Frete por nossa conta</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">Precisa de Ajuda?</h5>
                    <p class="card-text">Entre em contato para solicitar troca ou devolução:</p>
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

            <div class="card bg-warning bg-opacity-25">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-lightbulb me-2"></i>Dica
                    </h5>
                    <p class="card-text small mb-0">
                        Ao receber seu pedido, confira imediatamente se todos os itens estão corretos
                        e em perfeito estado. Em caso de problemas, registre com fotos e entre em
                        contato o mais rápido possível.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
