<?php
session_start();

include "../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
    <title>Listado Productos</title>
</head>

<body>
    <?php include "includes/header.php"; ?> 
    <section id="container">

        <h1>Listado</h1>
        <a href="registro_producto.php" class="btn_new">
            <i class="fa-sharp fa-solid fa-cart-plus"></i> Producto</a>

        <form action="buscar_producto.php" method="get" class="form_search">
            <input type="text" class="light-table-filter" data-table="table_id" name="busqueda" id="busqueda" placeholder="Buscar">
            <button type="submit" name="enviar" class="btn_search">
                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
            </button>
        </form> 
        <?php
        $where = "";
        if (isset($_GET['enviar'])) {
            $busqueda = $_GET['busqueda'];

            if (isset($_GET['busqueda'])) {
                $where = "p.codproducto LIKE '%$busqueda%' OR
                p.descripcion LIKE '%$busqueda%' LIKE
                pr.proveedor '%$busqueda%' AND";
            }
        }
        ?>
        <table class="table_id" id="tblDatos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Existencia</th>
                    <th>
                        <?php
                        $query_proveedor = mysqli_query($conection, "SELECT codproveedor, proveedor 
                                                FROM proveedor 
                                                WHERE estado = 1 ORDER BY proveedor ASC");
                        $result_proveedor = mysqli_num_rows($query_proveedor);
                        ?>
                        <select name="proveedor" id="search_proveedor">
                            <option value="" selected>Proveedor</option>
                            <?php
                            if ($result_proveedor > 0) {
                                while ($proveedor = mysqli_fetch_array($query_proveedor)) {
                            ?>
                                    <option value="<?php echo $proveedor['codproveedor']; ?>">
                                        <?php echo $proveedor['proveedor']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </th>
                    <th>Imagen</th>
                    <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
                        <th>Acciones</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                //Para el paginador
                $sql_registe = mysqli_query($conection, "SELECT COUNT(*) as total_registro
                                         FROM producto WHERE estado = 1");

                $result_register = mysqli_fetch_array($sql_registe);
                $total_registro = $result_register['total_registro'];

                $por_pagina = 8;
                if (empty($_GET['pagina'])) {
                    $pagina = 1;
                } else {
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina - 1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                $query = mysqli_query($conection, "SELECT p.codproducto, p.descripcion, p.precio,
                                  p.existencia, pr.proveedor, p.foto FROM producto p
                                  INNER JOIN proveedor pr ON p.proveedor = pr.codproveedor
                                  WHERE $where p.estado = 1 ORDER BY p.codproducto DESC 
                                  LIMIT $desde,$por_pagina");

                mysqli_close($conection);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {

                        if ($data['foto'] != 'img_producto.png') {
                            $foto = 'images/uploads/' . $data['foto'];
                        } else {
                            $foto = 'images/' . $data['foto'];
                        }
                ?>
                        <tr class="row<?Php echo $data['codproducto']; ?>">
                            <td><?Php echo $data['codproducto']; ?></td>
                            <td><?Php echo $data['descripcion']; ?></td>
                            <td class="celPrecio">$<?Php echo $data['precio']; ?></td>
                            <td class="celExistencia"><?Php echo $data['existencia']; ?></td>
                            <td><?Php echo $data['proveedor']; ?></td>
                            <td class="img_producto">
                                <img src="<?php echo $foto; ?>" alt="<?Php echo $data['descripcion']; ?>">
                            </td>

                            <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
                                <td>
                                    <a class="btn_add add_product" product="<?Php echo $data['codproducto'];
                                                                            ?>" href="#"><i class="fa-solid fa-plus"></i> Agregar</a>

                                    <a class="btn_editar" href="editar_producto.php? 
                            id=<?Php echo $data['codproducto'];
                                ?>"><i class="fa-sharp fa-solid fa-pen-to-square">
                                        </i> Editar</a>

                                    <a class="btn_eliminar del_product" href="#" product="<?Php echo $data['codproducto'];
                                                                                            ?>"><i class="fa-sharp fa-solid fa-trash">
                                        </i> Eliminar</a>

                                </td>
                            <?php } ?>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr class="text-center">
                        <td colspan="16">No existen registros</td>
                    </tr>
                <?php
                }

                ?>
            </tbody>
        </table>
        <div class="paginador">
            <?php
            if ($pagina != 1) {
            ?>
                <a href="?pagina=<?php echo 1; ?>">
                    <i class="fa-sharp fa-solid fa-angles-left" style="color: #3d7ba8"></i></a>
                <a href="?pagina=<?php echo $pagina - 1; ?>">
                    <i class="fa-sharp fa-solid fa-chevron-left" style="color: #3d7ba8"></i></a>
            <?php
            }
            ?>

            <?php
            for ($i = 1; $i <= $total_paginas; $i++) {
                if ($i == $pagina) {
                    echo '<a class="activo">' . $i . '</a>';
                } else {
                    echo '<a href="?pagina=' . $i . '">' . $i . '</a>';
                }
            }
            if ($pagina != $total_paginas) {
            ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>">
                    <i class="fa-solid fa-angle-right" style="color: #3d7ba8"></i></a>
                <a href="?pagina=<?php echo $total_paginas; ?>">
                    <i class="fa-solid fa-angles-right" style="color: #3d7ba8"></i></a>
            <?php } ?>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>