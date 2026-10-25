<?php
/**
 * app/Controllers/AuthController.php
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\CSRF;
use App\Models\User;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showRegister(Request $request, Response $response): string
    {
        if (isset($_SESSION['user_id'])) {
            $response->redirect('/');
        }

        return $this->render('auth/register', [
            'pageTitle' => 'Registro de Cliente - HiDalMei'
        ]);
    }

    public function register(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido. Inténtelo de nuevo.';
            $response->redirect('/register');
        }

        $errors = [];
        $nombre    = trim($body['nombre'] ?? '');
        $apellido  = trim($body['apellido'] ?? '');
        $correo    = trim($body['correo'] ?? '');
        $password  = $body['password'] ?? '';
        $confirm   = $body['password_confirm'] ?? '';
        $telefono  = trim($body['telefono'] ?? '');
        $direccion = trim($body['direccion'] ?? '');
        $ciudad    = trim($body['ciudad'] ?? '');

        if (empty($nombre) || strlen($nombre) < 2) {
            $errors[] = 'El nombre es obligatorio (mínimo 2 caracteres).';
        }
        if (empty($apellido) || strlen($apellido) < 2) {
            $errors[] = 'El apellido es obligatorio (mínimo 2 caracteres).';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe ingresar un correo electrónico válido.';
        } elseif ($this->userModel->findByEmail($correo)) {
            $errors[] = 'El correo electrónico ya se encuentra registrado.';
        }

        // Política de Contraseña con Expresión Regular Requerida
        $pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';
        if (!preg_match($pattern, $password)) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres, incluir al menos una letra mayúscula, una minúscula y un número.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['old_input'] = [
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'correo'    => $correo,
                'telefono'  => $telefono,
                'direccion' => $direccion,
                'ciudad'    => $ciudad
            ];
            $response->redirect('/register');
        }

        $success = $this->userModel->registerCustomer([
            'nombre'    => $nombre,
            'apellido'  => $apellido,
            'correo'    => $correo,
            'password'  => $password,
            'telefono'  => $telefono,
            'direccion' => $direccion,
            'ciudad'    => $ciudad
        ]);

        if ($success) {
            $_SESSION['flash_success'] = '¡Registro completado exitosamente! Ahora puede iniciar sesión.';
            $response->redirect('/login');
        } else {
            $_SESSION['flash_error'] = 'Error al registrar el usuario en la base de datos.';
            $response->redirect('/register');
        }
    }

    public function showLogin(Request $request, Response $response): string
    {
        if (isset($_SESSION['user_id'])) {
            $response->redirect('/');
        }

        return $this->render('auth/login', [
            'pageTitle' => 'Iniciar Sesión - HiDalMei'
        ]);
    }

    public function login(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido. Inténtelo de nuevo.';
            $response->redirect('/login');
        }

        $correo   = trim($body['correo'] ?? '');
        $password = $body['password'] ?? '';

        if (empty($correo) || empty($password)) {
            $_SESSION['flash_error'] = 'Por favor, complete todos los campos.';
            $response->redirect('/login');
        }

        $user = $this->userModel->findByEmail($correo);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Credenciales de acceso incorrectas.';
            $_SESSION['old_input'] = ['correo' => $correo];
            $response->redirect('/login');
        }

        if ((int)$user['activo'] !== 1) {
            $_SESSION['flash_error'] = 'La cuenta se encuentra inactiva.';
            $response->redirect('/login');
        }

        session_regenerate_id(true);

        // Guardar información en sesión para el Menú Superior
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_name']     = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_email']    = $user['email'];
        $_SESSION['user_role']     = strtoupper($user['role_name'] ?? 'CUSTOMER');
        $_SESSION['flash_success'] = '¡Bienvenido de nuevo, ' . htmlspecialchars($user['nombre']) . '!';

        if ($_SESSION['user_role'] === 'ADMIN' || $_SESSION['user_role'] === 'ADMINISTRADOR') {
            $response->redirect('/admin/dashboard');
        } else {
            $response->redirect('/');
        }
    }

    public function logout(Request $request, Response $response): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        session_start();
        $_SESSION['flash_success'] = 'Ha cerrado sesión correctamente.';
        $response->redirect('/login');
    }
}