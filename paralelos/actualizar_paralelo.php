<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

function existeParalelo($pa_nombre, $id_curso, $id_jornada)
{
	global $db;

	$consulta = $db->consulta("SELECT * FROM sw_paralelo WHERE pa_nombre = '$pa_nombre' AND id_curso = $id_curso AND id_jornada = $id_jornada");
	return $db->num_rows($consulta) > 0;
}

$id_paralelo = $_POST["id_paralelo"];
$id_curso = $_POST["id_curso"];
$pa_nombre = $_POST["pa_nombre"];
$id_jornada = $_POST["id_jornada"];

$qry = "UPDATE sw_paralelo SET ";
$qry .= "id_curso = " . $id_curso . ",";
$qry .= "pa_nombre = '" . $pa_nombre . "', ";
$qry .= "id_jornada = " . $id_jornada;
$qry .= " WHERE id_paralelo = " . $id_paralelo;

$consulta = $db->consulta("SELECT * FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
$registro = $db->fetch_assoc($consulta);
$nombreActual = $registro["pa_nombre"];

if ($nombreActual != $pa_nombre && existeParalelo($pa_nombre, $id_curso, $id_jornada)) {
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "Ya existe un paralelo con ese nombre en la Base de Datos.",
		'estado' => 'error'
	];
} else {
	try {
		$consulta = $db->consulta($qry);

		//Mensaje de operación exitosa
		$datos = [
			'titulo' => "¡Paralelo Actualizado con éxito!",
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
}

echo json_encode($datos);
