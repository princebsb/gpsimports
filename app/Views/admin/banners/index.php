<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Banners</h1>
    <a href="<?= base_url('admin/banners/criar') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Novo Banner
    </a>
</div>

<div class="table-card">
    <div class="card-body">
        <div class="row g-4" id="bannersGrid">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $banner): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <?php if ($banner['image']): ?>
                                <img src="<?= base_url('uploads/banners/' . $banner['image']) ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($banner['title'] ?? 'Sem titulo') ?></h5>
                                <p class="card-text small text-muted">
                                    <?php if ($banner['link']): ?>
                                        <i class="bi bi-link-45deg"></i> <?= esc($banner['link']) ?>
                                    <?php endif; ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($banner['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/banners/editar/' . $banner['id']) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('<?= base_url('admin/banners/excluir/' . $banner['id']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-image fs-1 text-muted"></i>
                    <p class="mt-2 text-muted">Nenhum banner cadastrado</p>
                    <a href="<?= base_url('admin/banners/criar') ?>" class="btn btn-primary">
                        <i class="bi bi-plus me-1"></i>Criar Banner
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
