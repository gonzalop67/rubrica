<?php
	include("../scripts/clases/class.mysql.php");
	// include("../scripts/clases/class.especialidades.php");
	// $especialidad = new especialidades();
	// $especialidad->id_tipo_educacion = $_POST["id_tipo_educacion"];
	// $especialidad->es_nombre = $_POST["es_nombre"];
	// $especialidad->es_figura = $_POST["es_figura"];
	// $especialidad->es_abreviatura = $_POST["es_abreviatura"];
	// echo $especialidad->insertarEspecialidad();

	$db = new MySQL();

	$subnivel_id = $_POST["id_tipo_educacion"];

	//Obtener el máximo es_orden
		// $consulta = $db->consulta("SELECT MAX(es_orden) AS maximo FROM sw_especialidad WHERE id_tipo_educacion = $this->id_tipo_educacion");
		// if ($db->num_rows($consulta) > 0) {
		// 	$record = $db->fetch_object($consulta);
		// 	$maximo_orden = $record->maximo + 1;
		// } else {
		// 	$maximo_orden = 1;
		// }

		$qry = "INSERT INTO sw_especialidad (categoria_id, subnivel_id, es_nombre, es_figura, es_abreviatura) VALUES (";
		$qry .= $this->id_tipo_educacion . ",";
		$qry .= "'" . $this->es_nombre . "',";
		$qry .= "'" . $this->es_figura . "',";
		$qry .= "'" . $this->es_abreviatura . "',";
		$qry .= $maximo_orden . ")";

		try {
			$consulta = $db->consulta($qry);

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

		return json_encode($datos);
?>
