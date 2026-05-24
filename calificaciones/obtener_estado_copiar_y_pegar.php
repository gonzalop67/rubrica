<?php
	require_once("../scripts/clases/class.mysql.php");
	$db = new MySQL();
    //consulta SQL para determinar el estado de copiar y pegar
    $query = "SELECT in_copiar_y_pegar FROM sw_institucion WHERE id_institucion = 1";
    $consulta = $db->consulta($query);
    $result = $db->fetch_object($consulta);
    $in_copiar_y_pegar = $result->in_copiar_y_pegar;
    echo json_encode(array('in_copiar_y_pegar' => $in_copiar_y_pegar));
?>