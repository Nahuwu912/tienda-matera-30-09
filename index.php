<?php

session_start();


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
    die("Error de conexión");
}

$conexion->set_charset("utf8");


/* =========================================================
   CONTADOR DEL CARRITO
   Ahora utiliza las tablas:
   carritos + carrito_items
========================================================= */

$cant_carrito = 0;

if (
    isset($_SESSION['id']) &&
    isset($_SESSION['rol']) &&
    strtolower(trim($_SESSION['rol'])) == "cliente"
) {

    $id_usuario = $_SESSION['id'];

    $stmt_carrito = $conexion->prepare(
        "SELECT COALESCE(SUM(ci.cantidad), 0) AS cantidad_total
         FROM carrito_items ci
         INNER JOIN carritos c
            ON ci.carrito_id = c.id
         WHERE c.usuario_id = ?"
    );

    $stmt_carrito->bind_param(
        "i",
        $id_usuario
    );

    $stmt_carrito->execute();

    $resultado_carrito =
        $stmt_carrito->get_result();

    if (
        $fila_carrito =
        $resultado_carrito->fetch_assoc()
    ) {

        $cant_carrito =
            intval(
                $fila_carrito['cantidad_total']
            );
    }

    $stmt_carrito->close();
}


/* =========================================================
   OBTENER PRODUCTOS
========================================================= */

$resultado = $conexion->query(
    "SELECT *
     FROM productos
     ORDER BY id DESC"
);

if (!$resultado) {
    die(
        "Error al obtener productos: " .
        $conexion->error
    );
}


/* =========================================================
   ESTADO DEL USUARIO
========================================================= */

$logueado = isset($_SESSION['id']);

$esCliente = (
    $logueado &&
    isset($_SESSION['rol']) &&
    strtolower(trim($_SESSION['rol'])) == "cliente"
);

