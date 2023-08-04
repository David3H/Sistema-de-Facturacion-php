<?php
session_start();
if($_SESSION['rol'] != 1){
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
    <title>Listado Usuarios</title>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <section id="container">

        <?php
        $busqueda = strtolower($_REQUEST['busqueda']);
        if (empty($busqueda)) {
            header("location: lista_usuarios.php");
            mysqli_close($conection);
        }
        ?>
        <h1>Listado</h1>
        <a href="registro_usuario.php" class="btn_new"><i class="fa-solid fa-user-plus"></i> Crear</a>

        <form action="buscar_usuario.php" method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar" 
            value="<?php echo $busqueda; ?>">
            <button type="submit"class="btn_search"><i class="fa-sharp fa-solid fa-magnifying-glass">
            </i></button>
            
        </form>

        <table>
            <tr>
                <th>Número Usuario</th>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Usuario</th>
                <th>Tipo Usuario</th>
                <th>Acciones</th>
            </tr>
            <?php
            //Para el paginador
            $rol = '';
            if ($busqueda == 'administrador') {
                $rol = "OR rol LIKE '%1%' ";
            } elseif ($busqueda == 'supervisor') {
                $rol = "OR rol LIKE '%2%'";
            } elseif ($busqueda == 'vendedor') {
                $rol = "OR rol LIKE '%3%'";
            }

            $sql_registe = mysqli_query($conection, "SELECT COUNT(*) as total_registro FROM usuario 
                                         WHERE (idusuario LIKE '%$busqueda%' OR
                                                nombre LIKE '%$busqueda%' OR
                                                correo LIKE '%$busqueda%' OR
                                                usuario LIKE '%$busqueda%'
                                                $rol )
                                                AND estado = 1");

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

            $query = mysqli_query($conection, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, r.rol 
                                  FROM usuario u INNER JOIN rol r ON u.rol = r.idrol
                                  WHERE (u.idusuario LIKE '%$busqueda%' OR
                                        u.nombre LIKE '%$busqueda%' OR
                                        u.correo LIKE '%$busqueda%' OR
                                        u.usuario LIKE '%$busqueda%' OR
                                        r.rol LIKE '%$busqueda%')
                                  AND   estado = 1 ORDER BY u.idusuario ASC 
                                  LIMIT $desde,$por_pagina");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_array($query)) {
            ?>
                    <tr>
                        <td><?Php echo $data['idusuario']; ?></td>
                        <td><?Php echo $data['nombre']; ?></td>
                        <td><?Php echo $data['correo']; ?></td>
                        <td><?Php echo $data['usuario']; ?></td>
                        <td><?Php echo $data['rol']; ?></td>
                        <td>

                            <a class="btn_editar" href="editar_usuario.php? 
                            id=<?Php echo $data['idusuario']; ?>">
                            <i class="fa-sharp fa-solid fa-pen-to-square"></i> Editar</a>

                            <?php if ($data["idusuario"] != 1) { ?>

                                <a class="btn_eliminar" href="eliminar_usuario.php? 
                     id=<?Php echo $data['idusuario']; ?>">
                     <i class="fa-sharp fa-solid fa-trash"></i> Eliminar</a>
                            <?php } ?>


                        </td>
                    </tr>
            <?php
                }
            }

            ?>
        </table>
        <?php
        if ($total_registro != 0) {

        ?>
            <div class="paginador">
                <?php
                if ($pagina != 1) {
                ?>
                    <a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>">
                    <i class="fa-sharp fa-solid fa-angles-left" style="color: #3d7ba8"></i></a>
                    <a href="?pagina=<?php echo $pagina - 1; ?>&busqueda=<?php echo $busqueda; ?>">
                    <i class="fa-sharp fa-solid fa-chevron-left" style="color: #3d7ba8"></i></a>
                <?php
                }
                ?>

                <?php
                for ($i = 1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina) {
                        echo '<a class="activo">' . $i . '</a>';
                    } else {
                        echo '<a href="?pagina=' . $i . '&busqueda=' . $busqueda . '">' . $i . '</a>';
                    }
                }
                if ($pagina != $total_paginas) {
                ?>
                    <a href="?pagina=<?php echo $pagina + 1; ?>&busqueda=<?php echo $busqueda; ?>">
                    <i class="fa-solid fa-angle-right" style="color: #3d7ba8"></i></a>
                    <a href="?pagina=<?php echo $total_paginas; ?>&busqueda=<?php echo $busqueda; ?>">
                    <i class="fa-solid fa-angles-right" style="color: #3d7ba8"></i></a>
                <?php } ?>
            </div>
        <?php
        }
        ?>
    </section>
    <?php include "includes/footer.php"; ?>
</body>

</html>