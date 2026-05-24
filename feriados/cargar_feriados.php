<?php
    session_start();
    $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];
    include("../scripts/clases/class.mysql.php");
	include("../scripts/clases/class.feriados.php");
	$feriado = new feriados();
	echo $feriado->listarFeriados($id_periodo_lectivo);
?>