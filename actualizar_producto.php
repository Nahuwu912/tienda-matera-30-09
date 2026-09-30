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
   CONEXIÓN A LA BASE DE DATOS
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
   COMPROBAR DATOS RECIBIDOS
========================================================= */

if (
    !isset($_POST['id']) ||
    !isset($_POST['nombre']) ||
    !isset($_POST['categoria']) ||
    !isset($_POST['precio']) ||
    !isset($_POST['stock'])
) {
    header("Location: productos.php");
    exit();
}


$id = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$categoria = trim($_POST['categoria']);
$precio = floatval($_POST['precio']);
$stock = intval($_POST['stock']);


/* =========================================================
   BUSCAR IMAGEN ACTUAL
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

$imagen = $producto['imagen'];

$stmt->close();


/* =========================================================
   COMPROBAR SI SE SUBIÓ UNA NUEVA IMAGEN
========================================================= */

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] == 0
) {

    $carpeta = "uploads/";

    // Crear carpeta si no existe
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }


    // Obtener extensión
    $nombre_original = basename(
        $_FILES['imagen']['name']
    );

    $extension = pathinfo(
        $nombre_original,
        PATHINFO_EXTENSION
    );


    // Crear nombre único
    $nueva_imagen = uniqid() . "." . $extension;


    // Mover nueva imagen
    if (
        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            $carpeta . $nueva_imagen
        )
    ) {

        // Eliminar imagen anterior
        if (
            !empty($imagen) &&
            file_exists($carpeta . $imagen)
        ) {
            unlink($carpeta . $imagen);
        }

        $imagen = $nueva_imagen;
    }
}


/* =========================================================
   ACTUALIZAR PRODUCTO
========================================================= */

$stmt = $conexion->prepare(
    "UPDATE productos
     SET
        nombre = ?,
        categoria = ?,
        precio = ?,
        stock = ?,
        imagen = ?
     WHERE id = ?"
);


/*
   Tipos:
   s = nombre
   s = categoria
   d = precio
   i = stock
   s = imagen
   i = id
*/

$stmt->bind_param(
    "ssdisi",
    $nombre,
    $categoria,
    $precio,
    $stock,
    $imagen,
    $id
);


if (!$stmt->execute()) {

    die(
        "Error al actualizar el producto: " .
        $stmt->error
    );
}


$stmt->close();
$conexion->close();


/* =========================================================
   VOLVER A PRODUCTOS
========================================================= */

header("Location: productos.php");
exit();

?>
```
