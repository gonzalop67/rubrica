<?php
    require_once("../scripts/clases/class.mysql.php");
    $db = new MySQL();
    $consulta = $db->consulta("SELECT * FROM sw_ofertas_educativas WHERE activo = 1 ORDER BY orden ASC");
	$num_total_registros = $db->num_rows($consulta);
	$cadena = "";
	if($num_total_registros>0)
	{
		while($oferta = $db->fetch_assoc($consulta))
		{
			$code = $oferta["id"];
			$name = $oferta["nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
	}
	echo $cadena;
?>