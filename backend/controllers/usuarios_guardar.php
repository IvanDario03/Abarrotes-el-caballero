<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';

if (($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    die("Acceso no autorizado");
}

$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$rol      = $_POST['rol'] ?? 'cajero';

if ($nombre === '' || $email === '' || $password === '') {
    header("Location: ../../frontend/views/usuarios.php?error=1");
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO users (nombre, email, password, rol)
     VALUES (:nombre, :email, :password, :rol)"
);

$stmt->execute([
    ':nombre'   => $nombre,
    ':email'    => $email,
    ':password' => $password, // (en texto plano, consistente con tu sistema)
    ':rol'      => $rol
]);

header("Location: ../../frontend/views/usuarios.php?ok=1");
exit;
