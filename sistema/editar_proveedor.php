<?php
session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    header("location: ./");
}

include "../conexion.php";

if (!empty($_POST)) {
    $alert = '';
    if(empty($_POST['proveedor']) || empty($_POST['contacto']) || 
    empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    } else {

        $idproveedor = $_POST['id'];
        $proveedor = $_POST['proveedor'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];

        $query_update = mysqli_query($conection, "UPDATE proveedor
        SET proveedor = '$proveedor', contacto = '$contacto', telefono = '$telefono',
        direccion = '$direccion'
        WHERE codproveedor = '$idproveedor'");

        if($query_update){
            $alert = '<p class="msg_save">Proveedor modificado correctamente.</p>';
        }else{
            $alert = '<p class="msg_error">Se produjo un error al modificar el proveedor.</p>';

        }
        
    }
}
//Mostar recuperacion de dastos 
if (empty($_REQUEST['id'])) {
    header('Location: listado_proveedores.php');
    mysqli_close($conection);
}
$idproveedor = $_REQUEST['id'];

$sql = mysqli_query($conection, "SELECT * FROM proveedor WHERE codproveedor = $idproveedor
AND estado = 1");
mysqli_close($conection);
$result_sql = mysqli_num_rows($sql);

if ($result_sql == 0) {
    header('Location: listado_proveedores.php');
} else {

    while ($data = mysqli_fetch_array($sql)) {
        $idproveedor = $data['codproveedor'];
        $proveedor = $data['proveedor'];
        $contacto = $data['contacto'];
        $telefono = $data['telefono'];
        $direccion = $data['direccion'];
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>Acturalizar Proveedores</title>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-regular fa-pen-to-square"></i> Actualizar Proveedor</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idproveedor; ?>">
                <label for="proveedor">Proveedor</label>
                <input type="text" name="proveedor" id="proveedor" placeholder="Nombre empresa" value="<?php echo $proveedor; ?>">
                <label for="contacto">Contacto Proveedor</label>
                <input type="text" name="contacto" id="contacto" placeholder="Nombre encargado" value="<?php echo $contacto; ?>">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion; ?>">

                <button type="submit" class="btn_save">Actualizar</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>