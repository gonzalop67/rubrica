<?php
    require_once("../scripts/clases/class.mysql.php");
    $db = new MySQL();
    $consulta = $db->consulta("SELECT * FROM sw_quien_inserta_comp ORDER BY id ASC");
	$num_total_registros = $db->num_rows($consulta);
	$cadena = "";
	if($num_total_registros>0)
	{
		while($row = $db->fetch_assoc($consulta))
		{
			$code = $row["id"];
			$name = $row["nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
	}
	echo $cadena;
?>