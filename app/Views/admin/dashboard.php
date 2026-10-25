<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Panel de Administración General</h2>
    <span class="badge bg-danger p-2 fs-6">Rol: Administrador</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white text-dark h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="fs-1 text-danger me-3">🛍️</div>
                <div>
                    <h6 class="text-muted mb-1">Total Productos</h6>
                    <h2 class="fw-bold mb-0"><?= $totalProducts ?></h2>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pb-3">
                <a href="<?= APP_URL ?>/admin/products" class="btn btn-sm btn-outline-danger w-100">Gestionar Catálogo &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white text-dark h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="fs-1 text-primary me-3">🏷️</div>
                <div>
                    <h6 class="text-muted mb-1">Categorías</h6>
                    <h2 class="fw-bold mb-0"><?= $totalCategories ?></h2>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pb-3">
                <a href="<?= APP_URL ?>/admin/categories" class="btn btn-sm btn-outline-primary w-100">Gestionar Categorías &rarr;</a>
            </div>
        </div>
    </div>
</div>
