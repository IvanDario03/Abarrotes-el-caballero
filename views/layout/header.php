<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abarrotes El Caballero</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<!-- NAVBAR -->
<nav class="bg-green-700 text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

    <!-- Logo / Título -->
    <a href="pos.php" class="text-lg font-bold flex items-center gap-2">
      <span>Abarrotes El Caballero</span>
    </a>

    <!-- Usuario / Acciones -->
    <?php if (!empty($_SESSION['user_name'])): ?>
      <div class="flex items-center gap-3 text-sm">

        <span class="hidden sm:block">
          <?php echo htmlspecialchars($_SESSION['user_name']); ?>
          <span class="opacity-80 text-xs">
            (<?php echo htmlspecialchars($_SESSION['rol'] ?? ''); ?>)
          </span>
        </span>

        <!-- BOTÓN POS -->
        <a href="pos.php"
           class="px-3 py-1 border border-white rounded hover:bg-white hover:text-green-700 transition">
          Menú principal
        </a>

        <!-- BOTÓN PRODUCTOS (SOLO ADMIN) -->
        <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
          <a href="productos.php"
             class="px-3 py-1 border border-white rounded hover:bg-white hover:text-green-700 transition">
            Productos
          </a>
        <?php endif; ?>

        <!-- BOTÓN USUARIOS (SOLO ADMIN) -->
        <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
          <a href="usuarios.php"
             class="px-3 py-1 border border-white rounded hover:bg-white hover:text-green-700 transition">
            Usuarios
          </a>
        <?php endif; ?>

        <!-- BOTÓN SALIR -->
        <a href="logout.php"
           class="px-3 py-1 border border-white rounded hover:bg-red-600 hover:border-red-600 transition">
          Salir
        </a>
      </div>
    <?php endif; ?>

  </div>
</nav>

<!-- CONTENEDOR PRINCIPAL -->
<div class="max-w-7xl mx-auto px-4 py-6">


