<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
        }

        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo i {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .login-logo h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
            color: #1e293b;
        }

        .login-logo p {
            color: #64748b;
            margin: 0;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 0.5rem;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .input-group-text {
            background: #f8fafc;
            border-right: 0;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group .form-control:focus {
            border-left: 0;
        }

        .password-toggle {
            cursor: pointer;
            background: #f8fafc;
            border-left: 0;
        }

        .alert {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <i class="bi bi-box-seam"></i>
            <h1><?= setting('store_name') ?? 'GPS Imports' ?></h1>
            <p>Painel Administrativo</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com" required autofocus value="<?= old('email') ?>">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="passwordIcon"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Lembrar-me</label>
                </div>
                <a href="<?= base_url('admin/esqueci-senha') ?>" class="text-decoration-none">Esqueci a senha</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url() ?>" class="text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Voltar para a loja
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

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
    </script>
</body>
</html>
