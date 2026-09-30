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
   DATOS
========================================================= */

if (
    !isset($_POST['nombre']) ||
    !isset($_POST['categoria']) ||
    !isset($_POST['precio']) ||
    !isset($_POST['stock'])
) {
    header("Location: agregar_producto.php");
    exit();
}

$nombre = trim($_POST['nombre']);
$categoria = trim($_POST['categoria']);
$precio = floatval($_POST['precio']);
$stock = intval($_POST['stock']);

$imagen = "";


/* =========================================================
   SUBIR IMAGEN
========================================================= */

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] == 0
) {

    $carpeta = "uploads/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombre_original = basename(
        $_FILES['imagen']['name']
    );

    $extension = pathinfo(
        $nombre_original,
        PATHINFO_EXTENSION
    );

    $imagen = uniqid() . "." . $extension;

    move_uploaded_file(
        $_FILES['imagen']['tmp_name'],
        $carpeta . $imagen
    );
}


/* =========================================================
   INSERTAR PRODUCTO
========================================================= */

$stmt = $conexion->prepare(
    "INSERT INTO productos
    (nombre, precio, stock, imagen, categoria)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sdiss",
    $nombre,
    $precio,
    $stock,
    $imagen,
    $categoria
);

$stmt->execute();

$stmt->close();
$conexion->close();

header("Location: productos.php");
exit();

?>
```
