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
   COMPROBAR DATOS
========================================================= */

if (
    !isset($_GET['id']) ||
    !isset($_GET['rol'])
) {
    header("Location: usuarios.php");
    exit();
}


$id_usuario = intval($_GET['id']);

$nuevo_rol = strtolower(
    trim($_GET['rol'])
);


/* =========================================================
   VALIDAR ROL
========================================================= */

if (
    $nuevo_rol != 'admin' &&
    $nuevo_rol != 'cliente'
) {
    header("Location: usuarios.php");
    exit();
}


/* =========================================================
   EVITAR CAMBIAR EL PROPIO ROL
========================================================= */

if ($id_usuario == $_SESSION['id']) {

    header("Location: usuarios.php");
    exit();

}


/* =========================================================
   CONEXIÓN
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
   CAMBIAR ROL
========================================================= */

$stmt = $conexion->prepare(
    "UPDATE usuarios
     SET rol = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "si",
    $nuevo_rol,
    $id_usuario
);

$stmt->execute();


/* =========================================================
   FINALIZAR
========================================================= */

$stmt->close();

$conexion->close();


header("Location: usuarios.php");

exit();

?>
```
