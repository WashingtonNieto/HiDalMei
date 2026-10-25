<?php use App\Core\CSRF; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Gestión de Categorías</h2>
    <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#createCategoryModal">+ Nueva Categoría</button>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No existen categorías registradas.</td></tr>
                    <?php else: foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($cat['nombre'] ?? $cat['name'] ?? '') ?></td>
                            <td><code><?= htmlspecialchars($cat['slug'] ?? '') ?></code></td>
                            <td><?= htmlspecialchars($cat['descripcion'] ?? $cat['description'] ?? 'Sin descripción') ?></td>
                            <td>
                                <span class="badge bg-<?= ($cat['is_active'] ?? $cat['activo'] ?? 1) ? 'success' : 'secondary' ?>">
                                    <?= ($cat['is_active'] ?? $cat['activo'] ?? 1) ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="<?= APP_URL ?>/admin/categories/delete/<?= $cat['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmar la eliminación de la categoría?');">
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

<!-- Modal de Creación de Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= APP_URL ?>/admin/categories/create" method="POST" class="modal-content">
            <?= CSRF::field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="createCategoryModalLabel">Agregar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Categoría *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                    <label class="form-check-label" for="is_active">Categoría Activa</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Script Bootstrap JS para activar el Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>