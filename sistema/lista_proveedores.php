<?php
session_start();
if ($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2) {
    header("location: ./");
}
include "../conexion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <link rel="stylesheet" href="../sistema/css/mystile.css">
    <title>Listado Proveedores</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">

        <h1>Listado</h1>

        <a href="registro_proveedor.php" class="btn_new">
            <i class="fa-solid fa-user-plus"></i> Proveedor</a>

        <form action="buscar_proveedor.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" class="light-table-filter" data-table="table_id" placeholder="Buscar">
            <button type="submit" name="enviar" class="btn_search">
                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
        <?php
        $where = "";
        if (isset($_GET['enviar'])) {
            $busqueda = $_GET['busqueda'];

            if (isset($_GET['busqueda'])) {
                $where = "(codproveedor LIKE '%$busqueda%' OR
                            proveedor LIKE '%$busqueda%' OR
                            contacto LIKE '%$busqueda%' OR
                            telefono LIKE '%$busqueda%' OR
                            fechaadd LIKE '%$busqueda%') AND";
            }
        }
        ?>
        <table class="table_id" id="tblDatos">
            <thead>
                <tr>
                    <th>Número Proveedor</th>
                    <th>Empresa</th>
                    <th>Nombre Encargado</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //Para el paginador
                $sql_registe = mysqli_query($conection, "SELECT COUNT(*) as total_registro
                                         FROM proveedor WHERE estado = 1");

                $result_register = mysqli_fetch_array($sql_registe);
                $total_registro = $result_register['total_registro'];

                $por_pagina = 4;
                if (empty($_GET['pagina'])) {
                    $pagina = 1;
                } else {
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina - 1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                $query = mysqli_query($conection, "SELECT * FROM proveedor
                                  WHERE $where estado = 1 ORDER BY codproveedor ASC 
                                  LIMIT $desde,$por_pagina");

                mysqli_close($conection);
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_array($query)) {
                        $formato = 'Y-m-d H:i:s';
                        $fecha = DateTime::createFromFormat($formato, $data["fechaadd"]);
                ?>
                        <tr>
                            <td><?Php echo $data['codproveedor']; ?></td>
                            <td><?Php echo $data['proveedor']; ?></td>
                            <td><?Php echo $data['contacto']; ?></td>
                            <td><?Php echo $data['telefono']; ?></td>
                            <td><?Php echo $data['direccion']; ?></td>
                            <td><?Php echo $fecha->format('d-m-Y'); ?></td>

                            <td>
                                <a class="btn_editar" href="editar_proveedor.php? 
                            id=<?Php echo $data['codproveedor']; ?>">
                                    <i class="fa-sharp fa-solid fa-pen-to-square">
                                    </i> Editar</a>
                                <?php if ($_SESSION['rol'] == 1) { ?>
                                    <a class="btn_eliminar" href="eliminar_proveedor.php? 
                            id=<?Php echo $data['codproveedor']; ?>">
                                        <i class="fa-sharp fa-solid fa-trash">
                                        </i> Eliminar</a>
                                <?php } ?>
                            </td>
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