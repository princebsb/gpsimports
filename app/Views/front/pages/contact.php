<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <h1 class="h3 mb-4">Entre em Contato</h1>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('contato') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= old('phone') ?>">
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Assunto *</label>
                    <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Mensagem *</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required><?= old('message') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Enviar Mensagem
                </button>
            </form>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informações de Contato</h5>

                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Endereço</h6>
                            <p class="text-muted mb-0">
                                <?= setting('store_address') ?><br>
                                <?= setting('store_neighborhood') ?> - <?= setting('store_city') ?>/<?= setting('store_state') ?><br>
                                CEP: <?= setting('store_zipcode') ?>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-whatsapp fs-4 text-success me-3"></i>
                        <div>
                            <h6 class="mb-1">WhatsApp</h6>
                            <p class="text-muted mb-0">
                                <a href="https://wa.me/<?= setting('store_whatsapp') ?>" target="_blank">
                                    <?= format_phone(setting('store_whatsapp') ?? '') ?>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Email</h6>
                            <p class="text-muted mb-0">
                                <a href="mailto:<?= setting('store_email') ?>"><?= setting('store_email') ?></a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-clock fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Horário de Atendimento</h6>
                            <p class="text-muted mb-0">Segunda a Sexta: 9h às 18h</p>
                        </div>
                    </div>

                    <hr>

                    <p class="small text-muted mb-0">
                        <strong><?= setting('store_razao_social') ?></strong><br>
                        CNPJ: <?= setting('store_cnpj') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
