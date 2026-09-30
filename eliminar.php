```php
<?php

session_start();

/* =========================================================
   PROTECCIÓN: SOLO ADMIN
========================================================= */

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (
    !isset($_SESSION['rol']) ||
    strtolower(trim($_SESSION['rol'])) != 'admin'
) {
    header("Location: index.php");
    exit();
}


/* =========================================================
   CONEXIÓN
========================================================= */

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "sistema"
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");


/* =========================================================
   ID
========================================================= */

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id = intval($_GET['id']);


/* =========================================================
   BUSCAR IMAGEN
========================================================= */

$stmt = $conexion->prepare(
    "SELECT imagen
     FROM productos
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    $stmt->close();
    $conexion->close();

    header("Location: productos.php");
    exit();
}

$producto = $resultado->fetch_assoc();

$stmt->close();


/* =========================================================
   ELIMINAR PRODUCTO
========================================================= */

$stmt = $conexion->prepare(
    "DELETE FROM productos
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->close();


/* =========================================================
   ELIMINAR IMAGEN
========================================================= */

if (!empty($producto['imagen'])) {

    $ruta_imagen =
        "uploads/" . $producto['imagen'];

    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
    }
}


$conexion->close();


/* =========================================================
   VOLVER
========================================================= */

header("Location: productos.php");
exit();

?>
```
