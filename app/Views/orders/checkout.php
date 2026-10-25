<?php use App\Core\CSRF; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h3 class="fw-bold mb-4">Confirmación y Envío de Pedido</h3>

                <form action="<?= APP_URL ?>/orders/checkout" method="POST">
                    <?= CSRF::field() ?>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-semibold">Dirección Completa de Envío *</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" required placeholder="Ej: Calle 100 #15-20, Apto 502, Bogotá"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Notas Adicionales (Opcional)</label>
                        <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Ej: Dejar en portería"></textarea>
                    </div>

                    <div class="alert alert-info small mb-4">
                        ℹ️ Su pedido se registrará en estado <strong>PENDIENTE</strong> para su procesamiento.
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold fs-5">Confirmar y Generar Pedido</button>
                </form>
            </div>
        </div>
    </div>
</div>
