<?php
session_start();
// Conexión con la base de datos
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.periodos_lectivos.php");
if (!isset($_SESSION['usuario_logueado']))
	header("Location: index.php");
else {
	// Recepción de las variables GET
	$id_usuario = $_GET["id_usuario"];
	$id_perfil = $_GET["id_perfil"];
	$db = new mysql();
	//Obtengo los nombres del usuario
	$consulta = $db->consulta("
		SELECT SUBSTRING_INDEX(us_apellidos, ' ', 1) AS primer_apellido, 
		SUBSTRING_INDEX(us_nombres, ' ', 1) AS primer_nombre,
		us_foto 
		FROM sw_usuario 
		WHERE id_usuario = $id_usuario
		");
	$usuario = $db->fetch_assoc($consulta);
	$nombreUsuario = $usuario["primer_nombre"] . " " . $usuario["primer_apellido"];
	$userImage = "assets/images/" . $usuario["us_foto"];
	if (!isset($_GET['nivel'])) {
		$titulo = "SIAE-WEB Admin";
		$enlace = "central.php";
	} else {
		if (isset($_GET["enlace"])) {
			$enlace = $_GET["enlace"];
			$titulo = "SIAE-WEB Admin";
		} else {
			$consulta = $db->consulta("SELECT mnu_texto, mnu_enlace, mnu_nivel FROM sw_menu WHERE mnu_publicado = 1 AND id_menu = " . $_GET['id_menu']);
			$pagina = $db->fetch_assoc($consulta);
			$titulo = $pagina["mnu_texto"];
			$enlace = $pagina["mnu_enlace"];
			$nivel = $pagina["mnu_nivel"];
			$_SESSION['titulo_pagina'] = $titulo;
		}
	}
	//Obtengo los paralelos tutorados
	$consulta = $db->consulta("
		SELECT pe_nombre 
		FROM sw_perfil
		WHERE id_perfil = $id_perfil
		");
	$perfil = $db->fetch_assoc($consulta);
	$nombrePerfil = strtoupper($perfil["pe_nombre"]);
}
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
//Obtengo los años de inicio y de fin del periodo lectivo actual
$periodos_lectivos = new periodos_lectivos();
$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($id_periodo_lectivo);
$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php echo $titulo ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<!-- jquery-ui -->
	<link rel="stylesheet" href="assets/template/jquery-ui/jquery-ui.css">
	<link href="estilos.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link rel="stylesheet" href="assets/template/dist/css/AdminLTE.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="assets/template/dist/css/skins/_all-skins.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="assets/template/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="assets/template/Ionicons/css/ionicons.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="assets/template/datatables.net-bs/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="assets/template/sweetalert/dist/sweetalert.css">
	<link rel="stylesheet" href="assets/template/select2/select2.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
	<input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>">
	<input type="hidden" id="nombrePeriodoLectivo" value="<?php echo $nombrePeriodoLectivo ?>">
	<input type="hidden" id="nombrePerfil" value="<?php echo $nombrePerfil ?>">
	<!-- Site wrapper -->
	<div class="wrapper">
		<!-- Inicio Header -->
		<header class="main-header">
			<!-- Logo -->
			<a href="/" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>SIAE</b></span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>SIAE <?php echo $nombrePeriodoLectivo ?></b></span>
			</a>
			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<!-- Messages: style can be found in dropdown.less-->
						<li class="dropdown messages-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-comments-o"></i>
								<?php
								$comentarios = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_comentario");
								$num_comentarios = $db->fetch_object($comentarios)->num_rows;
								$terminacion = $num_comentarios == 1 ? '' : 's';
								?>
								<span class="label label-warning"><?php echo $num_comentarios ?></span>
							</a>
							<ul class="dropdown-menu">
								<li><?php echo "Tiene $num_comentarios comentario$terminacion" ?></li>
								<li>
									<!-- inner menu: contains the actual data -->
									<ul class="menu">
										<?php
										$comentarios = $db->consulta("SELECT * FROM sw_comentario WHERE id_comentario_padre = 0 ORDER BY id_comentario DESC LIMIT 1, 5");
										while ($comentario = $db->fetch_assoc($comentarios)) {
											$co_id_usuario = $comentario["co_id_usuario"];
											$co_nombre = $comentario["co_nombre"];
											$co_texto = $comentario["co_texto"];
											$co_tipo = $comentario["co_tipo"];
											if ($co_tipo == 1) {
												//Aqui obtengo el genero del estudiante que ha comentado
												$recordset = $db->consulta("SELECT es_genero FROM sw_estudiante WHERE id_estudiante = $co_id_usuario");
												$record = $db->fetch_assoc($recordset);
												$genero = $record["es_genero"];
												if ($genero == 'F') {
													$userImageComment = "assets/images/student-female-avatar.jpg";
												} else {
													$userImageComment = "assets/images/student-male.png";
												}
											} else {
												//Aqui obtengo el avatar del usuario que ha comentado
												$recordset = $db->consulta("SELECT us_foto FROM sw_usuario WHERE id_usuario = $co_id_usuario");
												$record = $db->fetch_assoc($recordset);
												$foto = $record["us_foto"];
												$userImageComment = "assets/images/" . $foto;
											}
										?>
											<li>
												<!-- start message -->
												<a href="#">
													<div class="pull-left">
														<img src="<?php echo $userImageComment; ?>" class="img-circle" alt="User Image">
													</div>
													<h4>
														<?php echo $co_nombre ?>
													</h4>
													<p>
														<?php
														if (strlen($co_texto) <= 25) {
															echo strtoupper($co_texto);
														} else {
															echo substr(strtoupper($co_texto), 0, 24) . "...";
														}
														?>
													</p>
												</a>
											</li>
											<!-- end message -->
										<?php
										}
										?>
									</ul>
								</li>
								<li class="footer"><a href="#">Ver todos los comentarios</a></li>
							</ul>
						</li>
						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="<?php echo $userImage; ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $nombreUsuario ?></span>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="admin2.php?id_usuario=<?php echo $id_usuario ?>&id_perfil=<?php echo $id_perfil ?>&enlace=change_password.php&nivel=0"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cambiar Clave</a></li>
								<li><a href="admin2.php?id_usuario=<?php echo $id_usuario ?>&id_perfil=<?php echo $id_perfil ?>&enlace=comentarios/index.php&nivel=0"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
								<?php
								if ($nombrePerfil == "TUTOR" && $num_registros > 1) {
								?>
									<li>
										<a href="admin2.php?id_usuario=<?php echo $id_usuario ?>&id_perfil=<?php echo $id_perfil ?>&enlace=tutores/view_cambiar_paralelo.php&nivel=0"><i class="fa fa-users" aria-hidden="true"></i> Cambiar Paralelo</a>
									</li>
								<?php
								}
								?>
								<li role="separator" class="divider"></li>
								<li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
						<img src="<?php echo $userImage; ?>" class="img-circle" alt="User Image">
					</div>
					<div class="pull-left info">
						<p><?php echo $nombrePerfil ?></p>
						<small><?php echo $nombrePeriodoLectivo ?></small>
					</div>
				</div>
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
					<!-- sidebar menu: : style can be found in sidebar.less -->
					<?php
					$menus = $db->consulta("SELECT * FROM sw_menu WHERE id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");
					?>
					<ul class="sidebar-menu" data-widget="tree">
						<li class="active">
							<a href="#">
								<i class="fa fa-home"></i> <span>Dashboard</span>
							</a>
						</li>
						<li class="treeview">
							<a href="#">
								<i class="fa fa-laptop"></i> <span><?php echo "Administración"; ?></span>
								<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
								</span>
							</a>
							<ul class="treeview-menu">
								<li><a href="#"><i class="fa fa-circle-o"></i> Modalidades</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> Periodos Lectivos</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> Perfiles</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> Menús</a></li>
								<li><a href="#"><i class="fa fa-circle-o"></i> Usuarios</a></li>
							</ul>
						</li>
					</ul>
				</section>
			</section>
		</aside>
	</div>

	<!-- jQuery 3 -->
	<script src="assets/template/jquery/jquery.min.js"></script>
	<!-- Bootstrap 3.3.7 -->
	<script src="assets/template/bootstrap/js/bootstrap.min.js"></script>
	<!-- AdminLTE App -->
	<script src="assets/template/dist/js/adminlte.min.js"></script>
	<!-- jquery-ui -->
	<script src="assets/template/jquery-ui/jquery-ui.js"></script>
	<!-- plotly -->
	<script src="js/plotly-latest.min.js"></script>
	<!-- Sweetalert -->
	<script src="assets/template/sweetalert/dist/sweetalert.js"></script>
	<!-- Select2 -->
	<script src="assets/template/select2/select2.min.js"></script>

	<script src="js/keypress.js"></script>

	<script>
		$(document).ready(function() {
			$('.sidebar-menu').tree();
		});
	</script>
</body>

</html>