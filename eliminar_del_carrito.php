```php
<?php
session_start();

// Verificar que haya sesión
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar que sea cliente
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}


// =====================================================
// VERIFICAR ID DEL PRODUCTO
// =====================================================

if (!isset($_GET['id'])) {
    header("Location: carrito.php");
    exit();
}

$id_producto = intval($_GET['id']);


// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "sistema"
);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


// =====================================================
// OBTENER EL CARRITO DEL USUARIO
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


// Si no existe carrito, volver
if ($resultado->num_rows == 0) {

    $stmt->close();
    $conexion->close();

    header("Location: carrito.php");
    exit();
}

$carrito = $resultado->fetch_assoc();

$id_carrito = $carrito['id'];

$stmt->close();


// =====================================================
// ELIMINAR EL PRODUCTO DEL CARRITO
// =====================================================

$stmt = $conexion->prepare(
    "DELETE FROM carrito_items
     WHERE carrito_id = ?
     AND producto_id = ?"
);

$stmt->bind_param(
    "ii",
    $id_carrito,
    $id_producto
);

$stmt->execute();

$stmt->close();
$conexion->close();


// =====================================================
// VOLVER AL CARRITO
// =====================================================

header("Location: carrito.php");
exit();

?>
```
