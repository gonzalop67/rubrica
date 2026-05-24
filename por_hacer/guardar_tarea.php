<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

$tarea_descripcion = $_POST['tarea'];

// echo $tarea->insertarTarea($tarea_descripcion);

try {
	$db->consulta("INSERT INTO sw_tarea (tarea, hecho) VALUES ('$tarea_descripcion', 0)");

	//Mensaje de operación exitosa
	$datos = [
		'titulo' => "¡Agregado con éxito!",
		'mensaje' => "Inserción realizada exitosamente.",
		'tipo_mensaje' => 'success'
	];
} catch (Exception $e) {
	//Mensaje de operación fallida
	$datos = [
		'titulo' => "¡Error!",
		'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
		'tipo_mensaje' => 'error'
	];
}

echo json_encode($datos);
