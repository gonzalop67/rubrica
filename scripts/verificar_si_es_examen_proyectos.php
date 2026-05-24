<?php
	require_once("clases/class.mysql.php");
	$db = new MySQL();
	//recibo las variables de tipo post de la pagina login.php
	$id_asignatura = $_POST['id_asignatura'];
	$id_aporte_evaluacion = $_POST['id_aporte_evaluacion'];
    //consulta SQL para determinar si se trata del examen de proyectos escolares
    //se trata del examen de proyectos si ap_tipo = 2 y id_tipo_asignatura = 2
    //primero obtengo el campo ap_tipo de la tabla sw_aporte_evaluacion
    $query = "SELECT id_tipo_aporte FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion";
    $consulta = $db->consulta($query);
    $result = $db->fetch_object($consulta);
    $id_tipo_aporte = $result->id_tipo_aporte;
    //segundo obtengo el campo id_tipo_asigantura de la tabla sw_asigantura
    $query = "SELECT id_tipo_asignatura FROM sw_asignatura WHERE id_asignatura = $id_asignatura";
    $consulta = $db->consulta($query);
    $result = $db->fetch_object($consulta);
    $id_tipo_asignatura = $result->id_tipo_asignatura;
    echo json_encode(array('id_tipo_aporte' => $id_tipo_aporte, 'id_tipo_asignatura' => $id_tipo_asignatura));
?>