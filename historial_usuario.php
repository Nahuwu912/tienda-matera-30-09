<?php

session_start();

include("conexion.php");


// =====================================================
// VERIFICAR SESIÓN
// =====================================================

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}


// =====================================================
// VERIFICAR QUE SEA ADMINISTRADOR
// =====================================================

if (
    !isset($_SESSION['rol']) ||
    strtolower(trim($_SESSION['rol'])) != 'admin'
) {
    header("Location: index.php");
    exit();
}


// =====================================================
// VERIFICAR ID DEL USUARIO
// =====================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: compras.php");
    exit();
}


$usuario_id = intval($_GET['id']);


// =====================================================
// OBTENER DATOS DEL USUARIO
// =====================================================

$sql_usuario = "
    SELECT
        id,
        nombre,
        email
    FROM usuarios
    WHERE id = ?
    LIMIT 1
";


$stmt_usuario = $conexion->prepare($sql_usuario);

$stmt_usuario->bind_param(
    "i",
    $usuario_id
);

$stmt_usuario->execute();

$resultado_usuario = $stmt_usuario->get_result();


if ($resultado_usuario->num_rows == 0) {
    header("Location: compras.php");
    exit();
}


$usuario = $resultado_usuario->fetch_assoc();

$stmt_usuario->close();


// =====================================================
// OBTENER COMPRAS DEL USUARIO
// =====================================================

$sql_compras = "
    SELECT
        id,
        fecha,
        estado,
        fecha_confirmacion
    FROM compras
    WHERE usuario_id = ?
    ORDER BY fecha DESC
";


$stmt_compras = $conexion->prepare($sql_compras);

$stmt_compras->bind_param(
    "i",
    $usuario_id
);

$stmt_compras->execute();

$resultado_compras = $stmt_compras->get_result();

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Historial de <?php echo htmlspecialchars($usuario['nombre']); ?>
    </title>


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

        .card {
            background-color: #242424;
            border: 1px solid #444;
        }

        .compra {
            border: 1px solid #444;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: #1e1e1e;
        }

        .producto {
            border-bottom: 1px solid #444;
            padding: 10px 0;
        }

        .producto:last-child {
            border-bottom: none;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
        }

        .btn-cuero {
            background-color: #8b5e3c;
            color: white;
        }

        .btn-cuero:hover {
            background-color: #6f482d;
            color: white;
        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-dark mb-4">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand"
        >
            🧉 Tienda Matera
        </a>


        <div>

            <a
                href="compras.php"
                class="btn btn-outline-light me-2"
            >
                ← Volver a compras
            </a>


            <a
                href="logout.php"
                class="btn btn-danger"
            >
                Cerrar sesión
            </a>

        </div>

    </div>

</nav>


<!-- =====================================================
     CONTENIDO
===================================================== -->

<div class="container">


    <div class="card p-4 mb-4">

        <h2>
            👤 <?php
            echo htmlspecialchars(
                $usuario['nombre']
            );
            ?>
        </h2>


        <p class="mb-1">

            📧
            <?php
            echo htmlspecialchars(
                $usuario['email']
            );
            ?>

        </p>


        <p class="mb-0">

            🆔 Usuario #<?php
            echo $usuario['id'];
            ?>

        </p>

    </div>


    <h3 class="mb-4">
        🛒 Historial de compras
    </h3>


    <?php if ($resultado_compras->num_rows > 0) { ?>


        <?php while ($compra = $resultado_compras->fetch_assoc()) { ?>


            <div class="compra">


                <!-- =====================================
                     INFORMACIÓN DE LA COMPRA
                ====================================== -->

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <h4>
                            Compra #<?php
                            echo $compra['id'];
                            ?>
                        </h4>


                        <p class="mb-1">

                            📅 Realizada el:

                            <?php
                            echo date(
                                "d/m/Y H:i:s",
                                strtotime(
                                    $compra['fecha']
                                )
                            );
                            ?>

                        </p>


                        <?php if (
                            $compra['estado'] == 'confirmada'
                            &&
                            !empty(
                                $compra['fecha_confirmacion']
                            )
                        ) { ?>

                            <p class="mb-1">

                                ✅ Confirmada el:

                                <strong>

                                    <?php
                                    echo date(
                                        "d/m/Y",
                                        strtotime(
                                            $compra['fecha_confirmacion']
                                        )
                                    );
                                    ?>

                                    a las

                                    <?php
                                    echo date(
                                        "H:i:s",
                                        strtotime(
                                            $compra['fecha_confirmacion']
                                        )
                                    );
                                    ?>

                                </strong>

                            </p>

                        <?php } else { ?>

                            <p class="mb-1">

                                ⏳

                                <strong>
                                    Todavía no confirmada
                                </strong>

                            </p>

                        <?php } ?>

                    </div>


                    <?php if (
                        $compra['estado'] == 'confirmada'
                    ) { ?>

                        <span class="badge bg-success fs-6">
                            Confirmada
                        </span>

                    <?php } else { ?>

                        <span class="badge bg-warning text-dark fs-6">
                            Pendiente
                        </span>

                    <?php } ?>

                </div>


                <hr>


                <!-- =====================================
                     PRODUCTOS DE LA COMPRA
                ====================================== -->

                <h5 class="mb-3">
                    📦 Productos
                </h5>


                <?php

                $sql_items = "
                    SELECT
                        ci.cantidad,
                        ci.precio,
                        p.nombre
                    FROM compra_items ci
                    INNER JOIN productos p
                        ON ci.producto_id = p.id
                    WHERE ci.compra_id = ?
                ";


                $stmt_items = $conexion->prepare(
                    $sql_items
                );


                $stmt_items->bind_param(
                    "i",
                    $compra['id']
                );


                $stmt_items->execute();


                $resultado_items =
                    $stmt_items->get_result();


                $total = 0;

                ?>


                <?php while ($item = $resultado_items->fetch_assoc()) { ?>


                    <?php

                    $subtotal =
                        $item['precio']
                        *
                        $item['cantidad'];

                    $total += $subtotal;

                    ?>


                    <div class="producto">


                        <div class="row align-items-center">

                            <div class="col-md-6">

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $item['nombre']
                                    );
                                    ?>

                                </strong>

                            </div>


                            <div class="col-md-2">

                                x<?php
                                echo $item['cantidad'];
                                ?>

                            </div>


                            <div class="col-md-2">

                                $<?php
                                echo number_format(
                                    $item['precio'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>

                            </div>


                            <div class="col-md-2 text-end">

                                <strong>

                                    $<?php
                                    echo number_format(
                                        $subtotal,
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>

                                </strong>

                            </div>

                        </div>

                    </div>


                <?php } ?>


                <?php

                $stmt_items->close();

                ?>


                <div class="text-end mt-3 total">

                    TOTAL:

                    $<?php
                    echo number_format(
                        $total,
                        2,
                        ',',
                        '.'
                    );
                    ?>

                </div>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="card p-4 text-center">

            <h4>
                Este usuario todavía no realizó compras.
            </h4>

        </div>


    <?php } ?>


</div>


</body>

</html>


<?php

$stmt_compras->close();

$conexion->close();

?>