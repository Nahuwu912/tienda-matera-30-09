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
// VERIFICAR ID DE COMPRA
// =====================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: compras.php");
    exit();
}


$compra_id = intval($_GET['id']);


// =====================================================
// INICIAR TRANSACCIÓN
// =====================================================

$conexion->begin_transaction();


try {


    // =================================================
    // BUSCAR COMPRA
    // =================================================

    $sql_compra = "
        SELECT
            id,
            estado
        FROM compras
        WHERE id = ?
        LIMIT 1
    ";


    $stmt_compra = $conexion->prepare($sql_compra);

    $stmt_compra->bind_param(
        "i",
        $compra_id
    );

    $stmt_compra->execute();

    $resultado_compra = $stmt_compra->get_result();


    // =================================================
    // VERIFICAR QUE EXISTA
    // =================================================

    if ($resultado_compra->num_rows == 0) {

        throw new Exception(
            "La compra no existe."
        );

    }


    $compra = $resultado_compra->fetch_assoc();

    $stmt_compra->close();


    // =================================================
    // VERIFICAR ESTADO
    // =================================================

    if ($compra['estado'] != 'pendiente') {

        throw new Exception(
            "Esta compra ya fue procesada."
        );

    }


    // =================================================
    // OBTENER PRODUCTOS DE LA COMPRA
    // =================================================

    $sql_items = "
        SELECT
            ci.producto_id,
            ci.cantidad,
            p.nombre,
            p.stock
        FROM compra_items ci
        INNER JOIN productos p
            ON ci.producto_id = p.id
        WHERE ci.compra_id = ?
    ";


    $stmt_items = $conexion->prepare($sql_items);

    $stmt_items->bind_param(
        "i",
        $compra_id
    );

    $stmt_items->execute();

    $resultado_items = $stmt_items->get_result();


    // =================================================
    // VERIFICAR QUE TENGA PRODUCTOS
    // =================================================

    if ($resultado_items->num_rows == 0) {

        throw new Exception(
            "La compra no tiene productos."
        );

    }


    // =================================================
    // VERIFICAR TODO EL STOCK ANTES DE MODIFICARLO
    // =================================================

    while ($item = $resultado_items->fetch_assoc()) {

        if ($item['cantidad'] > $item['stock']) {

            throw new Exception(
                "No hay suficiente stock de "
                . $item['nombre']
                . ". Stock disponible: "
                . $item['stock']
                . " y se necesitan: "
                . $item['cantidad']
            );

        }

    }


    // =================================================
    // VOLVER AL PRINCIPIO DEL RESULTADO
    // =================================================

    $resultado_items->data_seek(0);


    // =================================================
    // DESCONTAR STOCK
    // =================================================

    $sql_stock = "
        UPDATE productos
        SET stock = stock - ?
        WHERE id = ?
    ";


    $stmt_stock = $conexion->prepare($sql_stock);


    while ($item = $resultado_items->fetch_assoc()) {

        $cantidad = $item['cantidad'];

        $producto_id = $item['producto_id'];


        $stmt_stock->bind_param(
            "ii",
            $cantidad,
            $producto_id
        );


        $stmt_stock->execute();

    }


    $stmt_stock->close();

    $stmt_items->close();


    // =================================================
    // CONFIRMAR COMPRA
    // GUARDAR FECHA Y HORA
    // =================================================

    $sql_confirmar = "
        UPDATE compras
        SET
            estado = 'confirmada',
            fecha_confirmacion = NOW()
        WHERE id = ?
    ";


    $stmt_confirmar = $conexion->prepare(
        $sql_confirmar
    );


    $stmt_confirmar->bind_param(
        "i",
        $compra_id
    );


    $stmt_confirmar->execute();

    $stmt_confirmar->close();


    // =================================================
    // CONFIRMAR TODA LA TRANSACCIÓN
    // =================================================

    $conexion->commit();


    // =================================================
    // VOLVER A COMPRAS
    // =================================================

    header("Location: compras.php");

    exit();


} catch (Exception $e) {


    // =================================================
    // DESHACER TODOS LOS CAMBIOS
    // =================================================

    $conexion->rollback();


    echo "<!DOCTYPE html>";

    echo "<html lang='es'>";

    echo "<head>";

    echo "<meta charset='UTF-8'>";

    echo "<title>Error al confirmar compra</title>";

    echo "</head>";

    echo "<body style='background:#171717;color:white;font-family:Arial;padding:40px;'>";


    echo "<h2>No se pudo confirmar la compra</h2>";


    echo "<p>";
    echo htmlspecialchars(
        $e->getMessage()
    );
    echo "</p>";


    echo "<br>";


    echo "<a
            href='compras.php'
            style='
                color:white;
                background:#8b5e3c;
                padding:10px 18px;
                text-decoration:none;
                border-radius:6px;
            '
        >
            Volver a compras
        </a>";


    echo "</body>";

    echo "</html>";

    exit();

}

?>