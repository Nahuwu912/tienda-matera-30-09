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
   ID
========================================================= */

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id = intval($_GET['id']);


/* =========================================================
   BUSCAR PRODUCTO
========================================================= */

$stmt = $conexion->prepare(
    "SELECT *
     FROM productos
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    $stmt->close();
    $conexion->close();

    header("Location: productos.php");
    exit();
}

$producto = $resultado->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Editar producto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #171717;
            color: #ffffff;
        }


        .card {
            background-color: #242424;
            border: 1px solid #444;
            color: #ffffff;
        }


        .card h2 {
            color: #ffffff;
        }


        .form-label {
            color: #ffffff !important;
            font-weight: 500;
        }


        .form-control,
        .form-select {
            background-color: #ffffff;
            color: #222222;
            border: 1px solid #666;
        }


        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff;
            color: #222222;
            border-color: #b86b35;
            box-shadow: 0 0 0 0.2rem rgba(184, 107, 53, 0.25);
        }


        .form-select option {
            color: #222222;
            background-color: #ffffff;
        }


        .btn-cuero {
            background-color: #8b5e3c;
            color: #ffffff;
            border: none;
        }


        .btn-cuero:hover {
            background-color: #6f482d;
            color: #ffffff;
        }


        .btn-secondary {
            color: #ffffff;
        }


        .imagen-actual {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #555;
        }

    </style>

</head>

<body>

<div class="container mt-5">

    <div
        class="card p-4 mx-auto"
        style="max-width: 600px;"
    >

        <h2 class="mb-4">
            ✏️ Editar producto
        </h2>


        <form
            action="actualizar_producto.php"
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- =====================================================
                 ID DEL PRODUCTO
            ====================================================== -->

            <input
                type="hidden"
                name="id"
                value="<?php echo $producto['id']; ?>"
            >


            <!-- =====================================================
                 NOMBRE
            ====================================================== -->

            <div class="mb-3">

                <label class="form-label">
                    Nombre
                </label>

                <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    value="<?php echo htmlspecialchars($producto['nombre']); ?>"
                    required
                >

            </div>


            <!-- =====================================================
                 CATEGORÍA
            ====================================================== -->

            <div class="mb-3">

                <label class="form-label">
                    Categoría
                </label>

                <select
                    name="categoria"
                    class="form-select"
                    required
                >

                    <?php

                    $categorias = [
                        "Mates",
                        "Yerbas",
                        "Termos",
                        "Pavas",
                        "Bombillas"
                    ];


                    foreach ($categorias as $categoria) {

                        $seleccionada =
                            ($producto['categoria'] == $categoria)
                            ? "selected"
                            : "";

                    ?>

                        <option
                            value="<?php echo $categoria; ?>"
                            <?php echo $seleccionada; ?>
                        >

                            <?php echo $categoria; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- =====================================================
                 PRECIO
            ====================================================== -->

            <div class="mb-3">

                <label class="form-label">
                    Precio
                </label>

                <input
                    type="number"
                    name="precio"
                    class="form-control"
                    step="0.01"
                    min="0"
                    value="<?php echo $producto['precio']; ?>"
                    required
                >

            </div>


            <!-- =====================================================
                 STOCK
            ====================================================== -->

            <div class="mb-3">

                <label class="form-label">
                    Stock
                </label>

                <input
                    type="number"
                    name="stock"
                    class="form-control"
                    min="0"
                    value="<?php echo $producto['stock']; ?>"
                    required
                >

            </div>


            <!-- =====================================================
                 IMAGEN ACTUAL
            ====================================================== -->

            <?php if (!empty($producto['imagen'])) { ?>

                <div class="mb-3">

                    <label class="form-label">
                        Imagen actual
                    </label>

                    <br>

                    <img
                        src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>"
                        class="imagen-actual"
                        alt="Imagen actual"
                    >

                </div>

            <?php } ?>


            <!-- =====================================================
                 CAMBIAR IMAGEN
            ====================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Cambiar imagen
                </label>

                <input
                    type="file"
                    name="imagen"
                    class="form-control"
                    accept="image/*"
                >

            </div>


            <!-- =====================================================
                 BOTONES
            ====================================================== -->

            <button
                type="submit"
                class="btn btn-cuero"
            >
                Guardar cambios
            </button>


            <a
                href="productos.php"
                class="btn btn-secondary"
            >
                Cancelar
            </a>


        </form>

    </div>

</div>

</body>
</html>

<?php

$conexion->close();

?>