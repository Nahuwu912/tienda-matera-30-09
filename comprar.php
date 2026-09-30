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
// VERIFICAR QUE SEA CLIENTE
// =====================================================

if (
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] != 'cliente'
) {
    header("Location: index.php");
    exit();
}


$usuario_id = $_SESSION['id'];


// =====================================================
// OBTENER CARRITO DEL USUARIO
// =====================================================

$sql_carrito = "
    SELECT id
    FROM carritos
    WHERE usuario_id = ?
    LIMIT 1
";

$stmt_carrito = $conexion->prepare($sql_carrito);
$stmt_carrito->bind_param("i", $usuario_id);
$stmt_carrito->execute();

$resultado_carrito = $stmt_carrito->get_result();


if ($resultado_carrito->num_rows == 0) {

    header("Location: carrito.php");
    exit();

}


$carrito = $resultado_carrito->fetch_assoc();

$carrito_id = $carrito['id'];

$stmt_carrito->close();


// =====================================================
// INICIAR TRANSACCIÓN
// =====================================================

$conexion->begin_transaction();


try {


    // =================================================
    // OBTENER PRODUCTOS DEL CARRITO
    // =================================================

    $sql_items = "
        SELECT
            ci.producto_id,
            ci.cantidad,
            p.nombre,
            p.precio,
            p.stock
        FROM carrito_items ci
        INNER JOIN productos p
            ON ci.producto_id = p.id
        WHERE ci.carrito_id = ?
    ";


    $stmt_items = $conexion->prepare($sql_items);

    $stmt_items->bind_param(
        "i",
        $carrito_id
    );

    $stmt_items->execute();

    $resultado_items = $stmt_items->get_result();


    // =================================================
    // VERIFICAR QUE HAYA PRODUCTOS
    // =================================================

    if ($resultado_items->num_rows == 0) {

        throw new Exception(
            "El carrito está vacío."
        );

    }


    // =================================================
    // CREAR COMPRA PENDIENTE
    // =================================================

    $sql_compra = "
        INSERT INTO compras
        (usuario_id, estado)
        VALUES
        (?, 'pendiente')
    ";


    $stmt_compra = $conexion->prepare($sql_compra);

    $stmt_compra->bind_param(
        "i",
        $usuario_id
    );

    $stmt_compra->execute();


    $compra_id = $conexion->insert_id;

    $stmt_compra->close();


    // =================================================
    // PREPARAR INSERT DE PRODUCTOS
    // =================================================

    $sql_item = "
        INSERT INTO compra_items
        (
            compra_id,
            producto_id,
            cantidad,
            precio
        )
        VALUES
        (?, ?, ?, ?)
    ";


    $stmt_item = $conexion->prepare($sql_item);


    // =================================================
    // MENSAJE DE WHATSAPP
    // =================================================

    $mensaje =
        "Hola, quiero realizar el siguiente pedido:"
        . "\n\n";


    $total = 0;


    // =================================================
    // RECORRER PRODUCTOS
    // =================================================

    while ($item = $resultado_items->fetch_assoc()) {


        $producto_id = $item['producto_id'];

        $cantidad = $item['cantidad'];

        $nombre = $item['nombre'];

        $precio = $item['precio'];

        $stock = $item['stock'];


        // =============================================
        // VERIFICAR STOCK
        // =============================================

        if ($cantidad > $stock) {

            throw new Exception(
                "No hay suficiente stock de "
                . $nombre
                . ". Stock disponible: "
                . $stock
            );

        }


        // =============================================
        // CALCULAR SUBTOTAL
        // =============================================

        $subtotal = $precio * $cantidad;

        $total += $subtotal;


        // =============================================
        // GUARDAR PRODUCTO EN compra_items
        // =============================================

        $stmt_item->bind_param(
            "iiid",
            $compra_id,
            $producto_id,
            $cantidad,
            $precio
        );


        $stmt_item->execute();


        // =============================================
        // AGREGAR AL MENSAJE DE WHATSAPP
        // =============================================

        $mensaje .=
            "- "
            . $nombre
            . " x"
            . $cantidad
            . " ($"
            . number_format(
                $subtotal,
                0,
                ",",
                "."
            )
            . ")"
            . "\n";

    }


    $stmt_item->close();

    $stmt_items->close();


    // =================================================
    // AGREGAR TOTAL
    // =================================================

    $mensaje .=
        "\nTOTAL: $"
        . number_format(
            $total,
            0,
            ",",
            "."
        );


    // =================================================
    // VACIAR CARRITO
    // =================================================

    $sql_vaciar = "
        DELETE FROM carrito_items
        WHERE carrito_id = ?
    ";


    $stmt_vaciar = $conexion->prepare($sql_vaciar);

    $stmt_vaciar->bind_param(
        "i",
        $carrito_id
    );

    $stmt_vaciar->execute();

    $stmt_vaciar->close();


    // =================================================
    // CONFIRMAR TRANSACCIÓN
    // =================================================

    $conexion->commit();


    // =================================================
    // ABRIR WHATSAPP
    // =================================================

    $numero = "5492916423358";


    $url =
        "https://wa.me/"
        . $numero
        . "?text="
        . urlencode($mensaje);


    header(
        "Location: " . $url
    );

    exit();


} catch (Exception $e) {


    // =================================================
    // DESHACER CAMBIOS
    // =================================================

    $conexion->rollback();


    echo "<h2>No se pudo realizar la compra</h2>";

    echo "<p>";
    echo htmlspecialchars(
        $e->getMessage()
    );
    echo "</p>";


    echo "<br>";

    echo "<a href='carrito.php'>";
    echo "Volver al carrito";
    echo "</a>";


    exit();

}

?>