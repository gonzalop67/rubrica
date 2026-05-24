<?php

class especialidades extends MySQL
{
	var $code = "";
	var $id_tipo_educacion = 0;
	var $id_especialidad = 0;
	var $es_nombre = "";
	var $es_figura = "";
	var $es_abreviatura = "";

	function existeEspecialidad($campo, $valor)
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE $campo = '$valor' AND id_tipo_educacion = $this->id_tipo_educacion");
		return (parent::num_rows($consulta) > 0);
	}

	function obtenerIdEspecialidad($id_paralelo)
	{
		// Obtencion del Id de la Especialidad que corresponde al paralelo pasado como parametro
		$consulta = parent::consulta("SELECT e.id_especialidad FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->id_especialidad;
	}

	function obtenerEspecialidad()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_especialidad = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombreEspecialidad($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_nombre FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->es_nombre;
	}

	function obtenerNombreFiguraProfesional($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_figura FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$especialidad = parent::fetch_object($consulta);
		return $especialidad->es_figura;
	}

	function eliminarEspecialidad()
	{
		$qry = "SELECT id_curso FROM sw_curso WHERE id_especialidad = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar porque tiene cursos asociadas...",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_especialidad WHERE id_especialidad=" . $this->code;
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Eliminado con éxito!",
					'mensaje' => "Eliminación realizada exitosamente.",
					'estado' => 'success'
				];
			} catch (Exception $e) {
				//Mensaje de operación fallida
				$datos = [
					'titulo' => "¡Error!",
					'mensaje' => "No se pudo realizar la eliminación. Error: " . $e->getMessage(),
					'estado' => 'error'
				];
			}
		}
		return json_encode($datos);
	}

	function insertarEspecialidad()
	{
		//Obtener el máximo es_orden
		$consulta = parent::consulta("SELECT MAX(es_orden) AS maximo FROM sw_especialidad WHERE id_tipo_educacion = $this->id_tipo_educacion");
		if (parent::num_rows($consulta) > 0) {
			$record = parent::fetch_object($consulta);
			$maximo_orden = $record->maximo + 1;
		} else {
			$maximo_orden = 1;
		}

		$qry = "INSERT INTO sw_especialidad (id_tipo_educacion, es_nombre, es_figura, es_abreviatura, es_orden) VALUES (";
		$qry .= $this->id_tipo_educacion . ",";
		$qry .= "'" . $this->es_nombre . "',";
		$qry .= "'" . $this->es_figura . "',";
		$qry .= "'" . $this->es_abreviatura . "',";
		$qry .= $maximo_orden . ")";

		try {
			$consulta = parent::consulta($qry);

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
	}

	function actualizarEspecialidad()
	{
		$qry = "UPDATE sw_especialidad SET ";
		$qry .= "id_tipo_educacion = " . $this->id_tipo_educacion . ",";
		$qry .= "es_nombre = '" . $this->es_nombre . "',";
		$qry .= "es_figura = '" . $this->es_figura . "',";
		$qry .= "es_abreviatura = '" . $this->es_abreviatura . "'";
		$qry .= " WHERE id_especialidad = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_especialidad = $this->code");
		$registro = parent::fetch_object($consulta);
		$nombreActual = $registro->es_nombre;
		$figuraActual = $registro->es_figura;
		$abreviaturaActual = $registro->es_abreviatura;

		if ($nombreActual != $this->es_nombre && $this->existeEspecialidad('es_nombre', $this->es_nombre)) {
			//Mensaje de operación exitosa
			$datos = [
				'titulo' => "¡Ocurrió un error inesperado!",
				'mensaje' => "Ya existe una especialidad con este nombre.",
				'estado' => 'error'
			];
		} else if ($figuraActual != $this->es_figura && $this->existeEspecialidad('es_figura', $this->es_figura)) {
			//Mensaje de operación exitosa
			$datos = [
				'titulo' => "¡Ocurrió un error inesperado!",
				'mensaje' => "Ya existe una especialidad con esta figura.",
				'estado' => 'error'
			];
		} else if ($abreviaturaActual != $this->es_abreviatura && $this->existeEspecialidad('es_abreviatura', $this->es_abreviatura)) {
			//Mensaje de operación exitosa
			$datos = [
				'titulo' => "¡Ocurrió un error inesperado!",
				'mensaje' => "Ya existe una especialidad con esta abreviatura.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

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
		}
		return json_encode($datos);
	}

	function listarEspecialidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_tipo_educacion = " . $this->code . " ORDER BY id_especialidad ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($especialidades = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $especialidades["id_especialidad"];
				$name = $especialidades["es_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarEspecialidad(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarEspecialidad(" . $code . ",'" . $name . "')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Especialidades asociadas a este tipo de educaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarEspecialidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_especialidad WHERE id_tipo_educacion = " . $this->id_tipo_educacion . " ORDER BY es_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($especialidades = parent::fetch_assoc($consulta)) {
				$id = $especialidades["id_especialidad"];
				$name = $especialidades["es_nombre"];
				$figura = $especialidades["es_figura"];
				$orden = $especialidades["es_orden"];
				$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$figura</td>\n";
				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarEspecialidadModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarEspecialidad(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han definido Especialidades asociadas a este nivel de educaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}
}
