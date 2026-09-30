<?php

session_start();

include("conexion.php");

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios
WHERE email='$email'";

$resultado = $conexion->query($sql);

if($resultado->num_rows > 0){

    $usuario = $resultado->fetch_assoc();

    if(password_verify(
        $password,
        $usuario['password']
    )){

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        if($usuario['rol']=="admin"){

            header("Location: productos.php");

        }else{

            header("Location: index.php");

        }

        exit();

    }
}

echo "Usuario o contraseña incorrectos";
?>