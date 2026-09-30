<?php
session_start();

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "sistema"
);

if($conexion->connect_error){
    die("Error de conexión");
}

$esCliente = false;

if(
    isset($_SESSION['rol']) &&
    strtolower(trim($_SESSION['rol'])) == "cliente"
){
    $esCliente = true;
}

$resultado = $conexion->query(
    "SELECT * FROM productos"
);
?>

<!DOCTYPE html>
<html>
<head>

<title>Tienda</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<script>

function filtrarProductos(){

    let texto =
    document.getElementById("buscador")
    .value
    .toLowerCase();

    let categoria =
    document.getElementById("categoria")
    .value
    .toLowerCase();

    let productos =
    document.getElementsByClassName("producto");

    for(let i = 0; i < productos.length; i++){

        let nombre =
        productos[i]
        .getAttribute("data-nombre")
        .toLowerCase();

        let cat =
        productos[i]
        .getAttribute("data-categoria")
        .toLowerCase();

        let coincideNombre =
        nombre.includes(texto);

        let coincideCategoria =
        categoria == "" ||
        cat == categoria;

        if(
            coincideNombre &&
            coincideCategoria
        ){
            productos[i].style.display = "block";
        }else{
            productos[i].style.display = "none";
        }
    }
}

</script>

</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>
Bienvenido
<?php echo $_SESSION['nombre']; ?>
</h2>

<p class="text-muted">
Rol:
<?php echo $_SESSION['rol']; ?>
</p>

</div>

<div>

<?php if($esCliente){ ?>

<a
href="carrito.php"
class="btn btn-primary"
>
🛒 Ver carrito
</a>

<?php } ?>

<a
href="logout.php"
class="btn btn-danger"
>
Cerrar sesión
</a>

</div>

</div>

<div class="row mb-4">

<div class="col-md-6">

<input
type="text"
id="buscador"
class="form-control"
placeholder="Buscar producto..."
onkeyup="filtrarProductos()"
>

</div>

<div class="col-md-6">

<select
id="categoria"
class="form-control"
onchange="filtrarProductos()"
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

<div class="row">

<?php while($p = $resultado->fetch_assoc()){ ?>

<div
class="col-md-4 producto"
data-nombre="<?php echo $p['nombre']; ?>"
data-categoria="<?php echo $p['categoria']; ?>"
>

<div class="card shadow mb-4">

<img
src="uploads/<?php echo $p['imagen']; ?>"
class="card-img-top"
style="height:250px; object-fit:cover;"
>

<div class="card-body text-center">

<h5>
<?php echo $p['nombre']; ?>
</h5>

<p class="text-muted">
<?php echo $p['categoria']; ?>
</p>

<p class="fw-bold">
$<?php echo $p['precio']; ?>
</p>

<p>
Stock:
<?php echo $p['stock']; ?>
</p>

<?php if($esCliente){ ?>

<a
href="agregar_carrito.php?id=<?php echo $p['id']; ?>"
class="btn btn-success"
>
Agregar al carrito
</a>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

</body>
</html>