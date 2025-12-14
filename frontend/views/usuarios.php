<?php
require_once __DIR__ . '/../../backend/controllers/auth.php';

if (($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: pos.php");
    exit;
}

require_once __DIR__ . '/../../backend/controllers/config.php';
include __DIR__ . '/../../views/layout/header.php';

// Obtener usuarios
$usuarios = $pdo->query("SELECT id, nombre, email, rol, creado_en FROM users ORDER BY nombre ASC")
                ->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

  <!-- FORMULARIO -->
  <div class="lg:col-span-4">
    <div class="bg-white rounded-xl shadow">
      <div class="bg-green-700 text-white px-4 py-3 rounded-t-xl font-semibold">
        Nuevo usuario
      </div>

      <div class="p-4">
        <form action="../../backend/controllers/usuarios_guardar.php" method="post" class="space-y-3">

          <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" required class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Correo</label>
            <input type="email" name="email" required class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Contraseña</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-1">
          </div>

          <div>
            <label class="block text-sm font-medium">Rol</label>
            <select name="rol" class="w-full border rounded-lg px-3 py-1">
              <option value="cajero">Cajero</option>
              <option value="admin">Administrador</option>
            </select>
          </div>

          <button type="submit"
            class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded-lg font-semibold">
            Crear usuario
          </button>

        </form>
      </div>
    </div>
  </div>

  <!-- LISTADO -->
  <div class="lg:col-span-8">
    <div class="bg-white rounded-xl shadow">
      <div class="px-4 py-3 border-b font-semibold">
        Usuarios registrados
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2">#</th>
              <th class="px-3 py-2">Nombre</th>
              <th class="px-3 py-2">Correo</th>
              <th class="px-3 py-2">Rol</th>
              <th class="px-3 py-2">Creado</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td class="px-3 py-2"><?php echo $u['id']; ?></td>
                <td class="px-3 py-2"><?php echo htmlspecialchars($u['nombre']); ?></td>
                <td class="px-3 py-2"><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="px-3 py-2 font-semibold"><?php echo $u['rol']; ?></td>
                <td class="px-3 py-2"><?php echo $u['creado_en']; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../../views/layout/footer.php'; ?>
