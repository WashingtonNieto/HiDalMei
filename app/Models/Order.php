<?php
/**
 * app/Models/Order.php
 * Modelo alineado al 100% con la estructura real de "pedidos" y "detalle_pedidos"
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Exception;

class Order extends BaseModel
{
    protected string $table = 'pedidos';

    public function createOrder(int $userId, array $cartItems, float $shippingCost, string $shippingAddress, ?string $notes): string
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            $subtotal = 0.0;
            $itemsToInsert = [];

            // 1. Validar productos y existencias
            foreach ($cartItems as $productId => $item) {
                $stmt = $db->prepare("SELECT * FROM productos WHERE id = ? AND activo = 1 FOR UPDATE");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception("El producto '" . htmlspecialchars((string)($item['name'] ?? '')) . "' ya no está disponible.");
                }

                if ((int)$product['stock'] < (int)$item['quantity']) {
                    throw new Exception("Stock insuficiente para '" . htmlspecialchars((string)($product['nombre'] ?? '')) . "'. Disponibles: " . $product['stock']);
                }

                $price = (float)$product['precio'];
                $qty = (int)$item['quantity'];
                $subtotal += ($price * $qty);

                $itemsToInsert[] = [
                    'product_id' => $product['id'],
                    'unit_price' => $price,
                    'quantity'   => $qty
                ];

                // Descuento atómico de existencias en productos
                $updateStock = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $updateStock->execute([$qty, $product['id'], $qty]);

                if ($updateStock->rowCount() === 0) {
                    throw new Exception("Error al actualizar existencias de '" . htmlspecialchars((string)$product['nombre']) . "'.");
                }
            }

            $total = $subtotal + $shippingCost;

            // 2. Inserción en la tabla "pedidos"
            $sqlOrder = "
                INSERT INTO pedidos 
                (usuario_id, total, estado, direccion_envio, notas)
                VALUES (?, ?, ?, ?, ?)
            ";

            $stmtOrder = $db->prepare($sqlOrder);
            $stmtOrder->execute([
                $userId,
                $total,
                'PENDIENTE',
                trim($shippingAddress),
                !empty($notes) ? trim($notes) : ''
            ]);

            $orderId = (int)$db->lastInsertId();
            $orderNumber = 'HDM-ORD-' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT);

            // 3. Inserción en "detalle_pedidos" (solo con pedido_id, producto_id, precio_unitario, cantidad)
            $sqlItem = "
                INSERT INTO detalle_pedidos 
                (pedido_id, producto_id, precio_unitario, cantidad)
                VALUES (?, ?, ?, ?)
            ";

            $stmtItem = $db->prepare($sqlItem);

            foreach ($itemsToInsert as $detail) {
                $stmtItem->execute([
                    $orderId,
                    $detail['product_id'],
                    $detail['unit_price'],
                    $detail['quantity']
                ]);
            }

            $db->commit();
            return $orderNumber;

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function getOrdersByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT *, CONCAT('HDM-ORD-', LPAD(id, 6, '0')) AS numero_orden 
            FROM pedidos 
            WHERE usuario_id = ? 
            ORDER BY id DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllOrders(): array
    {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   CONCAT('HDM-ORD-', LPAD(o.id, 6, '0')) AS numero_orden,
                   CONCAT(u.nombre, ' ', u.apellido) AS customer_name, 
                   u.email 
            FROM pedidos o
            INNER JOIN usuarios u ON o.usuario_id = u.id
            ORDER BY o.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }
}
