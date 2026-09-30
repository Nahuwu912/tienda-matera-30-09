<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$dni_original = $_POST['dni_original'];
$nombre = $_POST['nombre_completo'];
$dni = $_POST['dni'];
$telefono = $_POST['telefono'];

$sql = "UPDATE usuario 
        SET nombre_completo='$nombre', dni='$dni', telefono='$telefono' 
        WHERE dni='$dni_original'";

if ($conexion->query($sql) === TRUE) {
    echo "Usuario actualizado correctamente";
} else {
    echo "Error: " . $conexion->error;
}

$conexion->close();
?>