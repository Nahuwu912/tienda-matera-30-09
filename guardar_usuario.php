```php
<?php

include("conexion.php");

// =====================================================
// RECIBIR DATOS DEL FORMULARIO
// =====================================================

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$localidad = trim($_POST['localidad'] ?? '');
$password = $_POST['password'] ?? '';


// =====================================================
// VERIFICAR QUE TODOS LOS CAMPOS ESTÉN COMPLETOS
// =====================================================

if (
    empty($nombre) ||
    empty($email) ||
    empty($direccion) ||
    empty($codigo_postal) ||
    empty($localidad) ||
    empty($password)
) {
    echo "Error: Todos los campos son obligatorios.";
    exit();
}


// =====================================================
// VERIFICAR SI EL EMAIL YA EXISTE
// =====================================================

$consulta = $conexion->prepare(
    "SELECT id FROM usuarios WHERE email = ?"
);

$consulta->bind_param("s", $email);
$consulta->execute();

$resultado = $consulta->get_result();

if ($resultado->num_rows > 0) {

    echo "Error: Este correo electrónico ya está registrado.";
    exit();

}

$consulta->close();


// =====================================================
// ENCRIPTAR CONTRASEÑA
// =====================================================

$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// =====================================================
// REGISTRAR USUARIO
// =====================================================

$sql = $conexion->prepare(
    "INSERT INTO usuarios
    (nombre, email, direccion, codigo_postal, localidad, password)
    VALUES (?, ?, ?, ?, ?, ?)"
);

$sql->bind_param(
    "ssssss",
    $nombre,
    $email,
    $direccion,
    $codigo_postal,
    $localidad,
    $password_hash
);


// =====================================================
// EJECUTAR REGISTRO
// =====================================================

if ($sql->execute()) {

    header("Location: login.php");
    exit();

} else {

    echo "Error al registrar el usuario: " . $conexion->error;

}


$sql->close();
$conexion->close();

?>
```
