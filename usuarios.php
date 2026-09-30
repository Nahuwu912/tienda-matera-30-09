<?php

session_start();

/* =========================================================
   MOSTRAR ERRORES PARA PODER DETECTAR PROBLEMAS
========================================================= */

error_reporting(E_ALL);
ini_set('display_errors', 1);


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
   CONEXIÓN A LA BASE DE DATOS
========================================================= */

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "sistema"
);

if ($conexion->connect_error) {
    die(
        "Error de conexión a la base de datos: " .
        $conexion->connect_error
    );
}

$conexion->set_charset("utf8");


/* =========================================================
   COMPROBAR QUE EXISTE LA TABLA USUARIOS
========================================================= */

$resultado = $conexion->query(
    "SELECT id, nombre, email, rol
     FROM usuarios
     ORDER BY id DESC"
);

if (!$resultado) {

    die(
        "Error al consultar la tabla usuarios: " .
        $conexion->error
    );

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Administrar usuarios - Tienda Matera</title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        body {
            background-color: #171717;
            color: white;
        }

        .navbar {
            background-color: #111;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card {
            background-color: #242424;
            border: 1px solid #444;
        }

        .table {
            color: white;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .btn-cuero {
            background-color: #8b5e3c;
            color: white;
            border: none;
        }

        .btn-cuero:hover {
            background-color: #6f482d;
            color: white;
        }

        .titulo {
            font-weight: bold;
        }

        .acciones {
            white-space: nowrap;
        }

    </style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-dark mb-4">

    <div class="container">


        <!-- LOGO -->

        <a
            href="index.php"
            class="navbar-brand"
        >

            🧉 Tienda Matera

        </a>


        <div class="d-flex align-items-center gap-2">


            <!-- USUARIO -->

            <span class="text-white me-2">

                👤

                <?php

                echo htmlspecialchars(
                    $_SESSION['nombre']
                );

                ?>

            </span>


            <!-- PRODUCTOS -->

            <a
                href="productos.php"
                class="btn btn-warning"
            >

                📦 Productos

            </a>


            <!-- TIENDA -->

            <a
                href="index.php"
                class="btn btn-outline-light"
            >

                🏠 Tienda

            </a>


            <!-- CERRAR SESIÓN -->

            <a
                href="logout.php"
                class="btn btn-danger"
            >

                Cerrar sesión

            </a>


        </div>

    </div>

</nav>



<!-- =========================================================
     CONTENIDO
========================================================= -->

<div class="container">


    <!-- TÍTULO -->

    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <h2 class="titulo">

            👥 Administrar usuarios

        </h2>


        <span class="badge bg-secondary fs-6">

            Total:

            <?php

            echo $resultado->num_rows;

            ?>

        </span>

    </div>



    <!-- TABLA -->

    <div class="card p-3">

        <div class="table-responsive">


            <table class="table table-dark table-hover">


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Rol
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if ($resultado->num_rows > 0) {

                    while (
                        $usuario =
                        $resultado->fetch_assoc()
                    ) {

                ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <?php

                            echo $usuario['id'];

                            ?>

                        </td>


                        <!-- NOMBRE -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $usuario['nombre']
                            );

                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $usuario['email']
                            );

                            ?>

                        </td>


                        <!-- ROL -->

                        <td>


                            <?php

                            $rol_usuario =
                                strtolower(
                                    trim(
                                        $usuario['rol']
                                    )
                                );


                            if (
                                $rol_usuario ==
                                'admin'
                            ) {

                            ?>

                                <span class="badge bg-danger">

                                    👑 ADMIN

                                </span>

                            <?php

                            } else {

                            ?>

                                <span class="badge bg-primary">

                                    👤 CLIENTE

                                </span>

                            <?php

                            }

                            ?>


                        </td>


                        <!-- ACCIONES -->

                        <td class="acciones">


                        <?php

                        /*
                           No permitimos modificar
                           al propio administrador.
                        */

                        if (
                            $usuario['id'] !=
                            $_SESSION['id']
                        ) {

                        ?>


                            <!-- CAMBIAR A ADMIN -->

                            <?php

                            if (
                                $rol_usuario ==
                                'cliente'
                            ) {

                            ?>

                                <a
                                    href="cambiar_rol.php?id=<?php echo $usuario['id']; ?>&rol=admin"
                                    class="btn btn-warning btn-sm"
                                    onclick="return confirm('¿Seguro que querés convertir este usuario en administrador?');"
                                >

                                    👑 Hacer admin

                                </a>


                            <?php

                            }

                            ?>


                            <!-- CAMBIAR A CLIENTE -->

                            <?php

                            if (
                                $rol_usuario ==
                                'admin'
                            ) {

                            ?>

                                <a
                                    href="cambiar_rol.php?id=<?php echo $usuario['id']; ?>&rol=cliente"
                                    class="btn btn-primary btn-sm"
                                    onclick="return confirm('¿Seguro que querés convertir este administrador en cliente?');"
                                >

                                    👤 Hacer cliente

                                </a>


                            <?php

                            }

                            ?>


                            <!-- ELIMINAR -->

                            <a
                                href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Seguro que querés eliminar este usuario? Esta acción no se puede deshacer.');"
                            >

                                🗑️ Eliminar

                            </a>


                        <?php

                        } else {

                        ?>


                            <span class="badge bg-secondary">

                                Usuario actual

                            </span>


                        <?php

                        }

                        ?>


                        </td>


                    </tr>


                <?php

                    }

                } else {

                ?>


                    <tr>

                        <td
                            colspan="5"
                            class="text-center"
                        >

                            No hay usuarios registrados.

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>


            </table>


        </div>

    </div>


</div>


</body>

</html>


<?php

$conexion->close();

?>
```

