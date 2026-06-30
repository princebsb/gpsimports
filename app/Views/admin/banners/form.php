<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= isset($banner) ? 'Editar Banner' : 'Novo Banner' ?></h1>
    <a href="<?= base_url('admin/banners') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<form action="<?= isset($banner) ? base_url('admin/banners/atualizar/' . $banner['id']) : base_url('admin/banners/salvar') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="table-card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Titulo</label>
                        <input type="text" name="title" class="form-control" value="<?= esc($banner['title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subtitulo</label>
                        <input type="text" name="subtitle" class="form-control" value="<?= esc($banner['subtitle'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" value="<?= esc($banner['link'] ?? '') ?>" placeholder="https://">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if (!empty($banner['image'])): ?>
                            <div class="mt-2">
                                <img src="<?= base_url('uploads/banners/' . $banner['image']) ?>" class="rounded" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Tamanho recomendado: 1920x600 pixels</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="table-card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($banner['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($banner['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Posicao</label>
                        <select name="position" class="form-select">
                            <option value="home" <?= ($banner['position'] ?? '') === 'home' ? 'selected' : '' ?>>Home - Principal</option>
                            <option value="category" <?= ($banner['position'] ?? '') === 'category' ? 'selected' : '' ?>>Categoria</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $banner['sort_order'] ?? 0 ?>" min="0">
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
