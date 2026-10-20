<?php
/**
 * app/Core/CSRF.php
 * Gestor de protección Cross-Site Request Forgery (CSRF)
 */

declare(strict_types=1);

namespace App\Core;

class CSRF
{
    /**
     * Genera un token CSRF único y lo almacena en la sesión del usuario.
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Campo HTML oculto para incluir en formularios.
     */
    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida si el token enviado coincide con el almacenado en la sesión.
     */
    public static function validateToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}