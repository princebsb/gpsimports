<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Categorias</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-plus-lg me-1"></i>Nova Categoria
    </button>
</div>

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover" id="categoriesTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Ordem</th>
                        <th>Categoria</th>
                        <th>Slug</th>
                        <th>Produtos</th>
                        <th>Status</th>
                        <th style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <tr data-id="<?= $category['id'] ?>">
                                <td>
                                    <span class="badge bg-secondary"><?= $category['sort_order'] ?></span>
                                </td>
                                <td>
                                    <?= str_repeat('<span class="text-muted">— </span>', $category['level'] ?? 0) ?>
                                    <?php if ($category['image']): ?>
                                        <img src="<?= base_url('uploads/categories/' . $category['image']) ?>" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                    <?php endif; ?>
                                    <strong><?= esc($category['name']) ?></strong>
                                </td>
                                <td><code><?= esc($category['slug']) ?></code></td>
                                <td><?= $category['products_count'] ?? 0 ?></td>
                                <td>
                                    <?php if ($category['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('<?= base_url('admin/categorias/' . $category['id'] . '/excluir') ?>')">
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
                                    <i class="bi bi-folder fs-1"></i>
                                    <p class="mt-2">Nenhuma categoria encontrada</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/categorias/salvar') ?>" method="post" enctype="multipart/form-data" id="categoryForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="categoryId">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="categoryName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="categorySlug" class="form-control" placeholder="gerado-automaticamente">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoria Pai</label>
                        <select name="parent_id" id="categoryParent" class="form-select">
                            <option value="">Nenhuma (raiz)</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php if (($cat['level'] ?? 0) < 2): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= str_repeat('- ', $cat['level'] ?? 0) ?><?= esc($cat['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" id="categoryDescription" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div id="currentImage" class="mt-2" style="display: none;">
                            <img src="" class="rounded" style="max-height: 80px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="sort_order" id="categorySortOrder" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="categoryStatus" class="form-select">
                                <option value="active">Ativa</option>
                                <option value="inactive">Inativa</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_menu" id="categoryShowMenu" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="categoryShowMenu">Exibir no menu</label>
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
    function editCategory(category) {
        document.getElementById('modalTitle').textContent = 'Editar Categoria';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categorySlug').value = category.slug;
        document.getElementById('categoryParent').value = category.parent_id || '';
        document.getElementById('categoryDescription').value = category.description || '';
        document.getElementById('categorySortOrder').value = category.sort_order;
        document.getElementById('categoryStatus').value = category.status || 'active';
        document.getElementById('categoryShowMenu').checked = category.is_menu == 1;

        if (category.image) {
            document.getElementById('currentImage').style.display = 'block';
            document.getElementById('currentImage').querySelector('img').src = '<?= base_url('uploads/categories/') ?>' + category.image;
        } else {
            document.getElementById('currentImage').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }

    // Reset form when modal closes
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('modalTitle').textContent = 'Nova Categoria';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('currentImage').style.display = 'none';
    });

    // Auto-generate slug
    document.getElementById('categoryName').addEventListener('input', function() {
        if (!document.getElementById('categoryId').value) {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('categorySlug').value = slug;
        }
    });
</script>
<?= $this->endSection() ?>
