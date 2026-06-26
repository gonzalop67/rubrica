<?php

class periodos_lectivos extends MySQL
{
	var $code = "";
	var $id_periodo_estado = "";
	var $id_oferta_educativa = "";
	var $pe_anio_inicio = "";
	var $pe_anio_fin = "";
	var $pe_fecha_inicio = "";
	var $pe_fecha_fin = "";
	var $pe_estado = "";
	var $pe_nota_minima = "";
	var $pe_nota_aprobacion = "";
	var $quien_inserta_comp_id = "";

	function getDiasLaborados($id_periodo_lectivo, $fecha)
	{
		$qry = "SELECT pe_fecha_inicio FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$fecha_inicio = parent::fetch_object(parent::consulta($qry))->pe_fecha_inicio;
		$fecha1 = strtotime($fecha_inicio);
		$fecha2 = strtotime($fecha);
		$cont_dias = 0;
		//Primero obtengo los dias feriados guardados en la base de datos
		$qry = "SELECT fe_fecha FROM sw_feriado WHERE id_periodo_lectivo = $id_periodo_lectivo";
		$resultado = parent::consulta($qry);
		$feriados = array();
		while ($fecha = parent::fetch_assoc($resultado)) {
			array_push($feriados, $fecha['fe_fecha']);
		}
		for ($fecha1; $fecha1 <= $fecha2; $fecha1 = strtotime('+1 day ' . date('Y-m-d', $fecha1))) {
			if (date('w', $fecha1) != 0 && date('w', $fecha1) != 6 && !in_array(date('Y-m-d', $fecha1), $feriados)) {
				$cont_dias++;
			}
		}
		return $cont_dias;
	}

	function obtenerValorMes($id_periodo_lectivo, $mes)
	{
		$consulta = parent::consulta("SELECT vm_valor FROM sw_valor_mes WHERE vm_mes = $mes");
		return parent::fetch_object($consulta)->vm_valor;
	}

