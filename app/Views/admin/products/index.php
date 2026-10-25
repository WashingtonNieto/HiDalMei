<?php use App\Core\CSRF; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Gestión de Catálogo de Productos</h2>
    <a href="<?= APP_URL ?>/admin/products/create" class="btn btn-danger fw-bold">+ Registrar Producto</a>
</div>

<!-- Barra de Filtros -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="<?= APP_URL ?>/admin/products" method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Buscar por SKU o Nombre..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="0">-- Todas las Categorías --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre'] ?? $cat['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                <a href="<?= APP_URL ?>/admin/products" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Productos -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Origen</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron productos en el catálogo.</td></tr>
                    <?php else: foreach ($products as $p): ?>
                        <?php 
                            $skuVal = $p['sku'] ?? $p['codigo'] ?? 'S/N';
                            $imgVal = !empty($p['imagen_url']) ? $p['imagen_url'] : (!empty($p['image_url']) ? $p['image_url'] : 'default_product.png');
                            $nombreVal = $p['nombre'] ?? $p['name'] ?? 'Sin Nombre';
                            $catVal = $p['category_name'] ?? 'Sin Categoría';
                            $origenVal = $p['pais_origen'] ?? $p['origin_country'] ?? 'KR';
                            $precioVal = (float)($p['precio'] ?? $p['price'] ?? 0);
                            $stockVal = (int)($p['stock'] ?? 0);
                            $estadoVal = $p['estado'] ?? $p['status'] ?? 'ACTIVE';
                        ?>
                        <tr>
                            <td>
                                <img src="<?= APP_URL ?>/assets/img/products/<?= htmlspecialchars($imgVal) ?>" 
                                     alt="Img" 
                                     class="rounded border" 
                                     style="width: 48px; height: 48px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/48?text=HDM';">
                            </td>
                            <td><code><?= htmlspecialchars((string)$skuVal) ?></code></td>
                            <td class="fw-bold"><?= htmlspecialchars((string)$nombreVal) ?></td>
                            <td><?= htmlspecialchars((string)$catVal) ?></td>
                            <td>
                                <span class="badge bg-dark">
                                    <?= $origenVal === 'KR' || $origenVal === 'Corea del Sur' ? 'Corea del Sur' : ($origenVal === 'JP' || $origenVal === 'Japón' ? 'Japón' : $origenVal) ?>
                                </span>
                            </td>
                            <td class="fw-bold text-success">$<?= number_format($precioVal, 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $stockVal > 5 ? 'info' : 'warning' ?>">
                                    <?= $stockVal ?> uds.
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $estadoVal === 'ACTIVE' || $estadoVal === 'Activo' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars((string)$estadoVal) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= APP_URL ?>/admin/products/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                <form action="<?= APP_URL ?>/admin/products/delete/<?= $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Confirma la eliminación del producto?');">
                                    <?= CSRF::field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>