<nav>
	<ul>
		<li><a href="index.php"><i class="fa-sharp fa-solid fa-house-user"></i> Inicio</a></li>
		<?php
		if ($_SESSION['rol'] == 1) {
		?>
			<li class="principal">
				<a href="#"><i class="fa-sharp fa-solid fa-user"></i> Usuarios</a>
				<ul>
					<li><a href="registro_usuario.php">
							<i class="fa-solid fa-user-plus"></i> Nuevo Usuario</a></li>
					<li><a href="lista_usuarios.php">
							<i class="fa-sharp fa-solid fa-users"></i> Lista de Usuarios</a></li>
				</ul>
			</li>
		

		<li class="principal">
			<a href="#"><i class="fa-solid fa-clipboard-user"></i> Clientes</a>
			<ul>
				<li><a href="registro_cliente.php">
						<i class="fa-solid fa-user-plus"></i> Nuevo Cliente</a></li>
				<li><a href="lista_clientes.php">
						<i class="fa-sharp fa-solid fa-users"></i> Lista de Clientes</a></li>
			</ul>
		</li>
		<?php } ?>
		<?php
		if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
		?>
			<li class="principal">
				<a href="#"><i class="fa-solid fa-building"></i> Proveedores</a>
				<ul>
					<li><a href="registro_proveedor.php">
							<i class="fa-solid fa-user-plus"></i> Nuevo Proveedor</a></li>
					<li><a href="lista_proveedores.php">
							<i class="fa-sharp fa-solid fa-users"></i> Lista de Proveedores</a></li>
				</ul>
			</li>
		<?php } ?>


		<li class="principal">
			<a href="#"><i class="fa-brands fa-product-hunt"></i> Productos</a>
			<ul>
				<?php
				if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
				?>
					<li><a href="registro_producto.php">
							<i class="fa-solid fa-cart-plus"></i> Nuevo Producto</a></li>
				<?php } ?>
				<li><a href="lista_productos.php">
						<i class="fa-solid fa-calendar-plus"></i> Lista de Productos</a></li>
			</ul>
		</li>


		<li class="principal">
			<a href="#"><i class="fa-regular fa-file-lines"></i> Ventas</a>
			<ul>
				<li><a href="nueva_venta.php">
						<i class="fa-solid fa-file-circle-plus"></i> Nuevo Factura</a></li>
				<li><a href="ventas.php">
						<i class="fa-regular fa-file-lines"></i> Venta</a></li>
			</ul>
		</li>
	</ul>
</nav>