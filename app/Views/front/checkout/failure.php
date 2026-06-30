<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-3">Pagamento Recusado</h2>
                    <p class="text-muted mb-4">
                        Infelizmente o pagamento do pedido <strong>#<?= esc($order['order_number']) ?></strong> foi recusado.
                        <br>Por favor, tente novamente com outro metodo de pagamento.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Numero do Pedido</small>
                                <h5 class="mb-0">#<?= esc($order['order_number']) ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Status</small>
                                <h5 class="mb-0 text-danger">Recusado</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-question-circle me-2"></i>Possiveis Motivos</h6>
                            <ul class="text-start mb-0">
                                <li>Saldo insuficiente no cartao</li>
                                <li>Dados do cartao incorretos</li>
                                <li>Cartao bloqueado ou vencido</li>
                                <li>Limite de credito excedido</li>
                                <li>Transacao nao autorizada pelo banco</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Dica:</strong> Tente usar outro cartao ou metodo de pagamento como PIX ou Boleto Bancario.
                    </div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="<?= base_url('checkout') ?>" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-2"></i>Tentar Novamente
                        </a>
                        <a href="<?= base_url('carrinho') ?>" class="btn btn-outline-primary">
                            <i class="bi bi-cart me-2"></i>Ver Carrinho
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Voltar para a Loja
                        </a>
                    </div>
                </div>
            </div>

            <!-- Suporte -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1"><i class="bi bi-headset me-2"></i>Precisa de Ajuda?</h6>
                            <p class="text-muted mb-0 small">
                                Nossa equipe de suporte esta disponivel para ajuda-lo com qualquer duvida.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="<?= base_url('contato') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-envelope me-1"></i>Falar com Suporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
