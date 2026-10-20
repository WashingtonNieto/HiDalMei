<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'HiDalMei') ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="<?= APP_URL ?>/">⛩️ HiDalMei</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="<?= APP_URL ?>/">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/catalog">Catálogo</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/login">Iniciar Sesión</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm ms-2" href="<?= APP_URL ?>/register">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container py-4">