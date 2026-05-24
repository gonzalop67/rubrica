<?php
include("../scripts/clases/class.mysql.php");

$db = new MySQL();

// include("../scripts/clases/class.paralelos.php");
// $paralelos = new paralelos();
$id_paralelo = $_POST["id_paralelo"];
// echo $paralelos->eliminarParalelo();

$qry = "SELECT COUNT(*) AS num_estudiantes FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = " . $id_paralelo;
$consulta = $db->consulta($qry);
$num_estudiantes = $db->fetch_object($consulta)->num_estudiantes;
if ($num_estudiantes > 0) {
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "No se puede eliminar el Paralelo porque tiene estudiantes matriculados.",
		'estado' => 'error'
	];
} else {
	try {
		$qry = "DELETE FROM sw_paralelo WHERE id_paralelo = $id_paralelo";
		$consulta = $db->consulta($qry);

		//Mensaje de operación exitosa
		$datos = [
			'titulo' => "¡Paralelo Eliminado con éxito!",
			'mensaje' => "Eliminación realizada exitosamente.",
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
