<?php
session_start();
// Conexión con la base de datos
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.encrypter.php");
if (!isset($_SESSION['usuario_logueado']))
	header("Location: index.php");
else {
	// Recepción de las variables GET
	$id_usuario = encrypter::decrypt($_GET['id_usuario']);
	$id_perfil = $_SESSION["id_perfil"];
	$db = new mysql();
	//Obtengo el perfil del usuario logueado
	$consulta = $db->consulta("SELECT pe_nombre 
								 FROM sw_perfil
								WHERE id_perfil = $id_perfil");
	$perfil = $db->fetch_assoc($consulta);
	$nombrePerfil = strtoupper($perfil["pe_nombre"]);

	//Obtengo los nombres del usuario
	$consulta = $db->consulta("SELECT SUBSTRING_INDEX(us_apellidos, ' ', 1) AS primer_apellido, 
									  SUBSTRING_INDEX(us_nombres, ' ', 1) AS primer_nombre,
									  us_foto 
								 FROM sw_usuario 
								WHERE id_usuario = $id_usuario");
	$usuario = $db->fetch_assoc($consulta);
	$nombreUsuario = $usuario["primer_nombre"] . " " . $usuario["primer_apellido"];
	$userImage = "public/uploads/" . $usuario["us_foto"];

	if (isset($_GET["enlace"])) {
		$enlace = $_GET["enlace"];
		$titulo = "Admin";
	} else {
		if (isset($_GET["id_menu"])) {
			$consulta = $db->consulta("SELECT mnu_texto, 
											  mnu_enlace, 
											  mnu_nivel 
										 FROM sw_menu 
										WHERE mnu_publicado = 1 
										  AND id_menu = " . $_GET['id_menu']);
			$pagina = $db->fetch_assoc($consulta);
			$titulo = $pagina["mnu_texto"];
			$enlace = $pagina["mnu_enlace"];
			$nivel = $pagina["mnu_nivel"];
			$_SESSION['titulo_pagina'] = $titulo;

			$titulo = "SIAE Web | " . $titulo;
		} else {
			$titulo = "SIAE Web";
			$enlace = "dashboards/dashboard_admin.php";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $titulo ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<!-- jquery-ui -->
	<link rel="stylesheet" href="assets/template/jquery-ui/jquery-ui.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="assets/template/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="assets/template/Ionicons/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="assets/template/dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
	<link rel="stylesheet" href="assets/template/dist/css/skins/skin-blue.min.css">

	<link href="estilos.css" rel="stylesheet" type="text/css" />

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<!-- jquery-ui -->
	<script src="assets/template/jquery-ui/jquery-ui.js"></script>
	<!-- jquery-ui-validation -->
	<script src="assets/template/jquery-validation/jquery.validate.min.js"></script>
	<script src="assets/template/jquery-validation/localization/messages_es.min.js"></script>
	<!-- sweetalert 2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!-- Chart JS -->
	<script src="js/chart.min.js"></script>
	<!-- plotly -->
	<script src="js/plotly-latest.min.js"></script>
	<!-- Select2 -->
	<script src="assets/template/select2/select2.min.js"></script>
	<link rel="stylesheet" href="assets/template/select2/select2.min.css">
	<!-- Toastr -->
	<link rel="stylesheet" href="assets/template/toastr/toastr.min.css">
	<script src="assets/template/toastr/toastr.min.js"></script>
	<!-- DataTables -->
	<link rel="stylesheet" href="assets/datatables/datatables.css">
	<script src="assets/datatables/datatables.js"></script>

	<script src="js/keypress.js"></script>
	<script src="js/funciones.js"></script>

	<style>
		.taskDone {
			text-decoration: line-through;
		}

		.alert-box {
			display: none;
			/* Oculto por defecto */
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			padding: 20px;
			background-color: white;
			border: 1px solid #ccc;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}
	</style>

	<!-- Google Font -->
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

<body class="hold-transition skin-blue sidebar-mini">
	<input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>">
	<input type="hidden" id="id_usuario" value="<?php echo $id_usuario ?>">
	<?php

	?>
	<input type="hidden" id="id_paralelo_tutor" value="<?php echo $id_paralelo_tutor; ?>">
	<div class="wrapper">

		<!-- Main Header -->
		<header class="main-header">

			<!-- Logo -->
			<a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=dashboard.php&nivel=0" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>SIAE</b></span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>SIAE</b></span>
			</a>

			<!-- Header Navbar -->
			<nav class="navbar navbar-static-top" role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<!-- Navbar Right Menu -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<!-- Messages: style can be found in dropdown.less-->
						<?php
						$comentarios = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_comentario");
						$num_comentarios = $db->fetch_object($comentarios)->num_rows;
						$terminacion = $num_comentarios == 1 ? '' : 's';
						?>
						<li class="dropdown messages-menu">
							<!-- Menu toggle button -->
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo $num_comentarios . ' comentario' . $terminacion; ?>">
								<i class="fa fa-comments-o"></i>
								<span class="label label-danger">4</span>
							</a>
							<ul class="dropdown-menu">
								<li class="header">You have 4 messages</li>
								<li>
									<!-- inner menu: contains the messages -->
									<ul class="menu">
										<li>
											<!-- start message -->
											<a href="#">
												<div class="pull-left">
													<!-- User Image -->
													<img src="assets/template/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
												</div>
												<!-- Message title and timestamp -->
												<h4>
													Support Team
													<small><i class="fa fa-clock-o"></i> 5 mins</small>
												</h4>
												<!-- The message -->
												<p>Why not buy a new awesome theme?</p>
											</a>
										</li>
										<!-- end message -->
									</ul>
									<!-- /.menu -->
								</li>
								<li class="footer"><a href="#">See All Messages</a></li>
							</ul>
						</li>
						<!-- /.messages-menu -->

						<!-- User Account Menu -->
						<li class="dropdown user user-menu">
							<!-- Menu Toggle Button -->
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<!-- The user image in the navbar-->
								<img src="<?php echo $userImage ?>" class="user-image" alt="User Image">
								<!-- hidden-xs hides the username on small devices so only the image appears. -->
								<span class="hidden-xs"><?php echo $nombreUsuario ?></span>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#"><i class="fa fa-cogs" aria-hidden="true"></i> Editar Perfil de Usuario</a></li>
								<li><a href="#"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
								<li>
									<a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=calificaciones/view_cambiar_periodo_lectivo.php&nivel=0"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Cambiar Periodo</a>
								</li>
								<li role="separator" class="divider"></li>
								<li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
							</ul>
						</li>
					</ul>
				</div>
				<!-- End Navbar Right Menu -->
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">

			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">

				<!-- Sidebar user panel (optional) -->
				<div class="user-panel">
					<div class="pull-left image">
						<img src="<?php echo $userImage ?>" class="img-circle" alt="User Image">
					</div>
					<div class="pull-left info">
						<p><?php echo $nombrePerfil ?></p>
					</div>
				</div>

				<!-- Sidebar Menu -->
				<ul class="sidebar-menu" data-widget="tree">
					<!-- Optionally, you can add icons to the links -->
					<?php $active = !isset($_GET['id_menu']) ? 'active' : '' ?>
					<li class="<?php echo $active ?>"><a href="administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=dashboard.php&nivel=0"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
					<?php
					$menus = $db->consulta("SELECT m.* FROM sw_menu m, sw_menu_perfil mp WHERE m.id_menu = mp.id_menu AND mp.id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");
					while ($menu = $db->fetch_assoc($menus)) {
						$submenus = $db->consulta("SELECT * FROM sw_menu WHERE mnu_publicado = 1 AND mnu_padre = " . $menu['id_menu'] . " ORDER BY mnu_orden");
						$num_submenus = $db->num_rows($submenus);
						if ($num_submenus > 0) {
					?>
							<li class="treeview">
								<a href="#"><i class="fa fa-link"></i> <span><?php echo $menu["mnu_texto"] ?></span>
									<span class="pull-right-container">
										<i class="fa fa-angle-left pull-right"></i>
									</span>
								</a>
								<ul class="treeview-menu">
									<?php
									while ($submenu = $db->fetch_assoc($submenus)) {
									?>
										<?php
										if (isset($_GET['id_menu']) && $_GET['id_menu'] == $submenu['id_menu']) {
											$active = 'active';
										} else {
											$active = '';
										}
										?>
										<li class="<?php echo $active ?>">
											<a href="administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $submenu["id_menu"] ?>&nivel=<?php echo $submenu["mnu_nivel"] ?>"><i class="fa fa-fw <?php echo $submenu["mnu_icono"] ?>"></i> <?php echo $submenu["mnu_texto"] ?></a>
										</li>
									<?php
									}
									?>
								</ul>
							</li>
						<?php
						} else {
						?>
							<?php
							if (isset($_GET['id_menu']) && $_GET['id_menu'] == $menu['id_menu']) {
								$active = 'active';
							} else {
								$active = '';
							}
							?>
							<li class="<?php echo $active ?>"><a href="administrador.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $menu["id_menu"] ?>&nivel=<?php echo $menu["mnu_nivel"] ?>"><i class="fa fa-link"></i> <span><?php echo $menu["mnu_texto"] ?></span></a></li>
					<?php
						}
					}
					?>
				</ul>
				<!-- /.sidebar-menu -->
			</section>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->

		<?php include($enlace); ?>

		<!-- Main Footer -->
		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> 2.4.0
			</div>
			<?php
			$consulta = $db->consulta("SELECT in_nombre,
											  in_url
										 FROM sw_institucion
										WHERE id_institucion = 1");
			$result = $db->fetch_object($consulta);
			$nom_institucion = $result->in_nombre;
			$url = $result->in_url;
			?>
			<strong><?php echo date("Y"); ?> &copy; <a href="<?php echo $url; ?>" target="_blank"><?php echo $nom_institucion; ?></a>.</strong> Todos los derechos reservados.
		</footer>
	</div>
	<!-- ./wrapper -->

	<!-- REQUIRED JS SCRIPTS -->

	<!-- AdminLTE App -->
	<script src="assets/template/dist/js/adminlte.min.js"></script>
	<script src="assets/template/dist/js/scripts.js"></script>

	<!-- InputMask -->
	<script src="public/assets/template/input-mask/jquery.inputmask.js"></script>
	<script src="public/assets/template/input-mask/jquery.inputmask.date.extensions.js"></script>
	<script src="public/assets/template/input-mask/jquery.inputmask.extensions.js"></script>

	<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>

</html>