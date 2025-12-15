<?php
session_start();

// Cargar configuración y conexión a la BD
require_once __DIR__ . '/../controllers/config.php';

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header("Location: /Abarrotes-el-caballero/frontend/views/login.php?error=1");
    exit;
}

// Consulta del usuario
$stmt = $pdo->prepare("
    SELECT * 
    FROM users 
    WHERE email = :email 
      AND password = :password 
    LIMIT 1
");

$stmt->execute([
    ':email'    => $email,
    ':password' => $password
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['rol']       = $user['rol'];

    // Redirigir al POS
    header("Location: /Abarrotes-el-caballero/frontend/views/pos.php");
    exit;
} else {
    header("Location: /Abarrotes-el-caballero/frontend/views/login.php?error=1");
    exit;
}
