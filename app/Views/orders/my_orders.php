<h2 class="fw-bold mb-4">Mis Pedidos Realizados</h2>

<?php if (empty($orders)): ?>
    <div class="alert alert-secondary">Aún no has realizado pedidos en HiDalMei.</div>
<?php else: ?>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Número de Orden</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="fw-bold"><code><?= $o['numero_orden'] ?? $o['order_number'] ?? '' ?></code></td>
                                <td><?= $o['created_at'] ?></td>
                                <td class="fw-bold text-success">$<?= number_format((float)$o['total'], 2) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars((string)($o['estado'] ?? $o['status'] ?? 'PENDIENTE')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
