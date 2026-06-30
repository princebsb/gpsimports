<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Marcas</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal">
        <i class="bi bi-plus-lg me-1"></i>Nova Marca
    </button>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover" id="brandsTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Marca</th>
                        <th>Slug</th>
                        <th>Produtos</th>
                        <th>Status</th>
                        <th style="width: 150px;">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td>
                                    <?php if ($brand['logo']): ?>
                                        <img src="<?= base_url('uploads/brands/' . $brand['logo']) ?>" class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-tag text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= esc($brand['name']) ?></strong></td>
                                <td><code><?= esc($brand['slug']) ?></code></td>
                                <td><?= $brand['products_count'] ?? 0 ?></td>
                                <td>
                                    <?php if ($brand['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="editBrand(<?= htmlspecialchars(json_encode($brand)) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('<?= base_url('admin/marcas/' . $brand['id'] . '/excluir') ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-tag fs-1"></i>
                                    <p class="mt-2">Nenhuma marca encontrada</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/marcas/salvar') ?>" method="post" enctype="multipart/form-data" id="brandForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="brandId">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="brandName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="brandSlug" class="form-control" placeholder="gerado-automaticamente">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descricao</label>
                        <textarea name="description" id="brandDescription" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" id="brandWebsite" class="form-control" placeholder="https://">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div id="currentLogo" class="mt-2" style="display: none;">
                            <img src="" class="rounded" style="max-height: 60px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="sort_order" id="brandSortOrder" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="brandStatus" class="form-select">
                                <option value="active">Ativa</option>
                                <option value="inactive">Inativa</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_featured" id="brandFeatured" class="form-check-input" value="1">
                        <label class="form-check-label" for="brandFeatured">Marca em destaque</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function editBrand(brand) {
        document.getElementById('modalTitle').textContent = 'Editar Marca';
        document.getElementById('brandId').value = brand.id;
        document.getElementById('brandName').value = brand.name;
        document.getElementById('brandSlug').value = brand.slug;
        document.getElementById('brandDescription').value = brand.description || '';
        document.getElementById('brandWebsite').value = brand.website || '';
        document.getElementById('brandSortOrder').value = brand.sort_order;
        document.getElementById('brandStatus').value = brand.status || 'active';
        document.getElementById('brandFeatured').checked = brand.is_featured == 1;

        if (brand.logo) {
            document.getElementById('currentLogo').style.display = 'block';
            document.getElementById('currentLogo').querySelector('img').src = '<?= base_url('uploads/brands/') ?>' + brand.logo;
        } else {
            document.getElementById('currentLogo').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('brandModal')).show();
    }

    // Reset form when modal closes
    document.getElementById('brandModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('modalTitle').textContent = 'Nova Marca';
        document.getElementById('brandForm').reset();
        document.getElementById('brandId').value = '';
        document.getElementById('currentLogo').style.display = 'none';
    });

    // Auto-generate slug
    document.getElementById('brandName').addEventListener('input', function() {
        if (!document.getElementById('brandId').value) {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('brandSlug').value = slug;
        }
    });
</script>
<?= $this->endSection() ?>
