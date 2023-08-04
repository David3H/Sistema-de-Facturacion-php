<?php
if (empty($_SESSION['active'])) {
	header('location: ../index.php');
}
?>
<header>
	<div class="header">
		<h1>Sistema Facturación</h1>
		<div class="optionsBar">
			<p>FECHA: <?php echo fechaC(); ?></p>
			<span>|</span>
			<span>Usuario: <?php echo $_SESSION['user'] ?></span>
			<img class="photouser" src="images/user.png" alt="Usuario">
			<a href="salir.php">
				<img class="close" src="images/salir.png" alt="Salir del sistema" title="Salir">
		    </a>
		</div>
	</div>
	<?php include "nav.php"; ?>
</header>

<div class="modal">
	<div class="bodyModal">
		
	</div>
</div>