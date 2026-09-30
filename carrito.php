```php
<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}


// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

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


// =====================================================
// OBTENER EL CARRITO DEL USUARIO
// =====================================================

$id_usuario = $_SESSION['id'];

$stmt = $conexion->prepare(
    "SELECT id
     FROM carritos
     WHERE usuario_id = ?"
);

$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado_carrito = $stmt->get_result();

$id_carrito = null;

if ($resultado_carrito->num_rows > 0) {

    $carrito_db = $resultado_carrito->fetch_assoc();
    $id_carrito = $carrito_db['id'];

}

$stmt->close();


// =====================================================
// OBTENER LOS PRODUCTOS DEL CARRITO
// =====================================================

$productos_carrito = [];
$total = 0;

if ($id_carrito !== null) {

    $stmt = $conexion->prepare(
        "SELECT
            carrito_items.id AS item_id,
            carrito_items.producto_id,
            carrito_items.cantidad,
            productos.nombre,
            productos.precio,
            productos.imagen,
            productos.stock
         FROM carrito_items
         INNER JOIN productos
            ON carrito_items.producto_id = productos.id
         WHERE carrito_items.carrito_id = ?
         ORDER BY carrito_items.id DESC"
    );

    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();

    $resultado_items = $stmt->get_result();

    while ($producto = $resultado_items->fetch_assoc()) {

        $subtotal =
            $producto['precio'] *
            $producto['cantidad'];

        $total += $subtotal;

        $productos_carrito[] = $producto;
    }

    $stmt->close();
}

$conexion->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mi Carrito - Tienda Matera</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Google Fonts -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121214;
            color: #e4e4e7;
        }


        .top-bar {
            background-color: #18181b;
            color: #a1a1aa;
            font-size: 13px;
            border-bottom: 1px solid #27272a;
        }


        .top-bar a {
            color: #d4d4d8;
            text-decoration: none;
            transition: color 0.2s;
        }


        .top-bar a:hover {
            color: #ffffff;
            text-decoration: underline;
        }


        .card-dark {
            background-color: #18181b;
            border: 1px solid #27272a !important;
            color: #e4e4e7;
        }


        .header-dark {
            background-color: #18181b;
            border-bottom: 1px solid #27272a;
        }


        .btn-virtue {
            background-color: #b86b35;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 4px;
            padding: 12px 20px;
            border: none;
            transition: all 0.2s;
        }


        .btn-virtue:hover {
            background-color: #9d5a2b;
            color: #ffffff;
        }


        .btn-outline-dark-custom {
            color: #a1a1aa;
            border: 1px solid #27272a;
            background-color: transparent;
            transition: all 0.2s;
        }


        .btn-outline-dark-custom:hover {
            background-color: #27272a;
            color: #ffffff;
            border-color: #3f3f46;
        }


        .img-carrito {
            width: 60px;
            height: 60px;
            object-fit: contain;
            background-color: #121214;
            border-radius: 6px;
            padding: 4px;
            border: 1px solid #27272a;
        }


        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-color: #e4e4e7;
            --bs-table-border-color: #27272a;
        }


        .table-dark-custom thead {
            background-color: #121214;
            color: #a1a1aa;
        }


        .badge-dark-custom {
            background-color: #121214;
            color: #a1a1aa;
            border: 1px solid #27272a;
        }


        .text-muted-custom {
            color: #a1a1aa !important;
        }


        .border-dark-custom {
            border-color: #27272a !important;
        }


        /* =====================================================
           CONTROLES DE CANTIDAD
        ===================================================== */

        .cantidad-control {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #3f3f46;
            border-radius: 5px;
            overflow: hidden;
            background-color: #121214;
        }


        .btn-cantidad {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: #27272a;
            color: #ffffff;
            font-size: 18px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }


        .btn-cantidad:hover {
            background-color: #3f3f46;
            color: #ffffff;
        }


        .btn-cantidad.disabled {
            color: #52525b;
            background-color: #18181b;
            cursor: not-allowed;
            pointer-events: none;
        }


        .cantidad-numero {
            min-width: 40px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            background-color: #18181b;
        }


        .stock-info {
            font-size: 10px;
            color: #71717a;
            margin-top: 5px;
        }

    </style>

</head>


<body>


    <!-- =====================================================
         BARRA SUPERIOR
    ====================================================== -->

    <div class="top-bar py-2 px-4">

        <div class="container d-flex justify-content-between align-items-center">

            <div>

                <span>

                    Hola,

                    <strong class="text-white">

                        <?php
                        echo htmlspecialchars(
                            $_SESSION['nombre'] ?? 'Cliente'
                        );
                        ?>

                    </strong>

                </span>

            </div>


            <div>

                <a
                    href="index.php"
                    class="me-3"
                >
                    ⬅ Volver a la tienda
                </a>


                <a
                    href="logout.php"
                    class="text-danger fw-semibold"
                >
                    Cerrar sesión
                </a>

            </div>

        </div>

    </div>


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <header class="header-dark py-4 mb-4">

        <div class="container text-center">

            <h1
                class="fw-bold m-0 text-white"
                style="letter-spacing: -1px;"
            >
                🛒 MI CARRITO
            </h1>

        </div>

    </header>


    <div class="container mb-5">


        <?php if (empty($productos_carrito)) { ?>


            <!-- =================================================
                 CARRITO VACÍO
            ================================================== -->

            <div
                class="text-center py-5 card-dark rounded shadow-sm p-4 my-4"
            >

                <div class="fs-1 mb-3">
                    🧉
                </div>


                <h4 class="fw-bold text-white mb-2">

                    No hay productos en el carrito

                </h4>


                <p class="text-muted-custom mb-4">

                    Elegí tus mates y accesorios favoritos
                    para empezar a comprar.

                </p>


                <a
                    href="index.php"
                    class="btn btn-virtue"
                >
                    Volver a la tienda
                </a>

            </div>


        <?php } else { ?>


            <div class="row g-4">


                <!-- =================================================
                     LISTADO DE PRODUCTOS
                ================================================== -->

                <div class="col-lg-8">

                    <div
                        class="card-dark rounded shadow-sm p-3 p-md-4"
                    >

                        <div class="table-responsive">

                            <table
                                class="table table-dark-custom align-middle mb-0"
                            >

                                <thead>

                                    <tr
                                        class="text-uppercase text-muted-custom"
                                        style="
                                            font-size: 11px;
                                            letter-spacing: 0.5px;
                                        "
                                    >

                                        <th>
                                            Producto
                                        </th>

                                        <th class="text-center">
                                            Precio
                                        </th>

                                        <th class="text-center">
                                            Cantidad
                                        </th>

                                        <th class="text-end">
                                            Subtotal
                                        </th>

                                        <th class="text-center">
                                            Acción
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    <?php foreach ($productos_carrito as $producto) { ?>


                                        <tr>


                                            <!-- PRODUCTO -->

                                            <td>

                                                <div
                                                    class="d-flex align-items-center"
                                                >


                                                    <img
                                                        src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>"
                                                        class="img-carrito me-3"
                                                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                    >


                                                    <div>

                                                        <h6
                                                            class="mb-0 fw-bold text-white"
                                                            style="font-size: 14px;"
                                                        >

                                                            <?php
                                                            echo htmlspecialchars(
                                                                $producto['nombre']
                                                            );
                                                            ?>

                                                        </h6>

                                                    </div>

                                                </div>

                                            </td>


                                            <!-- PRECIO -->

                                            <td
                                                class="text-center fw-semibold text-light"
                                                style="font-size: 14px;"
                                            >

                                                $<?php
                                                echo number_format(
                                                    $producto['precio'],
                                                    0,
                                                    ",",
                                                    "."
                                                );
                                                ?>

                                            </td>


                                            <!-- CANTIDAD -->

                                            <td class="text-center">

                                                <div
                                                    class="cantidad-control"
                                                >


                                                    <!-- RESTAR -->

                                                    <?php if ($producto['cantidad'] > 1) { ?>

                                                        <a
                                                            href="actualizar_cantidad.php?id=<?php echo $producto['producto_id']; ?>&accion=restar"
                                                            class="btn-cantidad"
                                                            title="Disminuir cantidad"
                                                        >
                                                            −
                                                        </a>

                                                    <?php } else { ?>

                                                        <span
                                                            class="btn-cantidad disabled"
                                                            title="Cantidad mínima"
                                                        >
                                                            −
                                                        </span>

                                                    <?php } ?>


                                                    <!-- CANTIDAD ACTUAL -->

                                                    <span
                                                        class="cantidad-numero"
                                                    >

                                                        <?php
                                                        echo $producto['cantidad'];
                                                        ?>

                                                    </span>


                                                    <!-- SUMAR -->

                                                    <?php if ($producto['cantidad'] < $producto['stock']) { ?>

                                                        <a
                                                            href="actualizar_cantidad.php?id=<?php echo $producto['producto_id']; ?>&accion=sumar"
                                                            class="btn-cantidad"
                                                            title="Aumentar cantidad"
                                                        >
                                                            +
                                                        </a>

                                                    <?php } else { ?>

                                                        <span
                                                            class="btn-cantidad disabled"
                                                            title="Stock máximo disponible"
                                                        >
                                                            +
                                                        </span>

                                                    <?php } ?>

                                                </div>


                                                <div class="stock-info">

                                                    Stock disponible:
                                                    <?php
                                                    echo $producto['stock'];
                                                    ?>

                                                </div>

                                            </td>


                                            <!-- SUBTOTAL -->

                                            <td
                                                class="text-end fw-bold text-white"
                                                style="font-size: 14px;"
                                            >

                                                $<?php

                                                $subtotal =
                                                    $producto['precio']
                                                    *
                                                    $producto['cantidad'];

                                                echo number_format(
                                                    $subtotal,
                                                    0,
                                                    ",",
                                                    "."
                                                );

                                                ?>

                                            </td>


                                            <!-- ELIMINAR -->

                                            <td class="text-center">

                                                <a
                                                    href="eliminar_del_carrito.php?id=<?php echo $producto['producto_id']; ?>"
                                                    class="btn btn-outline-danger btn-sm border-0"
                                                    title="Eliminar producto"
                                                >
                                                    🗑️
                                                </a>

                                            </td>


                                        </tr>


                                    <?php } ?>


                                </tbody>

                            </table>

                        </div>


                        <div
                            class="mt-4 pt-3 border-top border-dark-custom"
                        >

                            <a
                                href="index.php"
                                class="btn btn-outline-dark-custom btn-sm fw-semibold"
                            >

                                ⬅ Seguir comprando

                            </a>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     RESUMEN DE COMPRA
                ================================================== -->

                <div class="col-lg-4">

                    <div
                        class="card-dark rounded shadow-sm p-4 sticky-top"
                        style="top: 20px;"
                    >


                        <h5
                            class="fw-bold text-white mb-3 border-bottom border-dark-custom pb-2"
                        >

                            Resumen de Compra

                        </h5>


                        <div
                            class="d-flex justify-content-between mb-2 text-muted-custom"
                            style="font-size: 14px;"
                        >

                            <span>
                                Subtotal
                            </span>

                            <span class="text-light">

                                $<?php
                                echo number_format(
                                    $total,
                                    0,
                                    ",",
                                    "."
                                );
                                ?>

                            </span>

                        </div>


                        <div
                            class="d-flex justify-content-between mb-3 text-muted-custom"
                            style="font-size: 14px;"
                        >

                            <span>
                                Envío
                            </span>

                            <span class="text-success fw-bold">
                                A calcular
                            </span>

                        </div>


                        <hr class="border-dark-custom">


                        <div
                            class="d-flex justify-content-between mb-4"
                        >

                            <span class="fw-bold fs-5 text-white">
                                Total
                            </span>

                            <span class="fw-bold fs-4 text-white">

                                $<?php
                                echo number_format(
                                    $total,
                                    0,
                                    ",",
                                    "."
                                );
                                ?>

                            </span>

                        </div>


                        <!-- FINALIZAR COMPRA -->

                        <a
                            href="comprar.php"
                            class="btn btn-virtue w-100 text-center d-block py-3 mb-3"
                        >

                            Finalizar Compra

                        </a>


                        <!-- MEDIOS DE PAGO -->

                        <div
                            class="text-center pt-3 border-top border-dark-custom"
                        >

                            <p class="text-muted-custom small mb-2">

                                Medios de pago aceptados:

                            </p>


                            <div
                                class="d-flex justify-content-center flex-wrap gap-1 mb-2"
                            >

                                <span class="badge badge-dark-custom fw-normal">
                                    💳 Tarjetas
                                </span>

                                <span class="badge badge-dark-custom fw-normal">
                                    🏦 Transferencia
                                </span>

                                <span class="badge badge-dark-custom fw-normal">
                                    💵 Efectivo
                                </span>

                                <span class="badge badge-dark-custom fw-normal">
                                    📲 WhatsApp
                                </span>

                            </div>


                            <span
                                class="text-muted-custom d-block mt-2"
                                style="font-size: 11px;"
                            >
                                🔒 Compra 100% garantizada y segura
                            </span>

                        </div>


                    </div>

                </div>

            </div>


        <?php } ?>


    </div>


</body>

</html>
```
