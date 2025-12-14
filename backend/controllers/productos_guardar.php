<?php
require __DIR__ . '/config.php';
require __DIR__ . '/auth.php';

$rol = $_SESSION['rol'] ?? '';
$id  = $_POST['id'] ?? '';

if ($id !== '' && $rol !== 'admin') {
    http_response_code(403);
    die("Acceso no autorizado: solo el administrador puede editar productos.");
}

try {

    //  Datos
    $codigo        = trim($_POST['codigo_barras'] ?? '');
    $nombre        = trim($_POST['nombre'] ?? '');
    $descripcion   = trim($_POST['descripcion'] ?? '');
    $precio_compra = floatval($_POST['precio_compra'] ?? 0);
    $precio_venta  = floatval($_POST['precio_venta'] ?? 0);
    $stock         = intval($_POST['stock'] ?? 0);
    $id_categoria  = $_POST['id_categoria'] !== '' ? intval($_POST['id_categoria']) : null;
    $ruta_imagen   = null;

    //  SUBIDA DE IMAGEN (ROBUSTA)
    if (
        isset($_FILES['ruta_imagen']) &&
        $_FILES['ruta_imagen']['error'] === UPLOAD_ERR_OK
    ) {
        $dir = __DIR__ . '/../../frontend/public/imagenes/productos/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Limpiar nombre
        $extension = pathinfo($_FILES['ruta_imagen']['name'], PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        // Validar extensión
        $ext_validas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $ext_validas)) {
            throw new Exception("Formato de imagen no permitido");
        }

        $nombreArchivo = uniqid('prod_') . '.' . $extension;
        $rutaDestino   = $dir . $nombreArchivo;

        if (!move_uploaded_file($_FILES['ruta_imagen']['tmp_name'], $rutaDestino)) {
            throw new Exception("No se pudo guardar la imagen");
        }

        $ruta_imagen = $nombreArchivo;
    }

    //  INSERT
    if ($id === '') {

        $sql = "INSERT INTO productos
                (codigo_barras, nombre, descripcion, precio_compra, precio_venta, stock, id_categoria, ruta_imagen, estado)
                VALUES
                (:codigo, :nombre, :descripcion, :pc, :pv, :stock, :id_categoria, :ruta_imagen, 1)";

        $stmt = $pdo->prepare($sql);

    } 
    //  UPDATE
    else {

        if ($ruta_imagen !== null) {
            $sql = "UPDATE productos SET
                        codigo_barras = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio_compra = :pc,
                        precio_venta = :pv,
                        stock = :stock,
                        id_categoria = :id_categoria,
                        ruta_imagen = :ruta_imagen
                    WHERE id = :id";
        } else {
            $sql = "UPDATE productos SET
                        codigo_barras = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio_compra = :pc,
                        precio_venta = :pv,
                        stock = :stock,
                        id_categoria = :id_categoria
                    WHERE id = :id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    }

    //  BINDS
    $stmt->bindValue(':codigo', $codigo);
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':descripcion', $descripcion);
    $stmt->bindValue(':pc', $precio_compra);
    $stmt->bindValue(':pv', $precio_venta);
    $stmt->bindValue(':stock', $stock);

    if ($id_categoria === null) {
        $stmt->bindValue(':id_categoria', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':id_categoria', $id_categoria, PDO::PARAM_INT);
    }

    if (strpos($sql, ':ruta_imagen') !== false) {
        $stmt->bindValue(':ruta_imagen', $ruta_imagen);
    }

    $stmt->execute();

    header("Location: ../../frontend/views/productos.php?ok=1");
    exit;

} catch (Exception $e) {
    echo "<h2>Error al guardar producto</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}
