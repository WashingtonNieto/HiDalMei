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
                <p class="text-center text-muted small mb-4">Regístrate para explorar productos de Corea del Sur, Japón y China.</p>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/register" method="POST">
                    <?= CSRF::field() ?>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($old['apellido'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono (Opcional)</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="direccion" class="form-label">Dirección (Opcional)</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($old['direccion'] ?? '') ?>" placeholder="Ej: Calle 123 #45-67">
                        </div>
                        <div class="col-md-5">
                            <label for="ciudad" class="form-label">Ciudad (Opcional)</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= htmlspecialchars($old['ciudad'] ?? '') ?>" placeholder="Ej: Bogotá">
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text small text-muted">
                                Mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirm" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">Registrarse</button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">¿Ya tienes cuenta? <a href="<?= APP_URL ?>/login" class="text-danger fw-bold">Inicia Sesión</a></small>
                </div>
            </div>
        </div>
    </div>
</div>