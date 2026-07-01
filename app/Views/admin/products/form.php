<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= isset($product) ? 'Editar Produto' : 'Novo Produto' ?></h1>
    <a href="<?= base_url('admin/produtos') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Voltar
    </a>
</div>

<form action="<?= isset($product) ? base_url('admin/produtos/' . $product['id'] . '/atualizar') : base_url('admin/produtos/salvar') ?>" method="post" enctype="multipart/form-data" id="productForm">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informacoes Basicas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= esc($product['name'] ?? old('name')) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" required value="<?= esc($product['sku'] ?? old('sku')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug (URL)</label>
                            <input type="text" name="slug" class="form-control" placeholder="gerado-automaticamente" value="<?= esc($product['slug'] ?? old('slug')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descricao Curta</label>
                        <textarea name="short_description" class="form-control" rows="2" maxlength="500"><?= esc($product['short_description'] ?? old('short_description')) ?></textarea>
                        <small class="text-muted">Exibida na listagem de produtos (max 500 caracteres)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descricao Completa</label>
                        <textarea name="description" class="form-control" id="description" rows="6"><?= $product['description'] ?? old('description') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Precos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Preco de Custo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="cost_price" class="form-control money" value="<?= isset($product['cost_price']) ? number_format($product['cost_price'], 2, ',', '.') : old('cost_price') ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Preco de Venda <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="price" class="form-control money" required value="<?= isset($product['price']) ? number_format($product['price'], 2, ',', '.') : old('price') ?>">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Preco Promocional</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="sale_price" class="form-control money" value="<?= isset($product['sale_price']) ? number_format($product['sale_price'], 2, ',', '.') : old('sale_price') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estoque</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quantidade em Estoque</label>
                            <input type="number" name="stock" class="form-control" min="0" value="<?= $product['stock'] ?? old('stock') ?? 0 ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Alerta de Estoque Baixo</label>
                            <input type="number" name="stock_alert" class="form-control" min="0" value="<?= $product['stock_alert'] ?? old('stock_alert') ?? 5 ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Limite por Pedido</label>
                            <input type="number" name="max_per_order" class="form-control" min="0" value="<?= $product['max_per_order'] ?? old('max_per_order') ?? 0 ?>">
                            <small class="text-muted">0 = sem limite</small>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="manage_stock" class="form-check-input" id="manageStock" value="1" <?= ($product['manage_stock'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="manageStock">Gerenciar estoque automaticamente</label>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Frete e Dimensoes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="text" name="weight" class="form-control" value="<?= $product['weight'] ?? old('weight') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Comprimento (cm)</label>
                            <input type="text" name="length" class="form-control" value="<?= $product['length'] ?? old('length') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Largura (cm)</label>
                            <input type="text" name="width" class="form-control" value="<?= $product['width'] ?? old('width') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Altura (cm)</label>
                            <input type="text" name="height" class="form-control" value="<?= $product['height'] ?? old('height') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="table-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Imagens</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addImage">
                        <i class="bi bi-plus me-1"></i>Adicionar Imagem
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="imagesContainer">
                        <?php if (!empty($product['images'])): ?>
                            <?php foreach ($product['images'] as $image): ?>
                                <div class="col-md-3 image-item">
                                    <div class="position-relative">
                                        <img src="<?= base_url('uploads/products/' . $image['filename']) ?>" class="img-fluid rounded">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="removeExistingImage(<?= $image['id'] ?>, this)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <input type="hidden" name="existing_images[]" value="<?= $image['id'] ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="d-none">

                    <div class="text-center py-4 border-2 border-dashed rounded mt-3" id="dropZone" style="border-style: dashed; cursor: pointer;">
                        <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                        <p class="text-muted mb-0">Arraste imagens aqui ou clique para selecionar</p>
                        <small class="text-muted">JPG, PNG ou WebP. Max 2MB cada.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Publish -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Publicacao</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                            <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                        </select>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" <?= ($product['is_featured'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isFeatured">Produto em Destaque</label>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" name="is_new" class="form-check-input" id="isNew" value="1" <?= ($product['is_new'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isNew">Lancamento</label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_bestseller" class="form-check-input" id="isBestseller" value="1" <?= ($product['is_bestseller'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isBestseller">Mais Vendido</label>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i><?= isset($product) ? 'Atualizar' : 'Salvar' ?> Produto
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Categorias</h5>
                </div>
                <div class="card-body">
                    <select name="category_id" class="form-select">
                        <option value="">Selecione uma categoria</option>
                        <?php
                        $selectedCategory = $product['category_id'] ?? '';
                        foreach ($categories as $id => $name):
                        ?>
                            <option value="<?= $id ?>" <?= $selectedCategory == $id ? 'selected' : '' ?>>
                                <?= esc($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Brand -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Marca</h5>
                </div>
                <div class="card-body">
                    <select name="brand_id" class="form-select">
                        <option value="">Selecione uma marca</option>
                        <?php foreach ($brands as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($product['brand_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= esc($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Origem/Fonte -->
            <?php if (isset($product)): ?>
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-globe me-1"></i>Origem do Produto</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['fonte'])): ?>
                        <div class="mb-2">
                            <strong>Fonte:</strong>
                            <span class="badge bg-info"><?= esc($product['fonte']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['url_origem'])): ?>
                        <div class="mb-2">
                            <strong>Link Original:</strong><br>
                            <a href="<?= esc($product['url_origem']) ?>" target="_blank" class="small text-truncate d-block" style="max-width: 100%;">
                                <i class="bi bi-box-arrow-up-right me-1"></i><?= esc(substr($product['url_origem'], 0, 40)) ?>...
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['preco_usd'])): ?>
                        <div class="mb-2">
                            <strong>Preco USD:</strong>
                            <span class="text-success">$ <?= number_format($product['preco_usd'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="mb-0">
                        <strong>Fornecedor:</strong><br>
                        <a href="https://web.whatsapp.com/send/?phone=595982897556" target="_blank" class="btn btn-success btn-sm mt-1">
                            <i class="bi bi-whatsapp me-1"></i>WhatsApp Fornecedor
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SEO -->
            <div class="table-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">SEO</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" maxlength="70" value="<?= esc($product['meta_title'] ?? old('meta_title')) ?>">
                        <small class="text-muted">Max 70 caracteres</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3" maxlength="160"><?= esc($product['meta_description'] ?? old('meta_description')) ?></textarea>
                        <small class="text-muted">Max 160 caracteres</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Palavras-chave</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?= esc($product['meta_keywords'] ?? old('meta_keywords')) ?>">
                        <small class="text-muted">Separadas por virgula</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // CKEditor
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
        })
        .catch(error => console.error(error));

    // Money mask
    document.querySelectorAll('.money').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2);
            this.value = value.replace('.', ',');
        });
    });

    // Image upload
    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('imageInput');
    const imagesContainer = document.getElementById('imagesContainer');

    dropZone.addEventListener('click', () => imageInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary');
        handleFiles(e.dataTransfer.files);
    });

    imageInput.addEventListener('change', () => {
        handleFiles(imageInput.files);
    });

    function handleFiles(files) {
        [...files].forEach(file => {
            if (!file.type.startsWith('image/')) return;
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('Imagem muito grande: ' + file.name);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'col-md-3 image-item';
                div.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" onclick="this.closest('.image-item').remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                imagesContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeExistingImage(id, btn) {
        if (confirm('Remover esta imagem?')) {
            btn.closest('.image-item').remove();
        }
    }

    // Form submission - convert money values
    document.getElementById('productForm').addEventListener('submit', function() {
        document.querySelectorAll('.money').forEach(input => {
            input.value = input.value.replace('.', '').replace(',', '.');
        });
    });
</script>
<?= $this->endSection() ?>
