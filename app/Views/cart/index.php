<?php
/**
 * app/Views/cart/index.php
 */
use App\Core\CSRF;
?>

<h2 class="fw-bold mb-4">Tu Carrito de Compras</h2>

<?php if (empty($cart)): ?>
    <div class="alert alert-info text-center py-4">
        Tu carrito está vacío. <a href="<?= APP_URL ?>/catalog" class="fw-bold alert-link">Explorar productos</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $subtotalTotal = 0; ?>
                                <?php foreach ($cart as $id => $item): ?>
                                    <?php 
                                        $itemSubtotal = $item['price'] * $item['quantity']; 
                                        $subtotalTotal += $itemSubtotal; 
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= APP_URL ?>/assets/img/products/<?= htmlspecialchars($item['image_url']) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-3" onerror="this.src='https://via.placeholder.com/50?text=HDM';">
                                                <span class="fw-bold"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <form action="<?= APP_URL ?>/cart/update" method="POST" class="d-flex gap-1" style="max-width: 110px;">
                                                <?= CSRF::field() ?>
                                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center">
                                                <button type="submit" class="btn btn-sm btn-secondary">✓</button>
                                            </form>
                                        </td>
                                        <td class="fw-bold">$<?= number_format($itemSubtotal, 2) ?></td>
                                        <td>
                                            <a href="<?= APP_URL ?>/cart/remove/<?= $id ?>" class="text-danger fw-bold text-decoration-none">✕</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Resumen del Pedido</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-bold">$<?= number_format($subtotalTotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Envío estimado:</span>
                        <span>$12,000.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 fs-5 fw-bold">
                        <span>Total:</span>
                        <span class="text-success">$<?= number_format($subtotalTotal + 12000, 2) ?></span>
                    </div>
                    <a href="<?= APP_URL ?>/orders/checkout" class="btn btn-danger w-100 py-2 fw-bold">Proceder al Checkout &rarr;</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
