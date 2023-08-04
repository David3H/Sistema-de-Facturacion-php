<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?> 
	<link rel="stylesheet" href="../sistema/css/mystile.css">
	<title>Sistema Ventas</title>
</head>

<body>
<?php 
include "includes/header.php";
include "../conexion.php";

$query_dash = mysqli_query($conection, "CALL dataDashboard(); ");
$result_das = mysqli_num_rows($query_dash);
if($result_das > 0){
	$data_dash = mysqli_fetch_assoc($query_dash);
	mysqli_close($conection);
}

?>
	<section id="container">
		
		<div class="divContainer">
			<div>
				<h1 class="titlePanelControl">Panel de Control</h1>
			</div>

			<div class="dashboard">
				<?php 
					if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
				?>
				<a href="lista_usuarios.php">
					<i class="fas fa-users"></i>
					<p>
						<strong>Usuarios</strong>
						<span><?= $data_dash['usuarios']; ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_clientes.php">
					<i class="fas fa-user"></i>
					<p>
						<strong>Clientes</strong>
						<span><?= $data_dash['clientes']; ?></span>
					</p>
				</a>
				<?php 
					if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){
				?>
				<a href="lista_proveedores.php">
					<i class="fas fa-building"></i>
					<p>
						<strong>Provedores</strong>
						<span><?= $data_dash['proveedores']; ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_productos.php">
					<i class="fas fa-cubes"></i>
					<p>
						<strong>Productos</strong>
						<span><?= $data_dash['productos']; ?></span>
					</p>
				</a>
				<a href="ventas.php">
					<i class="fas fa-file-alt"></i>
					<p>
						<strong>Ventas</strong>
						<span><?= $data_dash['ventas']; ?></span>
					</p>
				</a>
			</div>
		</div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>