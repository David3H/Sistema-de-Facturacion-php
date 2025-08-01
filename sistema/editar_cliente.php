<?php
session_start();

include "../conexion.php";

if(!empty($_POST)){
    $alert='';
    if(empty($_POST['proveedor']) || empty($_POST['contacto']) || 
    empty($_POST['telefono']) || empty($_POST['direccion'])){
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    }else{

        $idproveedor = $_POST['id'];
        $proveedor = $_POST['proveedor'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];

        $result = 0;

        if(is_numeric($nit) && $nit != 0){
        $query = mysqli_query($conection,"SELECT * FROM cliente 
                            WHERE (nit = '$nit' AND idcliente != '$idCliente')");
        $result = mysqli_fetch_array($query);
        //$result = count($result);
        }

        if($result > 0){
            $alert = '<p class="msg_error">El RUC o el cliente ya existe, ingrse otro.</p>';
        }else{

            if($nit == ''){
                $nit = 0;
            }
            $query_update = mysqli_query($conection, "UPDATE cliente
            SET nit = '$nit', nombre = '$nombre', telefono = '$telefono',
            direccion = '$direccion'
            WHERE idcliente = '$idCliente'");

            if($query_update){
                $alert = '<p class="msg_save">Cliente modificado correctamente.</p>';
            }else{
                $alert = '<p class="msg_error">Se produjo un error al modificar el cliente.</p>';

            }
        }
    }
}
//Mostar recuperacion de dastos  
if(empty($_REQUEST['id'])){
    header('Location: listado_clientes.php');
    mysqli_close($conection);
}
$idcliente =$_REQUEST['id'];

$sql = mysqli_query($conection,"SELECT * FROM cliente WHERE idcliente = $idcliente
AND estado = 1 ");
mysqli_close($conection);
$result_sql = mysqli_num_rows($sql);

if($result_sql == 0){
    header('Location: listado_clientes.php');
}else{

    while($data = mysqli_fetch_array($sql)){
        $idcliente = $data['idcliente'];
        $nit = $data['nit'];
        $nombre = $data['nombre'];
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
    <title>Acturalizar Cliente</title>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-regular fa-pen-to-square"></i> Actualizar Cliente</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
                <label for="nit">Número RUC</label>
                <input type="number" name="nit" id="nit" placeholder="Número RUC"
                value="<?php echo $nit; ?>">              
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Cliente"
                value="<?php echo $nombre; ?>">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono"
                value="<?php echo $telefono; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa"
                value="<?php echo $direccion; ?>">

                <button type="submit" class="btn_save">Actualizar</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>