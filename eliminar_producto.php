<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que llegue el id
if (isset($_GET['id']) && !empty($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "DELETE FROM productos WHERE id=$id";

    if ($conexion->query($sql) === TRUE) {
        // 🔁 REDIRECCIÓN
        header("Location: productos.php");
        exit();
    } else {
        echo "Error: " . $conexion->error;
    }

} else {
    echo "ID no válido";
}

$conexion->close();
?>