<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-dark mb-0">Catálogo Oriental</h2>
        <p class="text-muted small">Explora moda, belleza, papelería y coleccionables directos de Corea del Sur, Japón y China.</p>
    </div>
    <div class="col-md-6">
        <form action="<?= APP_URL ?>/catalog" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Buscar producto..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-dark">Buscar</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <h4 class="text-muted">No se encontraron productos en el catálogo.</h4>
        </div>
    <?php else: foreach ($products as $p): ?>
        <?php 
            $nombreVal = $p['nombre'] ?? $p['name'] ?? 'Producto';
            $imgVal = !empty($p['imagen']) ? $p['imagen'] : (!empty($p['image_url']) ? $p['image_url'] : 'default_product.png');
            $precioVal = (float)($p['precio'] ?? $p['price'] ?? 0);
            $catVal = $p['category_name'] ?? 'General';
            $origenVal = $p['pais_origen'] ?? $p['origin_country'] ?? 'KR';
        ?>
        <div class="col-sm-6 col-md-4 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3">
                <img src="<?= APP_URL ?>/assets/img/products/<?= htmlspecialchars($imgVal) ?>" 
                     class="card-img-top p-3 rounded" 
                     style="height: 220px; object-fit: contain;" 
                     alt="<?= htmlspecialchars($nombreVal) ?>"
                     onerror="this.src='https://via.placeholder.com/220?text=HiDalMei';">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-danger"><?= htmlspecialchars($catVal) ?></span>
                        <span class="badge bg-dark"><?= $origenVal ?></span>
                    </div>
                    <h5 class="card-title fw-bold text-dark fs-6 mb-2"><?= htmlspecialchars($nombreVal) ?></h5>
                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($p['descripcion'] ?? '', 0, 80)) ?>...</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="fs-5 fw-bold text-success">$<?= number_format($precioVal, 2) ?></span>
                        <a href="<?= APP_URL ?>/catalog/detail/<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm fw-bold">Ver Detalle</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>