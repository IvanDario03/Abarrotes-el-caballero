<?php
require_once __DIR__ . '/../../backend/controllers/auth.php';

/**
 * ACCESO PERMITIDO
 * Admin y Cajero pueden entrar
 */
if (!in_array($_SESSION['rol'] ?? '', ['admin', 'cajero'])) {
    header("Location: pos.php");
    exit;
}

$esAdmin  = ($_SESSION['rol'] ?? '') === 'admin';
$esCajero = ($_SESSION['rol'] ?? '') === 'cajero';

require_once __DIR__ . '/../../backend/controllers/config.php';
include __DIR__ . '/../../views/layout/header.php';

// Categorías
$categorias = $pdo->query("SELECT * FROM categorias WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);

/**
 ** FILTRO POR ESTADO
 * - activos (default): estado = 1
 * - eliminados: estado = 0 (solo admin)
 * - todos: sin filtro (solo admin)
 */
$filtro = $_GET['filtro'] ?? 'activos';

$whereEstado = "p.estado = 1"; // default

if ($esAdmin) {
    if ($filtro === 'eliminados') {
        $whereEstado = "p.estado = 0";
    } elseif ($filtro === 'todos') {
        $whereEstado = "1=1";
    }
} else {
    // Si es cajero, forzar a activos aunque ponga otra cosa en URL
    $filtro = 'activos';
    $whereEstado = "p.estado = 1";
}

// Productos con filtro aplicado
$sql = "SELECT p.*, c.nombre AS categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id
        WHERE $whereEstado
        ORDER BY p.nombre ASC";
$productos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Producto a editar (SOLO ADMIN y SOLO si está activo)
$edit_id = $esAdmin ? ($_GET['id'] ?? '') : '';
$producto_edit = null;

if ($edit_id !== '') {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id AND estado = 1 LIMIT 1");
    $stmt->execute([':id' => $edit_id]);
    $producto_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- FORMULARIO -->
  <div class="lg:col-span-4">
    <div class="bg-white rounded-xl shadow">

      <div class="bg-green-700 text-white px-4 py-3 rounded-t-xl font-semibold">
        <?php echo $producto_edit ? 'Editar producto' : 'Nuevo producto'; ?>
      </div>

      <div class="p-4">
        <form action="../../backend/controllers/productos_guardar.php"
              method="post"
              enctype="multipart/form-data"
              class="space-y-3">

          <input type="hidden" name="id" value="<?php echo $producto_edit['id'] ?? ''; ?>">

          <div>
            <label class="block text-sm font-medium">Código de barras</label>
            <input type="text" name="codigo_barras"
              value="<?php echo $producto_edit['codigo_barras'] ?? ''; ?>"
              class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" required
              value="<?php echo $producto_edit['nombre'] ?? ''; ?>"
              class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Descripción</label>
            <textarea name="descripcion" rows="2"
              class="w-full border rounded-lg px-3 py-1"><?php echo $producto_edit['descripcion'] ?? ''; ?></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium">Precio compra</label>
              <input type="number" step="0.01" name="precio_compra"
                value="<?php echo $producto_edit['precio_compra'] ?? '0'; ?>"
                class="w-full border rounded-lg px-3 py-1">
            </div>
            <div>
              <label class="block text-sm font-medium">Precio venta</label>
              <input type="number" step="0.01" name="precio_venta"
                value="<?php echo $producto_edit['precio_venta'] ?? '0'; ?>"
                class="w-full border rounded-lg px-3 py-1">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium">Stock</label>
            <input type="number" name="stock"
              value="<?php echo $producto_edit['stock'] ?? '0'; ?>"
              class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Categoría</label>
            <select name="id_categoria" class="w-full border rounded-lg px-3 py-1">
              <option value="">-- Selecciona --</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"
                  <?php echo (isset($producto_edit['id_categoria']) && $producto_edit['id_categoria'] == $cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium">Imagen</label>
            <input type="file" name="ruta_imagen" accept="image/*" class="w-full text-sm">
            <?php if (!empty($producto_edit['ruta_imagen'])): ?>
              <p class="text-xs text-gray-500 mt-1">
                Imagen actual: <?php echo htmlspecialchars($producto_edit['ruta_imagen']); ?>
              </p>
            <?php endif; ?>
          </div>

          <button type="submit"
            class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded-lg font-semibold">
            Guardar
          </button>

        </form>
      </div>
    </div>
  </div>

  <!-- LISTADO -->
  <div class="lg:col-span-8">
    <div class="bg-white rounded-xl shadow">

      <div class="px-4 py-3 border-b font-semibold flex items-center justify-between">
        <span>Listado de productos</span>

        <!-- BOTONES DE FILTRO -->
        <div class="flex gap-2 text-sm">
          <a href="productos.php?filtro=activos"
             class="px-3 py-1 rounded border <?php echo $filtro==='activos' ? 'bg-green-700 text-white' : ''; ?>">
            Activos
          </a>

          <?php if ($esAdmin): ?>
            <a href="productos.php?filtro=eliminados"
               class="px-3 py-1 rounded border <?php echo $filtro==='eliminados' ? 'bg-red-600 text-white' : ''; ?>">
              Eliminados
            </a>

            <a href="productos.php?filtro=todos"
               class="px-3 py-1 rounded border <?php echo $filtro==='todos' ? 'bg-gray-700 text-white' : ''; ?>">
              Todos
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2">#</th>
              <th class="px-3 py-2">Imagen</th>
              <th class="px-3 py-2">Nombre</th>
              <th class="px-3 py-2">Categoría</th>
              <th class="px-3 py-2">Precio</th>
              <th class="px-3 py-2">Stock</th>
              <th class="px-3 py-2"></th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($productos as $p): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-3 py-2"><?php echo $p['id']; ?></td>
                <td class="px-3 py-2">
                  <?php if (!empty($p['ruta_imagen'])): ?>
                    <img src="../public/imagenes/productos/<?php echo htmlspecialchars($p['ruta_imagen']); ?>"
                         class="w-12 h-12 object-cover rounded">
                  <?php else: ?>
                    <span class="text-gray-400 text-xs">Sin foto</span>
                  <?php endif; ?>
                </td>
                <td class="px-3 py-2"><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td class="px-3 py-2"><?php echo htmlspecialchars($p['categoria_nombre'] ?? ''); ?></td>
                <td class="px-3 py-2">$<?php echo number_format($p['precio_venta'], 2); ?></td>
                <td class="px-3 py-2"><?php echo $p['stock']; ?></td>

                <td class="px-3 py-2 space-x-1">
                  <?php if ($esAdmin && $p['estado'] == 1): ?>
                    <a href="productos.php?id=<?php echo $p['id']; ?>"
                       class="px-2 py-1 text-xs border rounded hover:bg-blue-600 hover:text-white">
                      Editar
                    </a>

                    <a href="../../backend/controllers/productos_eliminar.php?id=<?php echo $p['id']; ?>"
                       onclick="return confirm('¿Eliminar producto?');"
                       class="px-2 py-1 text-xs border rounded hover:bg-red-600 hover:text-white">
                      Eliminar
                    </a>
                  <?php endif; ?>

                  <?php if ($esAdmin && $p['estado'] == 0): ?>
                    <a href="../../backend/controllers/productos_restaurar.php?id=<?php echo $p['id']; ?>"
                       onclick="return confirm('¿Restaurar producto?');"
                       class="px-2 py-1 text-xs border rounded hover:bg-green-600 hover:text-white">
                      Restaurar
                    </a>
                  <?php endif; ?>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<?php include __DIR__ . '/../../views/layout/footer.php'; ?>
