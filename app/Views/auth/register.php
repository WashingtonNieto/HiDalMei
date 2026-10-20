<?php
use App\Core\CSRF;

$old = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['old_input'], $_SESSION['flash_errors']);
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="card-title text-center fw-bold text-dark mb-3">Crear Cuenta en HiDalMei</h3>
                <p class="text-center text-muted small mb-4">Regístrate para explorar y comprar lo mejor de la moda y estética asiática.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/register" method="POST" class="needs-validation">
                    <?= CSRF::field() ?>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                            <div class="form-text">Mínimo 8 caracteres.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirm" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">Registrarse</button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">¿Ya tienes una cuenta? <a href="<?= APP_URL ?>/login" class="text-danger fw-bold">Inicia Sesión</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
