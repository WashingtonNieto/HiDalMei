<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'HiDalMei') ?></title>
    <!-- CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- JS de Bootstrap 5 (con Popper) para activar Modales, Alertas y Dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger fs-4" href="<?= APP_URL ?>/">⛩️ HiDalMei</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/catalog">Catálogo</a></li>
                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ADMINISTRADOR')): ?>
                        <li class="nav-item"><a class="nav-link text-warning fw-bold" href="<?= APP_URL ?>/admin/dashboard">Panel Admin</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item me-3">
                            <span class="navbar-text text-white">
                                👤 <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> 
                                <span class="badge bg-danger ms-1"><?= htmlspecialchars($_SESSION['user_role']) ?></span>
                            </span>
                        </li>
                        <li class="nav-item">
                            <form action="<?= APP_URL ?>/logout" method="POST" class="d-inline">
                                <?= \App\Core\CSRF::field() ?>
                                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/login">Iniciar Sesión</a></li>
                        <li class="nav-item"><a class="btn btn-danger btn-sm ms-2 px-3" href="<?= APP_URL ?>/register">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>