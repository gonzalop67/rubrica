<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

$id_curso = $_POST["id_curso"];

// echo json_encode($id_curso); die();

$qry = "SELECT id_asignatura_curso, 
					es_figura,
					c.id_curso, 
					cu_nombre, 
					a.id_asignatura, 
					as_nombre,
					ar_nombre,  
					ac_orden 
			FROM sw_asignatura_curso ac, 
					sw_curso c, 
					sw_especialidad e,
					sw_asignatura a, 
					sw_area ar 
			WHERE e.id_especialidad = c.id_especialidad
				AND ac.id_curso = c.id_curso 
				AND ac.id_asignatura = a.id_asignatura 
				AND ar.id_area = a.id_area 
				AND ac.id_curso = $id_curso 
		ORDER BY ac_orden ASC";

// echo json_encode(array(
// 	'cadena' => $qry,
// 	'total_asignaturas' => 0
// )); die();

$consulta = $db->consulta($qry);
$num_total_registros = $db->num_rows($consulta);

$cadena = "";
$contador = 0;
if ($num_total_registros > 0) {
	while ($paralelos = $db->fetch_assoc($consulta)) {
		$contador++;
		$code = $paralelos["id_asignatura_curso"];
		$orden = $paralelos["ac_orden"];
		$cadena .= "<tr data-index = '$code' data-orden = '$orden'>\n";
		$id_curso = $paralelos["id_curso"];
		$curso = $paralelos["es_figura"] . " - " . $paralelos["cu_nombre"];
		$area = $paralelos["ar_nombre"];
		$id_asignatura = $paralelos["id_asignatura"];
		$asignatura = $paralelos["as_nombre"];
		$nombre_asignatura = $asignatura . " [" . $area . " ]" . " - (" . $id_asignatura . ")";
		$cadena .= "<td>$code</td>\n";
		$cadena .= "<td>$curso</td>\n";
		$cadena .= "<td width=\"39%\" align=\"left\">$nombre_asignatura</td>\n";
		$cadena .= "<td><button class='btn btn-danger' onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\"><span class='fa fa-trash'></span></button></td>";
		$cadena .= "</tr>\n";
	}
} else {
	$cadena .= "<tr>\n";
	$cadena .= "<td colspan='6' align='center'>No se han asociado asignaturas a este curso...</td>\n";
	$cadena .= "</tr>\n";
}

$datos = array(
	'cadena' => $cadena,
	'total_asignaturas' => $contador
);

echo json_encode($datos);