	function existePeriodoLectivo($anio_inicio, $anio_fin)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE pe_anio_inicio = $anio_inicio AND pe_anio_fin = $anio_fin");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			return true;
		} else {
			return false;
		}
	}

	function obtenerIdPeriodoLectivo($anio_inicio, $anio_fin)
	{
		$consulta = parent::consulta("SELECT id_periodo_lectivo FROM sw_periodo_lectivo WHERE pe_anio_inicio = $anio_inicio AND pe_anio_fin = $anio_fin");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->id_periodo_lectivo;
	}

	function obtenerIdPeriodoLectivoActual()
	{
		$consulta = parent::consulta("SELECT id_periodo_lectivo FROM sw_periodo_lectivo ORDER BY id_periodo_lectivo DESC LIMIT 0,1");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->id_periodo_lectivo;
	}

	function obtenerPeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		return parent::fetch_object($consulta);
	}

	function obtenerDatosPeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombrePeriodoLectivo($id)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		$periodo_lectivo = parent::fetch_object($consulta);
		return $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;
	}

	function obtenerCiclo($id)
	{
		$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
		$num_total_registros = parent::num_rows($consulta);
		
		$cadena = "";
		
		if ($num_total_registros > 0) {
			$periodo_lectivo = parent::fetch_assoc($consulta);
			$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
			$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
			$cadena .= "Ciclo: " . $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];
		} else {
			$cadena .= "Ocurrió un error inesperado: No existen registros coincidentes.";
		}

		return $cadena;
	}

	function insertarPeriodoLectivo()
	{
		$consulta = parent::consulta("INSERT INTO sw_periodo_lectivo SET pe_anio_inicio = $this->pe_anio_inicio, pe_anio_fin = $this->pe_anio_fin, pe_fecha_inicio = '$this->pe_fecha_inicio', pe_fecha_fin = '$this->pe_fecha_fin', pe_nota_minima = $this->pe_nota_minima, pe_nota_aprobacion = $this->pe_nota_aprobacion, pe_estado = 'A', oferta_educativa_id = $this->id_oferta_educativa, quien_inserta_comp_id = $this->quien_inserta_comp_id, id_periodo_estado = 1, id_institucion = 1");
		if ($consulta) {
			// Cerrar el periodo lectivo anterior si existe
			$consulta = parent::consulta("SELECT id_periodo_lectivo FROM sw_periodo_lectivo WHERE id_oferta_educativa = $this->id_oferta_educativa ORDER BY id_periodo_lectivo DESC LIMIT 1, 1");
			$num_total_registros = parent::num_rows($consulta);
			if ($num_total_registros == 1) {
				$periodo_lectivo = parent::fetch_object($consulta);
				$id_periodo_lectivo = $periodo_lectivo->id_periodo_lectivo;
				$qry = parent::consulta("call sp_cerrar_periodo_lectivo($id_periodo_lectivo)");
			}
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El periodo lectivo se ha insertado exitosamente...",
				"tipo_mensaje" => "success"
			);
			return json_encode($data);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "El periodo lectivo no se pudo insertar exitosamente...Error: " . mysqli_error($this->conexion),
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		}
	}

	function actualizarPeriodoLectivo()
	{
		$qry = "UPDATE sw_periodo_lectivo SET pe_anio_inicio = $this->pe_anio_inicio, pe_anio_fin = $this->pe_anio_fin, pe_fecha_inicio = '$this->pe_fecha_inicio', pe_fecha_fin = '$this->pe_fecha_fin', pe_nota_minima = $this->pe_nota_minima, pe_nota_aprobacion = $this->pe_nota_aprobacion, quien_inserta_comp_id = $this->quien_inserta_comp_id WHERE id_periodo_lectivo = $this->code";

		try {
			$consulta = parent::consulta($qry);

			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El periodo lectivo se ha actualizado exitosamente...",
				"tipo_mensaje" => "success",
				"query"        => $qry
			);
		} catch (Exception $e) {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "El periodo lectivo no se pudo actualizar exitosamente...Error: " . $e->getMessage(),
				"tipo_mensaje" => "error",
				"query"        => $qry
			);
		}

		return json_encode($data);
	}

	function cerrarPeriodoLectivo($id_periodo_lectivo)
	{
		$qry = parent::consulta("call sp_cerrar_periodo_lectivo(" . $id_periodo_lectivo . ")");
		if ($qry) {
			$data = array(
				"titulo"       => "Operación exitosa.",
				"mensaje"      => "El periodo lectivo se ha cerrado exitosamente...",
				"tipo_mensaje" => "success"
			);
			return json_encode($data);
		} else {
			$data = array(
				"titulo"       => "Ocurrió un error inesperado.",
				"mensaje"      => "El periodo lectivo no se pudo cerrar exitosamente...Error: " . mysqli_error($this->conexion),
				"tipo_mensaje" => "error"
			);
			return json_encode($data);
		}
	}

	function listarPeriodosLectivos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_lectivo ORDER BY pe_anio_inicio DESC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($periodo_lectivo = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$cadena .= "<tr>\n";
				$code = $periodo_lectivo["id_periodo_lectivo"];
				$pe_anio_inicio = $periodo_lectivo["pe_anio_inicio"];
				$pe_anio_fin = $periodo_lectivo["pe_anio_fin"];
				$pe_estado = $periodo_lectivo["pe_estado"];
				$estado = ($pe_estado == 'A') ? 'ACTUAL' : 'TERMINADO';
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$pe_anio_inicio</td>\n";
				$cadena .= "<td>$pe_anio_fin</td>\n";
				$cadena .= "<td>$estado</td>\n";
				$disabled = ($pe_estado == 'T') ? ' disabled' : '';
				$cadena .= "<td>\n";
				$cadena .= "<div class=\"btn-group\">\n";
				$cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning item-edit\" data=\"" . $code . "\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan=\"8\">A&uacute;n no se han definido periodos lectivos...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function mostrarTitulosPeriodos($alineacion, $id_tipo_periodo, $id_periodo_lectivo)
	{
		if (!isset($alineacion)) $alineacion = "center";
		// Consulto el tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)
		$consulta = parent::consulta("SELECT pe_abreviatura, pe_principal FROM sw_periodo_evaluacion WHERE pe_principal = " . $id_tipo_periodo . " AND id_periodo_lectivo = " . $id_periodo_lectivo);
		$periodo = parent::fetch_assoc($consulta);
		$tipo_periodo = $periodo["pe_principal"];
		$abreviatura = $periodo["pe_abreviatura"];

		$mensaje = "<table id=\"titulos_periodos\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE pe_principal = 1 AND id_periodo_lectivo = " . $id_periodo_lectivo);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			while ($titulo_periodo = parent::fetch_assoc($consulta)) {
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">" . $titulo_periodo["pe_abreviatura"] . "</td>\n";
			}

			if ($tipo_periodo > 1) {
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">PROM</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">" . $abreviatura . "</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">PROM</td>\n";
				$mensaje .= "<td width=\"*\" align=\"" . $alineacion . "\">OBSERVACION</td>\n";
			} else {
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">SUMA</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">PROM</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">SUP.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">REM.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">GRA.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">P.F.</td>\n";
				$mensaje .= "<td width=\"*\" align=\"" . $alineacion . "\">OBSERVACION</td>\n";
			}
		}
		//$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tama�o de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
}
