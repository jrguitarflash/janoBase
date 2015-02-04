<!-- CSS -->

	<link rel="stylesheet" type="text/css" href="styles/decorador.css">

<!-- JS UTILS -->

	<script type="text/javascript" src="js/utils_gesti.js" ></script>

<!-- JS Notifi -->

	<script type="text/javascript" src="libJquery/notifiJs/notifi-min.js" ></script>

<!-- JS -->

	<script  type="text/javascript" src="js/finan_gesti.js" ></script>

<!-- PHP UTILS -->

<!-- PHP CONTROLADOR DOWN -->
	
	<?php require("clases/controlador/controladorInf.class.php"); ?>

<!-- PHP CONTROLADOR UP -->

	<?php require("clases/controlador/controladorSup.class.php"); ?>

<!-- HTML VIEW -->

	<input type="hidden" id="finan_view" value="<?php print $_GET['menu']; ?>" >

<!-- HTML ID -->

	<input type="hidden" id="finan_id" value="<?php print $_GET['id']; ?>" >

<!-- HTML NOTIFI -->

	<span class="elem-gd" ></span>

<!-- HTML THEME -->

<div class="box" >

	<div class="heading" >
		
		<h1>Lista de Operaciones Bancarias</h1>
		<div class="buttons" >
			<a href="#" id="finan_opeNuev" >Nuevo</a>
		</div>
	
	</div>

	<div class="content" >

		<table class="list" >
			<thead>
				<tr>
					<td align="center" >Item</td>
					<td>N° Operacion</td>
					<td>CC</td>
					<td>Proyecto</td>
					<td>Cliente</td>
					<td>N° Operaciones</td>
					<td align="center" >Accion</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center" >Item</td>
					<td>N° Operacion</td>
					<td>CC</td>
					<td>Proyecto</td>
					<td>Cliente</td>
					<td>N° Operaciones</td>
					<td align="center" >
						<a href="#">Editar</a> | 
						<a href="#">Eliminar</a>
					</td>
				</tr>
			</tbody>
		</table>

	</div>

</div>

<!-- HTML POPUP -->