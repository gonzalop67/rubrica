<?php
include("../scripts/clases/class.mysql.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {

	$db = new MySQL();
	$id = $db->filtrar($_POST['id']);

	// 1. Consultar los datos maestros del horario
	$sqlMaestro = "SELECT * FROM sw_horario_def WHERE id_horario_def = '$id' LIMIT 1";
	$resMaestro = $db->consulta($sqlMaestro);

	if ($db->num_rows($resMaestro) > 0) {
		$data = $db->fetch_assoc($resMaestro);

		// 2. Consultar las horas asociadas en la tabla detalle
		// Cambiamos el ORDER BY para usar 'hora_inicio', que sí existe con total seguridad
		$sqlDetalles = "SELECT * FROM sw_horario_detalles WHERE id_horario_def = '$id' ORDER BY hora_inicio ASC";
		$resDetalles = $db->consulta($sqlDetalles);

		$detallesLista = [];
		while ($row = $db->fetch_assoc($resDetalles)) {
			$detallesLista[] = $row;
		}

		// 3. Inyectamos la lista de detalles dentro del mismo objeto $data
		$data['detalles'] = $detallesLista;

		// Devolvemos todo junto
		echo json_encode($data);
	} else {
		echo json_encode(["error" => "No se encontró el registro."]);
	}
} else {
	echo json_encode(["error" => "Petición no válida."]);
}
