<?php
session_start();

include "../conexion.php";

if(!empty($_POST)){
    $alert='';
    if(empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])){
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    }else{

        $nit = $_POST['nit'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $usuarioid = $_SESSION['idUser'];

        $result = 0;

        if(is_numeric($nit) and $nit != 0){
            $query = mysqli_query($conection,"SELECT * FROM cliente 
            WHERE nit = '$nit'");
            $result = mysqli_fetch_array($query);
        }

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

        if($result > 0){
            $alert = '<p class="msg_error">La cédula ya existe.</p>';
        }else{
     //Preguntamos si la cedula consta de 10 digitos
     if(strlen($nit) == 10){
        
        //Obtenemos el digito de la region que sonclos dos primeros digitos
        $digito_region = substr($nit, 0, 2);
        
        //Pregunto si la region existe ecuador se divide en 24 regiones
        if( $digito_region >= 1 && $digito_region <=24 ){
          
          // Extraigo el ultimo digito
          $ultimo_digito = substr($nit, 9, 1);
          
          //Agrupo todos los pares y los sumo
          $pares = intval(substr($nit, 1, 1)) + intval(substr($nit, 3, 1)) + intval(substr($nit, 5, 1)) + intval(substr($nit, 7, 1));
          
          //Agrupo los impares, los multiplico por un factor de 2, si la resultante es > que 9 le restamos el 9 a la resultante
          $numero1 = substr($nit, 0,1);
          $numero1 = ($numero1 * 2);
          if( $numero1 > 9 ){ $numero1 = ($numero1 - 9); }

          $numero3 = substr($nit, 2,1);
          $numero3 = ($numero3 * 2);
          if( $numero3 > 9 ){ $numero3 = ($numero3 - 9); }

          $numero5 = substr($nit, 4,1);
          $numero5 = ($numero5 * 2);
          if( $numero5 > 9 ){ $numero5 = ($numero5 - 9); }

          $numero7 = substr($nit, 6,1);
          $numero7 = ($numero7 * 2);
          if( $numero7 > 9 ){ $numero7 = ($numero7 - 9); }

          $numero9 = substr($nit, 8,1);
          $numero9 = ($numero9 * 2);
          if( $numero9 > 9 ){ $numero9 = ($numero9 - 9); }

          $impares = $numero1 + $numero3 + $numero5 + $numero7 + $numero9;
          
          //Suma total
          $suma_total = ($pares + $impares);
         
          //extraemos el primero digito
          $primer_digito_suma =  substr(strval($suma_total), 0,1);
          
          //Obtenemos la decena inmediata
          $decena = (intval($primer_digito_suma) + 1)  * 10;
          
          //Obtenemos la resta de la decena inmediata - la suma_total esto nos da el digito validador
          $digito_validador = $decena - $suma_total;
          //print_r($digito_validador);
         
          //Si el digito validador es = a 10 toma el valor de 0
          if($digito_validador == 10){
            $digito_validador = 0;
          }
            //Validamos que el digito validador sea igual al de la cedula
          if($digito_validador == intval($ultimo_digito)){
            $query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,
                                        usuarioid) VALUES('$nit', '$nombre', '$telefono', '$direccion',
                                        '$usuarioid')");
            if($query_insert){
                $alert = '<p class="msg_save">Cliente guardado correctamente.</p>';
            }else{
                $alert = '<p class="msg_error">Se produjo un error al guardar el cliente.</p>';
            }

          }else{
            $alert = '<p class="msg_error">La cédula es incorrecta.</p>';
          }
          
        }else{
          // imprimimos en consola si la region no pertenece
	  $alert = '<p class="msg_error">La cedula no pertenece a ninguna region.</p>';
        }
     }else{
        //imprimimos en consola si la cedula tiene mas o menos de 10 digitos
	$alert = '<p class="msg_error">La cedula tiene menos de 10 Digitos.</p>';
     }
        }
    }
    mysqli_close($conection);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>Registro Cliente</title>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-sharp fa-solid fa-users"></i> Registro Cliente</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ' '; ?></div>

            <form action="" method="post">
                <label for="nit">Número Cédula</label>
                <input type="text" name="nit" id="nit" placeholder="Número Cédula">              
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre Cliente">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa">

                <button type="submit" class="btn_save">Guardar</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>