<?php
session_start();
if($_SESSION['rol'] != 1){
    header("location: ./");
}

include "../conexion.php";

if(!empty($_POST)){
    $alert='';
    if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario'])
    || empty($_POST['rol'])){
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    }else{

        $idUsuario = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['correo'];
        $user = $_POST['usuario'];
        $clave = md5($_POST['clave']);
        $rol = $_POST['rol'];

        $query = mysqli_query($conection,"SELECT * FROM usuario 
                            WHERE (usuario = '$user' AND idusuario != '$idUsuario') 
                            OR (correo = '$email' AND idusuario != '$idUsuario')");

        $result = mysqli_fetch_array($query);
        //$result = count($result);

        if($result > 0){
            $alert = '<p class="msg_error">El correo o el usuario ya existe.</p>';
        }else{
            if(empty($_POST['clave'])){
                $query_update = mysqli_query($conection, "UPDATE usuario
                SET nombre = '$nombre', correo = '$email', usuario = '$user',
                rol = '$rol'
                WHERE idusuario = '$idUsuario'");
            }else{
                $query_update = mysqli_query($conection, "UPDATE usuario
                SET nombre = '$nombre', correo = '$email', usuario = '$user',
                clave = '$clave', rol = '$rol'
                WHERE idusuario = '$idUsuario'");
            }

            if($query_update){
                $alert = '<p class="msg_save">Usuario modificado correctamente.</p>';
            }else{
                $alert = '<p class="msg_error">Se produjo un error al modificar el usuario.</p>';

            }
        }
    }
}
//Mostar recuperacion de dastos 
if(empty($_REQUEST['id'])){
    header('Location: listado_usuarios.php');
    mysqli_close($conection);
}
$iduser =$_REQUEST['id'];

$sql = mysqli_query($conection,"SELECT u.idusuario, u.nombre, u.correo, u.usuario, (u.rol) as
                    idrol, (r.rol) as rol
                    FROM usuario u
                    INNER JOIN rol r
                    on u.rol = r.idrol
                    WHERE idusuario = $iduser AND estado = 1");
mysqli_close($conection);
$result_sql = mysqli_num_rows($sql);

if($result_sql == 0){
    header('Location: listado_usuarios.php');
}else{
    $option = '';
    while($data = mysqli_fetch_array($sql)){
        $iduser = $data['idusuario'];
        $nombre = $data['nombre'];
        $correo = $data['correo'];
        $usuario = $data['usuario'];
        $idrol = $data['idrol'];
        $rol = $data['rol'];

        if($idrol == 1){
            $option = '<option value = "'.$idrol.'" select> '.$rol.' </option>';
        }elseif($idrol == 2){
            $option = '<option value = "'.$idrol.'" select> '.$rol.' </option>';
        }elseif($idrol == 3){
            $option = '<option value = "'.$idrol.'" select> '.$rol.' </option>';
        }

    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>Acturalizar Usuario</title>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-regular fa-pen-to-square"></i> Actualizar Usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $iduser; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Usuario" 
                value ="<?php echo $nombre;?>">
                <label for="correo">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" placeholder="Correo Electrónico"
                value ="<?php echo $correo;?>">
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario"
                value ="<?php echo $usuario;?>">
                <label for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave" placeholder="Contraseña">
                <label for="tipo_usuario">Tipo Usuario</label>
 
                <?php
                include "../conexion.php";
                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
                mysqli_close($conection);
                $resultado_rol = mysqli_num_rows($query_rol);
                ?>
                <select name="rol" id="rol" class = "notItemOne">
                    <?php 
                    echo $option;
                    if($resultado_rol > 0){

                        while($rol = mysqli_fetch_array($query_rol)){
                    ?>    
                            <option value="<?php echo $rol["idrol"];?>">
                            <?php echo $rol["rol"]?></option>
                    <?php
                        }
                        
                    }
                    ?>
                </select>
                <button type="submit" class="btn_save">Editar</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>