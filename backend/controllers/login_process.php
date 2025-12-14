<?php
session_start();
require __DIR__ . '/config.php';
// Procesar datos del formulario de login para el inicio de sesión
// del sistema POS de Abarrotes El Caballero
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    header("Location: ../../frontend/views/login.php?error=1");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1");
$stmt->execute([
    ':email' => $email,
    ':password' => $password
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['rol']       = $user['rol']; // CLAVE
    header("Location: ../../frontend/views/pos.php");
    exit;
} else {
    header("Location: ../../frontend/views/login.php?error=1");
    exit;
}

