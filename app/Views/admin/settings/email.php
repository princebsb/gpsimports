<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Configuracoes de Email</h1>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="list-group">
            <a href="<?= base_url('admin/configuracoes') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-gear me-2"></i>Geral
            </a>
            <a href="<?= base_url('admin/configuracoes/loja') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-shop me-2"></i>Loja
            </a>
            <a href="<?= base_url('admin/configuracoes/pagamento') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-credit-card me-2"></i>Pagamento
            </a>
            <a href="<?= base_url('admin/configuracoes/frete') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-truck me-2"></i>Frete
            </a>
            <a href="<?= base_url('admin/configuracoes/email') ?>" class="list-group-item list-group-item-action active">
                <i class="bi bi-envelope me-2"></i>Email
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <form action="<?= base_url('admin/configuracoes/email') ?>" method="post">
            <?= csrf_field() ?>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Remetente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Remetente</label>
                            <input type="text" name="email_from_name" class="form-control" value="<?= esc($settings['email_from_name'] ?? 'GPS Imports') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email do Remetente</label>
                            <input type="email" name="email_from_address" class="form-control" value="<?= esc($settings['email_from_address'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Servidor SMTP</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Servidor SMTP</label>
                            <input type="text" name="smtp_host" class="form-control" value="<?= esc($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Porta</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= $settings['smtp_port'] ?? 587 ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usuario SMTP</label>
                            <input type="text" name="smtp_user" class="form-control" value="<?= esc($settings['smtp_user'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Senha SMTP</label>
                            <input type="password" name="smtp_pass" class="form-control" value="<?= esc($settings['smtp_pass'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Criptografia</label>
                        <select name="smtp_crypto" class="form-select">
                            <option value="tls" <?= ($settings['smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($settings['smtp_crypto'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="" <?= empty($settings['smtp_crypto']) ? 'selected' : '' ?>>Nenhuma</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Salvar
                </button>
            </div>
        </form>

        <div class="table-card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Testar Configuracao</h5>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <input type="email" id="testEmail" class="form-control" placeholder="Digite um email para teste">
                    <button type="button" class="btn btn-outline-primary" onclick="testEmail()">
                        <i class="bi bi-send me-1"></i>Enviar Teste
                    </button>
                </div>
                <div id="testResult" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function testEmail() {
    const email = document.getElementById('testEmail').value;
    const resultDiv = document.getElementById('testResult');

    if (!email) {
        resultDiv.innerHTML = '<div class="alert alert-warning">Digite um email</div>';
        return;
    }

    resultDiv.innerHTML = '<div class="alert alert-info">Enviando...</div>';

    fetch('<?= base_url('admin/configuracoes/email/testar') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `test_email=${email}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    });
}
</script>
<?= $this->endSection() ?>
