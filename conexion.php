<?php
$host = 'localhost';
$user = 'root';
$password = '12345678';
$db = 'facturacion';

$conection = @mysqli_connect($host,$user,$password,$db);

if(!$conection){
    echo "Error en la conexion"; 
}
?>