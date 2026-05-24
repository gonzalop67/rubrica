<?php
session_start();
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

function existeParalelo($pa_nombre, $id_curso, $id_jornada)
{
	global $db;

	$consulta = $db->consulta("SELECT * FROM sw_paralelo WHERE pa_nombre = '$pa_nombre' AND id_curso = $id_curso AND id_jornada = $id_jornada");
	return $db->num_rows($consulta) > 0;
}

$id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
$id_curso = $_POST["id_curso"];
$id_jornada = $_POST["id_jornada"];
$pa_nombre = $_POST["pa_nombre"];

$qry = "SELECT secuencial_paralelo_periodo_lectivo($id_periodo_lectivo) AS secuencial";

$consulta = $db->consulta($qry);
$secuencial = $db->fetch_object($consulta)->secuencial;

$qry = "INSERT INTO sw_paralelo (id_periodo_lectivo, id_curso, id_jornada, pa_nombre, pa_orden) VALUES (";
$qry .= $id_periodo_lectivo . ",";
$qry .= $id_curso . ",";
$qry .= $id_jornada . ",";
$qry .= "'$pa_nombre',";
$qry .= $secuencial . ")";

if (existeParalelo($pa_nombre, $id_curso, $id_jornada)) {
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "Ya existe un paralelo con ese nombre en la Base de Datos.",
		'estado' => 'error'
	];
} else {
	try {
		$consulta = $db->consulta($qry);

		// Obtener el id del paralelo creado
		$qry = "SELECT MAX(id_paralelo) AS max_id_paralelo FROM `sw_paralelo`";
		$consulta = $db->consulta($qry);
		$resultado = $db->fetch_assoc($consulta);
		$id_paralelo = $resultado["max_id_paralelo"];

		// Asociar el paralelo creado con los aportes de evaluación creados para el cierre de periodos...

		$qry = "SELECT a.id_aporte_evaluacion,
					   a.ap_fecha_apertura, 
					   a.ap_fecha_cierre  
				  FROM `sw_aporte_evaluacion` a, 
					   `sw_periodo_evaluacion` p  
				 WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion
				   AND p.id_periodo_lectivo = $id_periodo_lectivo";

		$consulta = $db->consulta($qry);

		while ($aporte = $db->fetch_assoc($consulta)) {
			$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
			$fecha_apertura = $aporte["ap_fecha_apertura"];
			$fecha_cierre = $aporte["ap_fecha_cierre"];
			// Insertar la asociación para el futuro cierre...
			$qry = "SELECT * 
					  FROM `sw_aporte_paralelo_cierre` 
					 WHERE id_aporte_evaluacion = $id_aporte_evaluacion 
					   AND id_paralelo = $id_paralelo";
			$asociacion = $db->consulta($qry);
			if ($db->num_rows($asociacion) == 0) {
				$qry = "INSERT INTO `sw_aporte_paralelo_cierre` 
					(id_aporte_evaluacion, 
					id_paralelo, 
					ap_fecha_apertura, 
					ap_fecha_cierre, 
					ap_estado) 
					VALUES (
					$id_aporte_evaluacion, 
					$id_paralelo,
					'$fecha_apertura',
					'$fecha_cierre',
					'C'
					)";
				$resultado = $db->consulta($qry);
			}
		}

		//Mensaje de operación exitosa
		$datos = [
			'titulo' => "¡Paralelo Insertado con éxito!",
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
