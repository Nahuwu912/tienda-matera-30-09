<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel</title>
</head>
<body>

<h2>Bienvenido <?php echo $_SESSION['usuario']; ?></h2>

<a href="index.php">Ver usuarios</a><br><br>
<a href="logout.php">Cerrar sesión</a>

</body>
</html>