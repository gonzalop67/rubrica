<?php
	session_start();
	include_once("../funciones/funciones_sitio.php");
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.estudiantes.php");
	require_once("../scripts/clases/class.periodos_lectivos.php");

	//Instancia de la clase MySQL
	$db = new mysql();

	if (!isset($_SESSION['usuario_logueado']) OR !$_SESSION['usuario_logueado'])
		header("Location: index.php");
	else {
		//Primero tengo que obtener el id_estudiante para obtener los datos correspondientes
		$id_estudiante = $_GET['id_estudiante'];
		$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
		// Primero debo obtener el id_paralelo
		$consulta = $db->consulta("SELECT ep.id_paralelo FROM sw_estudiante e, sw_estudiante_periodo_lectivo ep WHERE ep.id_estudiante = e.id_estudiante AND ep.id_periodo_lectivo = $id_periodo_lectivo AND e.id_estudiante = $id_estudiante");
		$paralelo = $db->fetch_object($consulta);
		$id_paralelo = $paralelo->id_paralelo;
		//Obtengo los nombres del usuario
		$consulta = $db->consulta("
		SELECT SUBSTRING_INDEX(es_apellidos, ' ', 1) AS primer_apellido, 
		SUBSTRING_INDEX(es_nombres, ' ', 1) AS primer_nombre 
		FROM sw_estudiante 
		WHERE id_estudiante = $id_estudiante
		");
		$usuario = $db->fetch_assoc($consulta);
		$nombreUsuario = $usuario["primer_nombre"] . " " . $usuario["primer_apellido"];
		$estudiante = new estudiantes();
		$nombreParalelo = $estudiante->obtenerCursoParaleloEstudianteId($_SESSION["id_estudiante"], $_SESSION["id_periodo_lectivo"]);
		//Obtengo los años de inicio y de fin del periodo lectivo actual
		$periodos_lectivos = new periodos_lectivos();
		$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($_SESSION['id_periodo_lectivo']);
		$nombrePeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
		$id_estudiante = $_SESSION["id_estudiante"];
		if (!isset($_GET["enlace"])) {
			$enlace = "central.php";
		} else {
			$enlace = $_GET["enlace"];
		}
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>SIAE-WEB Consulta de Calificaciones Estudiantiles</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<script src="../js/keypress.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<!-- jquery-ui -->
	<link rel="stylesheet" href="../assets/template/jquery-ui/jquery-ui.css">
	<link href="estilos.css" rel="stylesheet" type="text/css" />
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>-->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<!-- jquery-ui -->
	<script src="../assets/template/jquery-ui/jquery-ui.js"></script>
	<!-- plotly -->
	<script src="../js/plotly-latest.min.js"></script>
	<!-- Theme style -->
	<link rel="stylesheet" href="../assets/template/dist/css/AdminLTE.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../assets/template/dist/css/skins/_all-skins.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="../assets/template/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="../assets/template/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../assets/template/datatables.net-bs/css/dataTables.bootstrap.min.css">
	<script src="../assets/template/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="../assets/template/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
	<!-- Sweetalert -->
	<script src="../assets/template/sweetalert/dist/sweetalert.js"></script>
    <link rel="stylesheet" href="../assets/template/sweetalert/dist/sweetalert.css">
	<!-- Select2 -->
	<script src="../assets/template/select2/select2.min.js"></script>
    <link rel="stylesheet" href="../assets/template/select2/select2.min.css">

	<style>
	    .error {
			color: #ff0000;
			display: none;
		}
		.rojo {
			color: #ff0000;
		}
		.taskDone {
			text-decoration: line-through;
		}
		.success {
			color:#006400;
			text-align:center;
			font-family:Arial, Helvetica, sans-serif;
		}
		.negrita {
			font-weight: bold;
		}
		.removeRow
		{
			background-color: #FF0000;
			color:#FFFFFF;
		}
	</style>
</head>
<body>
	<input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>">
	<input type="hidden" id="nombrePeriodoLectivo" value="<?php echo $nombrePeriodoLectivo ?>">
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=central.php">SIAE <?php echo $nombrePeriodoLectivo; ?></a>
			</div>
			<ul class="nav navbar-nav">
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						Consultar <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=parciales.php">
								Parciales
							</a>
						</li>
						<li>
							<a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=quimestrales.php">
								Sub-Periodo
							</a>
						</li>
						<li>
							<a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=anuales.php">
								Periodo
							</a>
						</li>
						<li>
							<a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=cuestionarios.php">
								Cuestionarios
							</a>
						</li>
					</ul>
				</li>
			</ul>
			<div id="nombre_paralelo">
				<p class = "navbar-text"><?php echo $nombreParalelo; ?></p>
			</div>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<?php 
						$comentarios = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_comentario WHERE id_usuario_para = 0");
						$num_comentarios = $db->fetch_object($comentarios)->num_rows;
						$terminacion = $num_comentarios == 1 ? '' : 's';
					?>
					<a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=comentarios.php" title="<?php echo $num_comentarios . ' comentario' . $terminacion; ?>">
						<i class="fa fa-comments-o"></i>
						<span class="label label-warning">
							<?php
								echo $num_comentarios;
							?>
						</span>
					</a>
				</li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<span class="glyphicon glyphicon-user"></span> <?php echo $nombreUsuario ?> <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="admin2.php?id_estudiante=<?php echo $id_estudiante?>&enlace=comentarios.php"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
						<li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir </a></li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
	<?php include($enlace); ?>
</body>
</html>