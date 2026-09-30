<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

$nombre = $_POST['nombre_completo'];
$dni = $_POST['dni'];
$telefono = $_POST['telefono'];

$sql = "INSERT INTO usuario (nombre_completo, dni, telefono) 
        VALUES ('$nombre', '$dni', '$telefono')";

if ($conexion->query($sql) === TRUE) {
    echo "Usuario guardado correctamente";
} else {
    echo "Error: " . $conexion->error;
}
?>