<?php

class aportes_evaluacion extends MySQL
{

	var $code = "";
	var $id_curso = "";
	var $id_paralelo = "";
	var $ap_nombre = "";
	var $ap_abreviatura = "";
	var $ap_descripcion = "";
	var $id_tipo_aporte = "";
	var $ap_fecha_apertura = "";
	var $ap_fecha_cierre = "";
	var $ap_fecha_inicio = "";
	var $ap_ponderacion = "";
	var $ap_fecha_fin = "";
	var $id_periodo_lectivo = "";
	var $id_periodo_evaluacion = "";
	var $id_asignatura = "";
	var $id_tipo_asignatura = "";

	function existeAporteEvaluacion($campo, $valor, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT * FROM sw_aporte_evaluacion WHERE $campo = '$valor' AND id_periodo_evaluacion = $id_periodo_evaluacion");
		return parent::num_rows($consulta) > 0;
	}

	function obtenerIdAporteEvaluacion($nombre)
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE ap_nombre = '$nombre'");
		$periodo_evaluacion = parent::fetch_object($consulta);
		return $periodo_evaluacion->id_periodo_evaluacion;
	}

	function obtenerTipoAporte()
	{
		$consulta = parent::consulta("SELECT ta_descripcion FROM sw_aporte_evaluacion ap, sw_tipo_aporte ta WHERE ta.id_tipo_aporte = ap.id_tipo_aporte AND id_aporte_evaluacion = " . $this->code);
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->ta_descripcion;
	}

	function obtenerAporteEvaluacion()
	{
		$consulta = parent::consulta("SELECT * FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombreAporteEvaluacion($id)
	{
		$consulta = parent::consulta("SELECT pe_nombre, ap_nombre FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND id_aporte_evaluacion = $id");
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->pe_nombre . " - " . $aporte_evaluacion->ap_nombre;
	}

	function getNombreAporte($id)
	{
		$consulta = parent::consulta("SELECT ap_nombre FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id");
		$aporte_evaluacion = parent::fetch_object($consulta);
		return $aporte_evaluacion->ap_nombre;
	}

	function listarAportesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"editarAporteEvaluacion(" . $code . ")\">editar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Parciales para el Quimestre seleccionado...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarAportesEvaluacionSecretaria()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarAporteEvaluacion(" . $code . ")\">seleccionar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Parciales para el Quimestre seleccionado...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function contarInsumos($id_aporte_evaluacion, $id_asignatura)
	{
		$consulta = parent::consulta("SELECT COUNT(*) AS total_registros FROM `sw_rubrica_evaluacion` r, `sw_asignatura` a WHERE a.id_tipo_asignatura = r.id_tipo_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion AND id_asignatura = $id_asignatura");
		$registro = parent::fetch_assoc($consulta);
		$num_insumos = $registro["total_registros"];
		return $num_insumos;
	}

	function obtenerNombresInsumos($id_aporte_evaluacion, $id_asignatura)
	{
		$consulta = parent::consulta("SELECT ru_nombre FROM `sw_rubrica_evaluacion` r, `sw_asignatura` a WHERE a.id_tipo_asignatura = r.id_tipo_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion AND id_asignatura = $id_asignatura");
		return $consulta;
	}

	function cargarAportesEvaluacion()
	{
		$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_nombre, ap_ponderacion, ap_orden, ap_estado FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion ORDER BY ap_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$id = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$ponderacion = ($aportes_evaluacion["ap_ponderacion"] * 100) . "%";
				$orden = $aportes_evaluacion["ap_orden"];
				$estado = $aportes_evaluacion["ap_estado"] == "A" ? "Activo" : "Cerrado";
				$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$ponderacion</td>\n";
				$cadena .= "<td>$estado</td>\n";

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
			$cadena .= "<td colspan='4' align='center'>No se han definido Aportes de Evaluaci&oacute;n para este Per&iacute;odo de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function asociarAporteCurso()
	{
		//Primero compruebo que el registro no está insertado...
		$qry = "SELECT * FROM sw_aporte_curso_cierre WHERE id_curso = " . $this->id_curso . " AND id_aporte_evaluacion = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0)
			return "El Aporte ya se encuentra asociado al Curso...";
		else {
			$qry = "INSERT INTO sw_aporte_curso_cierre (id_aporte_evaluacion, id_curso, ap_estado) VALUES (";
			$qry .= $this->code . ",";
			$qry .= $this->id_curso . ",'C')";
			$consulta = parent::consulta($qry);
			$mensaje = "Aporte de Evaluaci&oacute;n asociado exitosamente...";
		}
		return $mensaje;
	}

	function eliminarAsociacionAporteCurso()
	{
		$qry = "DELETE FROM sw_aporte_curso_cierre WHERE id_curso = " . $this->id_curso . " AND id_aporte_evaluacion=" . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asociacion eliminada exitosamente...";

		return $mensaje;
	}

	function listarAportesAsociados()
	{
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion, ap_nombre, ac.ap_estado, ac.id_curso, cu_nombre, es_nombre FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac, sw_curso c, sw_especialidad e WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND ac.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND a.id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND ac.id_curso = " . $this->id_curso . " ORDER BY a.id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$estado = $aportes_evaluacion["ap_estado"];
				if ($estado == 'A') {
					$mensaje = 'ABIERTO';
					$accion = 'cerrar';
				} else {
					$mensaje = 'CERRADO';
					$accion = 'reabrir';
				}
				$id_curso = $aportes_evaluacion["id_curso"];
				$curso = "[" . $aportes_evaluacion["es_nombre"] . "] " . $aportes_evaluacion["cu_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"5%\" align=\"left\">$mensaje</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $id_curso . "," . $code . ")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listar_aportes_evaluacion($tipo_aporte)
	{
		if (!isset($tipo_aporte)) $tipo_aporte = 1;
		$consulta = parent::consulta("SELECT a.id_aporte_evaluacion, ap_nombre, ac.ap_estado, ac.ap_fecha_apertura, ac.ap_fecha_cierre FROM sw_aporte_evaluacion a, sw_aporte_curso_cierre ac WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion AND a.id_periodo_evaluacion = " . $this->id_periodo_evaluacion . " AND id_curso = " . $this->id_curso . " ORDER BY a.id_aporte_evaluacion ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($aportes_evaluacion = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $aportes_evaluacion["id_aporte_evaluacion"];
				$name = $aportes_evaluacion["ap_nombre"];
				$estado = $aportes_evaluacion["ap_estado"];
				$fecha_apertura = $aportes_evaluacion["ap_fecha_apertura"];
				$fecha_cierre = $aportes_evaluacion["ap_fecha_cierre"];
				if ($estado == 'A') {
					$mensaje = 'ABIERTO';
					$accion = 'cerrar';
				} else {
					$mensaje = 'CERRADO';
					$accion = 'reabrir';
				}
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				if ($tipo_aporte == 1) {
					$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarAporteEvaluacion(" . $code . ")\">editar</a></td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAporteEvaluacion(" . $code . ")\">eliminar</a></td>\n";
				} else if ($tipo_aporte == 2) {
					$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"seleccionarAporteEvaluacion(" . $code . ")\">seleccionar</a></td>\n";
				} else {
					$cadena .= "<td width=\"16%\" align=\"left\">$name</td>\n";
					$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechaapertura_" . $contador . "\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_apertura\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_apertura" . $contador . "\" name=\"calendario_apertura" . $contador . "\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechaapertura_" . $contador . "\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_apertura" . $contador . "\"
					}
				);
			</script></td>\n";
					$cadena .= "<td width=\"16%\" align=\"left\"><input id=\"fechacierre_" . $contador . "\" class=\"cajaPequenia\" type=\"text\" value=\"$fecha_cierre\" onfocus=\"sel_texto(this)\" /> <img src=\"imagenes/calendario.png\" id=\"calendario_cierre" . $contador . "\" name=\"calendario_cierre" . $contador . "\" width=\"16\" height=\"16\" title=\"calendario\" alt=\"calendario\" onmouseover=\"style.cursor=cursor\"/> 
			<script type=\"text/javascript\">
				Calendar.setup(
					{
					inputField : \"fechacierre_" . $contador . "\",
					ifFormat   : \"%Y-%m-%d\",
					button     : \"calendario_cierre" . $contador . "\"
					}
				);
			</script></td>\n";
					$cadena .= "<td width=\"24%\" align=\"left\">$mensaje</td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"cerrarAporteEvaluacion(" . $code . ",'" . $estado . "')\">$accion</a></td>\n";
					$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"actualizarAporteEvaluacion(" . $code . ",'" . $name . "',document.getElementById('fechaapertura_" . $contador . "'),document.getElementById('fechacierre_" . $contador . "'))\">actualizar</a></td>\n";
				}
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido Cierres para los Aportes de Evaluaci&oacute;n...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function insertarAporteEvaluacion($id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT MAX(ap_orden) AS secuencial FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion");
		$num_total_registros = parent::num_rows($consulta);

		if ($num_total_registros == 0) {
			$ap_orden = 1;
		} else {
			$registro = parent::fetch_object($consulta);
			$ap_orden = $registro->secuencial + 1;
		}

		$qry = "INSERT INTO sw_aporte_evaluacion SET ";
		$qry .= "id_periodo_evaluacion = " . $this->id_periodo_evaluacion . ",";
		$qry .= "id_tipo_aporte = " . $this->id_tipo_aporte . ", ";
		$qry .= "ap_nombre = '" . $this->ap_nombre . "', ";
		$qry .= "ap_abreviatura = '" . $this->ap_abreviatura . "', ";
		$qry .= "ap_descripcion = '" . $this->ap_descripcion . "', ";
		$qry .= "ap_fecha_apertura = '" . $this->ap_fecha_apertura . "', ";
		$qry .= "ap_fecha_cierre = '" . $this->ap_fecha_cierre . "', ";
		$qry .= "ap_ponderacion = " . $this->ap_ponderacion . ", ";
		$qry .= "ap_orden = " . $ap_orden;

		if ($this->existeAporteEvaluacion('ap_nombre', $this->ap_nombre, $this->id_periodo_evaluacion)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un aporte de evaluación con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($this->existeAporteEvaluacion('ap_abreviatura', $this->ap_abreviatura, $this->id_periodo_evaluacion)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un aporte de evaluación con esa abreviatura en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($this->existeAporteEvaluacion('ap_descripcion', $this->ap_descripcion, $this->id_periodo_evaluacion)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un aporte de evaluación con esa descripción en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				// Aqui voy a asociar el nuevo aporte de evaluacion con los cursos ya creados...
				$fecha_apertura = $this->ap_fecha_apertura;
				$fecha_cierre = $this->ap_fecha_cierre;
				$qry = "SELECT MAX(id_aporte_evaluacion) AS max_id_aporte_evaluacion FROM sw_aporte_evaluacion";
				$consulta = parent::consulta($qry);
				$resultado = parent::fetch_assoc($consulta);
				$id_aporte_evaluacion = $resultado["max_id_aporte_evaluacion"];

				// Asociar el aporte de evaluación creado con los paralelos creados para el cierre de periodos...
				$qry = "SELECT id_paralelo 
						FROM `sw_paralelo`  
						WHERE id_periodo_lectivo = " . $id_periodo_lectivo;
				$consulta = parent::consulta($qry);
				while ($paralelo = parent::fetch_assoc($consulta)) {
					$id_paralelo = $paralelo['id_paralelo'];
					$fecha_apertura = $this->ap_fecha_apertura;
					$fecha_cierre = $this->ap_fecha_cierre;
					// Insertar la asociación para el futuro cierre...
					$qry = "SELECT * 
							FROM `sw_aporte_paralelo_cierre` 
							WHERE id_aporte_evaluacion = $id_aporte_evaluacion 
							AND id_paralelo = $id_paralelo";
					$asociacion = parent::consulta($qry);
					if (parent::num_rows($asociacion) == 0) {
						$qry = "INSERT INTO `sw_aporte_paralelo_cierre` 
								(id_aporte_evaluacion, id_paralelo, ap_fecha_apertura, ap_fecha_cierre, ap_estado) 
								VALUES (
								$id_aporte_evaluacion, 
								$id_paralelo,
								'$fecha_apertura',
								'$fecha_cierre',
								'C'
								)";
						$resultado = parent::consulta($qry);
					}
				}

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

	function actualizarAporteEvaluacion()
	{
		$qry = "UPDATE sw_aporte_evaluacion SET ";
		$qry .= "ap_nombre = '" . $this->ap_nombre . "', ";
		$qry .= "ap_abreviatura = '" . $this->ap_abreviatura . "',";
		$qry .= "ap_descripcion = '" . $this->ap_descripcion . "',";
		$qry .= "id_tipo_aporte = " . $this->id_tipo_aporte . ",";
		$qry .= "ap_fecha_apertura = '" . $this->ap_fecha_apertura . "',";
		$qry .= "ap_fecha_cierre = '" . $this->ap_fecha_cierre . "',";
		$qry .= "ap_ponderacion = " . $this->ap_ponderacion;
		$qry .= " WHERE id_aporte_evaluacion = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $this->code");
		$registro = parent::fetch_assoc($consulta);

		$nombreActual = $registro["ap_nombre"];
		$abreviaturaActual = $registro["ap_abreviatura"];

		if ($nombreActual != $this->ap_nombre && $this->existeAporteEvaluacion('ap_nombre', $this->ap_nombre, $this->id_periodo_evaluacion)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un aporte de evaluación con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($abreviaturaActual != $this->ap_abreviatura && $this->existeAporteEvaluacion('ap_abreviatura', $this->ap_abreviatura, $this->id_periodo_evaluacion)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un aporte de evaluación con esa abreviatura en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Actualizado con éxito!",
					'mensaje' => "Aporte de Evaluacion " . $this->ap_nombre . " actualizado exitosamente...",
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

	function actualizarFechasAporteEvaluacion()
	{
		date_default_timezone_set('America/Guayaquil');

		$qry = "UPDATE sw_aporte_curso_cierre SET ";
		$qry .= "ap_fecha_apertura = '" . $this->ap_fecha_apertura . "',";
		$qry .= "ap_fecha_cierre = '" . $this->ap_fecha_cierre . "'";
		$qry .= " WHERE id_aporte_evaluacion = " . $this->code;
		$qry .= " AND id_curso = " . $this->id_curso;
		$consulta = parent::consulta($qry);

		// A ver... ahora si voy a actualizar el estado...
		$fechaactual = Date("Y-m-d H:i:s");

		if ($fechaactual > $this->ap_fecha_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'A' WHERE id_curso = " . $this->id_curso . " AND id_aporte_evaluacion=" . $this->code;
			$consulta = parent::consulta($qry);
		}

		if ($fechaactual > $this->ap_fecha_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = 'C' WHERE id_curso = " . $this->id_curso . " AND id_aporte_evaluacion=" . $this->code;
			$consulta = parent::consulta($qry);
		}

		$mensaje = "Aporte de Evaluacion " . $this->ap_nombre . " actualizado exitosamente...";

		return $mensaje;
		//return $qry;
	}

	function eliminarAporteEvaluacion()
	{
		$qry = "SELECT * FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion=" . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar este Aporte de Evaluacion porque tiene Rubricas de Evaluacion relacionadas...",
				'estado' => 'error'
			];
			// $mensaje = "No se puede eliminar este Aporte de Evaluacion porque tiene Rubricas de Evaluacion relacionadas...";
		} else {
			try {
				$qry = "DELETE FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion=" . $this->code;
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Eliminado con éxito!",
					'mensaje' => "Aporte de Evaluacion eliminado exitosamente...",
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

			// $mensaje = "Aporte de Evaluacion eliminado exitosamente...";
		}
		return json_encode($datos);
	}

	function cerrarAporteEvaluacion($estado)
	{
		($estado == 'C') ? $accion = 'cerrado' : $accion = 'reabierto';
		$qry = "UPDATE sw_aporte_curso_cierre SET ap_estado = '$estado' WHERE id_aporte_evaluacion=" . $this->code . " AND id_curso = " . $this->id_curso;
		$consulta = parent::consulta($qry);
		$mensaje = "Aporte de Evaluaci&oacute;n $accion exitosamente...";

		return $mensaje;
	}

	function mostrarEstadoRubrica()
	{
		// Y bueno primero tengo que "actualizar" el estado del aporte
		$consulta = parent::consulta("SELECT ap_fecha_apertura, ap_fecha_cierre, ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_paralelo = " . $this->id_paralelo);
		if ($consulta) {
			$aporte = parent::fetch_assoc($consulta);
			$f_apertura = $aporte["ap_fecha_apertura"];
			$f_cierre = $aporte["ap_fecha_cierre"];
			$estado = $aporte["ap_estado"];
		} else {
			return "FECHAS: NO DEFINIDAS";
		}

		// A ver... ahora si voy a actualizar el estado...
		date_default_timezone_set('America/Guayaquil');
		$fechaactual = Date("Y-m-d H:i:s");

		if ($fechaactual > $f_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'A' WHERE id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion=" . $this->code;
			$consulta = parent::consulta($qry);
		}

		if ($fechaactual > $f_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'C' WHERE id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion=" . $this->code;
			$consulta = parent::consulta($qry);
		}

		// Consulto el estado del aporte (A: Abierto; C: Cerrado)
		$consulta = parent::consulta("SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_paralelo = " . $this->id_paralelo);
		if ($consulta) {
			$aporte = parent::fetch_assoc($consulta);
			$estado = $aporte["ap_estado"];
			$estado = ($estado == 'A') ? 'ABIERTO' : 'CERRADO';
			return "ESTADO: " . $estado;
		} else {
			return "ESTADO: NO DEFINIDO";
		}
	}

	function mostrarFechaCierre()
	{
		// Consulto la fecha de cierre del aporte de evaluación
		$consulta = parent::consulta("SELECT ap_fecha_cierre FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_paralelo = " . $this->id_paralelo);
		if ($consulta) {
			$aporte = parent::fetch_assoc($consulta);
			$fecha = $aporte["ap_fecha_cierre"];
			return "Fecha de cierre: " . $fecha . " 00H:00";
		} else {
			return "FECHA: NO DEFINIDA";
		}
	}

	function determinarFechaApertura()
	{
		// Consulto la fecha de apertura del aporte de evaluación
		$consulta = parent::consulta("SELECT ap_fecha_apertura FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = " . $this->code . " AND id_paralelo = " . $this->id_paralelo);
		if ($consulta) {
			$aporte = parent::fetch_assoc($consulta);
			$fecha = $aporte["ap_fecha_apertura"];
			// Comparar la fecha actual con la fecha de apertura
			date_default_timezone_set('America/Guayaquil');
			$fechaactual = Date("Y-m-d H:i:s");
			if ($fechaactual > $fecha) {
				return "1";
			} else {
				return "2";
			}
		} else {
			return "3";
		}
	}

	function mostrarLeyendasRubricas($alineacion)
	{
		if (!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT ru_nombre, 
											 ru_abreviatura
										FROM sw_rubrica_evaluacion r,
										     sw_asignatura a
									   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
									     AND a.id_asignatura = " . $this->id_asignatura
			. " AND id_aporte_evaluacion = " . $this->code);
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			while ($rubrica = parent::fetch_assoc($consulta)) {
				$mensaje .= "<td width=\"162px\" align=\"" . $alineacion . "\">" . $rubrica["ru_abreviatura"] . ": " . $rubrica["ru_nombre"] . ";</td>\n";
			}
		}
		$consulta = parent::consulta("SELECT quien_inserta_comp FROM sw_curso WHERE id_curso = " . $this->id_curso);
		$resultado = parent::fetch_assoc($consulta);
		if ($resultado["quien_inserta_comp"] == 0) {
			$mensaje .= "<td width=\"150px\" align=\"" . $alineacion . "\">COMP: COMPORTAMIENTO</td>\n";
		}
		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tamanio de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function mostrarTitulosRubricasEstudiante()
	{
		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";
		$consulta = parent::consulta("SELECT ru_abreviatura,
											 ru_nombre
										FROM sw_rubrica_evaluacion
									   WHERE id_tipo_asignatura = 1
									     AND id_aporte_evaluacion = " . $this->code);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			while ($titulo_rubrica = parent::fetch_assoc($consulta)) {
				$mensaje .= "<td width=\"120px\" align=\"center\">" . $titulo_rubrica["ru_abreviatura"] . ": " . $titulo_rubrica["ru_nombre"] . "</td>\n";
			}
			if ($num_total_registros > 1) {
				$mensaje .= "<td width=\"120px\" align=\"center\">PROM.: PROMEDIO</td>\n";
			}
		}
		return $mensaje;
	}

	function mostrarTitulosRubricas($alineacion, $id_periodo_lectivo)
	{
		// Consulto el tipo de aporte (1: aporte parcial; 2: examen quimestral; 3: supletorio/remedial/de gracia)
		$consulta = parent::consulta("SELECT ta_descripcion FROM sw_aporte_evaluacion a, sw_tipo_aporte ta WHERE ta.id_tipo_aporte = a.id_tipo_aporte AND id_aporte_evaluacion = " . $this->code);
		$aporte = parent::fetch_assoc($consulta);
		$tipo_aporte = $aporte["ta_descripcion"];

		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		if ($tipo_aporte == 'PARCIAL') {
			$consulta = parent::consulta("SELECT ru_abreviatura,
												 id_rubrica_evaluacion,
												 ru_descripcion,
												 a.id_tipo_asignatura 
											FROM sw_rubrica_evaluacion r,
											     sw_asignatura a
										   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
										     AND a.id_asignatura = " . $this->id_asignatura
				. " AND id_aporte_evaluacion = " . $this->code);
			$num_total_registros = parent::num_rows($consulta);
			if ($num_total_registros > 0) {
				while ($titulo_rubrica = parent::fetch_assoc($consulta)) {
					$id = $titulo_rubrica["id_rubrica_evaluacion"];
					$desc = $titulo_rubrica["ru_descripcion"];
					$id_tipo_asignatura = $titulo_rubrica["id_tipo_asignatura"];
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">\n";
					$mensaje .= "<a href=\"#\" id=\"$id\" style=\"color: #fff;\" title=\"$desc\">" . $titulo_rubrica["ru_abreviatura"] . "</a>\n";
					$mensaje .= "</td>\n";
				}
				if ($id_tipo_asignatura == 1 && $num_total_registros > 1)
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\" title=\"PROMEDIO\">PROM</td>\n";
			}
			// $consulta = parent::consulta("SELECT quien_inserta_comp FROM sw_curso WHERE id_curso = " . $this->id_curso);
			$consulta = parent::consulta("SELECT quien_inserta_comp_id FROM sw_periodo_lectivo WHERE id_periodo_lectivo = " . $id_periodo_lectivo);
			$resultado = parent::fetch_assoc($consulta);
			if ($resultado["quien_inserta_comp_id"] == 2) {
				if ($id_tipo_asignatura != 2)
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\" title=\"COMPORTAMIENTO\">COMP</td>\n";
			}
		} else if ($tipo_aporte == 'EXAMEN_SUB_PERIODO' || $tipo_aporte == 'FASE_PRI') {
			if ($this->id_tipo_asignatura == 1) {
				$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_abreviatura, ap_descripcion, ap_ponderacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion ORDER BY ap_orden");
				$num_total_registros = parent::num_rows($consulta);
				if ($num_total_registros > 0) {
					while ($titulo_aporte = parent::fetch_assoc($consulta)) {
						$id = $titulo_aporte["id_aporte_evaluacion"];
						$desc = $titulo_aporte["ap_descripcion"];
						$ponderacion = $titulo_aporte["ap_ponderacion"];
						$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">\n";
						$mensaje .= "<a href=\"#\" id=\"$id\" style=\"color: #fff;\" title=\"$desc\">" . $titulo_aporte["ap_abreviatura"] . "</a>\n";
						$mensaje .= "</td>\n";
						$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">" . ($ponderacion * 100) . "%</td>\n";
					}
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">\n";
					$result = parent::consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion");
					$pe_abreviatura = parent::fetch_object($result)->pe_abreviatura;
					$mensaje .= "<a href=\"#\" style=\"color: #fff;\" title=\"NOTA DEL SUB PERIODO\">" . $pe_abreviatura . "</a>\n";
					$mensaje .= "</td>\n";
				}
			} else {
				$consulta = parent::consulta("SELECT id_aporte_evaluacion, ap_abreviatura, ap_descripcion, ap_ponderacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $this->id_periodo_evaluacion AND id_tipo_aporte IN (1, 2) ORDER BY ap_orden");
				$num_total_registros = parent::num_rows($consulta);
				if ($num_total_registros > 0) {
					while ($titulo_aporte = parent::fetch_assoc($consulta)) {
						$id = $titulo_aporte["id_aporte_evaluacion"];
						$desc = $titulo_aporte["ap_descripcion"];
						$ponderacion = $titulo_aporte["ap_ponderacion"];
						$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">\n";
						$mensaje .= "<a href=\"#\" id=\"$id\" style=\"color: #fff;\" title=\"$desc\">" . $titulo_aporte["ap_abreviatura"] . "</a>\n";
						$mensaje .= "</td>\n";
					}
				}
			}
		} else if ($tipo_aporte == 'PROYECTO_FINAL' || $tipo_aporte == 'EVAL_SUBNIVEL' || $tipo_aporte == 'SUPLETORIO') {
			// session_start();
			// $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

			$qryString = "SELECT pe.id_periodo_evaluacion, id_tipo_periodo, pe_abreviatura, pe_nombre, pc.pe_ponderacion FROM sw_periodo_evaluacion pe, sw_periodo_evaluacion_curso pc WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion AND pc.id_curso = $this->id_curso AND pc.id_periodo_lectivo = $id_periodo_lectivo ORDER BY pc_orden";

			// print_r($qryString); die();

			$consulta = parent::consulta($qryString);
			$num_total_registros = parent::num_rows($consulta);

			if ($num_total_registros > 0) {
				while ($titulo_aporte = parent::fetch_assoc($consulta)) {
					$id_tipo_periodo = $titulo_aporte["id_tipo_periodo"];
					$id = $titulo_aporte["id_periodo_evaluacion"];
					$desc = $titulo_aporte["pe_nombre"];
					$abrev = $titulo_aporte["pe_abreviatura"];
					if ($id_tipo_periodo == 1 || $id_tipo_periodo == 7 || $id_tipo_periodo == 8) {
						$mensaje .= "<td width=\"60px\" align=\"center\">\n";
						$mensaje .= "<a href=\"#\" id=\"$id\" style=\"color: #fff;\" title=\"$desc\">" . $abrev . "</a>\n";
						$mensaje .= "</td>\n";
						$mensaje .= "<td width=\"60px\" align=\"center\">" . ($titulo_aporte["pe_ponderacion"] * 100) . "%</td>\n";
					}
				}

				$mensaje .= "<td width=\"60px\" align=\"center\">NOTA F.</td>\n";
				$mensaje .= "<td width=\"60px\" align=\"center\"><a href=\"#\" style=\"color: #fff;\" title=\"SUPLETORIO\">SUP.</a></td>\n";
				$mensaje .= "<td width=\"120px\" align=\"left\">OBSERVACION</td>\n";
			}
		}

		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tamaño de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}

	function mostrarTitulosAportes($alineacion)
	{
		if (!isset($alineacion)) $alineacion = "center";
		$consulta = parent::consulta("SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion);

		//return "SELECT ap_abreviatura FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $this->id_periodo_evaluacion;

		$mensaje = "<table id=\"titulos_rubricas\" class=\"fuente8\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
		$mensaje .= "<tr>\n";

		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$contador_aportes = 0;
			while ($titulo_aporte = parent::fetch_assoc($consulta)) {
				$contador_aportes++;
				if ($contador_aportes < $num_total_registros)
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
				else {
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">PROM.</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">80%</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">" . $titulo_aporte["ap_abreviatura"] . "</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">20%</td>\n";
					$mensaje .= "<td width=\"60px\" align=\"" . $alineacion . "\">NOTA Q.</td>\n";
				}
			}
		}

		$mensaje .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el tamanio de las columnas
		$mensaje .= "</tr>\n";
		$mensaje .= "</table>\n";
		return $mensaje;
	}
}
