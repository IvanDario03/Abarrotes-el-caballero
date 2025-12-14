<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';

/**
 * 🔐 PERMITIR ADMIN Y CAJERO
 * (si luego quieres solo admin, se cambia aquí)
 */
if (!in_array($_SESSION['rol'] ?? '', ['admin', 'cajero'])) {
    http_response_code(403);
    die("Acceso no autorizado");
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 🧠 BORRADO LÓGICO
    $stmt = $pdo->prepare(
        "UPDATE productos SET estado = 0 WHERE id = :id"
    );
    $stmt->execute([':id' => $id]);
}

// 🔁 Volver directo a eliminados
header("Location: ../../frontend/views/productos.php?filtro=eliminados");
exit;



