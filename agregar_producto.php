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

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Agregar producto</title>

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

    </style>

</head>

<body>

<div class="container mt-5">

    <div
        class="card p-4 mx-auto"
        style="max-width: 600px;"
    >

        <h2 class="mb-4">
            ➕ Agregar producto
        </h2>


        <form
            action="guardar_producto.php"
            method="POST"
            enctype="multipart/form-data"
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

                    <option value="">
                        Seleccionar categoría
                    </option>

                    <option value="Mates">
                        Mates
                    </option>

                    <option value="Yerbas">
                        Yerbas
                    </option>

                    <option value="Termos">
                        Termos
                    </option>

                    <option value="Pavas">
                        Pavas
                    </option>

                    <option value="Bombillas">
                        Bombillas
                    </option>

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
                    required
                >

            </div>


            <!-- =====================================================
                 IMAGEN
            ====================================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Imagen
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
                Guardar producto
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