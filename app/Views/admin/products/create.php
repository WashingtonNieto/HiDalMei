<?php 
/**
 * app/Views/admin/products/create.php
 * Formulario para el registro de nuevos productos en el sistema HiDalMei
 */

use App\Core\CSRF; 
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-dark mb-0">Registrar Nuevo Producto</h3>
                    <a href="<?= APP_URL ?>/admin/products" class="btn btn-outline-secondary btn-sm">&larr; Volver a Productos</a>
                </div>

                <form action="<?= APP_URL ?>/admin/products/create" method="POST" enctype="multipart/form-data">
                    <?= CSRF::field() ?>

                    <!-- SKU y Nombre -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="sku" class="form-label fw-semibold">SKU *</label>
                            <input type="text" id="sku" name="sku" class="form-control" placeholder="Ej: KBT-SER-001" required>
                        </div>
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Ej: Mascarilla Nocturna de Labios" required>
                        </div>
                    </div>

                    <!-- Categoría y País de Origen -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-semibold">Categoría *</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">-- Seleccionar Categoría --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= htmlspecialchars($cat['nombre'] ?? $cat['name'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="origin_country" class="form-label fw-semibold">País de Origen *</label>
                            <select id="origin_country" name="origin_country" class="form-select" required>
                                <option value="KR">Corea del Sur 🇰🇷</option>
                                <option value="JP">Japón 🇯🇵</option>
                                <option value="CN">China 🇨🇳</option>
                            </select>
                        </div>
                    </div>

                    <!-- Precio, Stock y Estado -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label fw-semibold">Precio ($) *</label>
                            <input type="number" step="0.01" id="price" name="price" class="form-control" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label fw-semibold">Stock (Existencias) *</label>
                            <input type="number" id="stock" name="stock" class="form-control" min="0" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-semibold">Estado *</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="ACTIVE">Activo</option>
                                <option value="INACTIVE">Inactivo</option>
                                <option value="OUT_OF_STOCK">Agotado</option>
                            </select>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Descripción del Producto</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Ingrese los detalles y características principales del producto..."></textarea>
                    </div>

                    <!-- Imagen -->
                    <div class="mb-4">
                        <label for="image" class="form-label fw-semibold">Imagen Principal (JPG, PNG, WebP - Máx 2MB)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Si no sube una imagen, se asignará una imagen predeterminada.</div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <a href="<?= APP_URL ?>/admin/products" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger fw-bold">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>