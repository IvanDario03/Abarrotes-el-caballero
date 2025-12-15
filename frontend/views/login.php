<?php
session_start();

$BASE_URL = "/Abarrotes-el-caballero";

if (!empty($_SESSION['user_id'])) {
    header("Location: $BASE_URL/frontend/views/pos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Abarrotes El Caballero</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

  <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">

    <!-- Header -->
    <div class="bg-green-700 text-white text-center py-5">
      <h1 class="text-xl font-bold">Abarrotes El Caballero</h1>
      <p class="text-sm opacity-90">Acceso al sistema </p>
    </div>

    <!-- Body -->
    <div class="p-6">

      <?php if (!empty($_GET['error'])): ?>
        <div class="mb-4 rounded bg-red-100 text-red-700 px-4 py-2 text-sm">
          Usuario o contraseña incorrectos.
        </div>
      <?php endif; ?>

      <!-- FORMULARIO -->
      <form action="<?= $BASE_URL ?>/backend/controllers/login_process.php" method="post"> 

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Correo
          </label>
          <input
            type="email"
            name="email"
            id="email"
            required
            value="admin@abarrotes.com"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Contraseña
          </label>
          <input
            type="password"
            name="password"
            id="password"
            required
            value="12345"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
          >
        </div>

        <button
          type="submit"
          class="w-full bg-green-700 hover:bg-green-800 text-white py-2 rounded-lg font-semibold transition"
        >
          Ingresar
        </button>

      </form>

    </div>

    <!-- Footer -->
    <div class="bg-gray-50 text-center text-xs text-gray-500 py-3">
      &copy; <?php echo date('Y'); ?> Abarrotes El Caballero. Todos los derechos reservados.
    </div>

  </div>

</body>
</html>
