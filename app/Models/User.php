<?php
/**
 * app/Models/User.php
 * Modelo de datos para la gestión de usuarios
 */

declare(strict_types=1);

namespace App\Models;

use PDO;

class User extends BaseModel
{
    protected string $table = 'usuarios';

    /**
     * Busca un usuario por correo electrónico.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name AS role_name 
            FROM {$this->table} u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email LIMIT 1
        ");
        $stmt->execute([':email' => strtolower(trim($email))]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Registra un nuevo cliente (rol CUSTOMER = 2 por defecto).
     */
    public function registerCustomer(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (role_id, first_name, last_name, email, password_hash, phone, is_active)
            VALUES (2, :first_name, :last_name, :email, :password_hash, :phone, 1)
        ");

        return $stmt->execute([
            ':first_name'    => trim($data['first_name']),
            ':last_name'     => trim($data['last_name']),
            ':email'         => strtolower(trim($data['email'])),
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]),
            ':phone'         => !empty($data['phone']) ? trim($data['phone']) : null
        ]);
    }
}