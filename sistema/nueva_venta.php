<?php
session_start();

include "../conexion.php";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
    <title>Nueva Venta</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>Datos Cliente</h4>
                <a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i> Nuevo</a>
            </div>
            <form name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required>
                <div class="wd5">
                    <label>Seleccionar</label>
                    <button class="btn_view listar_clientes"><i class="fas fa-plus"></i></button> 
                </div>
                <div class="wd20">
                    <label>Cédula</label>
                    <input type="text" name="nit_cliente" id="nit_cliente"> 
                </div>
                <div class="wd30">
                    <label>Nombre</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>
                <div class="wd20">
                    <label>Teléfono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                </div>
                <div class="wd100">
                    <label>Dirección</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                </div>
                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save">Guardar</button>
                </div>
            </form>
        </div>
        
        <?php 
        $total = 0;
        $fechaActual = date('d/m/Y');
        $query = mysqli_query($conection, "SELECT COUNT(*) as facturas
                                FROM factura 
                                WHERE estado != 10");
        
        mysqli_close($conection);
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
                    $total = $data['facturas'];
                }
            }
        ?>
        <div class="datos_venta">
            <h4>Datos de venta</h4>
            <div class="datos">
                <div class="wd50">
                    <p>Factura No. <?php echo ($total + 1) ;?></p>
                    <p>Fecha: <?php echo $fechaActual ;?></p>
                    <p>Vendedor: <?php echo $_SESSION['nombre']?></p>
                </div>
                <div class="wd50">
                    <label>Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta">
                            <i class="fas fa-ban"></i> Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display: none;">
                            <i class="far fa-edit"></i> Procesar</a>
                    </div>
                </div>
            </div>
        </div>

        <table class="tbl_venta">
            <thead>
                <tr>
                    <th>Añadir</th>
                    <th>Descripción</th>
                    <th>Existencia</th>
                    <th width="100px">Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Acción</th>
                </tr>
                <tr>
                    <td><button class="btn_view lista_productos"><i class="fas fa-plus"></i></button></td>
                    <input type="hidden" name="txt_cod_producto" id="txt_cod_producto">
                    <td id="txt_descripcion">-</td>
                    <td id="txt_existencia">-</td>
                    <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="
                    0" min="1" disabled></td>
                    <td id="txt_precio" class="textright">0.00</td>
                    <td id="txt_precio_total" class="textright">0.00</td>
                    <td><a href="#" id="add_product_venta" class="link_add">Agregar</a></td>
                </tr>
                <tr>
                    <th>Código</th>
                    <th colspan="2">Descripción</th>
                    <th>Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="detalle_venta">
                <!----Contenido Ajax--->
            </tbody>
            <tfoot id="detalle_totales">
                 <!----Contenido Ajax--->
            </tfoot>
        </table>
    </section>

    <?php include "includes/footer.php"; ?>

    <script type="text/javascript">
        $(document).ready(function(){
            var usuarioid = '<?php echo $_SESSION['idUser']?>';
            serchForDetalle(usuarioid);
        });
    </script>
</html>