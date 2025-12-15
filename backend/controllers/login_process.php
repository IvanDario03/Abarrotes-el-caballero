<?php
session_start();

/* BASE DEL PROYECTO */
$BASE_URL = "/Abarrotes-el-caballero";

/* CONEXIÓN A LA BASE DE DATOS */
require_once __DIR__ . '/../controllers/config.php';

/* OBTENER DATOS DEL FORMULARIO */
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

/* VALIDAR CAMPOS VACÍOS */
if ($email === '' || $password === '') {
    header("Location: $BASE_URL/frontend/views/login.php?error=1");
    exit;
}

/* CONSULTA DEL USUARIO */
$stmt = $pdo->prepare("
    SELECT id, nombre, rol, password 
    FROM users 
    WHERE email = :email 
    LIMIT 1
");
$stmt->execute([
    ':email' => $email
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

/* VALIDAR USUARIO Y CONTRASEÑA */
if ($user && $password === $user['password']) {

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['rol']       = $user['rol'];

    header("Location: $BASE_URL/frontend/views/pos.php");
    exit;

} else {
    header("Location: $BASE_URL/frontend/views/login.php?error=1");
    exit;
}


