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
// OBTENER COMPRAS
// =====================================================

$sql = "
    SELECT
        c.id,
        c.usuario_id,
        c.fecha,
        c.estado,
        c.fecha_confirmacion,
        u.nombre,
        u.email
    FROM compras c
    INNER JOIN usuarios u
        ON c.usuario_id = u.id
    ORDER BY c.fecha DESC
";


$resultado = $conexion->query($sql);

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
        Compras
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


        .usuario-link {
            color: #d89b6b;
            text-decoration: none;
            font-weight: bold;
        }


        .usuario-link:hover {
            color: #f0b98b;
            text-decoration: underline;
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
                href="productos.php"
                class="btn btn-cuero me-2"
            >
                📦 Administrar productos
            </a>


            <a
                href="index.php"
                class="btn btn-outline-light me-2"
            >
                Ver tienda
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


    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>
                🛒 Compras
            </h2>

            <p class="text-secondary mb-0">
                Administrá las compras realizadas por los clientes.
            </p>

        </div>

    </div>


    <?php if ($resultado && $resultado->num_rows > 0) { ?>


        <?php while ($compra = $resultado->fetch_assoc()) { ?>


            <div class="compra">


                <!-- =====================================
                     ENCABEZADO DE COMPRA
                ====================================== -->

                <div class="row">


                    <div class="col-md-6">

                        <h4>

                            Compra #

                            <?php
                            echo $compra['id'];
                            ?>

                        </h4>


                        <p class="mb-1">

                            👤 Cliente:

                            <a
                                href="historial_usuario.php?id=<?php echo $compra['usuario_id']; ?>"
                                class="usuario-link"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $compra['nombre']
                                );
                                ?>

                            </a>

                        </p>


                        <p class="mb-1">

                            📧

                            <?php
                            echo htmlspecialchars(
                                $compra['email']
                            );
                            ?>

                        </p>


                        <p class="mb-1">

                            📅 Compra realizada:

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

                            <p class="mb-0">

                                ✅ Confirmada:

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

                        <?php } ?>

                    </div>


                    <div class="col-md-6 text-md-end mt-3 mt-md-0">


                        <?php if (
                            $compra['estado'] == 'confirmada'
                        ) { ?>

                            <span class="badge bg-success fs-6">

                                ✓ Confirmada

                            </span>

                        <?php } else { ?>

                            <span class="badge bg-warning text-dark fs-6">

                                ⏳ Pendiente

                            </span>

                        <?php } ?>


                    </div>


                </div>


                <hr>


                <!-- =====================================
                     PRODUCTOS
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

                                Cantidad:

                                <?php
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


                <!-- =====================================
                     TOTAL
                ====================================== -->

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


                <!-- =====================================
                     CONFIRMAR COMPRA
                ====================================== -->

                <?php if (
                    $compra['estado'] == 'pendiente'
                ) { ?>

                    <div class="text-end mt-3">

                        <a
                            href="confirmar_compra.php?id=<?php echo $compra['id']; ?>"
                            class="btn btn-success"
                            onclick="return confirm('¿Seguro que querés confirmar esta compra? Se descontará el stock de los productos.');"
                        >

                            ✓ Confirmar compra

                        </a>

                    </div>

                <?php } ?>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="card p-4 text-center">

            <h4>
                No hay compras registradas.
            </h4>

        </div>


    <?php } ?>


</div>


</body>

</html>


<?php

$conexion->close();

?>