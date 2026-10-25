<?php 
/**
 * app/Views/admin/products/edit.php
 * Vista de edición de productos ajustada a la base de datos en español
 */

use App\Core\CSRF; 

// Mapeo defensivo de campos desde la base de datos MySQL
$skuVal         = $product['sku'] ?? $product['nombre'] ?? '';
$nombreVal      = $product['nombre'] ?? $product['name'] ?? '';
$categoriaIdVal = $product['categoria_id'] ?? $product['category_id'] ?? 0;
$paisOrigenVal  = $product['pais_origen'] ?? $product['origin_country'] ?? 'KR';
$precioVal      = $product['precio'] ?? $product['price'] ?? 0;
$stockVal       = $product['stock'] ?? 0;
$activoVal      = $product['activo'] ?? ($product['status'] === 'ACTIVE' ? 1 : 0);
$descripcionVal = $product['descripcion'] ?? $product['description'] ?? '';
$imagenVal      = !empty($product['imagen']) ? $product['imagen'] : (!empty($product['image_url']) ? $product['image_url'] : 'default_product.png');
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-dark mb-0">Editar Producto #<?= htmlspecialchars((string)$product['id']) ?></h3>
                    <a href="<?= APP_URL ?>/admin/products" class="btn btn-outline-secondary btn-sm">&larr; Volver a Productos</a>
                </div>

                <form action="<?= APP_URL ?>/admin/products/edit/<?= htmlspecialchars((string)$product['id']) ?>" method="POST" enctype="multipart/form-data">
                    <?= CSRF::field() ?>

                    <!-- SKU y Nombre -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">SKU *</label>
                            <input type="text" id="sku" name="sku" class="form-control" value="<?= htmlspecialchars((string)$skuVal) ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars((string)$nombreVal) ?>" required>
                        </div>
                    </div>

                    <!-- Categoría y País de Origen -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Categoría *</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">-- Seleccionar Categoría --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $categoriaIdVal == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre'] ?? $cat['name'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="origin_country" class="form-label fw-semibold">País de Origen *</label>
                            <select id="origin_country" name="origin_country" class="form-select" required>
                                <option value="KR" <?= $paisOrigenVal === 'KR' || $paisOrigenVal === 'Corea del Sur' ? 'selected' : '' ?>>Corea del Sur 🇰🇷</option>
                                <option value="JP" <?= $paisOrigenVal === 'JP' || $paisOrigenVal === 'Japón' ? 'selected' : '' ?>>Japón 🇯🇵</option>
                                <option value="CN" <?= $paisOrigenVal === 'CN' || $paisOrigenVal === 'China' ? 'selected' : '' ?>>China 🇨🇳</option>
                            </select>
                        </div>
                    </div>

                    <!-- Precio, Stock y Estado -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label fw-semibold">Precio ($) *</label>
                            <input type="number" step="0.01" id="price" name="price" class="form-control" min="0" value="<?= htmlspecialchars((string)$precioVal) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label fw-semibold">Stock (Existencias) *</label>
                            <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?= htmlspecialchars((string)$stockVal) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-semibold">Estado *</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="ACTIVE" <?= $activoVal == 1 ? 'selected' : '' ?>>Activo</option>
                                <option value="INACTIVE" <?= $activoVal == 0 ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Descripción del Producto</label>
                        <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars((string)$descripcionVal) ?></textarea>
                    </div>

                    <!-- Imagen del Producto -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Imagen Actual</label>
                        <div class="mb-2">
                            <img src="<?= APP_URL ?>/assets/img/products/<?= htmlspecialchars($imagenVal) ?>" 
                                 alt="Vista previa" 
                                 class="img-thumbnail" 
                                 style="max-height: 120px;"
                                 onerror="this.src='https://via.placeholder.com/120?text=Sin+Imagen';">
                        </div>
                        <label for="image" class="form-label fw-semibold">Sustituir Imagen (Opcional)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="<?= APP_URL ?>/admin/products" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger fw-bold">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>