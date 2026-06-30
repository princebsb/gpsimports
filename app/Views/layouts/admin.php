<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $title ?? 'Admin' ?> - <?= setting('store_name') ?? 'GPS Imports' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary-color: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f1f5f9;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand img {
            max-height: 40px;
        }

        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-weight: 600;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
        }

        .nav-section {
            padding: 0.5rem 1.5rem;
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #cbd5e1;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        .sidebar-nav .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .sidebar-nav .nav-link .badge {
            margin-left: auto;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-card .stat-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .stat-card .stat-change {
            font-size: 0.75rem;
        }

        /* Tables & Cards */
        .table-card {
            background: #fff;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .table-card .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .table-card .card-body {
            padding: 1.5rem;
        }

        .table-card .card-body.p-0,
        .table-card.has-table > .card-body {
            padding: 0;
        }

        .table-card .card-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        /* Form spacing inside cards */
        .table-card .form-label {
            margin-bottom: 0.5rem;
        }

        .table-card .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        .table-card .table th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        /* Status badges */
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-processing { background: #dbeafe; color: #1e40af; }
        .badge-shipped { background: #e0e7ff; color: #3730a3; }
        .badge-delivered { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        /* Mobile */
        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Form styles */
        .form-label {
            font-weight: 500;
            color: #374151;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        /* Image preview */
        .image-preview {
            width: 100px;
            height: 100px;
            border: 2px dashed #e2e8f0;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .image-preview:hover {
            border-color: var(--primary-color);
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Page header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #1e293b;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-box-seam me-2"></i><?= setting('store_name') ?? 'GPS Imports' ?></h4>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() === 'admin' ? 'active' : '' ?>" href="<?= base_url('admin') ?>">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>

                <li class="nav-section">Catalogo</li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/produtos') ? 'active' : '' ?>" href="<?= base_url('admin/produtos') ?>">
                        <i class="bi bi-box"></i>
                        Produtos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/categorias') ? 'active' : '' ?>" href="<?= base_url('admin/categorias') ?>">
                        <i class="bi bi-folder"></i>
                        Categorias
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/marcas') ? 'active' : '' ?>" href="<?= base_url('admin/marcas') ?>">
                        <i class="bi bi-tag"></i>
                        Marcas
                    </a>
                </li>

                <li class="nav-section">Vendas</li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/pedidos') ? 'active' : '' ?>" href="<?= base_url('admin/pedidos') ?>">
                        <i class="bi bi-cart3"></i>
                        Pedidos
                        <?php if (isset($pendingOrdersCount) && $pendingOrdersCount > 0): ?>
                            <span class="badge bg-danger"><?= $pendingOrdersCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/cupons') ? 'active' : '' ?>" href="<?= base_url('admin/cupons') ?>">
                        <i class="bi bi-ticket-perforated"></i>
                        Cupons
                    </a>
                </li>

                <li class="nav-section">Marketing</li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/banners') ? 'active' : '' ?>" href="<?= base_url('admin/banners') ?>">
                        <i class="bi bi-image"></i>
                        Banners
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/newsletter') ? 'active' : '' ?>" href="<?= base_url('admin/newsletter') ?>">
                        <i class="bi bi-envelope"></i>
                        Newsletter
                    </a>
                </li>

                <li class="nav-section">Clientes</li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/clientes') ? 'active' : '' ?>" href="<?= base_url('admin/clientes') ?>">
                        <i class="bi bi-people"></i>
                        Clientes
                    </a>
                </li>

                <li class="nav-section">Sistema</li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/configuracoes') ? 'active' : '' ?>" href="<?= base_url('admin/configuracoes') ?>">
                        <i class="bi bi-gear"></i>
                        Configuracoes
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(), 'admin/usuarios') ? 'active' : '' ?>" href="<?= base_url('admin/usuarios') ?>">
                        <i class="bi bi-person-badge"></i>
                        Usuarios
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button class="btn btn-link sidebar-toggle me-3 p-0" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Admin</a></li>
                        <?php if (isset($breadcrumb)): ?>
                            <?php foreach ($breadcrumb as $item): ?>
                                <?php if (isset($item['url'])): ?>
                                    <li class="breadcrumb-item"><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active"><?= $item['label'] ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>

            <div class="d-flex align-items-center">
                <!-- Store Link -->
                <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Ver Loja
                </a>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-decoration-none text-dark" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= session()->get('admin_name') ?? 'Admin' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('admin/perfil') ?>"><i class="bi bi-person me-2"></i>Meu Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('admin/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        });

        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            this.classList.remove('show');
        });

        // Toastr config
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3000
        };

        // CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });

        // Delete confirmation
        function confirmDelete(url, message = 'Tem certeza que deseja excluir?') {
            Swal.fire({
                title: 'Confirmacao',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        // Format currency input
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2);
            input.value = value.replace('.', ',');
        }

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
