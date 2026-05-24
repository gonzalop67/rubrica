<?php
include("../scripts/clases/class.mysql.php");
include("../scripts/clases/class.periodos_evaluacion.php");

$periodos_evaluacion = new periodos_evaluacion();

session_start();

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$id_periodo_evaluacion = $_POST["id_periodo_evaluacion"];
$pe_nombre = $_POST["edit_pe_nombre"];
$pe_abreviatura = $_POST["edit_pe_abreviatura"];
$pe_tipo = $_POST["edit_pe_principal"];
$pe_ponderacion = $_POST["edit_pe_ponderacion"];
$nota_desde = $_POST["nota_desdeu"];
$nota_hasta = $_POST["nota_hastau"];

$edit_id_curso = $_POST["edit_id_curso"];

$db = new MySQL();

$qry = "UPDATE sw_periodo_evaluacion SET ";
$qry .= "id_tipo_periodo = " . $pe_tipo . ", ";
$qry .= "pe_nombre = '" . $pe_nombre . "', ";
$qry .= "pe_abreviatura = '" . $pe_abreviatura . "', ";
$qry .= "pe_ponderacion = " . $pe_ponderacion;
$qry .= " WHERE id_periodo_evaluacion = $id_periodo_evaluacion";

// Recuperar la información actual del periodo de evaluación a editar
// $consulta = $db->consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion");

// $periodo_evaluacion = $db->fetch_object($consulta);

try {
	$consulta = $db->consulta($qry);

	if ($pe_tipo != 1 && $pe_tipo != 7 && $pe_tipo != 8) {
		$qry = "UPDATE sw_equivalencia_supletorios SET ";
		$qry .= "rango_desde = " . $nota_desde . ", ";
		$qry .= "rango_hasta = " . $nota_hasta;
		$qry .= " WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$qry .= " AND id_tipo_periodo = $pe_tipo";

		$consulta = $db->consulta($qry);
	}

	// Eliminar los cursos asociados
	$qry = "DELETE FROM sw_periodo_evaluacion_curso WHERE id_periodo_evaluacion = $id_periodo_evaluacion";
	$consulta = $db->consulta($qry);

	for ($i = 0; $i < count($edit_id_curso); $i++) {
		// Calcular el máximo ordinal para el periodo de evaluación
		$qry = "SELECT MAX(pc_orden) AS max_orden FROM sw_periodo_evaluacion_curso WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$consulta = $db->consulta($qry);
		$num_total_registros = $db->num_rows($consulta);
		if ($num_total_registros > 0) {
			$registro = $db->fetch_object($consulta);
			$max_orden = $registro->max_orden + 1;
		} else {
			$max_orden = 1;
		}
		// INSERTAR LA ASOCIACION
		$qry = "INSERT INTO sw_periodo_evaluacion_curso (id_periodo_lectivo, id_periodo_evaluacion, id_curso, pe_ponderacion, pc_orden) VALUES (";
		$qry .= $id_periodo_lectivo . ", ";
		$qry .= $id_periodo_evaluacion . ", "; // id_periodo_evaluacion
		$qry .= $edit_id_curso[$i] . ", ";
		$qry .= $pe_ponderacion . ", ";
		$qry .= $max_orden . ")";
		$consulta = $db->consulta($qry);
	}

	//Mensaje de operación exitosa
	$datos = [
		'titulo' => "¡Actualizado con éxito!",
		'mensaje' => "Actualización realizada exitosamente.",
		'estado' => 'success'
	];
} catch (Exception $e) {
	//Mensaje de operación fallida
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
		'estado' => 'error'
	];
}

echo json_encode($datos);
