<?php

$alert = '';
session_start();
if (!empty($_SESSION['active'])) {
    header('location: sistema/');
} else {

    if (!empty($_POST)) {
        if (empty($_POST['clave']) && empty($_POST['usuario'])) {
            $alert = 'Ingrese su usuario y contraseña';
        } else if (empty($_POST['clave'])) {
            $alert = 'Ingrese su contraseña';
        } else if (empty($_POST['usuario'])) {
            $alert = 'Ingrese su usuario';
        } else {
            require_once "conexion.php";
            $user = mysqli_real_escape_string($conection,$_POST['usuario']);
            $pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

            //Función para validar la cédula 
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

            //Función para la validación de la contraseña
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


            $query = mysqli_query($conection, "SELECT * FROM 
            usuario WHERE usuario= '$user' AND clave= '$pass'");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);

            if ($result > 0) {
                $data = mysqli_fetch_array($query);
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $data['idusuario'];
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['email'] = $data['correo'];
                $_SESSION['user'] = $data['usuario'];
                $_SESSION['rol'] = $data['rol'];

                header('location: sistema/');
            } else {
                $alert = 'La contraseña o el usuario es incorrecto';
                session_destroy();
            }


            
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
    <title>Login | Sistema Facturación</title>
</head>

<body> 
    <section id="container">
        <form action="" method="post"> 
            <h3>Inicio Sesión</h3>
            <img src="img/registro.jpg" alt="Login">

            <input type="text" name="usuario" placeholder="Usuario">
            <input type="password" name="clave" placeholder="Contraseña">
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
            <input type="submit" value="INGRESAR">

        </form>
    </section>
</body>

</html>