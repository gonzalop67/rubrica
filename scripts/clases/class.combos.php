<?php

class selects extends MySQL
{
	var $code = "";
	var $id_modalidad = "";
	var $id_dia_semana = "";
	var $id_horario_def = "";
	var $id_periodo_lectivo = "";
	var $id_aporte_evaluacion = "";
	var $id_periodo_evaluacion = "";

	function cargarAreas()
	{
		$consulta = parent::consulta("SELECT id_area, ar_nombre FROM sw_area ORDER BY ar_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($area = parent::fetch_assoc($consulta)) {
				$code = $area["id_area"];
				$name = $area["ar_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarPeriodosL()
	{
		$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
		$sql = "SELECT * FROM sw_modalidad WHERE mo_activo = 1 ORDER BY mo_orden";
		$consulta = parent::consulta($sql);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($oferta = parent::fetch_assoc($consulta)) {
				$code = $oferta["id_modalidad"];
				$name = $oferta["mo_nombre"];
				$cadena .= "<optgroup label='$name'>\n";
				$consulta2 = parent::consulta("SELECT p.*, pe_descripcion FROM sw_periodo_lectivo p, sw_periodo_estado pe WHERE pe.id_periodo_estado = p.id_periodo_estado AND id_modalidad = $code ORDER BY pe_fecha_inicio DESC");
				while ($periodo_lectivo = parent::fetch_assoc($consulta2)) {
					$code2 = $periodo_lectivo["id_periodo_lectivo"];
					$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
					$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
					$name2 = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0] . " [" . $periodo_lectivo["pe_descripcion"] . "]";
					$cadena .= "<option value=\"$code2\">$name2</option>";
				}
				$cadena .= "</optgroup>\n";
			}
		}
		return $cadena;
	}

	function cargarPeriodosLectivosVigentes()
	{
		$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
		$consulta = parent::consulta("SELECT p.*, mo.* FROM sw_periodo_lectivo p, sw_periodo_estado pe, sw_modalidad mo WHERE pe.id_periodo_estado = p.id_periodo_estado AND mo.id_modalidad = p.id_modalidad AND pe.pe_descripcion = 'ACTUAL' ORDER BY mo_orden");
		$cadena = "";
		while ($periodo_lectivo = parent::fetch_assoc($consulta)) {
			$code = $periodo_lectivo["id_periodo_lectivo"];
			$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
			$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
			$name = $periodo_lectivo["mo_nombre"] . " - " . $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
		return $cadena;
	}

	function cargarPeriodosLectivosPorIdModalidad()
	{
		$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
		$consulta = parent::consulta("SELECT p.*, pe_descripcion FROM sw_periodo_lectivo p, sw_periodo_estado pe WHERE pe.id_periodo_estado = p.id_periodo_estado AND id_modalidad = $this->id_modalidad ORDER BY pe_fecha_inicio DESC");
		$cadena = "";
		while ($periodo_lectivo = parent::fetch_assoc($consulta)) {
			$code = $periodo_lectivo["id_periodo_lectivo"];
			$fecha_inicial = explode("-", $periodo_lectivo["pe_fecha_inicio"]);
			$fecha_final = explode("-", $periodo_lectivo["pe_fecha_fin"]);
			$name = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0] . " [" . $periodo_lectivo["pe_descripcion"] . "]";
			$cadena .= "<option value=\"$code\">$name</option>";
		}
		return $cadena;
	}

	function cargarDefinicionGeneros()
	{
		$consulta = parent::consulta("SELECT * FROM sw_def_genero ORDER BY dg_nombre");
		$cadena = "";
		while ($genero = parent::fetch_assoc($consulta)) {
			$code = $genero["id_def_genero"];
			$name = $genero["dg_nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
		return $cadena;
	}

	function cargarDefinicionNacionalidades()
	{
		$consulta = parent::consulta("SELECT * FROM sw_def_nacionalidad ORDER BY id_def_nacionalidad");
		$cadena = "";
		while ($genero = parent::fetch_assoc($consulta)) {
			$code = $genero["id_def_nacionalidad"];
			$name = $genero["dn_nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
		return $cadena;
	}

	function cargarTiposDocumento()
	{
		$consulta = parent::consulta("SELECT * FROM sw_tipo_documento ORDER BY id_tipo_documento");
		$cadena = "";
		while ($tipo_documento = parent::fetch_assoc($consulta)) {
			$code = $tipo_documento["id_tipo_documento"];
			$name = $tipo_documento["td_nombre"];
			$cadena .= "<option value=\"$code\">$name</option>";
		}
		return $cadena;
	}

	function cargarOrdinalDiasSemana()
	{
		$consulta = parent::consulta("SELECT ds_ordinal, ds_nombre FROM sw_dia_semana WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY ds_ordinal ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($dia_semana = parent::fetch_assoc($consulta)) {
				$code = $dia_semana["ds_ordinal"];
				$name = $dia_semana["ds_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarDiasSemana()
	{
		$consulta = parent::consulta("SELECT id_dia_semana, ds_nombre FROM sw_dia_semana WHERE id_horario_def = " . $this->id_horario_def . " ORDER BY ds_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($dia_semana = parent::fetch_assoc($consulta)) {
				$code = $dia_semana["id_dia_semana"];
				$name = $dia_semana["ds_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarHorasClase()
	{
		$qry = "SELECT 
                id_hora_clase, 
                hc_nombre, 
                hc_orden, 
                DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
                DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin 
            FROM sw_hora_clase hc 
            WHERE id_horario_def = $this->id_horario_def  
            ORDER BY hc_orden ASC;";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($hora_clase = parent::fetch_assoc($consulta)) {
				$code = $hora_clase["id_hora_clase"];
				$name = $hora_clase["hc_nombre"] . " (" . $hora_clase["hora_inicio"] . " - " . $hora_clase["hora_fin"] . ")";
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarPerfiles()
	{
		$consulta = parent::consulta("SELECT id_perfil, pe_nombre FROM sw_perfil ORDER BY pe_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($perfiles = parent::fetch_assoc($consulta)) {
				$code = $perfiles["id_perfil"];
				$name = $perfiles["pe_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarTiposEducacion()
	{
		$consulta = parent::consulta("SELECT id_tipo_educacion, te_nombre FROM sw_tipo_educacion WHERE id_periodo_lectivo = $this->id_periodo_lectivo ORDER BY id_tipo_educacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($tipos_educacion = parent::fetch_assoc($consulta)) {
				$code = $tipos_educacion["id_tipo_educacion"];
				$name = $tipos_educacion["te_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarTiposAsignatura()
	{
		$consulta = parent::consulta("SELECT id_tipo_asignatura, ta_descripcion FROM sw_tipo_asignatura");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($tipos_asignatura = parent::fetch_assoc($consulta)) {
				$code = $tipos_asignatura["id_tipo_asignatura"];
				$name = $tipos_asignatura["ta_descripcion"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarEspecialidades()
	{
		$consulta = parent::consulta("SELECT id_especialidad, es_figura FROM sw_especialidad e, sw_tipo_educacion t WHERE e.id_tipo_educacion = t.id_tipo_educacion AND id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY id_especialidad ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($especialidades = parent::fetch_assoc($consulta)) {
				$code = $especialidades["id_especialidad"];
				$name = $especialidades["es_figura"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarJornadas()
	{
		$consulta = parent::consulta("SELECT id_jornada, jo_nombre FROM sw_jornada ORDER BY id_jornada ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($jornada = parent::fetch_assoc($consulta)) {
				$code = $jornada["id_jornada"];
				$name = $jornada["jo_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarTiposPeriodoEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_tipo_periodo, tp_descripcion FROM sw_tipo_periodo ORDER BY id_tipo_periodo ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($tipo_periodo = parent::fetch_assoc($consulta)) {
				$code = $tipo_periodo["id_tipo_periodo"];
				$name = $tipo_periodo["tp_descripcion"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarTiposAporteEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_tipo_aporte, ta_descripcion FROM sw_tipo_aporte ORDER BY id_tipo_aporte ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($tipo_aporte = parent::fetch_assoc($consulta)) {
				$code = $tipo_aporte["id_tipo_aporte"];
				$name = $tipo_aporte["ta_descripcion"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarPeriodosEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY pe_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarPeriodosEvaluacionPrincipales()
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_tipo_periodo IN (1, 7) ORDER BY id_periodo_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarPeriodosEvaluacionPrincipales2()
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion, pe_nombre FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = " . $this->id_periodo_lectivo . " AND id_tipo_periodo = 1 ORDER BY id_periodo_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($periodos_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $periodos_evaluacion["id_periodo_evaluacion"];
				$name = $periodos_evaluacion["pe_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAportesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " ORDER BY ap_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAportesPrincipalesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND id_tipo_aporte = 1 ORDER BY id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarRubricasEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_rubrica_evaluacion, ru_nombre FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $this->id_aporte_evaluacion . " ORDER BY id_rubrica_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($rubricas_evaluacion = parent::fetch_assoc($consulta)) {
				$code = $rubricas_evaluacion["id_rubrica_evaluacion"];
				$name = $rubricas_evaluacion["ru_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarCursos()
	{
		$consulta = parent::consulta("SELECT es_figura, id_curso, cu_nombre FROM sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND t.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY c.id_especialidad, cu_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($cursos = parent::fetch_assoc($consulta)) {
				$code = $cursos["id_curso"];
				$name = "[" . $cursos["es_figura"] . "] " . $cursos["cu_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarCursosSuperiores()
	{
		$consulta = parent::consulta("SELECT id_curso_superior, cs_nombre FROM sw_curso_superior WHERE id_periodo_lectivo = " . $this->code . " ORDER BY id_curso_superior ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($cursos = parent::fetch_assoc($consulta)) {
				$code = $cursos["id_curso_superior"];
				$name = $cursos["cs_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarParalelos()
	{
		$consulta = parent::consulta("SELECT es_nombre, 
											 es_figura, 
											 cu_nombre, 
											 id_paralelo, 
											 pa_nombre, 
											 pa_orden, 
											 jo_nombre
										FROM sw_paralelo p, 
											 sw_curso c, 
											 sw_especialidad e, 
											 sw_tipo_educacion t, 
											 sw_jornada j
									   WHERE p.id_curso = c.id_curso 
									     AND c.id_especialidad = e.id_especialidad 
										 AND e.id_tipo_educacion = t.id_tipo_educacion 
										 AND j.id_jornada = p.id_jornada 
										 AND t.id_periodo_lectivo = " . $this->code
			. " ORDER BY pa_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["cu_nombre"] . " " . $paralelos["pa_nombre"] . " - " . $paralelos["es_figura"] . " - " . $paralelos["jo_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarParalelosAnteriores($id_modalidad)
	{
		// Primero obtener el id_periodo_lectivo anterior considerando el id_modalidad
		$query = parent::consulta("SELECT MAX(id_periodo_lectivo) AS max_id 
									 FROM sw_periodo_lectivo 
									WHERE id_modalidad = $id_modalidad");
		$registro = parent::fetch_object($query);
		$max_id_periodo_lectivo = $registro->max_id;
		$query = parent::consulta("SELECT MAX(id_periodo_lectivo) AS id_periodo_lectivo
									 FROM sw_periodo_lectivo
									WHERE id_modalidad = $id_modalidad
									  AND id_periodo_lectivo < $max_id_periodo_lectivo");
		$num_total_registros = parent::num_rows($query);
		$cadena = "";
		if ($num_total_registros > 0) {
			$periodo_lectivo = parent::fetch_object($query);
			$id_periodo_lectivo_anterior = $periodo_lectivo->id_periodo_lectivo;
			$consulta = parent::consulta("SELECT es_nombre, 
											 	 es_figura, 
											 	 cu_nombre, 
											  	 id_paralelo, 
											 	 pa_nombre, 
											 	 pa_orden, 
											 	 jo_nombre
										    FROM sw_paralelo p, 
											     sw_curso c, 
											     sw_especialidad e, 
											     sw_tipo_educacion t, 
											     sw_jornada j
									       WHERE p.id_curso = c.id_curso 
									         AND c.id_especialidad = e.id_especialidad 
										     AND e.id_tipo_educacion = t.id_tipo_educacion 
										     AND j.id_jornada = p.id_jornada 
										     AND t.id_periodo_lectivo = $id_periodo_lectivo_anterior 
								           ORDER BY pa_orden ASC");
			$num_total_registros = parent::num_rows($consulta);

			if ($num_total_registros > 0) {
				while ($paralelos = parent::fetch_assoc($consulta)) {
					$code = $paralelos["id_paralelo"];
					$name = $paralelos["cu_nombre"] . " " . $paralelos["pa_nombre"] . " - " . $paralelos["es_figura"] . " - " . $paralelos["jo_nombre"];
					$cadena .= "<option value=\"$code\">$name</option>";
				}
			}
		}
		return $cadena;
	}

	function cargarParalelosEspecialidad()
	{
		$consulta = parent::consulta("SELECT es_nombre, 
											 es_figura, 
											 cu_nombre, 
											 id_paralelo, 
											 pa_nombre, 
											 pa_orden, 
											 jo_nombre
										FROM sw_paralelo p, 
											 sw_curso c, 
											 sw_especialidad e, 
											 sw_tipo_educacion t, 
											 sw_jornada j
									   WHERE p.id_curso = c.id_curso 
									     AND c.id_especialidad = e.id_especialidad 
										 AND e.id_tipo_educacion = t.id_tipo_educacion 
										 AND j.id_jornada = p.id_jornada 
										 AND t.id_periodo_lectivo = " . $this->code
			. " ORDER BY pa_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["cu_nombre"] . " " . $paralelos["pa_nombre"] . " - " . $paralelos["es_figura"] . " - " . $paralelos["jo_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarParalelosVigentes()
	{
		$consulta = parent::consulta("SELECT es_nombre, es_figura, cu_nombre, id_paralelo, pa_nombre, pa_orden, jo_nombre FROM sw_paralelo p, sw_periodo_lectivo pl, sw_curso c, sw_especialidad e, sw_tipo_educacion t, sw_jornada j WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND j.id_jornada = p.id_jornada AND pl.id_periodo_lectivo = t.id_periodo_lectivo AND pl.id_periodo_lectivo = p.id_periodo_lectivo AND pl.pe_estado = 'A' ORDER BY es_figura, pa_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["cu_nombre"] . " " . $paralelos["pa_nombre"] . " - " . $paralelos["es_figura"] . " - " . $paralelos["jo_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarParalelosPeriodoLectivo()
	{
		$consulta = parent::consulta("SELECT es_nombre, 
											 es_figura, 
											 cu_nombre, 
											 id_paralelo, 
											 pa_nombre, 
											 pa_orden, 
											 jo_nombre
										FROM sw_paralelo p, 
											 sw_curso c, 
											 sw_especialidad e, 
											 sw_tipo_educacion t, 
											 sw_jornada j
									   WHERE p.id_curso = c.id_curso 
									     AND c.id_especialidad = e.id_especialidad 
										 AND e.id_tipo_educacion = t.id_tipo_educacion 
										 AND j.id_jornada = p.id_jornada 
										 AND p.id_periodo_lectivo = " . $this->id_periodo_lectivo
			. " ORDER BY pa_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["cu_nombre"] . " " . $paralelos["pa_nombre"] . " - " . $paralelos["es_figura"] . " - " . $paralelos["jo_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarParalelosDocente($id_periodo_lectivo, $id_usuario)
	{
		$consulta = parent::consulta("SELECT DISTINCT es_nombre, cu_nombre, pa.id_paralelo, pa_nombre FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE pa.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND t.id_periodo_lectivo = $id_periodo_lectivo AND pa.id_usuario = $id_usuario ORDER BY c.id_especialidad, c.id_curso, pa_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$code = $paralelos["id_paralelo"];
				$name = "[" . $paralelos["es_nombre"] . "] " . $paralelos["cu_nombre"] . " - " . $paralelos["pa_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasListBox($id_paralelo)
	{
		$consulta = parent::consulta("SELECT id_asignatura, as_nombre FROM sw_asignatura a, sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso
AND a.id_curso = c.id_curso AND p.id_paralelo = $id_paralelo ORDER BY as_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasAsociadas($id_paralelo)
	{
		$consulta = parent::consulta("SELECT a.id_asignatura, as_nombre FROM sw_asignatura_curso ac, sw_asignatura a, sw_paralelo p WHERE ac.id_curso = p.id_curso AND ac.id_asignatura = a.id_asignatura AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasPorCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT as_nombre, ac.id_asignatura FROM sw_asignatura_curso ac, sw_asignatura a WHERE ac.id_asignatura = a.id_asignatura AND ac.id_curso = $id_curso ORDER BY ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasPorParalelo($id_paralelo)
	{
		$consulta = parent::consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasSupletorio($id_paralelo)
	{
		$consulta = parent::consulta("SELECT as_nombre, a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo AND id_tipo_asignatura = 1 ORDER BY ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturasParalelo($id_periodo_lectivo, $id_usuario, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT as_nombre, pa.id_asignatura FROM sw_paralelo_asignatura pa, sw_asignatura a WHERE pa.id_asignatura = a.id_asignatura AND pa.id_periodo_lectivo = $id_periodo_lectivo AND pa.id_usuario = $id_usuario AND pa.id_paralelo = $id_paralelo ORDER BY as_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$name = $asignatura["as_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarAsignaturas()
	{
		$consulta = parent::consulta("SELECT a.*, ar_nombre as area FROM sw_asignatura a, sw_area ar WHERE ar.id_area = a.id_area ORDER BY as_nombre");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($asignatura = parent::fetch_assoc($consulta)) {
				$code = $asignatura["id_asignatura"];
				$area = $asignatura["area"];
				$name = $asignatura["as_nombre"] . " [" . $area . "] - (" . $code . ")";
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarDocentes()
	{
		$consulta = parent::consulta("SELECT up.id_usuario, "
			. "       us_titulo, "
			. "       us_apellidos, "
			. "       us_nombres "
			. "  FROM sw_usuario u, "
			. "       sw_perfil p, "
			. "       sw_usuario_perfil up "
			. " WHERE u.id_usuario = up.id_usuario "
			. "   AND p.id_perfil = up.id_perfil "
			. "   AND p.pe_nombre = 'DOCENTE' "
			. "   AND us_activo = 1 "
			. "ORDER BY us_apellidos ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($docentes = parent::fetch_assoc($consulta)) {
				$code = $docentes["id_usuario"];
				$name = $docentes["us_titulo"] . " " . $docentes["us_apellidos"] . " " . $docentes["us_nombres"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarTutores()
	{
		$consulta = parent::consulta("SELECT up.id_usuario, "
			. "       us_titulo, "
			. "       us_apellidos, "
			. "       us_nombres "
			. "  FROM sw_usuario u, "
			. "       sw_perfil p, "
			. "       sw_usuario_perfil up "
			. " WHERE u.id_usuario = up.id_usuario "
			. "   AND p.id_perfil = up.id_perfil "
			. "   AND p.pe_nombre = 'TUTOR' "
			. "   AND us_activo = 1 "
			. "ORDER BY us_apellidos ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($docentes = parent::fetch_assoc($consulta)) {
				$code = $docentes["id_usuario"];
				$name = $docentes["us_titulo"] . " "  . $docentes["us_apellidos"] . " " . $docentes["us_nombres"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarInspectores()
	{
		$consulta = parent::consulta("SELECT up.id_usuario, "
			. "       us_titulo, "
			. "       us_apellidos, "
			. "       us_nombres "
			. "  FROM sw_usuario u, "
			. "       sw_perfil p, "
			. "       sw_usuario_perfil up "
			. " WHERE u.id_usuario = up.id_usuario "
			. "   AND p.id_perfil = up.id_perfil "
			. "   AND p.pe_nombre = 'INSPECCION' "
			. "   AND us_activo = 1 "
			. " ORDER BY us_apellidos ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($docentes = parent::fetch_assoc($consulta)) {
				$code = $docentes["id_usuario"];
				$name = $docentes["us_apellidos"] . " " . $docentes["us_nombres"] . ", " . $docentes["us_titulo"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarClubes()
	{
		$consulta = parent::consulta("SELECT id_club, cl_nombre FROM sw_club ORDER BY cl_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($club = parent::fetch_assoc($consulta)) {
				$code = $club["id_club"];
				$name = $club["cl_nombre"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}

	function cargarUsuarios()
	{
		$consulta = parent::consulta("SELECT id_usuario, us_fullname FROM sw_usuario WHERE us_activo = 1 ORDER BY us_apellidos, us_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($usuario = parent::fetch_assoc($consulta)) {
				$code = $usuario["id_usuario"];
				$name = "[" . $usuario["id_usuario"] . "] " . $usuario["us_fullname"];
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
	}
}
