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
   PRODUCTOS
========================================================= */

$resultado = $conexion->query(
    "SELECT * FROM productos ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Administrar productos</title>

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

        .table {
            color: white;
        }

        .btn-cuero {
            background-color: #8b5e3c;
            color: white;
        }

        .btn-cuero:hover {
            background-color: #6f482d;
            color: white;
        }

        .imagen-producto {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark mb-4">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand"
        >
            🧉 Tienda Matera
        </a>

        <div>

            <span class="me-3">
                👤 <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </span>

            <a
                href="compras.php"
                class="btn btn-success me-2"
            >
                🛒 Compras
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


<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            📦 Administrar productos
        </h2>

        <a
            href="agregar_producto.php"
            class="btn btn-cuero"
        >
            ➕ Agregar producto
        </a>

    </div>


    <div class="card p-3">

        <div class="table-responsive">

            <table class="table table-dark table-hover align-middle">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($resultado && $resultado->num_rows > 0) { ?>

                    <?php while ($p = $resultado->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo $p['id']; ?>
                            </td>

                            <td>

                                <?php if (!empty($p['imagen'])) { ?>

                                    <img
                                        src="uploads/<?php echo htmlspecialchars($p['imagen']); ?>"
                                        class="imagen-producto"
                                        alt="Producto"
                                    >

                                <?php } else { ?>

                                    Sin imagen

                                <?php } ?>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($p['categoria']); ?>
                            </td>

                            <td>
                                $<?php
                                echo number_format(
                                    $p['precio'],
                                    2,
                                    ',',
                                    '.'
                                );
                                ?>
                            </td>

                            <td>

                                <?php if ($p['stock'] > 0) { ?>

                                    <span class="badge bg-success">
                                        <?php echo $p['stock']; ?>
                                    </span>

                                <?php } else { ?>

                                    <span class="badge bg-danger">
                                        Sin stock
                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <a
                                    href="editar_producto.php?id=<?php echo $p['id']; ?>"
                                    class="btn btn-warning btn-sm"
                                >
                                    ✏️ Editar
                                </a>

                                <a
                                    href="eliminar.php?id=<?php echo $p['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Seguro que querés eliminar este producto?');"
                                >
                                    🗑️ Eliminar
                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="7"
                            class="text-center"
                        >
                            No hay productos cargados.
                        </td>

                    </tr>

                <?php } ?>

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