<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                    <li class="breadcrumb-item active">Termos de Uso</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 mb-4">Termos de Uso</h1>
                    <p class="text-muted mb-4">Ultima atualizacao: <?= date('d/m/Y') ?></p>

                    <div class="terms-content">
                        <h4>1. Aceitacao dos Termos</h4>
                        <p>Ao acessar e utilizar o site <?= esc($settings['site_name'] ?? 'GPS Imports') ?>, voce concorda em cumprir e estar vinculado aos seguintes termos e condicoes de uso. Se voce nao concordar com qualquer parte destes termos, nao devera utilizar nosso site.</p>

                        <h4>2. Uso do Site</h4>
                        <p>Voce concorda em usar este site apenas para fins legais e de maneira que nao infrinja os direitos de terceiros ou restrinja ou iniba o uso e aproveitamento deste site por terceiros.</p>
                        <p>E proibido:</p>
                        <ul>
                            <li>Usar o site de qualquer forma que seja ilegal ou fraudulenta</li>
                            <li>Usar o site para transmitir material publicitario ou promocional nao solicitado</li>
                            <li>Fazer uso nao autorizado do conteudo do site</li>
                            <li>Tentar obter acesso nao autorizado a qualquer parte do site</li>
                        </ul>

                        <h4>3. Cadastro e Conta</h4>
                        <p>Para realizar compras em nosso site, voce devera criar uma conta fornecendo informacoes verdadeiras, precisas e completas. Voce e responsavel por manter a confidencialidade de sua senha e por todas as atividades que ocorram em sua conta.</p>

                        <h4>4. Produtos e Precos</h4>
                        <p>Nos reservamos o direito de modificar os precos dos produtos a qualquer momento, sem aviso previo. Os precos exibidos sao validos apenas para compras online realizadas no momento da finalizacao do pedido.</p>
                        <p>As imagens dos produtos sao meramente ilustrativas. As cores podem variar de acordo com a configuracao do monitor.</p>

                        <h4>5. Pedidos e Pagamentos</h4>
                        <p>Ao realizar um pedido, voce esta fazendo uma oferta de compra. O contrato de compra so sera considerado celebrado apos a confirmacao do pagamento e aprovacao do pedido por nossa equipe.</p>
                        <p>Nos reservamos o direito de recusar ou cancelar qualquer pedido por motivos como:</p>
                        <ul>
                            <li>Erros de precos ou informacoes de produtos</li>
                            <li>Suspeita de fraude</li>
                            <li>Indisponibilidade de estoque</li>
                            <li>Problemas no processamento do pagamento</li>
                        </ul>

                        <h4>6. Entregas</h4>
                        <p>Os prazos de entrega sao estimados e comecam a contar apos a confirmacao do pagamento. Nao nos responsabilizamos por atrasos causados por eventos fora de nosso controle, como greves, desastres naturais ou problemas com transportadoras.</p>

                        <h4>7. Trocas e Devolucoes</h4>
                        <p>O cliente tem direito de arrependimento em ate 7 (sete) dias corridos apos o recebimento do produto, conforme o Codigo de Defesa do Consumidor. Para exercer esse direito, o produto deve estar em sua embalagem original, sem sinais de uso.</p>

                        <h4>8. Propriedade Intelectual</h4>
                        <p>Todo o conteudo deste site, incluindo textos, imagens, logotipos, icones e software, e de propriedade exclusiva de <?= esc($settings['site_name'] ?? 'GPS Imports') ?> ou de seus fornecedores de conteudo e esta protegido por leis de direitos autorais.</p>

                        <h4>9. Limitacao de Responsabilidade</h4>
                        <p>Na extensao maxima permitida pela lei aplicavel, nao seremos responsaveis por quaisquer danos indiretos, incidentais, especiais ou consequentes decorrentes do uso ou incapacidade de uso deste site.</p>

                        <h4>10. Alteracoes nos Termos</h4>
                        <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. As alteracoes entrarao em vigor imediatamente apos a publicacao no site. O uso continuado do site apos tais alteracoes constitui sua aceitacao dos novos termos.</p>

                        <h4>11. Lei Aplicavel</h4>
                        <p>Estes termos serao regidos e interpretados de acordo com as leis da Republica Federativa do Brasil. Qualquer disputa sera submetida a jurisdicao exclusiva dos tribunais brasileiros.</p>

                        <h4>12. Contato</h4>
                        <p>Em caso de duvidas sobre estes termos, entre em contato conosco:</p>
                        <ul>
                            <li>E-mail: <?= esc($settings['contact_email'] ?? 'contato@gpsimports.com.br') ?></li>
                            <li>Telefone: <?= esc($settings['contact_phone'] ?? '(44) 99999-9999') ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
