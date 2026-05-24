<?php
require_once("../scripts/clases/class.mysql.php");

$db = new MySQL();

session_start();
$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$consulta = $db->consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $id_periodo_lectivo . " AND id_tipo_periodo = 1 ORDER BY id_periodo_evaluacion ASC");
$num_total_registros = $db->num_rows($consulta);
$cadena = "";
if ($num_total_registros > 0) {
	while ($periodos_evaluacion = $db->fetch_assoc($consulta)) {
		$code = $periodos_evaluacion["id_periodo_evaluacion"];
		$name = $periodos_evaluacion["pe_nombre"];
		$cadena .= "<option value=\"$code\">$name</option>";
	}
}

echo $cadena;
