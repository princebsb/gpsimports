<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Criar sua conta</h2>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
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

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('cadastro') ?>" method="post" id="registerForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="redirect_url" value="<?= esc($redirect_url ?? '') ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required value="<?= old('first_name') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sobrenome <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required value="<?= old('last_name') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF <span class="text-danger">*</span></label>
                                <input type="text" name="cpf" id="cpf" class="form-control" required value="<?= old('cpf') ?>" maxlength="14">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" required value="<?= old('phone') ?>" maxlength="15">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= old('birth_date') ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required minlength="8">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', 'passwordIcon')">
                                        <i class="bi bi-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimo 8 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirm', 'passwordConfirmIcon')">
                                        <i class="bi bi-eye" id="passwordConfirmIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="newsletter" id="newsletter" class="form-check-input" value="1" checked>
                            <label for="newsletter" class="form-check-label">
                                Desejo receber ofertas e novidades por e-mail
                            </label>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                            <label for="terms" class="form-check-label">
                                Li e aceito os <a href="<?= base_url('termos-uso') ?>" target="_blank">Termos de Uso</a>
                                e a <a href="<?= base_url('politica-privacidade') ?>" target="_blank">Politica de Privacidade</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                                <i class="bi bi-person-plus me-2"></i>Criar Conta
                            </button>
                            <small class="text-muted text-center mt-2" id="termsHint">
                                <i class="bi bi-info-circle"></i> Aceite os termos de uso para continuar
                            </small>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Ja tem uma conta? <a href="<?= base_url('login') ?><?= !empty($redirect_url) ? '?redirect=' . urlencode($redirect_url) : '' ?>">Entrar</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // CPF mask
    document.getElementById('cpf')?.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        this.value = value;
    });

    // Phone mask
    document.getElementById('phone')?.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
        }
        this.value = value;
    });

    // Form validation
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        const termsCheckbox = document.getElementById('terms');
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value.replace(/\D/g, '');

        // Validar email
        if (!validateEmail(email)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'E-mail invalido',
                text: 'Digite um e-mail valido.',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('email').focus();
            return false;
        }

        // Validar telefone (10 ou 11 digitos)
        if (!validatePhone(phone)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Telefone invalido',
                text: 'Digite um telefone valido com DDD (10 ou 11 digitos).',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('phone').focus();
            return false;
        }

        // Validar CPF (11 digitos)
        if (cpf.length !== 11) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'CPF invalido',
                text: 'Digite um CPF valido com 11 digitos.',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('cpf').focus();
            return false;
        }

        // Validar CPF - algoritmo
        if (!validateCPF(cpf)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'CPF invalido',
                text: 'O CPF informado nao e valido.',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('cpf').focus();
            return false;
        }

        // Validar senhas
        if (password !== passwordConfirm) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Senhas diferentes',
                text: 'As senhas digitadas nao conferem.',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('password_confirm').focus();
            return false;
        }

        // Validar tamanho da senha
        if (password.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Senha muito curta',
                text: 'A senha deve ter no minimo 8 caracteres.',
                confirmButtonColor: '#3085d6'
            });
            document.getElementById('password').focus();
            return false;
        }

        // Validar termos
        if (!termsCheckbox.checked) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Termos de Uso',
                text: 'Voce precisa aceitar os Termos de Uso e a Politica de Privacidade para criar sua conta.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }

        return true;
    });

    // Validacao de email
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // Validacao de telefone brasileiro (10 ou 11 digitos)
    function validatePhone(phone) {
        phone = phone.replace(/\D/g, '');

        // Deve ter 10 (fixo) ou 11 (celular) digitos
        if (phone.length < 10 || phone.length > 11) return false;

        // DDD valido (11-99)
        const ddd = parseInt(phone.substring(0, 2));
        if (ddd < 11 || ddd > 99) return false;

        // Se celular (11 digitos), deve comecar com 9
        if (phone.length === 11 && phone[2] !== '9') return false;

        return true;
    }

    // Validacao de CPF no frontend
    function validateCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');

        if (cpf.length !== 11) return false;

        // Verifica se todos os digitos sao iguais
        if (/^(\d)\1{10}$/.test(cpf)) return false;

        // Calcula primeiro digito verificador
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf[i]) * (10 - i);
        }
        let resto = soma % 11;
        let digito1 = resto < 2 ? 0 : 11 - resto;

        if (parseInt(cpf[9]) !== digito1) return false;

        // Calcula segundo digito verificador
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf[i]) * (11 - i);
        }
        resto = soma % 11;
        let digito2 = resto < 2 ? 0 : 11 - resto;

        if (parseInt(cpf[10]) !== digito2) return false;

        return true;
    }

    // Feedback visual no campo CPF
    document.getElementById('cpf')?.addEventListener('blur', function() {
        const cpf = this.value.replace(/\D/g, '');
        if (cpf.length === 11) {
            if (validateCPF(cpf)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else if (cpf.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Feedback visual no campo Email
    document.getElementById('email')?.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email.length > 0) {
            if (validateEmail(email)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Feedback visual no campo Telefone
    document.getElementById('phone')?.addEventListener('blur', function() {
        const phone = this.value.replace(/\D/g, '');
        if (phone.length >= 10) {
            if (validatePhone(phone)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        } else if (phone.length > 0) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Controle do botao de submit baseado nos termos
    const termsCheckbox = document.getElementById('terms');
    const submitBtn = document.getElementById('submitBtn');
    const termsHint = document.getElementById('termsHint');

    function updateSubmitButton() {
        if (termsCheckbox.checked) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
            termsHint.style.display = 'none';
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
            termsHint.style.display = 'block';
        }
    }

    termsCheckbox?.addEventListener('change', updateSubmitButton);

    // Estado inicial
    updateSubmitButton();
</script>
<?= $this->endSection() ?>
