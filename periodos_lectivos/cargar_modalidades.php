<?php
    require_once("../scripts/clases/class.mysql.php");
    $db = new MySQL();
    $consulta = $db->consulta("SELECT * FROM sw_modalidad WHERE mo_activo = 1 ORDER BY mo_orden ASC");
	$num_total_registros = $db->num_rows($consulta);
	$cadena = "";
	if($num_total_registros>0)
	{
		while($modalidad = $db->fetch_assoc($consulta))
		{
			$code = $modalidad["id_modalidad"];
			$name = $modalidad["mo_nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
	}
	echo $cadena;
?>