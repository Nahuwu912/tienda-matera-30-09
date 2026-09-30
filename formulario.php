<?php
$conexion = new mysqli("localhost", "root", "", "sistema");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$resultado = $conexion->query("SELECT * FROM usuario");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Lista de Usuarios</h2>

    <div class="text-end mb-3">
        <a href="formulario.php" class="btn btn-success">➕ Agregar usuario</a>
    </div>

    <table class="table table-bordered table-striped text-center">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

        <?php
        if ($resultado->num_rows > 0) {
            while($fila = $resultado->fetch_assoc()){
                echo "<tr>";
                echo "<td>{$fila['nombre_completo']}</td>";
                echo "<td>{$fila['dni']}</td>";
                echo "<td>{$fila['telefono']}</td>";
                echo "<td>
                        <a href='editar.php?dni={$fila['dni']}' class='btn btn-warning btn-sm'>Editar</a>
                        <a href='eliminar.php?dni={$fila['dni']}' 
                           class='btn btn-danger btn-sm'
                           onclick='return confirm(\"¿Seguro?\")'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay datos</td></tr>";
        }
        ?>

        </tbody>
    </table>
</div>

</body>
</html>