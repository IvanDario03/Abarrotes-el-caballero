<?php
require_once __DIR__ . '/../../backend/controllers/auth.php';
require_once __DIR__ . '/../../backend/controllers/config.php';
include __DIR__ . '/../../views/layout/header.php';

// Obtener productos activos
$sql = "SELECT * FROM productos WHERE estado = 1 ORDER BY nombre ASC";
$productos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Rol
$rol = $_SESSION['rol'] ?? '';
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- PRODUCTOS -->
  <div class="lg:col-span-7">
    <div class="bg-white rounded-xl shadow h-full flex flex-col">
      
      <!-- Header -->
      <div class="flex justify-between items-center px-4 py-3 border-b gap-2">
        <h2 class="font-semibold text-lg">Productos</h2>

        <div class="flex items-center gap-2">
          <!-- BOTÓN REGISTRAR PRODUCTO -->
          <?php if (in_array($rol, ['admin', 'cajero'])): ?>
            <a href="productos.php"
               class="bg-green-700 hover:bg-green-800 text-white text-sm px-3 py-1 rounded-lg transition">
              + Registrar producto
            </a>
          <?php endif; ?>

          <!-- BUSCADOR -->
          <input
            type="text"
            id="buscadorProductos"
            placeholder="Buscar producto..."
            class="w-40 sm:w-56 px-3 py-1 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
          >
        </div>
      </div>

      <!-- Tabla -->
      <div class="overflow-y-auto" style="max-height: 400px;">
        <table class="w-full text-sm" id="tablaProductos">
          <thead class="bg-gray-100 sticky top-0">
            <tr>
              <th class="text-left px-4 py-2">Producto</th>
              <th class="text-left px-4 py-2">Precio</th>
              <th class="text-left px-4 py-2">Stock</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($productos as $p): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2">
                  <div class="font-medium">
                    <?php echo htmlspecialchars($p['nombre']); ?>
                  </div>
                  <div class="text-xs text-gray-500">
                    <?php echo htmlspecialchars($p['descripcion']); ?>
                  </div>
                </td>
                <td class="px-4 py-2">
                  $<?php echo number_format($p['precio_venta'], 2); ?>
                </td>
                <td class="px-4 py-2">
                  <?php echo $p['stock']; ?>
                </td>
                <td class="px-4 py-2 text-right">
                  <button
                    type="button"
                    class="btn-agregar bg-green-600 hover:bg-green-700 text-white w-7 h-7 rounded-full text-lg leading-none"
                    data-id="<?php echo $p['id']; ?>"
                    data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>"
                    data-precio="<?php echo $p['precio_venta']; ?>"
                  >
                    +
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- CARRITO -->
  <div class="lg:col-span-5">
    <div class="bg-white rounded-xl shadow h-full flex flex-col">

      <!-- Header -->
      <div class="bg-green-700 text-white px-4 py-3 rounded-t-xl flex justify-between items-center">
        <span class="font-semibold">Carrito de venta</span>
        <span class="text-xs opacity-90">Ticket en pantalla</span>
      </div>

      <!-- Tabla carrito -->
      <div class="flex-1 overflow-y-auto" style="max-height:260px;">
        <table class="w-full text-sm" id="tablaCarrito">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 py-2 text-left">Prod.</th>
              <th class="px-2 py-2 text-center">Cant.</th>
              <th class="px-2 py-2 text-right">Precio</th>
              <th class="px-2 py-2 text-right">Subt.</th>
              <th class="px-2 py-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <!-- JS -->
          </tbody>
        </table>
      </div>

      <!-- TOTAL + MÉTODO DE PAGO + BOTÓN -->
      <div class="p-4 border-t">
        <div class="flex justify-between items-center mb-3">
          <h3 class="text-lg font-semibold">Total:</h3>
          <span class="text-2xl font-bold text-green-700" id="totalGeneral">$0.00</span>
        </div>

        <form
          action="../../backend/controllers/procesar_venta.php"
          method="post"
          onsubmit="return prepararEnvioVenta();"
          class="space-y-3"
        >
          <input type="hidden" name="cart_json" id="cart_json">
          <input type="hidden" name="total" id="total_input">

          <!-- MÉTODO DE PAGO -->
          <div>
            <label class="block text-sm font-semibold mb-1">
              Método de pago
            </label>
            <select
              name="metodo_pago"
              required
              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
            >
              <option value="efectivo">Efectivo </option>
              <option value="tarjeta">Tarjeta </option>
              <option value="paypal">PayPal </option>
            </select>
          </div>

          <button
            type="submit"
            class="w-full bg-green-700 hover:bg-green-800 text-white py-3 rounded-lg font-bold transition"
          >
            Cobrar y generar ticket
          </button>
        </form>
      </div>

    </div>
  </div>

</div>

<?php include __DIR__ . '/../../views/layout/footer.php'; ?>
