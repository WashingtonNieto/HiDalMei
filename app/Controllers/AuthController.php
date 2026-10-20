<?php
/**
 * app/Controllers/AuthController.php
 * Controlador para registro, login y logout
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

    /**
     * Muestra el formulario de registro.
     */
    public function showRegister(Request $request, Response $response): string
    {
        if (isset($_SESSION['user_id'])) {
            $response->redirect('/');
        }

        return $this->render('auth/register', [
            'pageTitle' => 'Registro de Cliente - HiDalMei'
        ]);
    }

    /**
     * Procesa el registro de un nuevo cliente.
     */
    public function register(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido. Inténtelo de nuevo.';
            $response->redirect('/register');
        }

        $errors = [];
        $firstName = trim($body['first_name'] ?? '');
        $lastName  = trim($body['last_name'] ?? '');
        $email     = trim($body['email'] ?? '');
        $password  = $body['password'] ?? '';
        $confirm   = $body['password_confirm'] ?? '';
        $phone     = trim($body['phone'] ?? '');

        // Validaciones del servidor
        if (empty($firstName) || strlen($firstName) < 2) {
            $errors[] = 'El nombre es obligatorio y debe tener al menos 2 caracteres.';
        }
        if (empty($lastName) || strlen($lastName) < 2) {
            $errors[] = 'El apellido es obligatorio y debe tener al menos 2 caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe ingresar un correo electrónico válido.';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors[] = 'El correo electrónico ya se encuentra registrado.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['old_input'] = ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'phone' => $phone];
            $response->redirect('/register');
        }

        // Registrar cliente
        $success = $this->userModel->registerCustomer([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => $password,
            'phone'      => $phone
        ]);

        if ($success) {
            $_SESSION['flash_success'] = '¡Registro completado con éxito! Ya puedes iniciar sesión.';
            $response->redirect('/login');
        } else {
            $_SESSION['flash_error'] = 'Ocurrió un error al procesar el registro.';
            $response->redirect('/register');
        }
    }

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin(Request $request, Response $response): string
    {
        if (isset($_SESSION['user_id'])) {
            $response->redirect('/');
        }

        return $this->render('auth/login', [
            'pageTitle' => 'Iniciar Sesión - HiDalMei'
        ]);
    }

    /**
     * Procesa la autenticación del usuario.
     */
    public function login(Request $request, Response $response): void
    {
        $body = $request->getBody();

        if (!CSRF::validateToken($body['csrf_token'] ?? '')) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido. Inténtelo de nuevo.';
            $response->redirect('/login');
        }

        $email    = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Por favor, ingrese su correo y contraseña.';
            $response->redirect('/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Credenciales incorrectas.';
            $_SESSION['old_input'] = ['email' => $email];
            $response->redirect('/login');
        }

        if ((int)$user['is_active'] !== 1) {
            $_SESSION['flash_error'] = 'Su cuenta se encuentra inactiva. Contacte con soporte.';
            $response->redirect('/login');
        }

        // Regeneración del ID de sesión para prevenir Session Fixation
        session_regenerate_id(true);

        $_SESSION['user_id']        = $user['id'];
        $_SESSION['user_name']      = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email']     = $user['email'];
        $_SESSION['user_role']      = $user['role_name'];
        $_SESSION['flash_success']  = '¡Bienvenido de nuevo, ' . htmlspecialchars($user['first_name']) . '!';

        // Redireccionar según el rol
        if ($user['role_name'] === 'ADMIN') {
            $response->redirect('/admin/dashboard');
        } else {
            $response->redirect('/');
        }
    }

    /**
     * Cierra la sesión activa.
     */
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
