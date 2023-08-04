<?php
session_start();

include "../conexion.php";

$busqueda = '';
$fecha_de = '';
$fecha_a = '';

if(isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] == ''){
    header("location: ventas.php");
}

if(isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])){
    if($_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == ''){
        header("location: ventas.php");
    }
}

if(!empty($_REQUEST['busqueda'])){
    if(!is_numeric($_REQUEST['busqueda'])){
        header("location: ventas.php");
    }
    $busqueda = strtolower($_REQUEST['busqueda']);
    $where = "nofactura = $busqueda";
    $buscar = "busqueda = $busqueda";
}

if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
    $fecha_de = $_REQUEST['fecha_de'];
    $fecha_a = $_REQUEST['fecha_a'];

    $buscar = '';
    
    if($fecha_de > $fecha_a){
        header("location: ventas.php");
    }else if($fecha_de == $fecha_a){

        $where = "fecha LIKE '$fecha_de%'";
        $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
    }else{
        $f_de = $fecha_de.' 00:00:00';
        $f_a = $fecha_a.' 23:59:59';
        $where = "fecha BETWEEN '$f_de' AND '$f_a'";
        $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
    <title>Listado Ventas</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">

        <h1>Listado</h1>
        <a href="nueva_venta.php" class="btn_new">
            <i class="fas fa-plus"></i> Venta</a>

        <form action="buscar_venta.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" 
            placeholder="Buscar" value="<?php echo $busqueda; ?>">
            <button type="submit" class="btn_search">
                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_venta.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
                <label> A </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
                <button type="submit" class="btn_view">
                    <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <table>
            <tr>
                <th>Número Factura</th>
                <th>Fecha/Hora</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th class="textright">Total factura</th>
                <th class="textright">Acciones</th>
            </tr>
            <?php
            //Para el paginador
            $sql_registe = mysqli_query($conection, "SELECT COUNT(*) as total_registro
                        FROM factura WHERE $where");

            $result_register = mysqli_fetch_array($sql_registe);
            $total_registro = $result_register['total_registro'];

            $por_pagina = 10;
            if (empty($_GET['pagina'])) {
                $pagina = 1;
            } else {
                $pagina = $_GET['pagina'];
            }

            $desde = ($pagina - 1) * $por_pagina;
            $total_paginas = ceil($total_registro / $por_pagina);

            $query = mysqli_query($conection, "SELECT f.nofactura, f.fecha, f.totalfactura, f.codcliente, f.estado,
                                        u.nombre as vendedor,
                                        cl.nombre as cliente
                                        FROM factura f
                                        INNER JOIN usuario u
                                        ON f.usuario = u.idusuario
                                        INNER JOIN cliente cl
                                        ON f.codcliente = cl.idcliente
                                        WHERE $where AND f.estado != 10
                                        ORDER BY f.fecha DESC LIMIT $desde,$por_pagina");

            mysqli_close($conection);
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
                    if ($data["estado"] == 1) {
                        $estado = '<span class="pagada" style="color: #fff; background: #60a756; border-radius: 5px; padding: 4px 15px;">Pagado</span>';
                    } else {
                        $estado = '<span class="anulada" style="color: #fff; background: #f36a6a; border-radius: 5px; padding: 4px 15px;">Anulado</span>';
                    }
            ?>

                    <tr id="<?Php echo $data['nofactura']; ?>">
                        <td><?Php echo $data['nofactura']; ?></td>
                        <td><?Php echo $data['fecha']; ?></td>
                        <td><?Php echo $data['cliente']; ?></td>
                        <td><?Php echo $data['vendedor']; ?></td>
                        <td class="estado"><?Php echo $estado; ?></td>
                        <td class="textright totalfactura">$<?Php echo $data['totalfactura']; ?></td>

                        <td>
                            <div class="div_acciones">
                                <div>
                                    <button class="btn_view view_factura" type="button" cl="<?Php echo $data['codcliente']; ?>" f="<?Php echo $data['nofactura']; ?>">
                                        <i class="fas fa-eye"></i></button>
                                </div>

                                <?php
                                if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
                                    if ($data['estado'] == 1) {
                                ?>
                                        <div class="div_factura">
                                            <button class="btn_anular anular_factura" fac="<?Php echo $data['nofactura']; ?>">
                                                <i class="fas fa-ban"></i></button>
                                        </div>
                                    <?php } else { ?>
                                        <div class="div_factura">
                                            <button type="button" class="btn_anular inactive">
                                                <i class="fas fa-ban"></i></button>
                                        </div>
                                <?php }
                                }
                                ?>

                            </div>
                        </td>
                    </tr>
            <?php
                }
            }

            ?>
        </table>
        <div class="paginador">
            <?php
            if ($pagina != 1) {
            ?>
                <a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>">
                    <i class="fa-sharp fa-solid fa-angles-left" style="color: #3d7ba8"></i></a>
                <a href="?pagina=<?php echo $pagina - 1; ?>&<?php echo $buscar; ?>">
                    <i class="fa-sharp fa-solid fa-chevron-left" style="color: #3d7ba8"></i></a>
            <?php
            }
            ?>

            <?php
            for ($i = 1; $i <= $total_paginas; $i++) {
                if ($i == $pagina) {
                    echo '<a class="activo">' . $i . '</a>';
                } else {
                    echo '<a href="?pagina=' . $i . '&'.$buscar.'">' . $i . '</a>';
                }
            }
            if ($pagina != $total_paginas) {
            ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>&<?php echo $buscar; ?>">
                    <i class="fa-solid fa-angle-right" style="color: #3d7ba8"></i></a>
                <a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>">
                    <i class="fa-solid fa-angles-right" style="color: #3d7ba8"></i></a>
            <?php } ?>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>