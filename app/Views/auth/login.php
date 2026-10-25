<?php
use App\Core\CSRF;

$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="card-title text-center fw-bold text-dark mb-3">Iniciar Sesión</h3>
                <p class="text-center text-muted small mb-4">Ingresa con tu correo registrado.</p>

                <form action="<?= APP_URL ?>/login" method="POST">
                    <?= CSRF::field() ?>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">Ingresar</button>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">¿No tienes cuenta? <a href="<?= APP_URL ?>/register" class="text-danger fw-bold">Regístrate</a></small>
                </div>
            </div>
        </div>
    </div>
</div>