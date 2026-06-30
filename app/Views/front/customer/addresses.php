<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <?= $this->include('front/customer/_sidebar') ?>
        </div>

        <!-- Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Meus Enderecos</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                    <i class="bi bi-plus-lg me-2"></i>Novo Endereco
                </button>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($addresses)): ?>
                <div class="row">
                    <?php foreach ($addresses as $address): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 <?= $address['is_default'] ? 'border-primary' : '' ?>">
                                <div class="card-body">
                                    <?php if ($address['is_default']): ?>
                                        <span class="badge bg-primary mb-2">Padrao</span>
                                    <?php endif; ?>
                                    <h6 class="card-title"><?= esc($address['name'] ?? $address['label'] ?? 'Endereco') ?></h6>
                                    <p class="card-text mb-1">
                                        <?= esc($address['street']) ?>, <?= esc($address['number']) ?>
                                        <?php if (!empty($address['complement'])): ?>
                                            - <?= esc($address['complement']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text mb-1"><?= esc($address['neighborhood']) ?></p>
                                    <p class="card-text mb-1"><?= esc($address['city']) ?> - <?= esc($address['state']) ?></p>
                                    <p class="card-text"><strong>CEP:</strong> <?= esc($address['zipcode']) ?></p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editAddress(<?= htmlspecialchars(json_encode($address), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                    <?php if (!$address['is_default']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAddress(<?= $address['id'] ?>)">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-geo-alt fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Voce ainda nao tem enderecos cadastrados.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal">
                            <i class="bi bi-plus-lg me-2"></i>Adicionar Endereco
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('minha-conta/enderecos/salvar') ?>" method="post" id="addressForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="addressId">

                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalTitle">Novo Endereco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Endereco</label>
                            <input type="text" name="label" id="label" class="form-control" placeholder="Ex: Casa, Trabalho">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CEP <span class="text-danger">*</span></label>
                            <input type="text" name="zipcode" id="zipcode" class="form-control" required maxlength="9">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Rua <span class="text-danger">*</span></label>
                            <input type="text" name="street" id="street" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Numero <span class="text-danger">*</span></label>
                            <input type="text" name="number" id="number" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complement" id="complement" class="form-control" placeholder="Apto, Bloco, etc">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bairro <span class="text-danger">*</span></label>
                            <input type="text" name="neighborhood" id="neighborhood" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Cidade <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select name="state" id="state" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1">
                        <label for="is_default" class="form-check-label">Definir como endereco padrao</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Endereco</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // CEP mask
    document.getElementById('zipcode')?.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length <= 8) {
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        this.value = value;
    });

    // Auto-fill address from CEP
    document.getElementById('zipcode')?.addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('street').value = data.logradouro || '';
                        document.getElementById('neighborhood').value = data.bairro || '';
                        document.getElementById('city').value = data.localidade || '';
                        document.getElementById('state').value = data.uf || '';
                        document.getElementById('number').focus();
                    }
                })
                .catch(error => console.log('Erro ao buscar CEP:', error));
        }
    });

    // Edit address
    function editAddress(address) {
        document.getElementById('addressModalTitle').textContent = 'Editar Endereco';
        document.getElementById('addressId').value = address.id;
        document.getElementById('label').value = address.name || address.label || '';
        document.getElementById('zipcode').value = address.zipcode || '';
        document.getElementById('street').value = address.street || '';
        document.getElementById('number').value = address.number || '';
        document.getElementById('complement').value = address.complement || '';
        document.getElementById('neighborhood').value = address.neighborhood || '';
        document.getElementById('city').value = address.city || '';
        document.getElementById('state').value = address.state || '';
        document.getElementById('is_default').checked = address.is_default == 1;

        new bootstrap.Modal(document.getElementById('addressModal')).show();
    }

    // Reset form when modal closes
    document.getElementById('addressModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('addressModalTitle').textContent = 'Novo Endereco';
        document.getElementById('addressForm').reset();
        document.getElementById('addressId').value = '';
    });

    // Delete address
    function deleteAddress(id) {
        Swal.fire({
            title: 'Excluir endereco?',
            text: 'Esta acao nao pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit via POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('minha-conta/enderecos/excluir') ?>/' + id;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?= $this->endSection() ?>