$esAdmin = (
    $logueado &&
    isset($_SESSION['rol']) &&
    strtolower(trim($_SESSION['rol'])) == "admin"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tienda Matera</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Google Fonts (Poppins) -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >


    <style>


    /* =====================================================
       CARRITO DE LA BARRA SUPERIOR
    ===================================================== */

    .topbar-cart-link {

        color: #e4e4e7;

        text-decoration: none;

        font-size: 13px;

        font-weight: 500;

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding: 4px 10px;

        border-radius: 4px;

        background-color: #27272a;

        border: 1px solid #3f3f46;

        transition: all 0.2s ease;

    }


    .topbar-cart-link:hover {

        color: #ffffff;

        background-color: #3f3f46;

    }


    /* Número del carrito */

    .badge-topbar-count {

        background-color: #b86b35;

        color: #ffffff;

        font-size: 11px;

        font-weight: 600;

        padding: 1px 7px;

        border-radius: 10px;

    }


    /* =====================================================
       PALETA DARK MODE
    ===================================================== */

    body {

        font-family: 'Poppins', sans-serif;

        background-color: #121214;

        color: #e4e4e7;

    }


    /* =====================================================
       BARRA SUPERIOR
    ===================================================== */

    .top-bar {

        background-color: #09090b;

        color: #a1a1aa;

        font-size: 13px;

        border-bottom: 1px solid #27272a;

    }


    .top-bar a {

        color: #f4f4f5;

        text-decoration: none;

    }


    .top-bar a:hover {

        text-decoration: underline;

    }


    /* =====================================================
       HEADER PRINCIPAL
    ===================================================== */

    header.header-dark {

        background-color: #18181b;

        border-bottom: 1px solid #27272a;

    }


    /* =====================================================
       TARJETA DE PRODUCTO
    ===================================================== */

    .card-virtue {

        border: 1px solid #27272a;

        border-radius: 6px;

        background-color: #18181b;

        transition: all 0.25s ease-in-out;

    }


    .card-virtue:hover {

        border-color: #b86b35;

        box-shadow:
            0 10px 25px
            rgba(0, 0, 0, 0.6) !important;

        transform: translateY(-3px);

    }


    /* =====================================================
       CONTENEDOR DE IMAGEN
    ===================================================== */

    .img-container {

        height: 220px;

        background-color: #202024;

        display: flex;

        align-items: center;

        justify-content: center;

        padding: 15px;

        border-bottom: 1px solid #27272a;

        border-top-left-radius: 6px;

        border-top-right-radius: 6px;

    }


    .img-container img {

        max-height: 100%;

        max-width: 100%;

        object-fit: contain;

    }


    /* =====================================================
       BOTÓN CUERO COBRIZO
    ===================================================== */

    .btn-cuero {

        background-color: #b86b35;

        color: #ffffff;

        font-size: 11px;

        font-weight: 600;

        letter-spacing: 1px;

        text-transform: uppercase;

        border-radius: 4px;

        padding: 10px 12px;

        border: none;

        transition: all 0.2s ease;

    }


    .btn-cuero:hover {

        background-color: #d48348;

        color: #ffffff;

    }


    /* =====================================================
       BLOQUES PERSONALIZADOS
    ===================================================== */

    .box-custom {

        background-color: #18181b;

        border: 1px solid #27272a;

        border-radius: 6px;

    }


    /* =====================================================
       INPUTS OSCUROS
    ===================================================== */

    .dark-input {

        background-color: #27272a !important;

        color: #f4f4f5 !important;

        border: 1px solid #3f3f46 !important;

    }


    .dark-input::placeholder {

        color: #a1a1aa !important;

    }


    .dark-input option {

        background-color: #27272a;

        color: #f4f4f5;

    }


    /* =====================================================
       FOOTER
    ===================================================== */

    footer.footer-dark {

        background-color: #09090b;

        color: #a1a1aa;

    }


    .badge-dark-custom {

        background-color: #27272a;

        color: #e4e4e7;

        border: 1px solid #3f3f46;

    }


    </style>


    <script>

    /* =====================================================
       BUSCADOR Y FILTRO DE CATEGORÍAS
    ===================================================== */

    function filtrarProductos(){

        let texto =
            document
            .getElementById("buscador")
            .value
            .toLowerCase()
            .trim();


        let categoria =
            document
            .getElementById("categoria")
            .value
            .toLowerCase()
            .trim();


        let productos =
            document
            .getElementsByClassName("producto");


        for(
            let i = 0;
            i < productos.length;
            i++
        ){

            let nombre =
                productos[i]
                .getAttribute("data-nombre")
                .toLowerCase()
                .trim();


            let cat =
                productos[i]
                .getAttribute("data-categoria")
                .toLowerCase()
                .trim();


            let coincideTexto =
                nombre.includes(texto) ||
                cat.includes(texto);


            let coincideCategoria =
                categoria == "" ||
                cat == categoria;


            if(
                coincideTexto &&
                coincideCategoria
            ){

                productos[i].style.display = "";

            }else{

                productos[i].style.display = "none";

            }

        }

    }

    </script>

</head>


<body>


    <!-- =====================================================
         BARRA SUPERIOR
    ====================================================== -->

    <div class="top-bar py-2 px-4">

        <div
            class="container d-flex justify-content-between align-items-center"
        >

            <div>

                <?php if(!$logueado){ ?>

                    <a
                        href="login.php"
                        class="me-3"
                    >
                        Iniciar sesión
                    </a>


                    <a href="registro.php">
                        Registrarse
                    </a>

                <?php }else{ ?>

                    <span>

                        Hola,

                        <strong class="text-white">

                            <?php
                            echo htmlspecialchars(
                                $_SESSION['nombre']
                            );
                            ?>

                        </strong>

                    </span>

                <?php } ?>

            </div>


            <div
                class="d-flex align-items-center gap-3"
            >

                <?php if($logueado){ ?>


                    <!-- ==============================
                         CARRITO DEL CLIENTE
                    =============================== -->

                    <?php if($esCliente){ ?>

                        <a
                            href="carrito.php"
                            class="topbar-cart-link"
                        >

                            <span>
                                🛒 Ver carrito
                            </span>


                            <?php if($cant_carrito > 0): ?>

                                <span
                                    class="badge-topbar-count"
                                >

                                    <?php
                                    echo $cant_carrito;
                                    ?>

                                </span>

                            <?php endif; ?>

                        </a>

                    <?php } ?>


                    <!-- ==============================
                         OPCIONES DEL ADMIN
                    =============================== -->

                    <?php if($esAdmin){ ?>

                        <a
                            href="productos.php"
                            class="text-warning fw-semibold"
                        >
                            ⚙️ Productos
                        </a>


                        <a
                            href="usuarios.php"
                            class="text-info fw-semibold"
                        >
                            👥 Usuarios
                        </a>

                    <?php } ?>


                    <!-- ==============================
                         CERRAR SESIÓN
                    =============================== -->

                    <a
                        href="logout.php"
                        class="text-danger fw-semibold"
                    >
                        Cerrar sesión
                    </a>

                <?php } ?>

            </div>

        </div>

    </div>


    <!-- =====================================================
         HEADER PRINCIPAL
    ====================================================== -->

    <header class="header-dark py-4 mb-4">

        <div class="container text-center">

            <h1
                class="fw-bold m-0 text-white"
                style="letter-spacing: -1px;"
            >
                🧉 TIENDA MATERA
            </h1>


            <p
                class="text-secondary small mb-0 mt-1"
            >
                Los mejores mates y accesorios artesanales
            </p>

        </div>

    </header>


    <!-- =====================================================
         HERO BANNER
    ====================================================== -->

    <div
        class="p-5 mb-5 text-center text-white rounded-0"
        style="
            background:
            linear-gradient(
                rgba(18, 18, 20, 0.85),
                rgba(18, 18, 20, 0.85)
            ),
            url('https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=1200')
            center/cover;

            border-bottom: 1px solid #27272a;
        "
    >

        <div class="container py-4">

            <h1
                class="display-5 fw-bold text-uppercase"
                style="letter-spacing: 2px;"
            >
                El Arte del Buen Mate
            </h1>


            <p
                class="col-md-8 fs-5 mx-auto text-secondary"
            >
                Mates artesanales, bombillas de alpaca
                y los mejores accesorios para tus juntadas.
            </p>


            <a
                href="#catalogo"
                class="btn btn-cuero btn-lg mt-2 px-4"
                style="font-size: 13px;"
            >
                Ver Colección
            </a>

        </div>

    </div>


    <!-- =====================================================
         CATÁLOGO
    ====================================================== -->

    <div
        class="container mb-5"
        id="catalogo"
    >


        <!-- ==============================
             BENEFICIOS
        =============================== -->

        <div
            class="row text-center my-4 py-3 box-custom shadow-sm g-3"
            style="font-size: 13px;"
        >

            <div class="col-md-4">

                <div class="fw-bold text-white">

                    🚚 ENVÍOS A TODO EL PAÍS

                </div>

                <div class="text-secondary small">

                    Llegamos a la puerta de tu casa

                </div>

            </div>


            <div
                class="col-md-4 border-start border-end border-secondary border-opacity-25"
            >

                <div class="fw-bold text-white">

                    💳 3 CUOTAS SIN INTERÉS

                </div>

                <div class="text-secondary small">

                    Con todas las tarjetas de crédito

                </div>

            </div>


            <div class="col-md-4">

                <div class="fw-bold text-white">

                    🔒 COMPRA 100% SEGURA

                </div>

                <div class="text-secondary small">

                    Garantía de calidad en cada mate

                </div>

            </div>

        </div>


        <!-- ==============================
             BUSCADOR Y FILTROS
        =============================== -->

        <div
            class="row g-3 mb-4 p-3 box-custom shadow-sm"
        >

            <div class="col-md-7">

                <input
                    type="text"
                    id="buscador"
                    class="form-control form-control-lg dark-input"
                    placeholder="🔍 Buscar por nombre o categoría..."
                    onkeyup="filtrarProductos()"
                    style="font-size: 14px;"
                >

            </div>


            <div class="col-md-5">

                <select
                    id="categoria"
                    class="form-select form-select-lg dark-input"
                    onchange="filtrarProductos()"
                    style="font-size: 14px;"
                >

                    <option value="">
                        Todas las categorías
                    </option>


                    <option value="mates">
                        Mates
                    </option>


                    <option value="yerbas">
                        Yerbas
                    </option>


                    <option value="termos">
                        Termos
                    </option>


                    <option value="pavas">
                        Pavas
                    </option>


                    <option value="bombillas">
                        Bombillas
                    </option>

                </select>

            </div>

        </div>


        <!-- =================================================
             GRILLA DE PRODUCTOS
        ================================================== -->

        <div class="row g-4">


            <?php while($p = $resultado->fetch_assoc()){ ?>


                <div
                    class="col-6 col-md-4 col-lg-3 producto"

                    data-nombre="<?php
                        echo htmlspecialchars(
                            $p['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"

                    data-categoria="<?php
                        echo htmlspecialchars(
                            $p['categoria'],
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                    <div
                        class="card card-virtue h-100 text-center shadow-sm"
                    >


                        <!-- ==========================
                             IMAGEN
                        =========================== -->

                        <div class="img-container">

                            <?php if(!empty($p['imagen'])){ ?>

                                <img
                                    src="uploads/<?php
                                        echo htmlspecialchars(
                                            $p['imagen'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                    ?>"

                                    alt="<?php
                                        echo htmlspecialchars(
                                            $p['nombre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                    ?>"
                                >

                            <?php }else{ ?>

                                <span
                                    class="text-secondary"
                                >
                                    Sin imagen
                                </span>

                            <?php } ?>

                        </div>


                        <div
                            class="card-body d-flex flex-column justify-content-between p-3"
                        >

                            <div>


                                <!-- CATEGORÍA -->

                                <span
                                    class="text-uppercase text-secondary d-block mb-1"
                                    style="
                                        font-size: 10px;
                                        letter-spacing: 0.5px;
                                    "
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst(
                                            $p['categoria']
                                        )
                                    );
                                    ?>

                                </span>


                                <!-- NOMBRE -->

                                <h6
                                    class="fw-bold text-white mb-2"
                                    style="
                                        font-size: 14px;
                                        min-height: 38px;
                                    "
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $p['nombre']
                                    );
                                    ?>

                                </h6>


                                <!-- PRECIO -->

                                <p
                                    class="fs-5 fw-bold mb-1"
                                    style="color: #b86b35;"
                                >

                                    $

                                    <?php
                                    echo number_format(
                                        $p['precio'],
                                        0,
                                        ",",
                                        "."
                                    );
                                    ?>

                                </p>


                                <!-- STOCK -->

                                <p
                                    class="text-secondary mb-3"
                                    style="font-size: 12px;"
                                >

                                    Stock:

                                    <strong>

                                        <?php
                                        echo $p['stock'];
                                        ?>

                                    </strong>

                                </p>

                            </div>


                            <!-- ==========================
                                 BOTÓN DE ACCIÓN
                            =========================== -->

                            <div>


                                <?php if($p['stock'] > 0){ ?>


                                    <!-- VISITANTE -->

                                    <?php if(!$logueado){ ?>

                                        <a
                                            href="login.php"
                                            class="btn btn-cuero w-100"
                                        >
                                            Agregar al carrito
                                        </a>


                                    <!-- CLIENTE -->

                                    <?php }elseif($esCliente){ ?>

                                        <a
                                            href="agregar_carrito.php?id=<?php echo $p['id']; ?>"
                                            class="btn btn-cuero w-100"
                                        >
                                            Agregar al carrito
                                        </a>


                                    <!-- ADMIN -->

                                    <?php }elseif($esAdmin){ ?>

                                        <a
                                            href="editar_producto.php?id=<?php echo $p['id']; ?>"
                                            class="btn btn-outline-warning w-100"
                                        >
                                            ⚙️ Administrar producto
                                        </a>

                                    <?php } ?>


                                <?php }else{ ?>


                                    <button
                                        class="btn btn-secondary w-100"
                                        disabled
                                    >
                                        Sin stock
                                    </button>


                                <?php } ?>


                            </div>

                        </div>

                    </div>

                </div>


            <?php } ?>


        </div>

    </div>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer
        class="footer-dark pt-5 pb-4 mt-5 border-top border-secondary border-opacity-25"
    >

        <div class="container">

            <div class="row g-4">


                <div class="col-md-4">

                    <h5
                        class="fw-bold text-uppercase mb-3 text-white"
                    >
                        Tienda Matera
                    </h5>


                    <p
                        class="small text-secondary"
                    >
                        Especialistas en mates de calabaza,
                        cuero e imperial. Envíos garantizados
                        y la mejor calidad del mercado.
                    </p>

                </div>


                <div class="col-md-4">

                    <h6
                        class="fw-bold text-uppercase mb-3 text-white"
                    >
                        Contacto
                    </h6>


                    <p
                        class="small mb-1 text-secondary"
                    >
                        📍 Bahia Blanca, Argentina
                    </p>


                    <p
                        class="small mb-1 text-secondary"
                    >
                        📱 WhatsApp: +54 9 291 642-3358
                    </p>


                    <p
                        class="small text-secondary"
                    >
                        ✉️ contacto@tiendamatera.com
                    </p>

                </div>


                <div
                    class="col-md-4 text-md-end"
                >

                    <h6
                        class="fw-bold text-uppercase mb-3 text-white"
                    >
                        Medios de Pago
                    </h6>


                    <span
                        class="badge badge-dark-custom p-2 me-1"
                    >
                        MercadoPago
                    </span>


                    <span
                        class="badge badge-dark-custom p-2 me-1"
                    >
                        Efectivo
                    </span>


                    <span
                        class="badge badge-dark-custom p-2"
                    >
                        Transferencia
                    </span>

                </div>

            </div>


            <hr
                class="my-4 border-secondary opacity-25"
            >


            <div
                class="text-center small text-secondary"
            >

                © 2026 Tienda Matera -
                Todos los derechos reservados.

            </div>

        </div>

    </footer>


    <!-- =====================================================
         SCROLL
    ====================================================== -->

    <script>

    // Guardar posición del scroll

    window.addEventListener(
        'beforeunload',
        () => {

            sessionStorage.setItem(
                'scrollPos',
                window.scrollY
            );

        }
    );


    // Restaurar posición del scroll

    window.addEventListener(
        'load',
        () => {

            const scrollPos =
                sessionStorage.getItem(
                    'scrollPos'
                );


            if(scrollPos !== null){

                window.scrollTo(
                    0,
                    parseInt(scrollPos)
                );


                sessionStorage.removeItem(
                    'scrollPos'
                );

            }

        }
    );

    </script>


</body>

</html>


<?php

$conexion->close();

?>
```
