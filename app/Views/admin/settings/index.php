<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Configurações Gerais</h1>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <div class="list-group">
            <a href="<?= base_url('admin/configuracoes') ?>" class="list-group-item list-group-item-action active">
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
            <a href="<?= base_url('admin/configuracoes/email') ?>" class="list-group-item list-group-item-action">
                <i class="bi bi-envelope me-2"></i>Email
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <form action="<?= base_url('admin/configuracoes/salvar') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Identidade Visual</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Site</label>
                            <input type="text" name="setting_site_name" class="form-control" value="<?= esc($settings['site_name'] ?? 'GPS Imports') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slogan</label>
                            <input type="text" name="setting_site_slogan" class="form-control" value="<?= esc($settings['site_slogan'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="site_logo" class="form-control" accept="image/*">
                            <?php if (!empty($settings['site_logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/' . $settings['site_logo']) ?>" style="max-height: 50px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Favicon</label>
                            <input type="file" name="site_favicon" class="form-control" accept="image/*">
                            <?php if (!empty($settings['site_favicon'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url('uploads/' . $settings['site_favicon']) ?>" style="max-height: 32px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">SEO</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="setting_site_meta_title" class="form-control" value="<?= esc($settings['site_meta_title'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="setting_site_meta_description" class="form-control" rows="3"><?= esc($settings['site_meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="setting_site_meta_keywords" class="form-control" value="<?= esc($settings['site_meta_keywords'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Redes Sociais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-facebook text-primary"></i> Facebook</label>
                            <input type="url" name="setting_social_facebook" class="form-control" value="<?= esc($settings['social_facebook'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-instagram text-danger"></i> Instagram</label>
                            <input type="url" name="setting_social_instagram" class="form-control" value="<?= esc($settings['social_instagram'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-youtube text-danger"></i> YouTube</label>
                            <input type="url" name="setting_social_youtube" class="form-control" value="<?= esc($settings['social_youtube'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-twitter text-info"></i> Twitter/X</label>
                            <input type="url" name="setting_social_twitter" class="form-control" value="<?= esc($settings['social_twitter'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
