<?php
/**
 * app/Models/User.php
 * Modelo alineado a la estructura exacta de la tabla "usuarios" en phpMyAdmin
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class User extends BaseModel
{
    // Tabla exacta en la base de datos
    protected string $table = 'usuarios';

    /**
     * Busca un usuario por email relacionando la tabla "roles"
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.nombre AS role_name 
            FROM {$this->table} u
            INNER JOIN roles r ON u.rol_id = r.id
            WHERE LOWER(u.email) = :email LIMIT 1
        ");
        $stmt->execute([':email' => strtolower(trim($email))]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Registra un nuevo cliente usando los nombres exactos de las columnas
     */
    public function registerCustomer(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (rol_id, nombre, apellido, email, password, telefono, direccion, ciudad, activo)
            VALUES (2, :nombre, :apellido, :email, :password, :telefono, :direccion, :ciudad, 1)
        ");



        return $stmt->execute([
            ':nombre'   => trim($data['nombre']),
            ':apellido' => trim($data['apellido']),
            ':email'    => strtolower(trim($data['correo'])),
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]),
            ':telefono' => !empty($data['telefono']) ? trim($data['telefono']) : null,
            ':direccion' => !empty($data['direccion']) ? trim($data['direccion']) : null,
            ':ciudad'    => !empty($data['ciudad']) ? trim($data['ciudad']) : null

        ]);
    }
}


