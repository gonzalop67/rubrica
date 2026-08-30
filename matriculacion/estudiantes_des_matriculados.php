<?php
	include("../scripts/clases/class.mysql.php");
	$db = new MySQL();
    $id_paralelo = $_POST["id_paralelo"];
	$query = $db->consulta("SELECT ep.id_estudiante_periodo_lectivo,
								   e.id_estudiante,  
		                           es_apellidos,
		                           es_nombres, 
		                           nro_matricula 
	                          FROM sw_estudiante e, 
		                           sw_estudiante_periodo_lectivo ep
	                         WHERE e.id_estudiante = ep.id_estudiante 
	                           AND ep.id_paralelo = $id_paralelo 
	                           AND activo = 0 
	                      ORDER BY es_apellidos, es_nombres ASC");
    $num_rows = $db->num_rows($query);
	$cadena = "";
	if ($num_rows > 0) {
		$contador = 0;
		while($row = $db->fetch_object($query)) {
			$contador++;
			$codigo = $row->id_estudiante_periodo_lectivo;
			$id_estudiante = $row->id_estudiante;
			$apellidos = $row->es_apellidos;
			$nombres = $row->es_nombres;
			$nro_matricula = $row->nro_matricula;
			$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
			$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
			$cadena .= "<td>$contador</td>\n";
			$cadena .= "<td>$id_estudiante</td>\n";
			$cadena .= "<td>$nro_matricula</td>\n";
			$cadena .= "<td>$apellidos</td>\n";
			$cadena .= "<td>$nombres</td>\n";
			$cadena .= "<td>\n";
			$cadena .= "<div class='btn-group'>\n";
			$cadena .= "<a href='javascript:;' class='btn btn-info btn-sm item-deleted' data=" . $codigo . " title='Volver a matricular'><span class='fa fa-undo'></span></a>\n";
			$cadena .= "<a href='javascript:;' class='btn btn-danger btn-sm item-hard-delete' data=" . $codigo . " title='Quitar de este periodo lectivo'><span class='fa fa-times'></span></a>\n";
			$cadena .= "</div>\n";
			$cadena .= "</td>\n";
			$cadena .= "</tr>\n";
		} 
	} else {
		$cadena .= "<tr>\n";
		$cadena .= "<td colspan='6' align='center'>No se han des-matriculado estudiantes de este paralelo..</td>\n";
		$cadena .= "</tr>\n";
	}

	echo $cadena;
?>
