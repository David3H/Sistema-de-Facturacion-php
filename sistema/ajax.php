<?php

use LDAP\Result;

include "../conexion.php";

//print_r($_POST);exit;

session_start();

if(!empty($_POST)){

     //Extraer datos del producto
    if($_POST['action'] == 'infoProducto'){
        $producto_id = $_POST['producto'];

        $query = mysqli_query($conection, "SELECT codproducto,descripcion, existencia, precio FROM producto
        WHERE codproducto = $producto_id AND estado = 1  AND existencia > 0");

        mysqli_close($conection);

        $result = mysqli_num_rows($query);

        if ($result > 0) {
            $data = mysqli_fetch_assoc($query);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo 'error';
        exit;
    }

    //Extraer datos del producto para listar en ventas
    if($_POST['action'] == 'infoListProducto'){

        $query = mysqli_query($conection, "SELECT codproducto,descripcion, existencia, precio FROM producto
        WHERE estado = 1  AND existencia > 0
        ORDER BY codproducto DESC");
        $detalle = '';
        $arrayData = array();
        mysqli_close($conection);

        $result = mysqli_num_rows($query);
        if ($result > 0) {
            while($data = mysqli_fetch_assoc($query)){
                $detalle .= '<tr>
                <td><button class="btn_view" onclick="coloseModal(); resivirId('.$data['codproducto'].'); ">+</button></td>
                <td>'.$data['descripcion'].'</td>
                <td>'.$data['existencia'].'</td>
                <td>'.$data['precio'].'</td>
            </tr>';
            }
            $arrayData['detalles'] = $detalle;
            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
            exit;
        }
       
        echo 'error';
        exit;
    }

    //Extraer datos de los clientes para listar en ventas
    if($_POST['action'] == 'infoListCliente'){

        $where = "";
        if (isset($_GET['enviar'])) {
            $busqueda = $_GET['busqueda'];

            if (isset($_GET['busqueda'])) {
                $where = "p.codproducto LIKE '%$busqueda%' OR
                p.descripcion LIKE '%$busqueda%' LIKE
                pr.proveedor '%$busqueda%' AND";
            }
        }

        $query = mysqli_query($conection, "SELECT nit, nombre, telefono, direccion FROM cliente 
        WHERE $where estado = 1 AND idcliente > 1");
        $detalle = '';
        $arrayData = array();
        mysqli_close($conection);

        $result = mysqli_num_rows($query);
        if ($result > 0) {
            while($data = mysqli_fetch_assoc($query)){
                $detalle .= '<tbody>
            <tr>
                <td><button class="btn_view" onclick="coloseModal(); resivirCliente('.$data['nit'].'); ">+</button></td>
                <td>'.$data['nit'].'</td>
                <td>'.$data['nombre'].'</td>
                <td>'.$data['direccion'].'</td>
            </tr>
            </tbody>';
            }
            $arrayData['detallesCli'] = $detalle;
            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);
            exit;
        }
       
        echo 'error';
        exit;
    }

     //Agregar producto a entrada
     if($_POST['action'] == 'addProduct'){

        if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id'])){

            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];
            $producto_id = $_POST['producto_id'];
            $usuario_id = $_SESSION['idUser'];

            $query_insert = mysqli_query($conection, "INSERT INTO entradas(codproducto,cantidad,precio,
                                                idusuario)VALUES($producto_id,$cantidad,$precio,
                                                $usuario_id)"); 
            if($query_insert){
            
            //Ejecutar procedimiento almacenado
            try{
             $query_upd = mysqli_query($conection, "CALL actualizar_precio_producto(
                                                    $cantidad,$precio,$producto_id)");
            $result_pro = mysqli_num_rows($query_upd);
             }catch(Exception){
                mysqli_rollback($conection);
             }
                        if($result_pro > 0){
                                
                            $data = mysqli_fetch_assoc($query_upd);
                            $data['producto_id'] = $producto_id;
                            
                            echo json_encode($data,JSON_UNESCAPED_UNICODE);
                            exit;
                        }
            }else{
                    echo 'error';
            }
                mysqli_close($conection);

        }else{
                echo 'error';
        }
            exit;


    }
    
    //Eliminar producto
    if($_POST['action'] == 'delProduct'){
        
       if(empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])){
        echo 'error';
       }else{

        $idproducto = $_POST['producto_id'];

        $query_delete = mysqli_query($conection, "UPDATE producto SET estado = 0
                                     WHERE codproducto = $idproducto");
        mysqli_close($conection);
    
        if($query_delete){
            echo 'ok';
        }else{
            echo 'error';
        }

       }
        echo 'error';
        exit;
    }
    
    //Buscar cliente
    if($_POST['action'] == 'searchCliente'){
        
        if(!empty($_POST['cliente'])){
            $cedula = $_POST['cliente'];

            $query = mysqli_query($conection, "SELECT * FROM cliente 
                                  WHERE nit LIKE '$cedula' and estado = 1");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);

            $data = '';
            if($result > 0){
                $data = mysqli_fetch_assoc($query);
            }else{
                $data = 0;
            }
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

     //Registro cliente - Ventas
     if($_POST['action'] == 'addCliente'){
        
        $cedula = $_POST['nit_cliente'];
        $nombre = $_POST['nom_cliente'];
        $telefono = $_POST['tel_cliente'];
        $direccion = $_POST['dir_cliente'];
        $usuario_id = $_SESSION['idUser'];

        $query_insert = mysqli_query($conection,"INSERT INTO cliente(nit,nombre,telefono,direccion,
                                        usuarioid) VALUES('$cedula', '$nombre', '$telefono', '$direccion',
                                        '$usuario_id')");

        
        if($query_insert){
            $codCliente = mysqli_insert_id($conection);
            $msg = $codCliente;
        }else{
            $msg = 'error';
        }
        mysqli_close($conection);
        echo $msg;
        exit;
     }

     //Agregar producto al detalle temporal
     if($_POST['action'] == 'addProductoDetalle'){
        
        if(empty($_POST['producto']) || empty($_POST['cantidad'])){

            echo 'error';
        }else{
            $codproducto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $token1 = md5($_SESSION['idUser']);
            
            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);
            
            //
            $query_comp = mysqli_query($conection, "SELECT codproducto, cantidad FROM detalle_temp");
            while($data = mysqli_fetch_assoc($query_comp)){
                if($data['codproducto'] == $_POST['producto']){
                    echo 'error';
                    mysqli_close($conection);
                    exit;
                }
            }
            try{

            $query_detalle_temp = mysqli_query($conection, "CALL add_detalle_temp($codproducto, $cantidad, '$token1')");
            $result = mysqli_num_rows($query_detalle_temp);
            }catch(Exception){
                mysqli_rollback($conection);
            }
            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){
                
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query_detalle_temp)){

                    $precioTotal = number_format($data['cantidad'] * $data['precio_venta'], 2, '.', ',');
                    $sub_total = number_format($sub_total + $precioTotal, 2, '.', ',');
                    $total = number_format($total + $precioTotal, 2, '.', ',');

                    $detalleTabla .= '
                            <tr>
                                <td>'.$data['codproducto'].'</td>
                                <td colspan="2">'.$data['descripcion'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_venta'].'</td>
                                <td class="textright">'.$precioTotal.'</td>
                                <td class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault();
                                    del_product_detalle('.$data['correlativo'].'); "><i class="far fa-trash-alt"></i></a>
                                    <a class="" href="#" onclick="enviarDatosEdit('.$data['correlativo'].',
                                    '.$data['codproducto'].', '.$data['cantidad'].' );
                                    "><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>';
                }

                $impuesto = number_format($sub_total * ($iva / 100), 2, '.', ',');
                $tl_sniva = number_format($sub_total - $impuesto, 2, '.', ',');
                $total = number_format($tl_sniva + $impuesto, 2, '.', ',');

                $detalleTotales = '
                                    <tr>
                                        <td colspan="5" class="textright">SUBTOTAL SIN IMPUESTOS</td>
                                        <td class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.round($iva).'%)</td>
                                        <td class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">VALOR TOTAL</td>
                                        <td class="textright">'.$total.'</td>
                                    </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
     }

     //Extraer datos del detalle temp
     if($_POST['action'] == 'serchForDetalle'){
       
        if(empty($_POST['user'])){

            echo 'error';
        }else{

            $token = md5($_SESSION['idUser']);

            $query = mysqli_query($conection, "SELECT tmp.correlativo,
                                                      tmp.token_user,
                                                      tmp.cantidad,
                                                      tmp.precio_venta,
                                                      p.codproducto, 
                                                      p.descripcion
                                                FROM detalle_temp tmp
                                                INNER JOIN producto p
                                                ON tmp.codproducto = p.codproducto
                                                WHERE token_user = '$token'");
            
            $result = mysqli_num_rows($query);

            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){
               
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query)){

                    $precioTotal = number_format($data['cantidad'] * $data['precio_venta'], 2, '.', ',');
                    $sub_total = number_format($sub_total + $precioTotal, 2, '.', ',');
                    $total = number_format($total + $precioTotal, 2, '.', ',');

                    $detalleTabla .= '
                            <tr>
                                <td>'.$data['codproducto'].'</td>
                                <td colspan="2">'.$data['descripcion'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_venta'].'</td>
                                <td class="textright">'.$precioTotal.'</td>
                                <td class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault();
                                    del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
                                    <a class="" href="#" onclick="enviarDatosEdit('.$data['correlativo'].',
                                    '.$data['codproducto'].', '.$data['cantidad'].' );
                                    "><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>';
                }

                $impuesto = number_format($sub_total * ($iva / 100), 2, '.', ',');
                $tl_sniva = number_format($sub_total - $impuesto, 2, '.', ',');
                $total = number_format($tl_sniva + $impuesto, 2, '.', ',');

                $detalleTotales = '
                                    <tr>
                                        <td colspan="5" class="textright">SUBTOTAL SIN IMPUESTOS</td>
                                        <td class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.round($iva).'%)</td>
                                        <td class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">VALOR TOTAL</td>
                                        <td class="textright">'.$total.'</td>
                                    </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
     }

      //Eliminar datos del detalle temp
      if($_POST['action'] == 'del_product_detalle'){

        if(empty($_POST['id_detalle'])){

            echo 'error';
        }else{

            $id_detalle = $_POST['id_detalle'];
            $token = md5($_SESSION['idUser']);

            $query_iva = mysqli_query($conection, "SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);
            try{
            $query_detalle_temp = mysqli_query($conection, "CALL del_detalle_temp($id_detalle, '$token')");
            $result = mysqli_num_rows($query_detalle_temp);
            }catch(Exception){
                mysqli_rollback($conection);
            }
            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){

                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }

                while($data = mysqli_fetch_assoc($query_detalle_temp)){

                    $precioTotal = number_format($data['cantidad'] * $data['precio_venta'], 2, '.', ',');
                    $sub_total = number_format($sub_total + $precioTotal, 2, '.', ',');
                    $total = number_format($total + $precioTotal, 2, '.', ',');

                    $detalleTabla .= '
                            <tr>
                                <td>'.$data['codproducto'].'</td>
                                <td colspan="2">'.$data['descripcion'].'</td>
                                <td class="textcenter">'.$data['cantidad'].'</td>
                                <td class="textright">'.$data['precio_venta'].'</td>
                                <td class="textright">'.$precioTotal.'</td>
                                <td class="">
                                    <a class="link_delete" href="#" onclick="event.preventDefault();
                                    del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
                                    <a class="" href="#" onclick="enviarDatosEdit('.$data['correlativo'].',
                                    '.$data['codproducto'].', '.$data['cantidad'].' );
                                    "><i class="fa-sharp fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>';
                }

                $impuesto = number_format($sub_total * ($iva / 100), 2, '.', ',');
                $tl_sniva = number_format($sub_total - $impuesto, 2, '.', ',');
                $total = number_format($tl_sniva + $impuesto, 2, '.', ',');

                $detalleTotales = '
                                    <tr>
                                        <td colspan="5" class="textright">SUBTOTAL SIN IMPUESTOS</td>
                                        <td class="textright">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">IVA ('.round($iva).'%)</td>
                                        <td class="textright">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="textright">VALOR TOTAL</td>
                                        <td class="textright">'.$total.'</td>
                                    </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
      }

      //Anular Venta
      if($_POST['action'] == 'anularVenta'){

        $token = md5($_SESSION['idUser']);

            $query_del = mysqli_query($conection, "DELETE FROM detalle_temp WHERE token_user = '$token'");
            mysqli_close($conection);

            if($query_del){
                echo 'ok';
            }else{
                echo 'error';
            }
            exit;
      }

        
       //Procesar Venta
       if($_POST['action'] == 'procesarVenta'){

        if(empty($_POST['codcliente'])){
            $codcliente = 1;
        }else{
            $codcliente = $_POST['codcliente'];
        }

        $token = md5($_SESSION['idUser']);
        $usuario = $_SESSION['idUser'];

        $query = mysqli_query($conection, "SELECT * FROM detalle_temp WHERE token_user = '$token'");
        $result = mysqli_num_rows($query);

        if($result > 0){
            try{
            $query_procesar = mysqli_query($conection, "CALL procesar_venta($usuario,$codcliente,'$token')");
            $result_detalle = mysqli_num_rows($query_procesar);
            }catch(Exception){
                mysqli_rollback($conection);
            }
            if($result_detalle > 0){
                $data = mysqli_fetch_assoc($query_procesar);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }
        }else{
            echo 'error';
        }
        mysqli_close($conection);
        exit;
       }

       //INFO Factura
       if($_POST['action'] == 'infoFactura'){
        if(!empty($_POST['nofactura'])){

            $nofactura = $_POST['nofactura'];

            $query = mysqli_query($conection, "SELECT * FROM factura WHERE nofactura = '$nofactura' AND 
            estado = 1");

            mysqli_close($conection);

            $result = mysqli_num_rows($query);
            if($result > 0){
                $data = mysqli_fetch_assoc($query);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo 'error';
        exit;
       }

       //Anular Factura
       if($_POST['action'] == 'anularFactura'){
        if(!empty($_POST['noFactura'])){

            $noFactura = $_POST['noFactura'];
            try{
            $query_anular = mysqli_query($conection, "CALL anular_factura($noFactura)");
            mysqli_close($conection);
            $result = mysqli_num_rows($query_anular);
            }catch(Exception){
                mysqli_rollback($conection);
            }
            if($result > 0){
                $data = mysqli_fetch_assoc($query_anular);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo 'error';
        exit;
       }

}
exit;

?>