<?php
	require_once("../scripts/clases/class.mysql.php");
	require_once("../scripts/clases/class.tutores.php");
	session_start();
	$id_paralelo_tutor = $_POST['id_paralelo'];
	$tutor = new tutores();
	$nombre_paralelo = $tutor->obtenerNombreParalelo($id_paralelo_tutor);
	$_SESSION['nombre_paralelo'] = $nombre_paralelo;
	$_SESSION['id_paralelo_tutor'] = $id_paralelo_tutor;
	$_SESSION['cambio_paralelo'] = 1;
	echo $nombre_paralelo;
?>
