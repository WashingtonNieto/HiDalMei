<?php
/**
 * app/Models/Product.php
 * Modelo alineado exactamente a las columnas de la tabla "productos"
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class Product extends BaseModel
{
    protected string $table = 'productos';

    public function getPaginatedProducts(int $page = 1, int $perPage = 8, string $search = '', int $categoryId = 0): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = ["1=1"];

        if (!empty($search)) {
            $where[] = "p.nombre LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $where[] = "p.categoria_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $whereClause = implode(" AND ", $where);

        $sql = "
            SELECT p.*, 
                   p.nombre AS sku, 
                   p.nombre AS name, 
                   COALESCE(p.imagen, 'default_product.png') AS image_url,
                   IF(p.activo = 1, 'ACTIVE', 'INACTIVE') AS status,
                   c.nombre AS category_name 
            FROM {$this->table} p
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE {$whereClause}
            ORDER BY p.id DESC
            LIMIT :offset, :perPage
        ";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalCount(string $search = '', int $categoryId = 0): int
    {
        $params = [];
        $where = ["1=1"];

        if (!empty($search)) {
            $where[] = "nombre LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $where[] = "categoria_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $whereClause = implode(" AND ", $where);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}");
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function skuExists(string $sku, int $excludeId = 0): bool
    {
        return false;
    }

    public function createProduct(array $data): bool
    {
        $slug = $this->generateSlug($data['name']);
        
        // Mapeo del estado 'ACTIVE'/'INACTIVE' hacia la columna entero 'activo'
        $activo = ($data['status'] === 'ACTIVE' || $data['status'] == 1) ? 1 : 0;

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (categoria_id, nombre, slug, descripcion, pais_origen, precio, stock, imagen, activo)
            VALUES (:categoria_id, :nombre, :slug, :descripcion, :pais_origen, :precio, :stock, :imagen, :activo)
        ");

        return $stmt->execute([
            ':categoria_id' => $data['category_id'],
            ':nombre'       => trim($data['name']),
            ':slug'         => $slug,
            ':descripcion'  => !empty($data['description']) ? trim($data['description']) : null,
            ':pais_origen'  => $data['origin_country'],
            ':precio'       => $data['price'],
            ':stock'        => $data['stock'],
            ':imagen'       => $data['image_url'],
            ':activo'       => $activo
        ]);
    }

    public function updateProduct(int $id, array $data): bool
    {
        $slug = $this->generateSlug($data['name']);
        $activo = ($data['status'] === 'ACTIVE' || $data['status'] == 1) ? 1 : 0;

        $sql = "
            UPDATE {$this->table}
            SET categoria_id = :categoria_id, nombre = :nombre, slug = :slug,
                descripcion = :descripcion, pais_origen = :pais_origen,
                precio = :precio, stock = :stock, activo = :activo
        ";

        if (!empty($data['image_url'])) {
            $sql .= ", imagen = :imagen";
        }

        $sql .= " WHERE id = :id";

        $params = [
            ':id'           => $id,
            ':categoria_id' => $data['category_id'],
            ':nombre'       => trim($data['name']),
            ':slug'         => $slug,
            ':descripcion'  => !empty($data['description']) ? trim($data['description']) : null,
            ':pais_origen'  => $data['origin_country'],
            ':precio'       => $data['price'],
            ':stock'        => $data['stock'],
            ':activo'       => $activo
        ];

        if (!empty($data['image_url'])) {
            $params[':imagen'] = $data['image_url'];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteProduct(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    private function generateSlug(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
    }
}