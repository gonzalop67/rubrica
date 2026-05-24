<?php
include("../scripts/clases/class.mysql.php");

session_start();

$db = new MySQL;

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_usuario = $_POST["id_usuario"];

$query = "SELECT d.*,
				 m.*, 
				 pa_nombre,
				 cu_abreviatura,
				 es_abreviatura, 
				 as_nombre,
				 pa_orden,
				 ma_orden 
			FROM sw_distributivo d, 
				 sw_malla_curricular m,
				 sw_paralelo p, 
				 sw_curso c,
				 sw_especialidad e,  
				 sw_asignatura a 
		   WHERE m.id_malla_curricular = d.id_malla_curricular
			 AND e.id_especialidad = c.id_especialidad
			 AND c.id_curso = p.id_curso 
			 AND p.id_paralelo = d.id_paralelo 
			 AND c.id_curso = m.id_curso 
			 AND a.id_asignatura = d.id_asignatura 
			 AND d.id_asignatura = m.id_asignatura 
			 AND d.id_usuario = $id_usuario
			 AND d.id_periodo_lectivo = $id_periodo_lectivo
		   ORDER BY pa_orden, ma_orden";

$consulta = $db->consulta($query);

$num_total_registros = $db->num_rows($consulta);
$suma_horas_presenciales = 0;
$suma_horas_tutorias = 0;
$suma_horas_totales = 0;
$cadena = "";

if ($num_total_registros > 0) {
	$suma_horas_presenciales = 0;
	$suma_horas_tutorias = 0;
	$suma_horas_totales = 0;
	while ($malla = $db->fetch_assoc($consulta)) {
		$cadena .= "<tr>\n";
		$code = $malla["id_distributivo"];
		$paralelo = $malla["cu_abreviatura"] . " \"" . $malla["pa_nombre"] . "\" " . $malla["es_abreviatura"];
		$asignatura = $malla["as_nombre"];
		$presenciales = $malla["ma_horas_presenciales"];
		$autonomas = $malla["ma_horas_autonomas"];
		$tutorias = $malla["ma_horas_tutorias"];
		$suma_horas_presenciales = $suma_horas_presenciales + $presenciales;
		$suma_horas_tutorias = $suma_horas_tutorias + $tutorias;
		$suma_horas_totales = $suma_horas_totales + $presenciales + $tutorias;
		$subtotal = $presenciales + $tutorias;
		$cadena .= "<td>$code</td>\n";
		$cadena .= "<td>$paralelo</td>\n";
		$cadena .= "<td>$asignatura</td>\n";
		$cadena .= "<td>$presenciales</td>\n";
		$cadena .= "<td>$autonomas</td>\n";
		$cadena .= "<td>$tutorias</td>\n";
		$cadena .= "<td>$subtotal</td>\n";
		$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarDistributivo(" . $code . ")\">Eliminar</button></td>";
		$cadena .= "</tr>\n";
	}
} else {
	$cadena .= "<tr>\n";
	$cadena .= "<td colspan='8' align='center'>No se han definido items asociados a este docente...</td>\n";
	$cadena .= "</tr>\n";
}
$datos = array(
	'cadena' => $cadena,
	'horas_presenciales' => $suma_horas_presenciales,
	'horas_tutorias' => $suma_horas_tutorias,
	'total_horas' => $suma_horas_totales
);

echo json_encode($datos);
