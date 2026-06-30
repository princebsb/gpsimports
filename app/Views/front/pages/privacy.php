<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                    <li class="breadcrumb-item active">Politica de Privacidade</li>
                </ol>
            </nav>

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 mb-4">Politica de Privacidade</h1>
                    <p class="text-muted mb-4">Ultima atualizacao: <?= date('d/m/Y') ?></p>

                    <div class="privacy-content">
                        <h4>1. Introducao</h4>
                        <p>A <?= esc($settings['site_name'] ?? 'GPS Imports') ?> esta comprometida em proteger sua privacidade. Esta Politica de Privacidade explica como coletamos, usamos, divulgamos e protegemos suas informacoes pessoais quando voce utiliza nosso site e servicos.</p>

                        <h4>2. Informacoes que Coletamos</h4>
                        <p>Coletamos diferentes tipos de informacoes para fornecer e melhorar nossos servicos:</p>

                        <h5>2.1. Informacoes Fornecidas por Voce</h5>
                        <ul>
                            <li><strong>Dados de cadastro:</strong> nome, e-mail, CPF/CNPJ, telefone, data de nascimento</li>
                            <li><strong>Enderecos:</strong> para entrega e cobranca</li>
                            <li><strong>Dados de pagamento:</strong> informacoes de cartao de credito (processadas de forma segura)</li>
                            <li><strong>Comunicacoes:</strong> mensagens enviadas ao nosso suporte</li>
                        </ul>

                        <h5>2.2. Informacoes Coletadas Automaticamente</h5>
                        <ul>
                            <li><strong>Dados de navegacao:</strong> endereco IP, tipo de navegador, paginas visitadas</li>
                            <li><strong>Cookies:</strong> para melhorar sua experiencia no site</li>
                            <li><strong>Dados do dispositivo:</strong> tipo de dispositivo, sistema operacional</li>
                        </ul>

                        <h4>3. Como Usamos suas Informacoes</h4>
                        <p>Utilizamos suas informacoes para:</p>
                        <ul>
                            <li>Processar e entregar seus pedidos</li>
                            <li>Gerenciar sua conta e fornecer suporte ao cliente</li>
                            <li>Enviar atualizacoes sobre seus pedidos</li>
                            <li>Enviar ofertas e promocoes (se voce optou por recebe-las)</li>
                            <li>Melhorar nosso site e servicos</li>
                            <li>Prevenir fraudes e garantir a seguranca</li>
                            <li>Cumprir obrigacoes legais</li>
                        </ul>

                        <h4>4. Compartilhamento de Informacoes</h4>
                        <p>Suas informacoes podem ser compartilhadas com:</p>
                        <ul>
                            <li><strong>Processadores de pagamento:</strong> para processar transacoes</li>
                            <li><strong>Transportadoras:</strong> para realizar entregas</li>
                            <li><strong>Prestadores de servicos:</strong> que nos auxiliam em operacoes</li>
                            <li><strong>Autoridades legais:</strong> quando exigido por lei</li>
                        </ul>
                        <p>Nao vendemos suas informacoes pessoais a terceiros.</p>

                        <h4>5. Cookies</h4>
                        <p>Utilizamos cookies para:</p>
                        <ul>
                            <li>Manter voce conectado em sua conta</li>
                            <li>Lembrar itens no seu carrinho de compras</li>
                            <li>Analisar o uso do site para melhorias</li>
                            <li>Personalizar sua experiencia</li>
                        </ul>
                        <p>Voce pode configurar seu navegador para recusar cookies, mas algumas funcionalidades do site podem nao funcionar corretamente.</p>

                        <h4>6. Seguranca dos Dados</h4>
                        <p>Implementamos medidas de seguranca tecnicas e organizacionais para proteger suas informacoes, incluindo:</p>
                        <ul>
                            <li>Criptografia SSL/TLS para transmissao de dados</li>
                            <li>Armazenamento seguro de senhas (criptografadas)</li>
                            <li>Acesso restrito a dados pessoais</li>
                            <li>Monitoramento continuo de seguranca</li>
                        </ul>

                        <h4>7. Seus Direitos (LGPD)</h4>
                        <p>De acordo com a Lei Geral de Protecao de Dados (LGPD), voce tem direito a:</p>
                        <ul>
                            <li><strong>Acesso:</strong> solicitar informacoes sobre seus dados</li>
                            <li><strong>Correcao:</strong> corrigir dados incompletos ou incorretos</li>
                            <li><strong>Exclusao:</strong> solicitar a exclusao de seus dados</li>
                            <li><strong>Portabilidade:</strong> receber seus dados em formato estruturado</li>
                            <li><strong>Revogacao:</strong> revogar consentimentos dados anteriormente</li>
                            <li><strong>Oposicao:</strong> opor-se ao tratamento de dados</li>
                        </ul>
                        <p>Para exercer esses direitos, entre em contato conosco pelos canais informados abaixo.</p>

                        <h4>8. Retencao de Dados</h4>
                        <p>Mantemos suas informacoes pelo tempo necessario para:</p>
                        <ul>
                            <li>Fornecer nossos servicos</li>
                            <li>Cumprir obrigacoes legais e regulatorias</li>
                            <li>Resolver disputas e fazer cumprir nossos acordos</li>
                        </ul>

                        <h4>9. Menores de Idade</h4>
                        <p>Nosso site nao e direcionado a menores de 18 anos. Nao coletamos intencionalmente informacoes de menores. Se tomarmos conhecimento de que coletamos dados de um menor, tomaremos medidas para excluir essas informacoes.</p>

                        <h4>10. Alteracoes nesta Politica</h4>
                        <p>Podemos atualizar esta Politica de Privacidade periodicamente. Notificaremos sobre mudancas significativas publicando a nova politica em nosso site. Recomendamos revisar esta pagina periodicamente.</p>

                        <h4>11. Contato</h4>
                        <p>Para duvidas, solicitacoes ou exercicio de direitos relacionados a esta politica:</p>
                        <ul>
                            <li><strong>E-mail:</strong> <?= esc($settings['contact_email'] ?? 'privacidade@gpsimports.com.br') ?></li>
                            <li><strong>Telefone:</strong> <?= esc($settings['contact_phone'] ?? '(44) 99999-9999') ?></li>
                            <li><strong>Endereco:</strong> <?= esc($settings['address'] ?? 'Parana, Brasil') ?></li>
                        </ul>

                        <h4>12. Encarregado de Dados (DPO)</h4>
                        <p>Nosso Encarregado de Protecao de Dados pode ser contatado pelo e-mail: <?= esc($settings['contact_email'] ?? 'dpo@gpsimports.com.br') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
