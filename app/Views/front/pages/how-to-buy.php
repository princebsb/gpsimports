<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="h3 mb-4">Como Comprar</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-primary me-2">1</span> Escolha seus Produtos</h5>
                    <p class="card-text">
                        Navegue pelo nosso catálogo, use a busca ou explore as categorias para encontrar os produtos desejados.
                        Clique no produto para ver mais detalhes, fotos e especificações.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-primary me-2">2</span> Adicione ao Carrinho</h5>
                    <p class="card-text">
                        Selecione a quantidade desejada e clique em "Adicionar ao Carrinho".
                        Você pode continuar comprando ou ir direto para o carrinho.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-primary me-2">3</span> Faça Login ou Cadastre-se</h5>
                    <p class="card-text">
                        Para finalizar a compra, você precisa estar logado. Se ainda não tem conta,
                        o cadastro é rápido e gratuito. Precisamos apenas de algumas informações básicas.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-primary me-2">4</span> Informe o Endereço de Entrega</h5>
                    <p class="card-text">
                        Digite seu CEP para calcular o frete e escolha a opção de entrega que preferir.
                        Confira se o endereço está correto antes de continuar.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-primary me-2">5</span> Escolha a Forma de Pagamento</h5>
                    <p class="card-text">
                        Você será redirecionado ao Mercado Pago, onde poderá escolher pagar com:
                    </p>
                    <ul>
                        <li>Cartão de Crédito em até 6x</li>
                        <li>PIX (aprovação instantânea)</li>
                        <li>Boleto Bancário</li>
                    </ul>
                    <div class="alert alert-info small">
                        <i class="bi bi-shield-lock me-1"></i>
                        <strong>Pagamento Seguro:</strong> Não armazenamos dados do seu cartão.
                        Todo o processo é realizado pelo Mercado Pago.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><span class="badge bg-success me-2">6</span> Pronto!</h5>
                    <p class="card-text">
                        Após a confirmação do pagamento, você receberá um e-mail com os detalhes do pedido.
                        Acompanhe o status da entrega em "Minha Conta > Meus Pedidos".
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Precisa de Ajuda?</h5>
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
