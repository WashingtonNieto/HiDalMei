<?php
/**
 * PROYECTO: HiDalMei E-Commerce
 * ARCHIVO: database/create_admin.php
 * DESCRIPCIÓN: Script CLI interactivo para la creación segura del Administrador Inicial.
 * USO EN CONSOLA: php database/create_admin.php
 */

declare(strict_types=1);

// Cargar configuración de base de datos
$config = [
    'host' => '127.0.0.1',
    'db'   => 'hidalmei_db',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4'
];

$dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
    echo "=== GENERADOR DE ADMINISTRADOR INICIAL HIDALMEI ===\n\n";

    // Solicitar datos por consola
    echo "Nombre: ";
    $nombre = trim((string) fgets(STDIN));

    echo "Apellido: ";
    $apellido = trim((string) fgets(STDIN));

    echo "Email: ";
    $email = trim((string) fgets(STDIN));

    echo "Contraseña: ";
    // Ocultar entrada en sistemas Unix o leer directo
    $password = trim((string) fgets(STDIN));

    if (empty($nombre) || empty($apellido) || empty($email) || strlen($password) < 8) {
        die("Error: Todos los campos son obligatorios. La contraseña debe tener al menos 8 caracteres.\n");
    }

    // Verificar si el rol Administrador existe
    $stmtRol = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'Administrador'");
    $stmtRol->execute();
    $rol = $stmtRol->fetch();

    if (!$rol) {
        die("Error: El rol 'Administrador' no está cargado en la tabla 'roles'. Ejecute primero schema.sql y seed.sql.\n");
    }

    // Generar Hash Seguro (BCRYPT / ARGON2ID)
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Insertar Usuario Admin
    $sql = "INSERT INTO usuarios (rol_id, nombre, apellido, email, password, activo) 
            VALUES (:rol_id, :nombre, :apellido, :email, :password, 1)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'rol_id'   => $rol['id'],
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'email'    => $email,
        'password' => $passwordHash
    ]);

    echo "\n¡ÉXITO! Usuario administrador '{$email}' creado correctamente.\n";

} catch (PDOException $e) {
    die("\nError en la base de datos: " . $e->getMessage() . "\n");
}