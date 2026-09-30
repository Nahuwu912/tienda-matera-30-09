<?php

session_start();


/*
|--------------------------------------------------------------------------
| VERIFICAR USUARIO
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['id'])) {

    header("Location: login.php");
    exit();

}


if (
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] != 'cliente'
) {

    header("Location: index.php");
    exit();

}


/*
|--------------------------------------------------------------------------
| VERIFICAR DATOS RECIBIDOS
|--------------------------------------------------------------------------
*/

if (
    !isset($_GET['id']) ||
    !isset($_GET['accion'])
) {

    header("Location: carrito.php");
    exit();

}


$id_producto = intval($_GET['id']);

$accion = $_GET['accion'];


/*
|--------------------------------------------------------------------------
| VALIDAR ACCIÓN
|--------------------------------------------------------------------------
*/

if (
    $accion != 'sumar' &&
    $accion != 'restar'
) {

    header("Location: carrito.php");
    exit();

}


/*
|--------------------------------------------------------------------------
| CONEXIÓN
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| OBTENER CARRITO DEL USUARIO
|--------------------------------------------------------------------------
*/

$id_usuario = $_SESSION['id'];


$stmt = $conexion->prepare(
    "SELECT id
     FROM carritos
     WHERE usuario_id = ?"
);


$stmt->bind_param(
    "i",
    $id_usuario
);


$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows == 0) {

    $stmt->close();
    $conexion->close();

    header("Location: carrito.php");
    exit();

}


$carrito = $resultado->fetch_assoc();

$id_carrito = $carrito['id'];

$stmt->close();


/*
|--------------------------------------------------------------------------
| OBTENER PRODUCTO DEL CARRITO
|--------------------------------------------------------------------------
*/

$stmt = $conexion->prepare(
    "SELECT
        ci.id,
        ci.cantidad,
        p.stock
     FROM carrito_items ci
     INNER JOIN productos p
        ON ci.producto_id = p.id
     WHERE ci.carrito_id = ?
     AND ci.producto_id = ?"
);


$stmt->bind_param(
    "ii",
    $id_carrito,
    $id_producto
);


$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows == 0) {

    $stmt->close();
    $conexion->close();

    header("Location: carrito.php");
    exit();

}


$item = $resultado->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| CALCULAR NUEVA CANTIDAD
|--------------------------------------------------------------------------
*/

$cantidad_actual = intval(
    $item['cantidad']
);

$stock = intval(
    $item['stock']
);


if ($accion == 'sumar') {

    $nueva_cantidad =
        $cantidad_actual + 1;


    /*
    | No permitir superar el stock
    */

    if ($nueva_cantidad > $stock) {

        $nueva_cantidad = $stock;

    }

} else {

    $nueva_cantidad =
        $cantidad_actual - 1;


    /*
    | Mínimo 1 unidad
    */

    if ($nueva_cantidad < 1) {

        $nueva_cantidad = 1;

    }

}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR CANTIDAD
|--------------------------------------------------------------------------
*/

$stmt = $conexion->prepare(
    "UPDATE carrito_items
     SET cantidad = ?
     WHERE id = ?
     AND carrito_id = ?"
);


$stmt->bind_param(
    "iii",
    $nueva_cantidad,
    $item['id'],
    $id_carrito
);


$stmt->execute();

$stmt->close();

$conexion->close();


/*
|--------------------------------------------------------------------------
| VOLVER AL CARRITO
|--------------------------------------------------------------------------
*/

header("Location: carrito.php");
exit();

?>
```
