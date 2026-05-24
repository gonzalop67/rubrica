<?php

class periodos_evaluacion extends MySQL
{

	var $code = "";
	var $pe_nombre = "";
	var $pe_principal = "";
	var $pe_abreviatura = "";
	var $pe_tipo = "";
	var $pe_ponderacion = "";
	var $nota_desde = "";
	var $nota_hasta = "";
	var $id_periodo_lectivo = "";
	var $id_curso = "";
	var $id_paralelo = "";

	function truncateFloat($number, $digitos)
	{
		if ($number > 0)
			return round($number - 5 * pow(10, - ($digitos + 1)), $digitos);
		else
			return $number;
	}

	function existePeriodoEvaluacion($campo, $nombre, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE $campo = '$nombre' AND id_periodo_lectivo = $id_periodo_lectivo");
		return parent::num_rows($consulta) > 0;
	}

	function obtenerNombrePeriodoEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->pe_nombre;
	}

	function getNombrePeriodoEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $id");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->pe_nombre;
	}

	function obtenerIdPeriodoEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE pe_nombre = '$nombre'");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->id_periodo_evaluacion;
	}

	function obtenerPeriodoEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerRangoSupletorio()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = " . $this->code);
		$registro = parent::fetch_object($consulta);
		$id_periodo_lectivo = $registro->id_periodo_lectivo;
		$id_tipo_periodo = $registro->id_tipo_periodo;

		$consulta = parent::consulta("SELECT * FROM sw_equivalencia_supletorios WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = $id_tipo_periodo");

		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerTipoPeriodo()
	{
		$consulta = parent::consulta("SELECT pe_principal FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdAporte()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_periodo_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdAporteEvaluacionSupRemGracia()
	{
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion FROM sw_aporte_evaluacion a, sw_aporte_paralelo_cierre ac, sw_periodo_evaluacion p WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_paralelo = " . $this->id_paralelo . " AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = " . $this->pe_principal);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function contarCalificacionesErroneas($id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS num_registros FROM sw_rubrica_estudiante r, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru WHERE r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND pe.id_periodo_evaluacion = $id_periodo_evaluacion AND re_calificacion > 10");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function listarCalificacionesErroneas($id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT as_nombre, es_apellidos, es_nombres, us_titulo, us_fullname, cu_nombre, pa_nombre, ap_nombre, ru_nombre, re_calificacion FROM sw_rubrica_estudiante r, sw_asignatura a, sw_estudiante e, sw_paralelo_asignatura pa, sw_usuario u, sw_periodo_evaluacion pe, sw_aporte_evaluacion ap, sw_rubrica_evaluacion ru, sw_curso cu, sw_paralelo p WHERE r.id_paralelo = pa.id_paralelo AND pa.id_paralelo = p.id_paralelo AND p.id_curso = cu.id_curso AND r.id_asignatura = pa.id_asignatura AND r.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND r.id_rubrica_personalizada = ru.id_rubrica_evaluacion AND ap.id_aporte_evaluacion = ru.id_aporte_evaluacion AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion AND r.id_estudiante = e.id_estudiante AND pe.id_periodo_evaluacion = $id_periodo_evaluacion AND (re_calificacion > 10 OR re_calificacion < 0) ORDER BY us_fullname, as_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$asignatura = $periodos_evaluacion["as_nombre"];
				$docente = $periodos_evaluacion["us_titulo"] . " " . $periodos_evaluacion["us_fullname"];
				$estudiante = $periodos_evaluacion["es_apellidos"] . " " . $periodos_evaluacion["es_nombres"];
				$curso = $periodos_evaluacion["cu_nombre"] . " \"" . $periodos_evaluacion["pa_nombre"] . "\"";
				$aporte = $periodos_evaluacion["ap_nombre"];
				$rubrica = $periodos_evaluacion["ru_nombre"];
				$calificacion = $periodos_evaluacion["re_calificacion"];
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"15%\">$asignatura</td>\n";
				$cadena .= "<td width=\"15%\">$docente</td>\n";
				$cadena .= "<td width=\"15%\">$estudiante</td>\n";
				$cadena .= "<td width=\"15%\">$curso</td>\n";
				$cadena .= "<td width=\"15%\">$aporte</td>\n";
				$cadena .= "<td width=\"15%\">$rubrica</td>\n";
				$cadena .= "<td width=\"5%\">$calificacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han encontrado calificaciones err&oacute;neas...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listar_periodos_evaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY id_periodo_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarPeriodoEvaluacion(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarPeriodoEvaluacion(" . $code . ")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Periodos de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargar_periodos_evaluacion($id_curso)
	{
		$qry = "SELECT pe.id_periodo_evaluacion, 
					   pe.pe_nombre, 
					   pc.id_periodo_evaluacion_curso,  
					   pc.pe_ponderacion, 
					   pc.pc_orden 
				  FROM sw_periodo_evaluacion pe, 
				       sw_periodo_evaluacion_curso pc 
				 WHERE pe.id_periodo_lectivo = pc.id_periodo_lectivo 
				   AND pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
				   AND pc.id_periodo_lectivo = $this->id_periodo_lectivo 
				   AND pc.id_curso = $id_curso 
				 ORDER BY pc.pc_orden ASC";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$id = $periodos_evaluacion["id_periodo_evaluacion"];
				$id_pc = $periodos_evaluacion["id_periodo_evaluacion_curso"];
				$name = $periodos_evaluacion["pe_nombre"];
				$orden = $periodos_evaluacion["pc_orden"];
				$ponderacion = $periodos_evaluacion["pe_ponderacion"] * 100;

				$cadena .= "<tr data-index='$id_pc' data-orden='$orden'>\n";

				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$ponderacion%</td>\n";

				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarPeriodoEvaluacionModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarPeriodoEvaluacion(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>";
				$cadena .= "</td>\n";

				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han definido Periodos de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function insertarPeriodoEvaluacion()
	{
		$qry = "INSERT INTO sw_periodo_evaluacion (id_periodo_lectivo, id_tipo_periodo, pe_nombre, pe_abreviatura, pe_ponderacion, rango_desde, rango_hasta) VALUES (";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= $this->pe_tipo . ",";
		$qry .= "'" . $this->pe_nombre . "',";
		$qry .= "'" . $this->pe_abreviatura . "',";
		$qry .= $this->pe_ponderacion . ",";
		if ($this->pe_tipo != 1) {
			$qry .= $this->nota_desde . ",";
			$qry .= $this->nota_hasta . ")";
		} else {
			$qry .= "null, null)";
		}
		if ($this->existePeriodoEvaluacion('pe_nombre', $this->pe_nombre, $this->id_periodo_lectivo)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un periodo de evaluación con ese nombre en el presente periodo lectivo.",
				'estado' => 'error'
			];
		} else if ($this->existePeriodoEvaluacion('pe_abreviatura', $this->pe_abreviatura, $this->id_periodo_lectivo)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un periodo de evaluación con esa abreviatura en el presente periodo lectivo.",
				'estado' => 'error'
			];
		} else {
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
		}
		return json_encode($datos);
	}

	function actualizarPeriodoEvaluacion()
	{
		$qry = "UPDATE sw_periodo_evaluacion SET ";
		$qry .= "pe_nombre = '" . $this->pe_nombre . "',";
		$qry .= "pe_abreviatura = '" . $this->pe_abreviatura . "',";
		$qry .= "id_tipo_periodo = " . $this->pe_tipo . ",";
		if ($this->pe_tipo != 1 && $this->pe_tipo != 7) {
			$qry .= "pe_ponderacion = null,";
			$qry .= "rango_desde = " . $this->nota_desde . ",";
			$qry .= "rango_hasta = " . $this->nota_hasta;
		} else {
			$qry .= "pe_ponderacion = " . $this->pe_ponderacion . ",";
			$qry .= "rango_desde = null,";
			$qry .= "rango_hasta = null";
		}
		$qry .= " WHERE id_periodo_evaluacion = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $this->code");
		$registro = parent::fetch_assoc($consulta);
		$nombreActual = $registro["pe_nombre"];
		$abreviaturaActual = $registro["pe_abreviatura"];

		if ($nombreActual != $this->pe_nombre && $this->existePeriodoEvaluacion('pe_nombre', $this->pe_nombre, $this->id_periodo_lectivo)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un periodo de evaluación con ese nombre en el presente periodo lectivo.",
				'estado' => 'error'
			];
		} else if ($abreviaturaActual != $this->pe_abreviatura && $this->existePeriodoEvaluacion('pe_abreviatura', $this->pe_abreviatura, $this->id_periodo_lectivo)) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un periodo de evaluación con esa abreviatura en el presente periodo lectivo.",
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

	function eliminarPeriodoEvaluacion()
	{
		$qry = "SELECT * FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion=" . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			//Mensaje de operación fallida
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar el Periodo de Evaluacion, porque tiene Aportes de Evaluacion asociados.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion=" . $this->code;
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
					'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
					'estado' => 'error'
				];
			}
		}
		return json_encode($datos);
	}

	function mostrarTitulosPeriodos($alineacion)
	{
		if (!isset($alineacion)) $alineacion = "center";
		// Consulto el tipo de periodo (2: supletorio; 3: remedial; 4: de gracia)
		$qry = "SELECT pe_abreviatura, id_tipo_periodo FROM sw_periodo_evaluacion WHERE id_tipo_periodo = $this->pe_principal AND id_periodo_lectivo = $this->id_periodo_lectivo";

		$consulta = parent::consulta($qry);
		$periodo = parent::fetch_assoc($consulta);
		$tipo_periodo = $periodo["id_tipo_periodo"];
		$abreviatura = $periodo["pe_abreviatura"];

		$mensaje = "<table id=\"titulos_periodos\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$consulta = parent::consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE pe_principal = 1 AND id_periodo_lectivo = " . $this->id_periodo_lectivo);
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
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function calcularPromedioQuimestre($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura)
	{
		// Primero se calcula el promedio de cada parcial
		$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
		$suma_aportes = 0;
		$contador_aportes = 0;
		$suma_promedios = 0;
		while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
			$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
			$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
			$suma_rubricas = 0;
			$contador_rubricas = 0;
			$total_rubricas = parent::num_rows($rubrica_evaluacion);
			while ($rubricas = parent::fetch_assoc($rubrica_evaluacion)) {
				$contador_rubricas++;
				$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
				$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
				$total_registros = parent::num_rows($qry);
				if ($total_registros > 0) {
					$rubrica_estudiante = parent::fetch_assoc($qry);
					$calificacion = $rubrica_estudiante["re_calificacion"];
				} else {
					$calificacion = 0;
				}
				$suma_rubricas += $calificacion;
			}

			$promedio = $suma_rubricas / $contador_rubricas;

			if ($contador_aportes < $total_rubricas)
				$suma_promedios += $promedio;
			else
				$examen_quimestral = $promedio;

			$contador_aportes++;
		}

		// Aqui debo calcular el ponderado de los promedios parciales
		$promedio_aportes = $this->truncateFloat($suma_promedios / ($contador_aportes - 1), 2);
		$ponderado_aportes = $this->truncateFloat(0.8 * $promedio_aportes, 2);
		$ponderado_examen = $this->truncateFloat(0.2 * $examen_quimestral, 2);

		$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

		return $calificacion_quimestral;
	}
}
