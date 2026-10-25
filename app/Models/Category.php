<?php
/**
 * app/Models/Category.php
 * Modelo totalmente adaptado a la tabla "categorias" en MySQL
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class Category extends BaseModel
{
    protected string $table = 'categorias';

    public function getAllCategories(bool $onlyActive = false): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1"; // Corregido: 'activo' en lugar de 'is_active'
        }
        $sql .= " ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function nameExists(string $name, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE LOWER(nombre) = :name AND id != :id");
        $stmt->execute([
            ':name' => strtolower(trim($name)),
            ':id'   => $excludeId
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function createCategory(string $name, ?string $description, int $isActive): bool
    {
        $slug = $this->generateSlug($name);
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (nombre, slug, descripcion, activo)
            VALUES (:name, :slug, :description, :is_active)
        ");

        return $stmt->execute([
            ':name'        => trim($name),
            ':slug'        => $slug,
            ':description' => !empty($description) ? trim($description) : null,
            ':is_active'   => $isActive
        ]);
    }

    public function updateCategory(int $id, string $name, ?string $description, int $isActive): bool
    {
        $slug = $this->generateSlug($name);
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET nombre = :name, slug = :slug, descripcion = :description, activo = :is_active
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'          => $id,
            ':name'        => trim($name),
            ':slug'        => $slug,
            ':description' => !empty($description) ? trim($description) : null,
            ':is_active'   => $isActive
        ]);
    }

    public function hasAssociatedProducts(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id");
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function deleteCategory(int $id): bool
    {
        if ($this->hasAssociatedProducts($id)) {
            return false;
        }

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
        return strtolower($text);
    }
}