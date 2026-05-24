<?php

class dias_semana extends MySQL
{

	var $code = "";
	var $ds_nombre = "";
	var $ds_ordinal = "";
	var $id_horario_def = "";
	var $id_periodo_lectivo = "";

	var $id_dia_semana = "";
	var $id_hora_clase = "";

	function insertarHoraDia()
	{
		$qry = "SELECT * FROM sw_hora_dia WHERE id_dia_semana = " . $this->id_dia_semana . " AND id_hora_clase = " . $this->id_hora_clase;
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$mensaje = "Ya existe la asociacion de hora clase y dia de la semana seleccionados...";
		} else {
			$qry = "INSERT INTO sw_hora_dia(id_dia_semana, id_hora_clase) VALUES(";
			$qry .= $this->id_dia_semana . ",";
			$qry .= $this->id_hora_clase . ")";
			$consulta = parent::consulta($qry);
			if (!$consulta) {
				$mensaje = "No se pudo insertar la asociacion. Error: " . mysqli_error($this->conexion);
			} else {
				$mensaje = "Asociacion insertada exitosamente.";
			}
		}
		return $mensaje;
	}

	function listarHorasAsociadas()
	{
		$consulta = parent::consulta("SELECT id_hora_dia, 
											 ds_nombre,
                                             hc_nombre,
											 DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
											 DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
                                             hc_ordinal 
                                        FROM sw_hora_dia hd, 
                                             sw_dia_semana ds, 
                                             sw_hora_clase hc
                                       WHERE ds.id_dia_semana = hd.id_dia_semana
                                         AND hc.id_hora_clase = hd.id_hora_clase 
                                         AND hd.id_dia_semana = " . $this->id_dia_semana
			. " ORDER BY hc_ordinal");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		$contador = 0;
		if ($num_total_registros > 0) {
			while ($hora_asociada = parent::fetch_assoc($consulta)) {
				$contador++;
				$cadena .= "<tr>\n";
				$code = $hora_asociada["id_hora_dia"];
				$dia_semana = $hora_asociada["ds_nombre"];
				$hora_clase = $hora_asociada["ds_nombre"] . " - " . $hora_asociada["hc_nombre"] . " (" . $hora_asociada["hora_inicio"] . " - " . $hora_asociada["hora_fin"] . ")";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$dia_semana</td>\n";
				$cadena .= "<td>$hora_clase</td>\n";
				$cadena .= "<td><button class='btn btn-block btn-danger' onclick=\"eliminarAsociacion(" . $code . ")\">Eliminar</button></td>";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han asociado horas clase a este dia de la semana...</td>\n";
			$cadena .= "</tr>\n";
		}
		$datos = array(
			'cadena' => $cadena,
			'total_horas' => $contador
		);
		return json_encode($datos);
	}

	function eliminarHoraDia()
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_rows FROM sw_asistencia_estudiante WHERE id_hora_dia = " . $this->code);
		$num_rows = parent::fetch_object($consulta)->num_rows;
		if ($num_rows > 0) {
			$mensaje = "No se puede eliminar la asociacion porque tiene asistencias relacionadas...";
		} else {
			$consulta = parent::consulta("DELETE FROM sw_hora_dia WHERE id_hora_dia = " . $this->code);
			if (!$consulta) {
				$mensaje = "No se pudo eliminar la asociacion. Error: " . mysqli_error($this->conexion);
			} else {
				$mensaje = "Asociacion eliminada exitosamente.";
			}
		}
		return $mensaje;
	}

	function listar_dias_semana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_horario_def = " . $this->id_horario_def . " ORDER BY ds_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($dia_semana = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $dia_semana["id_dia_semana"];
				$name = $dia_semana["ds_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarDiaSemana(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarDiaSemana(" . $code . ")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido D&iacute;as de la Semana...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarDiasSemana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_horario_def = " . $this->id_horario_def . " ORDER BY ds_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($dia_semana = parent::fetch_assoc($consulta)) {
				$code = $dia_semana["id_dia_semana"];
				$name = $dia_semana["ds_nombre"];
				$orden = $dia_semana["ds_orden"];

				$cadena .= "<tr data-index='$code' data-orden='$orden'>\n";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$name</td>\n";

				$cadena .= "<td>\n";
				$cadena .= "<div class=\"btn-group\">\n";
				$cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning btn-sm item-edit\" data=\"$code\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
				$cadena .= "<a href=\"javascript:;\" class=\"btn btn-danger btn-sm item-delete\" data=\"$code\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";

				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han definido D&iacute;as de la Semana...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function insertarDiaSemana()
	{
		$qry = "SELECT * FROM sw_dia_semana WHERE id_horario_def = " . $this->id_horario_def . " AND ds_nombre = '" . $this->ds_nombre . "'";
		$consulta = parent::consulta($qry);
		$num_rows = parent::num_rows($consulta);
		if ($num_rows > 0) {
			$data = array(
				"titulo" => "Ocurrió un error inesperado.",
				"mensaje"  => "Ya se ha definido el Día de la Semana $this->ds_nombre para este horario",
				"tipo_mensaje"  => "error"
			);
		} else {
			try {
				// Calcular el máximo orden en sw_dia_semana dentro del periodo lectivo
				$qry = "SELECT MAX(ds_orden) AS max_orden FROM sw_dia_semana WHERE id_horario_def = $this->id_horario_def";
				$consulta = parent::consulta($qry);
				if (parent::num_rows($consulta) > 0) {
					$max_orden = parent::fetch_object($consulta)->max_orden;
					$max_orden++;
				} else {
					$max_orden = 1;
				}

				$qry = "INSERT INTO sw_dia_semana (id_horario_def, ds_nombre, ds_orden) VALUES (";
				$qry .= $this->id_horario_def . ",";
				$qry .= "'" . strtoupper($this->ds_nombre) . "',";
				$qry .= "'" . $max_orden . "')";

				$consulta = parent::consulta($qry); // Ejecuta el insert

				// Asociar el nuevo dia_semana con las horas_clase definidas en la tabla sw_hora_dia
				$qry = "SELECT MAX(id_dia_semana) AS max_id FROM sw_dia_semana";
				$consulta = parent::consulta($qry);

				$registro = parent::fetch_object($consulta);
				$id_dia_semana = $registro->max_id;

				$qry = "SELECT id_hora_clase FROM sw_hora_clase WHERE id_horario_def = $this->id_horario_def";
				$registros = parent::consulta($qry);

				while ($row = parent::fetch_object($registros)) {
					$id_hora_clase = $row->id_hora_clase;
					$qry = "INSERT INTO sw_hora_dia SET id_dia_semana = $id_dia_semana, id_hora_clase = $id_hora_clase, id_horario_def = $this->id_horario_def";
					$consulta = parent::consulta($qry);
				}

				$data = array(
					"titulo" => "Inserción exitosa.",
					"mensaje"  => 'El Día de la Semana se insertó correctamente.',
					"tipo_mensaje"  => "success"
				);
			} catch (Exception $e) {
				$data = array(
					"titulo" => "Ocurrió un error inesperado.",
					"mensaje"  => 'El Día de la Semana NO se insertó correctamente. Error: ' . $e->getMessage(),
					"tipo_mensaje"  => "error"
				);
			}
		}
		return json_encode($data);
	}

	function obtenerDiaSemana()
	{
		$consulta = parent::consulta("SELECT * FROM sw_dia_semana WHERE id_dia_semana = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdDiaSemana()
	{
		$consulta = parent::consulta("SELECT id_dia_semana 
										FROM sw_dia_semana 
									   WHERE id_horario_def = $this->id_horario_def 
										 AND ds_orden = $this->ds_ordinal");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function actualizarDiaSemana()
	{
		try {
			$qry = "UPDATE sw_dia_semana SET ";
			$qry .= "ds_nombre = '" . strtoupper($this->ds_nombre) . "'";
			$qry .= " WHERE id_dia_semana = " . $this->code;
			$consulta = parent::consulta($qry);

			$data = array(
				"titulo" => "Actualización exitosa.",
				"mensaje"  => 'El Día de la Semana se actualizó correctamente.',
				"tipo_mensaje"  => "success"
			);
		} catch (Exception $e) {
			$data = array(
				"titulo" => "Ocurrió un error inesperado.",
				"mensaje"  => 'El Día de la Semana NO se insertó correctamente. Error: ' . $e->getMessage(),
				"tipo_mensaje"  => "error"
			);
		}

		return json_encode($data);
	}

	function eliminarDiaSemana()
	{
		$qry = "SELECT * FROM sw_horario WHERE id_dia_semana = $this->code";
		$consulta = parent::consulta($qry);

		if (parent::num_rows($consulta) > 0) {
			$data = [
				'titulo' => 'Oh no!',
				'mensaje' => 'Existen registros relacionados en la tabla de horarios...',
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_dia_semana WHERE id_dia_semana = " . $this->code;
				$consulta = parent::consulta($qry);

				$data = [
					'titulo' => 'Yes!',
					'mensaje' => 'Dia de la Semana eliminado exitosamente...',
					'estado' => 'success'
				];
			} catch (Exception $e) {
				$data = array(
					"titulo" => "Ocurrió un error inesperado.",
					"mensaje"  => 'El Día de la Semana NO se eliminó correctamente. Error: ' . $e->getMessage(),
					"estado"  => "error"
				);
			}
		}

		return json_encode($data);
	}

	function mostrarDiasSemana($alineacion)
	{
		if (!isset($alineacion)) $alineacion = "center";

		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT ds_nombre FROM sw_dia_semana WHERE id_horario_def = " . $this->id_horario_def . " ORDER BY ds_ordinal");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			while ($dia_semana = parent::fetch_assoc($consulta)) {
				$mensaje .= "<td width=\"75px\" align=\"" . $alineacion . "\">" . $dia_semana["ds_nombre"] . "</td>\n";
			}
		}

		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
}
