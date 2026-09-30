<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

if ($conexion->connect_error) {
    die("Error: " . $conexion->connect_error);
}
?>