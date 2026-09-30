```php
<?php
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar que sea cliente
if ($_SESSION['rol'] != 'cliente') {
    header("Location: tienda.php");
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "sistema"
);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener ID del producto
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_producto = intval($_GET['id']);

// Buscar producto
$stmt = $conexion->prepare(
    "SELECT id, nombre, precio, stock 
     FROM productos 
     WHERE id = ?"
);

$stmt->bind_param("i", $id_producto);
$stmt->execute();

$resultado = $stmt->get_result();

// Verificar que exista el producto
if ($resultado->num_rows == 0) {
    $stmt->close();
    $conexion->close();

    header("Location: index.php");
    exit();
}

$producto = $resultado->fetch_assoc();

$stmt->close();

// Verificar que haya stock
if ($producto['stock'] <= 0) {
    $conexion->close();

    header("Location: index.php");
    exit();
}


// =====================================================
// BUSCAR SI EL USUARIO YA TIENE UN CARRITO
// =====================================================

$id_usuario = $_SESSION['id'];

$stmt = $conexion->prepare(
    "SELECT id 
     FROM carritos 
     WHERE usuario_id = ?"
);

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();


// =====================================================
// SI NO TIENE CARRITO, CREARLO
// =====================================================

if ($resultado->num_rows == 0) {

    $stmt->close();

    $stmt = $conexion->prepare(
        "INSERT INTO carritos (usuario_id)
         VALUES (?)"
    );

    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    $id_carrito = $conexion->insert_id;

} else {

    $carrito = $resultado->fetch_assoc();

    $id_carrito = $carrito['id'];

}

$stmt->close();


// =====================================================
// BUSCAR SI EL PRODUCTO YA ESTÁ EN EL CARRITO
// =====================================================

$stmt = $conexion->prepare(
    "SELECT id, cantidad
     FROM carrito_items
     WHERE carrito_id = ?
     AND producto_id = ?"
);

$stmt->bind_param(
    "ii",
    $id_carrito,
    $id_producto
);

$stmt->execute();

$resultado = $stmt->get_result();


// =====================================================
// SI YA EXISTE → AUMENTAR CANTIDAD
// =====================================================

if ($resultado->num_rows > 0) {

    $item = $resultado->fetch_assoc();

    $nueva_cantidad = $item['cantidad'] + 1;

    // No permitir superar el stock
    if ($nueva_cantidad <= $producto['stock']) {

        $stmt->close();

        $stmt = $conexion->prepare(
            "UPDATE carrito_items
             SET cantidad = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ii",
            $nueva_cantidad,
            $item['id']
        );

        $stmt->execute();

    }

} else {

    // =================================================
    // SI NO EXISTE → AGREGAR PRODUCTO
    // =================================================

    $stmt->close();

    $cantidad = 1;

    $stmt = $conexion->prepare(
        "INSERT INTO carrito_items
        (carrito_id, producto_id, cantidad)
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iii",
        $id_carrito,
        $id_producto,
        $cantidad
    );

    $stmt->execute();
}

$stmt->close();
$conexion->close();


// Volver a la tienda
header("Location: index.php");
exit();

?>
```
