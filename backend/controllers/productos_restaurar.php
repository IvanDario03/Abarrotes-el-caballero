<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';

if (($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    die("Acceso no autorizado");
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE productos SET estado = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../../frontend/views/productos.php?filtro=eliminados");
exit;
