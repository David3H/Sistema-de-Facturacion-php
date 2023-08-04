<?php
session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    header("location: ./");
}
include "../conexion.php";

if (!empty($_POST)) {

    $alert = '';
    if (empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || 
    $_POST['precio'] <= 0 || empty($_POST['cantidad']) || $_POST['cantidad'] <= 0) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios o no deben ser
        menores que cero.</p>';
        
    } else {

        $proveedor = $_POST['proveedor'];
        $producto = $_POST['producto'];
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $usuarioid = $_SESSION['idUser'];

        $foto = $_FILES['foto'];
        $nombre_foto = $foto['name'];
        $type = $foto['type'];
        $url_temp = $foto['tmp_name'];

        $imgProducto = 'img_producto.png';

        if($nombre_foto != ''){
            $destino = 'images/uploads/';
            $img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
            $imgProducto = $img_nombre.'.jpg';
            $src = $destino.$imgProducto;
        }

        $query_insert = mysqli_query($conection, "INSERT INTO producto(proveedor,descripcion,precio,
                                    existencia,usuarioid, foto) VALUES('$proveedor', '$producto', '$precio',
                                    '$cantidad','$usuarioid', '$imgProducto')");
        if ($query_insert) {
            if($nombre_foto != ''){
                move_uploaded_file($url_temp,$src);
            }
            $alert = '<p class="msg_save">Producto registrado correctamente.</p>';
        } else {
            $alert = '<p class="msg_error">Se produjo un error al registrar el producto.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>Registro Productos</title>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-brands fa-product-hunt"></i> Registro Producto</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post" enctype="multipart/form-data">
                <label for="proveedor">Proveedor</label>
                <?php 
                $query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor 
                                                FROM proveedor 
                                                WHERE estado = 1 ORDER BY proveedor ASC");
                $result_proveedor = mysqli_num_rows($query_proveedor);
                
                mysqli_close($conection);
                ?>
                <select name="proveedor" id="proveedor">
                <?php
                if($result_proveedor > 0){
                    while($proveedor = mysqli_fetch_array($query_proveedor)){
                ?>
                 <option value="<?php echo $proveedor['codproveedor']; ?>">
                 <?php echo $proveedor['proveedor']; ?></option>
                <?php
                    }
                }
                ?>
                </select>
                <label for="producto">Producto</label>
                <input type="text" name="producto" id="producto" placeholder="Nombre producto">

                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Precio producto">

                <label for="cantidad">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad producto">

                <div class="photo">
                    <label for="foto">Imagen</label>
                    <div class="prevPhoto">
                        <span class="delPhoto notBlock"></span>
                        <label for="foto"></label>
                    </div>
                    <div class="upimg">
                        <input type="file" name="foto" id="foto">
                    </div>
                    <div id="form_alert"></div>
                </div>

                <button type="submit" class="btn_save">Guardar</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>