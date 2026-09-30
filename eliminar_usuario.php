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
   COMPROBAR ID
========================================================= */

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}


$id_usuario = intval($_GET['id']);


/* =========================================================
   EVITAR QUE EL ADMIN SE ELIMINE A SÍ MISMO
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
   ELIMINAR USUARIO
========================================================= */

$stmt = $conexion->prepare(
    "DELETE FROM usuarios
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
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
