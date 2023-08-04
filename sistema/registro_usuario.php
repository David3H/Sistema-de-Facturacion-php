<?php
session_start();
if($_SESSION['rol'] != 1){
    header("location: ./");
}
 
include "../conexion.php";

if(!empty($_POST)){
    $alert='';
    if(empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario'])
    || empty($_POST['clave']) || empty($_POST['rol'])){
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    }else{

        $nombre = $_POST['nombre'];
        $email = $_POST['correo'];
        $user = $_POST['usuario'];
        $clave = md5($_POST['clave']);
        $rol = $_POST['rol'];

        $query = mysqli_query($conection,"SELECT * FROM usuario 
                            WHERE usuario = '$user' OR correo = '$email'");
       
        $result = mysqli_fetch_array($query);

        if($result > 0){
            $alert = '<p class="msg_error">El correo o el usuario ya existe.</p>';
        }else{

            //Validación de la cédula
            function cedula($cedula) {
                $sum = 0;
                $sumi = 0;
                for ($i = 0; $i < strlen($cedula) - 2; $i++) {
                    if ($i % 2 == 0) {
                        $sum += substr($cedula, $i + 1, 1);
                    }
                }
                $j = 0;
                while ($j < strlen($cedula) - 1) {
                    $b = substr($cedula, $j, 1);
                    $b = $b * 2;
                    if ($b > 9) {
                        $b = $b - 9;
                    }
                    $sumi += $b;
                    $j = $j + 2;
                }
                $t = $sum + $sumi;
                $res = 10 - $t % 10;
                $aux = substr($cedula, 9, 9);
                if ($res == $aux) {
                    return 1;
                } else {
                    return 0;
                }
            }
            
            //Validación de la contraseña
            function validar_clave($claves,&$error_clave){
                if(strlen($claves) < 4){
                   $error_clave = "La clave debe tener al menos 4 caracteres";
                   return false;
                }
                if(strlen($claves) > 16){
                   $error_clave = "La clave no puede tener más de 16 caracteres";
                   return false;
                }
                if (!preg_match('`[a-z]`',$claves)){
                   $error_clave = "La clave debe tener al menos una letra minúscula";
                   return false;
                }
                if (!preg_match('`[A-Z]`',$claves)){
                   $error_clave = "La clave debe tener al menos una letra mayúscula";
                   return false;
                }
                if (!preg_match('`[0-9]`',$claves)){
                   $error_clave = "La clave debe tener al menos un caracter numérico";
                   return false;
                }
                if (!preg_match('`[#$@_%&]`',$claves)){
                    $error_clave = "La clave debe tener al menos un caracter especial";
                    return false;
                 }
                $error_clave = "";
                return true;
             }

             $error_encontrado="";
            if (validar_clave($_POST['clave'], $error_encontrado)){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if(cedula($user)){
            $query_insert = mysqli_query($conection,"INSERT INTO 
                                         usuario(nombre,correo,usuario,clave,rol) 
                              VALUES('$nombre','$email','$user','$clave ','$rol')");
              if($query_insert){
                $alert = '<p class="msg_save">Usuario creado correctamente.</p>';
            }else{
                $alert = '<p class="msg_error">Se produjo un error al crear el usuario.</p>';

            }
            }else{
                $alert = '<p class="msg_error">La cedula no es valida o es incorrecta.</p>';
            }
        }else{ 
            $alert = '<p class="msg_error">Ingrese un correo electrónico valido.</p>';
        }

        }else{
            $alert = '<p class="msg_error">'.$error_encontrado.'</p>';
         }
        }
        //mysqli_close($conection);
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" type="text/css" href="./css/mystile.css">
    <title>Registro Usuario</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-sharp fa-solid fa-users"></i> Registro Usuario</h1> 
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Usuario">
                <label for="correo">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" placeholder="Correo Electrónico">
                <label for="usuario">Cédula</label>
                <input type="text" name="usuario" id="usuario" placeholder="Cédula">
                <label for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave" placeholder="Contraseña">
                <label for="tipo_usuario">Tipo Usuario</label>

                <?php
                $query_rol = mysqli_query($conection, "SELECT * FROM rol");
               
                $resultado_rol = mysqli_num_rows($query_rol);
                ?>
                <select name="rol" id="rol">
                    <?php 
                    if($resultado_rol > 0){

                        while($rol = mysqli_fetch_array($query_rol)){
                    ?>    
                            <option value="<?php echo $rol["idrol"];?>"><?php echo $rol["rol"]?></option>
                    <?php
                        }
                        
                    }
                    mysqli_close($conection);
                    ?>
                </select>
                 
                <button type="submit" class="btn_save">Crear usuario</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>