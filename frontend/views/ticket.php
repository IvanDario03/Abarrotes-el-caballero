<?php
require_once __DIR__ . '/../../backend/controllers/auth.php';
require_once __DIR__ . '/../../backend/controllers/config.php';

/**
 *  OBTENER ID DE VENTA DESDE SESIÓN
 * (ya no desde la URL)
 */
$venta_id = intval($_SESSION['venta_ticket_id'] ?? 0);

if ($venta_id <= 0) {
    die("Ticket no disponible.");
}

/**
 * (Opcional pero recomendado)
 * Evita que el ticket se vuelva a ver al refrescar
 */
unset($_SESSION['venta_ticket_id']);

// Obtener venta
$stmtVenta = $pdo->prepare(
    "SELECT v.*, u.nombre AS usuario_nombre
     FROM ventas v
     LEFT JOIN users u ON v.usuario_id = u.id
     WHERE v.id = :id"
);
$stmtVenta->execute([':id' => $venta_id]);
$venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada.");
}

/**
 *  VALIDACIÓN EXTRA DE SEGURIDAD
 * El cajero solo puede ver sus propios tickets
 * El admin puede ver todos
 */
if ($venta['usuario_id'] != $_SESSION['user_id'] && ($_SESSION['rol'] ?? '') !== 'admin') {
    die("Acceso no autorizado.");
}

// Obtener detalle
$stmtDet = $pdo->prepare(
    "SELECT d.*, p.nombre
     FROM venta_detalle d
     LEFT JOIN productos p ON d.producto_id = p.id
     WHERE d.venta_id = :venta_id"
);
$stmtDet->execute([':venta_id' => $venta_id]);
$detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ticket de venta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center py-6">

  <!-- TICKET -->
  <div class="bg-white w-[320px] rounded-lg shadow px-4 py-4 text-sm">

    <!-- Encabezado -->
    <div class="text-center mb-3">
      <h1 class="text-base font-bold tracking-wide">ABARROTES EL CABALLERO</h1>
      <p class="text-xs text-gray-500">Ticket de venta</p>
    </div>

    <div class="border-t border-dashed my-2"></div>

    <!-- Datos venta -->
    <div class="space-y-1 text-xs">
      <div><span class="font-semibold">Folio:</span> <?php echo $venta['id']; ?></div>
      <div><span class="font-semibold">Fecha:</span> <?php echo $venta['fecha']; ?></div>
      <div><span class="font-semibold">Cajero:</span> <?php echo htmlspecialchars($venta['usuario_nombre'] ?? ''); ?></div>
      <div><span class="font-semibold">Método:</span> <?php echo htmlspecialchars($venta['metodo_pago']); ?></div>
    </div>

    <div class="border-t border-dashed my-2"></div>

    <!-- Detalle -->
    <table class="w-full text-xs">
      <thead>
        <tr class="border-b">
          <th class="text-left py-1">Prod.</th>
          <th class="text-center py-1">Cant.</th>
          <th class="text-right py-1">Precio</th>
          <th class="text-right py-1">Subt.</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($detalles as $d): ?>
          <tr class="border-b last:border-b-0">
            <td class="py-1"><?php echo htmlspecialchars($d['nombre']); ?></td>
            <td class="py-1 text-center"><?php echo $d['cantidad']; ?></td>
            <td class="py-1 text-right">$<?php echo number_format($d['precio_unitario'], 2); ?></td>
            <td class="py-1 text-right">$<?php echo number_format($d['subtotal'], 2); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="border-t border-dashed my-2"></div>

    <!-- Total -->
    <div class="flex justify-between font-bold text-sm mb-2">
      <span>Total:</span>
      <span>$<?php echo number_format($venta['total'], 2); ?></span>
    </div>

    <!-- Footer -->
    <div class="text-center mt-3">
      <p class="text-xs text-gray-500 mb-2">Gracias por su compra</p>

      <a href="pos.php"
         class="block bg-green-700 hover:bg-green-800 text-white text-sm py-2 rounded-lg transition">
        Nueva venta
      </a>
    </div>

  </div>

</body>
</html>


