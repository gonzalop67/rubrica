<?php
include("../scripts/clases/class.mysql.php");
// include("../scripts/clases/class.periodos_evaluacion.php");
// die(var_dump($_POST));

session_start();

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

$pe_nombre = $_POST["new_pe_nombre"];
$pe_abreviatura = $_POST["new_pe_abreviatura"];
$pe_tipo = $_POST["new_pe_principal"];
$pe_ponderacion = isset($_POST["new_pe_ponderacion"]) ? $_POST["new_pe_ponderacion"] : "null";
$nota_desde = $_POST["new_nota_desde"];
$nota_hasta = $_POST["new_nota_hasta"];

$new_id_curso = $_POST["new_id_curso"];

$db = new MySQL();

$consulta = $db->consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = $pe_tipo AND pe_nombre = '$pe_nombre'");

if ($db->num_rows($consulta) > 0) {
	//Mensaje de error
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "Ya existe el periodo de evaluación [$pe_nombre] en el presente periodo lectivo.",
		'estado' => 'error'
	];
} else {
	try {
		$consulta = $db->consulta("SELECT MAX(pe_orden) AS secuencial FROM sw_periodo_evaluacion");

		if ($db->num_rows($consulta) > 0) {
			$pe_orden = $db->fetch_object($consulta)->secuencial + 1;
		} else {
			$pe_orden = 1;
		}

		$qry = "INSERT INTO sw_periodo_evaluacion (id_periodo_lectivo, id_tipo_periodo, pe_nombre, pe_abreviatura, pe_ponderacion, pe_orden) VALUES (";
		$qry .= $id_periodo_lectivo . ",";
		$qry .= $pe_tipo . ",";
		$qry .= "'" . $pe_nombre . "',";
		$qry .= "'" . $pe_abreviatura . "',";

		if ($pe_tipo != 1 && $pe_tipo != 7 && $pe_tipo != 8) {
			$qry .= "null, $pe_orden)";
		} else {
			$qry .= $pe_ponderacion . ", $pe_orden)";
		}

		// die(json_encode($qry));

		$consulta = $db->consulta($qry);

		if ($pe_tipo == 2 || $pe_tipo == 3 || $pe_tipo == 4) {
			$qry = "SELECT * FROM sw_tipo_periodo WHERE id_tipo_periodo = $pe_tipo";
			$consulta = $db->consulta($qry);
			$registro = $db->fetch_object($consulta);
			$nombre_periodo = $registro->tp_descripcion;

			$qry = "SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = $pe_tipo AND nombre_examen = '$nombre_periodo'";
			$consulta = $db->consulta($qry);
			$num_rows = $db->num_rows($consulta);

			if ($num_rows == 0) {
				$qry = "INSERT INTO sw_equivalencia_supletorios (id_periodo_lectivo, id_tipo_periodo, rango_desde, rango_hasta, nombre_examen) VALUES (";
				$qry .= $id_periodo_lectivo . ",";
				$qry .= $pe_tipo . ",";
				$qry .= $nota_desde . ",";
				$qry .= $nota_hasta . ",";
				$qry .= "'$nombre_periodo')";

				$consulta = $db->consulta($qry);
			}
		}

		// Insertar los cursos asociados
		$qry = "SELECT MAX(id_periodo_evaluacion) AS max_id FROM sw_periodo_evaluacion";
		$consulta = $db->consulta($qry);
		$registro = $db->fetch_object($consulta);
		$max_id = $registro->max_id;

		for ($i = 0; $i < count($new_id_curso); $i++) {
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
			$qry .= $max_id . ", "; // id_periodo_evaluacion
			$qry .= $new_id_curso[$i] . ", ";

			if ($pe_tipo == 1 || $pe_tipo == 7 || $pe_tipo == 8) {
				$qry .= $pe_ponderacion . ", ";
			} else {
				$qry .= "null, ";
			}

			$qry .= $max_orden . ")";
			$consulta = $db->consulta($qry);
		}

		//Mensaje de operación exitosa
		$datos = [
			'titulo' => "¡Insertado con éxito!",
			'mensaje' => "Inserción realizada exitosamente.",
			'estado' => 'success'
		];
	} catch (Exception $e) {
		//Mensaje de operación fallida
		$datos = [
			'titulo' => "¡Error!",
			'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
			'estado' => 'error'
		];
	}
}

echo json_encode($datos);
