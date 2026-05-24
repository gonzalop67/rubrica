<?php
	require_once("../scripts/clases/class.mysql.php");
	$db = new MySQL();
	$query = "
	SELECT * FROM `sw_usuario` u,
	`sw_usuario_perfil` up, `sw_perfil` p 
	WHERE u.id_usuario = up.id_usuario
	AND p.id_perfil = up.id_perfil 
	AND (pe_slug = 'administrador' 
	OR pe_slug = 'docente'
	OR pe_slug = 'estudiante') 
	AND u.id_usuario <> " . $_POST["emisor_id"] . 
	" AND `us_activo` = 1  
	ORDER BY `pe_nombre`, `us_apellidos`, `us_nombres`
	";
	$result = $db->consulta($query);
	$output = "";
	foreach($result as $row)
	{
		$id_usuario = $row["id_usuario"];
        $us_shortname = $row["us_shortname"] . " (" . $row["pe_nombre"] . ")";
		$output .= "<option value='$id_usuario'>";
        $output .= $us_shortname;
		$output .= "</option>";
    }

	echo $output;
?>