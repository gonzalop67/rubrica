<?php
	require_once("clases/class.mysql.php");
	
	$id_periodo_lectivo = $_GET["id_periodo_lectivo"];
	$db = new MySQL();
	$consulta = $db->consulta("SELECT id_tipo_educacion, te_nombre FROM sw_tipo_educacion WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY id_tipo_educacion ASC");
	$num_total_registros = $db->num_rows($consulta);
	$cadena = "";
	if($num_total_registros > 0)
	{
		while($tipos_educacion = $db->fetch_assoc($consulta))
		{
			$code = $tipos_educacion["id_tipo_educacion"];
			$name = $tipos_educacion["te_nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
	}
	echo $cadena
?>