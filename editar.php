<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

$dni = $_GET['dni'];
$sql = "SELECT * FROM usuario WHERE dni='$dni'";
$resultado = $conexion->query($sql);
$fila = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4">Editar Usuario</h3>

        <form method="POST" action="actualizar.php">
            <input type="hidden" name="dni_original" value="<?php echo $fila['dni']; ?>">

            <input type="text" name="nombre_completo" class="form-control mb-3" value="<?php echo $fila['nombre_completo']; ?>" required>
            <input type="text" name="dni" class="form-control mb-3" value="<?php echo $fila['dni']; ?>" required>
            <input type="text" name="telefono" class="form-control mb-3" value="<?php echo $fila['telefono']; ?>" required>

            <button class="btn btn-warning w-100">Actualizar</button>
        </form>

        <a href="index.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
</div>

</body>
</html>