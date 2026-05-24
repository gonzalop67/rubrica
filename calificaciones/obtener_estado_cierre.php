<?php
	require_once("../scripts/clases/class.mysql.php");
	$db = new MySQL();
	//recibo las variables de tipo post de la pagina calificaciones/index.php
	$id_paralelo = $_POST['id_paralelo'];
	$id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
    //consulta SQL para determinar el estado de cierre del curso y aporte de evaluación asociados
    $query = "SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo";
    $consulta = $db->consulta($query);
    $result = $db->fetch_object($consulta);
    $ap_estado = $result->ap_estado;
    echo json_encode(array('ap_estado' => $ap_estado));
?>