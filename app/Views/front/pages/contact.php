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
                    <h5 class="card-title mb-4">Informacoes de Contato</h5>

                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Endereco</h6>
                            <p class="text-muted mb-0"><?= setting('address') ?? 'Av. Paulista, 1000 - Sao Paulo, SP' ?></p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-telephone fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Telefone</h6>
                            <p class="text-muted mb-0"><?= setting('phone') ?? '(11) 99999-9999' ?></p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Email</h6>
                            <p class="text-muted mb-0"><?= setting('email') ?? 'contato@gpsimports.com.br' ?></p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-clock fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1">Horario de Atendimento</h6>
                            <p class="text-muted mb-0">Segunda a Sexta: 9h as 18h<br>Sabado: 9h as 13h</p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Redes Sociais</h6>
                    <div class="d-flex gap-2">
                        <a href="<?= setting('facebook') ?? '#' ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="<?= setting('instagram') ?? '#' ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="<?= setting('youtube') ?? '#' ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', setting('whatsapp') ?? '') ?>" class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
