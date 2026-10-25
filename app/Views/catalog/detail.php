<?php
/**
 * app/Views/catalog/detail.php
 */
use App\Core\CSRF;

$nombreVal    = $product['nombre'] ?? $product['name'] ?? 'Producto';
$imgVal       = !empty($product['imagen']) ? $product['imagen'] : (!empty($product['image_url']) ? $product['image_url'] : 'default_product.png');
$precioVal    = (float)($product['precio'] ?? $product['price'] ?? 0);
$stockVal     = (int)($product['stock'] ?? 0);
$origenVal    = $product['pais_origen'] ?? $product['origin_country'] ?? 'KR';
$descripcion  = $product['descripcion'] ?? 'Sin descripción detallada disponible.';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-5 text-center">
                        <img src="<?= APP_URL ?>/assets/img/products/<?= htmlspecialchars($imgVal) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 350px; object-fit: contain;" 
                             alt="<?= htmlspecialchars($nombreVal) ?>"
                             onerror="this.src='https://via.placeholder.com/350?text=HiDalMei';">
                    </div>
                    <div class="col-md-7 d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-dark me-2">Origen: <?= $origenVal ?></span>
                            <span class="badge bg-<?= $stockVal > 0 ? 'success' : 'danger' ?>">
                                <?= $stockVal > 0 ? 'Disponible (' . $stockVal . ' uds)' : 'Agotado' ?>
                            </span>
                        </div>
                        <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($nombreVal) ?></h2>
                        <h3 class="fw-bold text-success mb-4">$<?= number_format($precioVal, 2) ?></h3>

                        <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($descripcion)) ?></p>

                        <?php if ($stockVal > 0): ?>
                            <form action="<?= APP_URL ?>/cart/add" method="POST" class="mt-auto">
                                <?= CSRF::field() ?>
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <label for="quantity" class="col-form-label fw-semibold">Cantidad:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?= $stockVal ?>" style="width: 90px;" required>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-danger fw-bold w-100 py-2">🛒 Agregar al Carrito</button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 py-2 mt-auto" disabled>Producto Agotado</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>