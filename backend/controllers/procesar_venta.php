<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config.php';

// 📦 Datos recibidos
$cart_json   = $_POST['cart_json'] ?? '[]';
$total       = floatval($_POST['total'] ?? 0);
$metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';

$cart = json_decode($cart_json, true);
if (!is_array($cart) || count($cart) === 0) {
    header("Location: ../../frontend/views/pos.php?error=1");
    exit;
}

$usuario_id = $_SESSION['user_id'] ?? null;

// 🔐 Métodos permitidos
$metodos_validos = ['efectivo', 'tarjeta', 'paypal'];
if (!in_array($metodo_pago, $metodos_validos)) {
    $metodo_pago = 'efectivo';
}

try {
    $pdo->beginTransaction();

    // 🧾 Insertar venta
    $stmtVenta = $pdo->prepare(
        "INSERT INTO ventas (total, usuario_id, metodo_pago)
         VALUES (:total, :usuario_id, :metodo_pago)"
    );

    $stmtVenta->execute([
        ':total'       => $total,
        ':usuario_id'  => $usuario_id,
        ':metodo_pago' => $metodo_pago
    ]);

    $venta_id = $pdo->lastInsertId();

    // 🧾 Insertar detalle
    $stmtDetalle = $pdo->prepare(
        "INSERT INTO venta_detalle
         (venta_id, producto_id, cantidad, precio_unitario, subtotal)
         VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)"
    );

    // 🔍 Consultar stock y estado
    $stmtProducto = $pdo->prepare(
        "SELECT stock, estado FROM productos WHERE id = :id FOR UPDATE"
    );

    // 📉 Descontar stock
    $stmtDescontarStock = $pdo->prepare(
        "UPDATE productos SET stock = stock - :cantidad WHERE id = :id"
    );

    // 🚫 Desactivar producto
    $stmtDesactivarProducto = $pdo->prepare(
        "UPDATE productos SET estado = 0 WHERE id = :id"
    );

    foreach ($cart as $item) {

        $producto_id = intval($item['id']);
        $cantidad    = intval($item['cantidad']);
        $precio      = floatval($item['precio']);
        $subtotal    = $cantidad * $precio;

        // 🔍 Verificar producto
        $stmtProducto->execute([':id' => $producto_id]);
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto || $producto['estado'] != 1) {
            throw new Exception("Producto no disponible");
        }

        if ($producto['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente");
        }

        // 🧾 Guardar detalle
        $stmtDetalle->execute([
            ':venta_id'        => $venta_id,
            ':producto_id'     => $producto_id,
            ':cantidad'        => $cantidad,
            ':precio_unitario' => $precio,
            ':subtotal'        => $subtotal
        ]);

        // 📉 Descontar stock
        $stmtDescontarStock->execute([
            ':cantidad' => $cantidad,
            ':id'       => $producto_id
        ]);

        // 🚫 Desactivar si se agotó
        if (($producto['stock'] - $cantidad) <= 0) {
            $stmtDesactivarProducto->execute([':id' => $producto_id]);
        }
    }

    $pdo->commit();

    // 🔐 Ticket por sesión
    $_SESSION['venta_ticket_id'] = $venta_id;

    header("Location: ../../frontend/views/ticket.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../../frontend/views/pos.php?error=stock");
    exit;
}
