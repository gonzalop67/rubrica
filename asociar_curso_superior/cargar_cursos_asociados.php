<?php
	include("../scripts/clases/class.mysql.php");
    include("../scripts/clases/class.cursos.php");
    session_start();
    $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
	$curso = new cursos();
	echo $curso->cargarCursosAsociados($id_periodo_lectivo);
?>
