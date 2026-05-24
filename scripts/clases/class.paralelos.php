<?php

// require_once("class.funciones.php");
require_once("../funciones/funciones_sitio.php");

class paralelos extends MySQL
{
	var $code = "";
	var $id_curso = 0;
	var $pa_nombre = "";
	var $id_usuario = 0;
	var $id_docente = 0;
	var $id_jornada = 0;
	var $id_paralelo = 0;
	var $id_estudiante = 0;
	var $id_asignatura = 0;
	var $id_periodo_lectivo = 0;
	var $id_aporte_evaluacion = 0;
	var $id_periodo_evaluacion = 0;

	function truncar($numero, $digitos)
	{
		$truncar = pow(10, $digitos);
		return intval($numero * $truncar) / $truncar;
	}

	function equivalencia($promedio)
	{
		$record = parent::consulta("SELECT ec_equivalencia"
			. " FROM sw_escala_comportamiento"
			. " WHERE ec_nota_minima <= " . $promedio
			. " AND ec_nota_maxima >= " . $promedio);
		$escala = parent::fetch_assoc($record);
		return $escala["ec_equivalencia"];
	}

	function obtenerParalelo()
	{
		$consulta = parent::consulta("SELECT id_paralelo, p.id_curso, es_figura, cu_nombre, pa_nombre, id_jornada FROM sw_paralelo p, sw_curso c, sw_especialidad e WHERE c.id_curso = p.id_curso AND e.id_especialidad = c.id_especialidad AND id_paralelo = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerTipoPeriodo()
	{
		$consulta = parent::consulta("SELECT tp.id_tipo_periodo FROM sw_tipo_periodo tp, sw_periodo_evaluacion pe WHERE tp.id_tipo_periodo = pe.id_tipo_periodo AND pe.id_periodo_evaluacion = $this->id_periodo_evaluacion");
		$registro = parent::fetch_object($consulta);
		return $registro->id_tipo_periodo;
	}

	function obtenerTipoEducacion($id)
	{
		$consulta = parent::consulta("SELECT te_bachillerato FROM sw_paralelo p, sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND p.id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->te_bachillerato;
	}

	function obtenerIdCurso($id_paralelo)
	{
		$consulta = parent::consulta("SELECT cu.id_curso FROM sw_curso cu, sw_paralelo pa WHERE cu.id_curso = pa.id_curso AND pa.id_paralelo = $id_paralelo");
		$resultado = parent::fetch_object($consulta);
		return $resultado->id_curso;
	}

	function obtenerNombreParalelo($id)
	{
		$consulta = parent::consulta("SELECT es_figura, 
											 cu_nombre, 
											 pa_nombre, 
											 jo_nombre 
										FROM sw_especialidad es, 
											 sw_curso cu, 
											 sw_paralelo pa, 
											 sw_jornada jo
									   WHERE pa.id_curso = cu.id_curso 
									     AND cu.id_especialidad = es.id_especialidad 
										 AND jo.id_jornada = pa.id_jornada 
										 AND pa.id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->cu_nombre . " " . $paralelo->pa_nombre . " - " . $paralelo->es_figura;
	}

	function obtenerNomCurso($id)
	{
		$consulta = parent::consulta("SELECT cu_nombre, pa_nombre FROM sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNomParalelo($id)
	{
		$consulta = parent::consulta("SELECT pa_nombre FROM sw_paralelo WHERE id_paralelo = $id");
		$paralelo = parent::fetch_object($consulta);
		return " \"" . $paralelo->pa_nombre . "\"";
	}

	function getNombreParalelo($id_paralelo)
	{
		$qry = "SELECT CONCAT(cu_abreviatura, pa_nombre, ' ', es_abreviatura) AS paralelo
				  FROM sw_especialidad e,
				  	   sw_curso c, 
					   sw_paralelo p
				 WHERE e.id_especialidad = c.id_especialidad
				   AND c.id_curso = p.id_curso
				   AND p.id_paralelo = $id_paralelo";
		$consulta = parent::consulta($qry);
		$paralelo = parent::fetch_object($consulta);
		return $paralelo->paralelo;
	}

	function obtenerNombreCurso($id)
	{
		$consulta = parent::consulta("SELECT cu_nombre FROM sw_curso cu, sw_paralelo pa WHERE pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return (parent::fetch_object($consulta)->cu_nombre);
	}

	function getNombreCurso($id)
	{
		$consulta = parent::consulta("SELECT CONCAT(cu_nombre, ' ', es_figura) AS nombre FROM sw_especialidad es, sw_curso cu, sw_paralelo pa WHERE es.id_especialidad = cu.id_especialidad AND pa.id_curso = cu.id_curso AND pa.id_paralelo = $id");
		return (parent::fetch_object($consulta)->nombre);
	}

	function obtenerJornada($id_paralelo)
	{
		$consulta = parent::consulta("SELECT jo_nombre FROM sw_jornada j, sw_paralelo p WHERE j.id_jornada = p.id_jornada AND id_paralelo = $id_paralelo");
		return (parent::fetch_object($consulta)->jo_nombre);
	}

	function existeParalelo($pa_nombre, $id_curso, $id_jornada)
	{
		$consulta = parent::consulta("SELECT * FROM sw_paralelo WHERE pa_nombre = '$pa_nombre' AND id_curso = $id_curso AND id_jornada = $id_jornada");
		return parent::num_rows($consulta) > 0;
	}

	function insertarParalelo()
	{
		$qry = "SELECT secuencial_paralelo_periodo_lectivo(" . $this->id_periodo_lectivo . ") AS secuencial";
		$consulta = parent::consulta($qry);
		$secuencial = parent::fetch_object($consulta)->secuencial;

		$qry = "INSERT INTO sw_paralelo (id_periodo_lectivo, id_curso, id_jornada, pa_nombre, pa_orden) VALUES (";
		$qry .= $this->id_periodo_lectivo . ",";
		$qry .= $this->id_curso . ",";
		$qry .= $this->id_jornada . ",";
		$qry .= "'" . $this->pa_nombre . "',";
		$qry .= $secuencial . ")";

		if ($this->existeParalelo($this->pa_nombre, $this->id_periodo_lectivo, $this->id_curso, $this->id_jornada)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un paralelo con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				// Obtener el id del paralelo creado
				$qry = "SELECT MAX(id_paralelo) AS max_id_paralelo FROM `sw_paralelo`";
				$consulta = parent::consulta($qry);
				$resultado = parent::fetch_assoc($consulta);
				$id_paralelo = $resultado["max_id_paralelo"];

				// Asociar el paralelo creado con los aportes de evaluación creados para el cierre de periodos...
				$qry = "SELECT a.id_aporte_evaluacion,
							   a.ap_fecha_apertura, 
							   a.ap_fecha_cierre  
						 FROM `sw_aporte_evaluacion` a, 
							  `sw_periodo_evaluacion` p  
						WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion
						  AND p.id_periodo_lectivo = " . $this->id_periodo_lectivo;
				$consulta = parent::consulta($qry);

				while ($aporte = parent::fetch_assoc($consulta)) {
					$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
					$fecha_apertura = $aporte["ap_fecha_apertura"];
					$fecha_cierre = $aporte["ap_fecha_cierre"];
					// Insertar la asociación para el futuro cierre...
					$qry = "SELECT * 
							  FROM `sw_aporte_paralelo_cierre` 
							 WHERE id_aporte_evaluacion = $id_aporte_evaluacion 
							   AND id_paralelo = $id_paralelo";
					$asociacion = parent::consulta($qry);
					if (parent::num_rows($asociacion) == 0) {
						$qry = "INSERT INTO `sw_aporte_paralelo_cierre` 
							(id_aporte_evaluacion, 
							id_paralelo, 
							ap_fecha_apertura, 
							ap_fecha_cierre, 
							ap_estado) 
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
					'titulo' => "¡Paralelo Insertado con éxito!",
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

	function actualizarParalelo()
	{
		$qry = "UPDATE sw_paralelo SET ";
		$qry .= "id_curso = " . $this->id_curso . ",";
		$qry .= "pa_nombre = '" . $this->pa_nombre . "', ";
		$qry .= "id_jornada = " . $this->id_jornada;
		$qry .= " WHERE id_paralelo = " . $this->code;

		$consulta = parent::consulta("SELECT * FROM sw_paralelo WHERE id_paralelo = $this->code");
		$registro = parent::fetch_assoc($consulta);
		$nombreActual = $registro["pa_nombre"];

		if ($nombreActual != $this->pa_nombre && $this->existeParalelo($this->pa_nombre, $this->id_curso, $this->id_jornada)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un paralelo con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Paralelo Actualizado con éxito!",
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

	function eliminarParalelo()
	{
		$qry = "SELECT COUNT(*) AS num_estudiantes FROM sw_estudiante_periodo_lectivo WHERE id_paralelo = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_estudiantes = parent::fetch_object($consulta)->num_estudiantes;
		if ($num_estudiantes > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar el Paralelo porque tiene estudiantes matriculados.",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_paralelo WHERE id_paralelo = " . $this->code;
				$consulta = parent::consulta($qry);

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
		return json_encode($datos);
	}

	function listarParalelos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_paralelo WHERE id_curso = " . $this->code . " ORDER BY id_paralelo ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_paralelo"];
				$name = $paralelos["pa_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarParalelo(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarParalelo(" . $code . ",'" . $name . "')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han definido paralelos asociados a este curso...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarParalelos()
	{
		$qry = "SELECT id_paralelo, 
					   pa_nombre, 
					   es_figura AS especialidad, 
					   cu_nombre AS curso, 
					   jo_nombre, 
					   pa_orden
				  FROM sw_paralelo p, 
					   sw_curso c, 
					   sw_especialidad e, 
					   sw_jornada j 
				 WHERE c.id_curso = p.id_curso 
				   AND e.id_especialidad = c.id_especialidad 
				   AND j.id_jornada = p.id_jornada 
				   AND id_periodo_lectivo = $this->id_periodo_lectivo
				 ORDER BY pa_orden ASC";

		// return $qry;

		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$contador++;
				$id = $paralelo["id_paralelo"];
				$name = $paralelo["pa_nombre"];
				$especialidad = $paralelo["especialidad"];
				$curso = $paralelo["curso"];
				$jornada = $paralelo["jo_nombre"];
				$orden = $paralelo["pa_orden"];
				$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$especialidad</td>\n";
				$cadena .= "<td>$curso</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$jornada</td>\n";
				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarParaleloModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarParalelo(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='8' align='center'>No se han definido paralelos en este periodo lectivo...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function contarAsignaturas($id_paralelo, $id_curso, $tipo_reporte)
	{
		if ($tipo_reporte == 1) { // CUADRO FINAL
			$condicion = " AND id_tipo_asignatura = 1";
		} else {
			$condicion = "";
		}
		$consulta = parent::consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso" . $condicion);
		$registro = parent::fetch_assoc($consulta);
		$num_asignaturas = $registro["total_registros"];
		if ($num_asignaturas == 0) {
			$consulta = parent::consulta("SELECT COUNT(*) AS total_registros FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo");
			$registro = parent::fetch_assoc($consulta);
			$num_asignaturas = $registro["total_registros"];
			if ($num_asignaturas == 0) {
				$consulta = parent::consulta("SELECT COUNT(*) AS total_registros FROM sw_malla_curricular m, sw_asignatura a WHERE a.id_asignatura = m.id_asignatura AND id_curso = $id_curso" . $condicion);
				$registro = parent::fetch_assoc($consulta);
				$num_asignaturas = $registro["total_registros"];
			}
		}
		return $num_asignaturas;
	}

	function listarAsignaturas()
	{
		$consulta = parent::consulta("SELECT id_paralelo_asignatura, cu_nombre, pa_nombre, a.id_asignatura, as_nombre, us_titulo, us_fullname FROM sw_paralelo_asignatura pa, sw_paralelo p, sw_curso c, sw_asignatura_curso ac, sw_asignatura a, sw_usuario u WHERE pa.id_paralelo = p.id_paralelo AND p.id_curso = ac.id_curso AND ac.id_curso = c.id_curso AND pa.id_asignatura = ac.id_asignatura AND ac.id_asignatura = a.id_asignatura AND pa.id_usuario = u.id_usuario AND pa.id_paralelo = " . $this->code . " ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_paralelo_asignatura"];
				$paralelo = $paralelos["cu_nombre"] . " - " . $paralelos["pa_nombre"];
				$asignatura = $paralelos["as_nombre"];
				$docente = $paralelos["us_titulo"] . " " . $paralelos["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">$paralelo</td>\n";
				$cadena .= "<td width=\"40%\" align=\"left\">$asignatura</td>\n";
				$cadena .= "<td width=\"19%\" align=\"left\">$docente</td>\n";
				$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . ")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han asociado asignaturas a este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarAsignaturasCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT id_asignatura_curso, c.id_curso, cu_nombre, a.id_asignatura, as_nombre, ac_orden FROM sw_asignatura_curso ac, sw_curso c, sw_asignatura a WHERE ac.id_curso = c.id_curso AND ac.id_asignatura = a.id_asignatura AND ac.id_curso = $id_curso ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $paralelos["id_asignatura_curso"];
				$id_curso = $paralelos["id_curso"];
				$curso = $paralelos["cu_nombre"];
				$id_asignatura = $paralelos["id_asignatura"];
				$asignatura = $paralelos["as_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"38%\" align=\"left\">$curso</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$asignatura</td>\n";
				if ($contador == 1) {
					if ($num_total_registros > 1) {
						$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\">eliminar</a></td>\n";
						$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"bajarAsociacion(" . $code . "," . $id_curso . ")\">bajar</a></td>\n";
					} else {
						$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\">eliminar</a></td>\n";
					}
				} else if ($contador == $num_total_registros) {
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\">eliminar</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"subirAsociacion(" . $code . "," . $id_curso . ")\">subir</a></td>\n";
				} else {
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\">eliminar</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"subirAsociacion(" . $code . "," . $id_curso . ")\">subir</a></td>\n";
					$cadena .= "<td width=\"6%\" class=\"link_table\"><a href=\"#\" onclick=\"bajarAsociacion(" . $code . "," . $id_curso . ")\">bajar</a></td>\n";
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar las columnas
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han asociado asignaturas a este curso...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarAsignaturasCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT id_asignatura_curso, 
											 es_figura,
											 c.id_curso, 
											 cu_nombre, 
											 a.id_asignatura, 
											 as_nombre, 
											 ac_orden 
										FROM sw_asignatura_curso ac, 
											 sw_curso c, 
											 sw_especialidad e,
											 sw_asignatura a 
									   WHERE e.id_especialidad = c.id_especialidad
									     AND ac.id_curso = c.id_curso 
										 AND ac.id_asignatura = a.id_asignatura 
										 AND ac.id_curso = $id_curso 
									ORDER BY ac_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		$contador = 0;
		if ($num_total_registros > 0) {
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$code = $paralelos["id_asignatura_curso"];
				$orden = $paralelos["ac_orden"];
				$cadena .= "<tr data-index = '$code' data-orden = '$orden'>\n";
				$id_curso = $paralelos["id_curso"];
				$curso = $paralelos["es_figura"] . " - " . $paralelos["cu_nombre"];
				$asignatura = $paralelos["as_nombre"];
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$curso</td>\n";
				$cadena .= "<td width=\"39%\" align=\"left\">$asignatura</td>\n";
				$cadena .= "<td><button class='btn btn-danger' onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\"><span class='fa fa-trash'></span></button></td>";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='6' align='center'>No se han asociado asignaturas a este curso...</td>\n";
			$cadena .= "</tr>\n";
		}
		$datos = array(
			'cadena' => $cadena,
			'total_asignaturas' => $contador
		);
		return json_encode($datos);
	}

	function asociarAsignaturaCurso()
	{
		// Verifico si ya existe la asociacion
		$consulta = parent::consulta("SELECT * FROM sw_asignatura_curso WHERE id_curso = " . $this->id_curso . " AND id_asignatura = " . $this->id_asignatura);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$mensaje = "Ya existe la asociacion entre el curso y la asignatura seleccionados.";
		} else {
			// Aqui primero llamo a la funcion almacenada secuencial_curso_asignatura
			$consulta = parent::consulta("SELECT secuencial_curso_asignatura(" . $this->id_curso . ") AS secuencial");
			$ac_orden = parent::fetch_object($consulta)->secuencial;

			$qry = "INSERT INTO sw_asignatura_curso (id_curso, id_asignatura, id_periodo_lectivo, ac_orden) VALUES (";
			$qry .= $this->id_curso . ",";
			$qry .= $this->id_asignatura . ",";
			$qry .= $this->id_periodo_lectivo . ",";
			$qry .= $ac_orden . ")";
			$consulta = parent::consulta($qry);
			$mensaje = "Asignatura asociada exitosamente...";
		}
		return $mensaje;
	}

	function eliminarAsignaturaCurso()
	{
		// Primero se actualiza el orden (decrementar en uno) de los registros mayores que el actual...
		$qry = "UPDATE sw_asignatura_curso SET ac_orden = ac_orden - 1 WHERE id_curso = " . $this->id_curso . " AND id_asignatura_curso > " . $this->code;
		$consulta = parent::consulta($qry);

		$qry = "DELETE FROM sw_asignatura_curso WHERE id_asignatura_curso =" . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Asignatura des-asociada exitosamente...";
		return $mensaje;
	}



	function listarCalificacionesProyectoIntegrador($id_paralelo, $id_periodo_lectivo, $id_asignatura)
	{
		$cadena = "<table class=\"table table-striped table-hover fuente8\">\n";

		$qry = "SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo";
		$consulta = parent::consulta($qry);
		$registro = parent::fetch_object($consulta);
		$id_curso = $registro->id_curso;

		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 es_apellidos, 
											 es_nombres, 
											 c.id_curso 
										FROM sw_estudiante e, 
											 sw_estudiante_periodo_lectivo p, 
											 sw_curso c, 
											 sw_paralelo pa 
									   WHERE c.id_curso = pa.id_curso 
									     AND pa.id_paralelo = $id_paralelo 
										 AND e.id_estudiante = p.id_estudiante 
										 AND p.id_paralelo = $id_paralelo 
										 AND es_retirado = 'N' 
										 AND activo = 1 
									ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$contador++;
				$cadena .= "<tr>\n";
				$cadena .= "<td width='5%'>$contador</td>";
				$cadena .= "<td width='5%'>$id_estudiante</td>";
				$cadena .= "<td width='30%' class='text-left'>$apellidos $nombres</td>\n";

				$periodos_evaluacion = parent::consulta("SELECT pe.id_periodo_evaluacion, 
																tp_descripcion,  
																pc.pe_ponderacion 
															FROM sw_periodo_evaluacion pe,
																sw_periodo_evaluacion_curso pc, 
																sw_tipo_periodo tp 
															WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
															AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
															AND tp.id_tipo_periodo = pe.id_tipo_periodo 
															AND pe.id_tipo_periodo IN (1, 7, 8) 
															AND pc.id_periodo_lectivo = $id_periodo_lectivo
															AND pc.id_curso = $id_curso 
															ORDER BY pc_orden ASC");
				$num_total_registros = parent::num_rows($periodos_evaluacion);
				if ($num_total_registros > 0) {
					$suma_ponderados = 0;
					while ($periodo = parent::fetch_assoc($periodos_evaluacion)) {
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$pe_ponderacion = $periodo["pe_ponderacion"];
						//
						$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$calificacion = parent::fetch_assoc($resultado);
						$calificacion_sub_periodo = $calificacion["promedio"];
						$promedio_ponderado = $calificacion_sub_periodo * $pe_ponderacion;
						$suma_ponderados += $promedio_ponderado;

						$nota_sub_periodo = $calificacion_sub_periodo == 0 ? "" : $this->truncar($calificacion_sub_periodo, 2);

						$nota_promedio_ponderado = $promedio_ponderado == 0 ? "" : $this->truncar($promedio_ponderado, 4);

						$cadena .= "<td width='60px' align='left'>$nota_sub_periodo</td>\n";
						$cadena .= "<td width='60px' align='left'>$nota_promedio_ponderado</td>\n";
					}
					//
					$puntaje_final = $suma_ponderados == 0 ? "" : $this->truncar($suma_ponderados, 2);
					$cadena .= "<td width='60px' align='left'>$puntaje_final</td>\n";
					$observacion = "";
					// Desplegar la equivalencia final (APRUEBA, NO APRUEBA, SUPLETORIO, REMEDIAL)

					$qry = "SELECT pe_nota_aprobacion FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id_periodo_lectivo";

					$query = parent::consulta($qry);
					$registro = parent::fetch_object($query);
					$nota_minima = $registro->pe_nota_aprobacion;

					$cadena .= "<td width='*' align='left'>$nota_minima</td>\n";
				}

				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function listarCalificacionesSupletoriosParalelo($id_paralelo, $id_asignatura, $id_periodo_lectivo, $tipo_supletorio)
	{
		$cadena = "<table class=\"table table-striped table-hover fuente8\">\n";
		$cadena .= "<thead class=\"thead-dark fuente9\">\n";
		$cadena .= "<th>Nro.</th>\n";
		$cadena .= "<th>Id</th>\n";
		$cadena .= "<th>Nómina</th>\n";

		//Obtener el id_curso correspondiente
		$query = parent::consulta("SELECT id_curso FROM sw_paralelo WHERE id_paralelo = $id_paralelo");
		$registro = parent::fetch_object($query);
		$id_curso = $registro->id_curso;

		$query = parent::consulta("SELECT pc.pe_ponderacion, 
										  pe_abreviatura 
									 FROM sw_periodo_evaluacion pe, 
									      sw_periodo_evaluacion_curso pc 
									WHERE pe.id_periodo_lectivo = pc.id_periodo_lectivo 
									  AND pe.id_periodo_evaluacion = pc.id_periodo_evaluacion
									  AND id_tipo_periodo IN (1, 7, 8)   
									  AND pc.id_curso = $id_curso 
									  AND pc.id_periodo_lectivo = $id_periodo_lectivo 
									ORDER BY pc_orden");
		$num_total_registros = parent::num_rows($query);
		if ($num_total_registros > 0) {
			while ($titulo_periodo = parent::fetch_assoc($query)) {
				$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
				$cadena .= "<th>" . ($titulo_periodo["pe_ponderacion"] * 100) . "%</th>\n";
			}
		}

		$cadena .= "<th>NOTA F.</th>\n";
		$cadena .= "<th>OBSERVACION</th>\n";

		//Imprimir las cabeceras de exámenes supletorios
		$query = parent::consulta("SELECT pe_abreviatura FROM sw_periodo_evaluacion WHERE id_tipo_periodo IN (2, 3) AND id_periodo_lectivo = " . $id_periodo_lectivo);
		$num_total_registros = parent::num_rows($query);
		if ($num_total_registros > 0) {
			while ($titulo_periodo = parent::fetch_assoc($query)) {
				$cadena .= "<th>" . $titulo_periodo["pe_abreviatura"] . "</th>\n";
			}
		}

		$cadena .= "<th>OBS. FINAL</th>\n";
		$cadena .= "</thead>\n";

		$cadena .= "<tbody>\n";
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 es_apellidos, 
											 es_nombres, 
											 c.id_curso 
										FROM sw_estudiante e, 
											 sw_estudiante_periodo_lectivo p, 
											 sw_curso c, 
											 sw_paralelo pa 
									   WHERE c.id_curso = pa.id_curso 
									     AND pa.id_paralelo = $id_paralelo 
										 AND e.id_estudiante = p.id_estudiante 
										 AND p.id_paralelo = $id_paralelo 
										 AND es_retirado = 'N' 
										 AND activo = 1 
									ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
				$qry = "SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";

				$resultado = parent::consulta($qry);
				$calificacion = parent::fetch_assoc($resultado);
				$puntaje_final = $calificacion["promedio"];

				// Determinar si tiene que rendir examen supletorio o remedial

				$qry = "SELECT * 
						  FROM sw_equivalencia_supletorios 
						 WHERE id_periodo_lectivo = $id_periodo_lectivo 
						 ORDER BY rango_desde DESC";
				$query = parent::consulta($qry);
				$total_registros = parent::num_rows($query);

				$observacion = "";

				while ($registro = parent::fetch_object($query)) {
					$rango_desde = $registro->rango_desde;
					$rango_hasta = $registro->rango_hasta;
					if ($puntaje_final >= $rango_desde && $puntaje_final <= $rango_hasta) {
						$observacion = $registro->nombre_examen;
						$tipo_supletorio = $registro->id_tipo_periodo;
					}
				}

				if ($observacion == "SUPLETORIO" || $observacion == "REMEDIAL") {
					$contador++;
					$cadena .= "<tr>\n";
					$cadena .= "<td align='left'>$contador</td>\n";
					$cadena .= "<td align='left'>$id_estudiante</td>\n";
					$cadena .= "<td align='left'>$apellidos $nombres</td>\n";

					$periodo_evaluacion = parent::consulta("SELECT pc.id_periodo_evaluacion, 
																   pc.pe_ponderacion 
				 										  	  FROM sw_periodo_evaluacion pe, 
															  	   sw_periodo_evaluacion_curso pc 
				 										 	 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
															   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
															   AND pc.id_curso = $id_curso 
															   AND pc.id_periodo_lectivo = $id_periodo_lectivo 
				 										   	   AND id_tipo_periodo IN (1, 7, 8) 
															 ORDER BY pc_orden");
					$num_total_registros = parent::num_rows($periodo_evaluacion);
					if ($num_total_registros > 0) {
						$suma_ponderados = 0;
						while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
							$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
							$pe_ponderacion = $periodo["pe_ponderacion"];

							$qry = "SELECT calcular_promedio_sub_periodo($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
							$resultado = parent::consulta($qry);
							$calificacion = parent::fetch_assoc($resultado);
							$calificacion_sub_periodo = $calificacion["promedio"];
							$promedio_ponderado = $calificacion_sub_periodo * $pe_ponderacion;
							$suma_ponderados += $promedio_ponderado;

							$nota_sub_periodo = $calificacion_sub_periodo == 0 ? "" : $this->truncar($calificacion_sub_periodo, 2);

							$nota_promedio_ponderado = $promedio_ponderado == 0 ? "" : $this->truncar($promedio_ponderado, 4);

							$cadena .= "<td align='left'>$nota_sub_periodo</td>\n";
							$cadena .= "<td align='left'>$nota_promedio_ponderado</td>\n";
						}

						$suma_ponderados = $suma_ponderados == 0 ? "" : $this->truncar($suma_ponderados, 2);
						$cadena .= "<td align='left'>$suma_ponderados</td>\n";
						$cadena .= "<td align='left'>$observacion</td>\n";

						if ($observacion == "SUPLETORIO") {
							// Determinar la nota del examen supletorio
							$qry = "SELECT id_rubrica_evaluacion, 
										   ac.ap_estado 
									  FROM sw_rubrica_evaluacion r, 
										   sw_aporte_evaluacion a, 
										   sw_aporte_paralelo_cierre ac, 
										   sw_periodo_evaluacion p 
									 WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
									   AND ac.id_paralelo = $id_paralelo 
									   AND r.id_aporte_evaluacion = a.id_aporte_evaluacion 
									   AND a.id_periodo_evaluacion = p.id_periodo_evaluacion 
									   AND p.id_tipo_periodo = $tipo_supletorio";
							$query = parent::consulta($qry);
							$registro = parent::fetch_assoc($query);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
							$estado_aporte = $registro["ap_estado"];

							$qry = "SELECT re_calificacion 
									  FROM sw_rubrica_estudiante 
									 WHERE id_estudiante = $id_estudiante 
									   AND id_paralelo = $id_paralelo 
									   AND id_asignatura = $id_asignatura 
									   AND id_rubrica_personalizada = $id_rubrica_personalizada";
							$query = parent::consulta($qry);
							$total_registros = parent::num_rows($query);

							if ($total_registros > 0) {
								$rubrica_estudiante = parent::fetch_assoc($query);
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}

							// Aqui formo el input para ingresar la calificacion del examen supletorio
							$nota_supletorio = $calificacion == 0 ? "" : $this->truncar($calificacion, 2);

							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"supletorio_" . $contador . "\"value=\"" . $nota_supletorio . "\"";
							if ($estado_aporte == 'A') {
								$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_personalizada . "," . $calificacion . ")\" /></td>\n";
							} else {
								$cadena .= " disabled /></td>\n";
							}

							$observacion_final = "";
							if ($calificacion < 7 && $calificacion >= 0) {
								$observacion_final = "NO APRUEBA";
							} else {
								$observacion_final = "APRUEBA";
							}

							$cadena .= "<td id='observacion_" . $contador . "' align='left'>$observacion_final</td>\n";
						}
					}
					$cadena .= "</tr>\n";
				}
			}

			if ($contador == 0) {
				$cadena .= "<div class='txt-center fuente9' style='margin-top: 5px; margin-bottom: 5px;'>No existen estudiantes para rendir el examen supletorio.</div>\n";
			}
		}

		$cadena .= "</tbody>\n";
		$cadena .= "</table>\n";
		return $cadena;
	}

	function listarResumenAnual($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$retirado = $paralelo["es_retirado"];
				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"5%\" align=\"left\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" align=\"left\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion pe, sw_tipo_periodo tp WHERE tp.id_tipo_periodo = pe.id_tipo_periodo AND id_periodo_lectivo = $id_periodo_lectivo AND tipo_periodo = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if ($num_total_registros > 0) {
					$suma_periodos = 0;
					$contador_periodos = 0;
					while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

						$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
						$aporte_evaluacion = parent::consulta($qry);
						//echo $qry . "<br>";
						$num_total_registros = parent::num_rows($aporte_evaluacion);
						if ($num_total_registros > 0) {
							// Aqui calculo los promedios y desplegar en la tabla
							$suma_promedios = 0;
							$contador_aportes = 0;
							$suma_promedios = 0;
							while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
								$contador_aportes++;
								$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_asignatura a WHERE r.id_tipo_asignatura = a.id_tipo_asignatura AND a.id_asignatura = $id_asignatura AND id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = parent::num_rows($rubrica_evaluacion);
								if ($total_rubricas > 0) {
									$suma_rubricas = 0;
									$contador_rubricas = 0;
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
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = $this->truncar($suma_rubricas / $contador_rubricas, 2);
								if ($contador_aportes <= $num_total_registros - 1) {
									$suma_promedios += $promedio;
								} else {
									$examen_quimestral = $promedio;
								}
							}
						}
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $this->truncar($ponderado_aportes + $ponderado_examen, 2);
						$suma_periodos += $calificacion_quimestral;
						$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($calificacion_quimestral, 2) . "</td>\n";
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion

				// Calculo la suma y el promedio de los dos quimestres
				$promedio_quimestral = $this->truncar($suma_periodos / $contador_periodos, 2);
				$promedio_periodos = $promedio_quimestral;
				$examen_supletorio = 0;
				$examen_remedial = 0;
				$examen_de_gracia = 0;

				$funciones = new funciones();

				if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
					// Obtencion de la calificacion del examen supletorio
					$examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo);
					if ($examen_supletorio >= 7)
						$promedio_periodos = 7;
					else {
						// Obtencion de la calificacion del examen remedial
						$examen_remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
						if ($examen_remedial >= 7)
							$promedio_periodos = 7;
						else {
							$examen_de_gracia = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
							if ($examen_de_gracia >= 7) // Examen de Gracia
								$promedio_periodos = 7;
						}
					}
				} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
					// Obtencion de la calificacion del examen remedial
					$examen_remedial = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo);
					if ($examen_remedial >= 7)
						$promedio_periodos = 7;
					else {
						$examen_de_gracia = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo);
						if ($examen_de_gracia >= 7) // Examen de Gracia
							$promedio_periodos = 7;
					}
				}
				$equiv_final = equiv_anual($promedio_periodos, $retirado, $terminacion);
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($suma_periodos, 2) . "</td>\n"; // Suma
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($promedio_quimestral, 2) . "</td>\n"; // Prom. Quim.
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($examen_supletorio, 2) . "</td>\n"; // Supletorio
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($examen_remedial, 2) . "</td>\n"; // Remedial
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($examen_de_gracia, 2) . "</td>\n"; // Gracia
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($promedio_periodos, 2) . "</td>\n"; // Promedio Final
				$cadena .= "<td width=\"20%\" align=\"left\">$equiv_final</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarComportamientoAnual($id_paralelo, $id_asignatura, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, es_genero, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$retirado = $paralelo["es_retirado"];
				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"5%\" align=\"left\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" align=\"left\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if ($num_total_registros > 0) {
					$suma_periodos = 0;
					$contador_periodos = 0;
					while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
						$contador_periodos++;
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

						$qry = parent::consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
						//echo "SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion<br>";
						$registro = parent::fetch_assoc($qry);
						$promedio = ceil($registro["calificacion"]);
						//echo $promedio." ";
						$suma_periodos += $promedio;
						//Obtengo el correlativo de la escala de comportamiento
						$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cualitativo = $equivalencia["ec_equivalencia"];
						$cadena .= "<td width=\"5%\" align=\"left\">" . $promedio_cualitativo . "</td>\n";
					} // fin while $periodo_evaluacion
				} // fin if $periodo_evaluacion
				// Calculo la suma y el promedio de los dos quimestres
				$promedio_quimestral = $this->truncar($suma_periodos / $contador_periodos, 2);

				//$equiv_final = equiv_anual($promedio_periodos, $retirado, $terminacion);
				$cadena .= "<td width=\"5%\" align=\"left\">" . number_format($suma_periodos, 2) . "</td>\n"; // Suma
				$cadena .= "<td width=\"10%\" align=\"left\">" . number_format($promedio_quimestral, 2) . "</td>\n"; // Prom. Quim.
				$cadena .= "<td width=\"35%\" align=\"left\">" . equiv_comportamiento($promedio_quimestral) . "</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarPromocionesSecretaria($id_paralelo, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador_gracia = 0;
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador_cero = 0;
				$contador_supletorios = 0;
				$contador_remediales = 0;

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";
				$cadena .= "<td width=\"250px\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if ($total_asignaturas > 0) {

					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if ($num_total_registros > 0) {
							$suma_periodos = 0;
							$contador_periodos = 0;
							while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if ($num_total_registros > 0) {
									// Aqui calculo los promedios
									$suma_aportes = 0;
									$contador_aportes = 0;
									$suma_promedios = 0;
									while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if ($total_rubricas > 0) {
											$suma_rubricas = 0;
											$contador_rubricas = 0;
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
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if ($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del periodo de evaluacion
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;
						$observacion = "";

						if ($promedio_periodos == 0) $contador_cero++;
						else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = 2");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

							if ($qry) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
								if ($calificacion >= 7) {
									$promedio_periodos = 7;
								} else {
									$contador_supletorios++;
								}
							}
						} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
							// Obtencion de la calificacion del examen remedial

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = 3");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

							if ($qry) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
								if ($calificacion >= 7) {
									$promedio_periodos = 7;
								} else {
									$contador_remediales++;
								}
							}
						}

						$cadena .= "<td width=\"50px\" align=\"right\">" . number_format($promedio_periodos, 2) . "</td>\n";
						if ($contador_cero > 0 || $contador_supletorios > 0 || $contador_remediales > 0) {
							if ($contador_remediales == 1) { // Tiene que dar examen de gracia
								// Obtencion de la calificacion del examen de gracia
								$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = 4");
								$registro = parent::fetch_assoc($qry);
								$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

								$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");

								if ($qry) {
									$rubrica_estudiante = parent::fetch_assoc($qry);
									$calificacion = $rubrica_estudiante["re_calificacion"];
									if ($calificacion >= 7) {
										$promedio_periodos = 7;
										$observacion = "APRUEBA";
									}
								}
							} else $observacion = "NO APRUEBA";
						} else $observacion = "APRUEBA";
					} // while ($asignatura = parent::fetch_assoc($asignaturas))

				}

				$cadena .= "<td width=\"80px\">$observacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesSupletoriosSecretaria($id_paralelo, $id_periodo_lectivo, $tipo_supletorio)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";
				$cadena .= "<td width=\"250px\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if ($total_asignaturas > 0) {

					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if ($num_total_registros > 0) {
							$suma_periodos = 0;
							$contador_periodos = 0;
							while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {

								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								//echo $qry . "<br>";
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if ($num_total_registros > 0) {
									// Aqui calculo los promedios
									$suma_aportes = 0;
									$contador_aportes = 0;
									$suma_promedios = 0;
									while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if ($total_rubricas > 0) {
											$suma_rubricas = 0;
											$contador_rubricas = 0;
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
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if ($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del periodo de evaluacion
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;

						if ($promedio_periodos >= 7) {

							$cadena .= "<td width=\"50px\" align=\"right\">&nbsp;</td>\n";
						} else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $tipo_supletorio");
							$registro = parent::fetch_assoc($qry);
							$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];

							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
							$total_registros = parent::num_rows($qry);
							if ($total_registros > 0) {
								$rubrica_estudiante = parent::fetch_assoc($qry);
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}

							// Aqui desplego la calificacion del examen supletorio
							$cadena .= "<td width=\"50px\" align=\"right\">" . number_format($calificacion, 2) . "</td>\n";
						} else if ($promedio_periodos < 5) {

							$observacion = ($promedio_periodos == 0) ? "S/N" : "REMED.";
							$cadena .= "<td width=\"50px\" align=\"right\">$observacion</td>\n";
						}
					} // while ($asignatura = parent::fetch_assoc($asignaturas))

					$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

				} // if($total_asignaturas>0)

			} // while($paralelo = parent::fetch_assoc($consulta))
		} // if($num_total_registros>0)
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCuadroFinal($id_periodo_lectivo, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$contador_gracia = 0;
		$funciones = new funciones();
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador_cero = 0;
				$contador_supletorios = 0;
				$contador_remediales = 0;

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";
				$cadena .= "<td width=\"250px\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui obtengo el recordset de las asignaturas del paralelo
				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = $id_paralelo ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if ($total_asignaturas > 0) {

					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];

						// Antes de desplegar las calificaciones del estudiante, tengo que determinar si tiene que dar examen supletorio
						$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
						$num_total_registros = parent::num_rows($periodo_evaluacion);
						if ($num_total_registros > 0) {
							$suma_periodos = 0;
							$contador_periodos = 0;
							while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
								$contador_periodos++;
								$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];

								$qry = "SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion";
								$aporte_evaluacion = parent::consulta($qry);
								$num_total_registros = parent::num_rows($aporte_evaluacion);
								if ($num_total_registros > 0) {
									// Aqui calculo los promedios
									$suma_aportes = 0;
									$contador_aportes = 0;
									$suma_promedios = 0;
									while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
										$contador_aportes++;
										$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
										$total_rubricas = parent::num_rows($rubrica_evaluacion);
										if ($total_rubricas > 0) {
											$suma_rubricas = 0;
											$contador_rubricas = 0;
											while ($rubricas = parent::fetch_assoc($rubrica_evaluacion)) {
												$contador_rubricas++;
												//$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
												$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = " . $rubricas["id_rubrica_evaluacion"]);
												//$total_registros = parent::num_rows($qry);
												//if($total_registros>0) {
												if (parent::num_rows($qry) > 0) {
													$rubrica_estudiante = parent::fetch_assoc($qry);
													$calificacion = $rubrica_estudiante["re_calificacion"];
												} else {
													$calificacion = 0;
												}
												$suma_rubricas += $calificacion;
											}
											// Aqui calculo el promedio del aporte de evaluacion
											$promedio = $suma_rubricas / $contador_rubricas;
											if ($contador_aportes <= $num_total_registros - 1) {
												$suma_promedios += $promedio;
											} else {
												$examen_quimestral = $promedio;
											}
										} // if($total_rubricas>0)
									} // while($aporte = parent::fetch_assoc($aporte_evaluacion))
								} // if($num_total_registros>0)
								// Aqui se calculan las calificaciones del quimestre
								$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
								$ponderado_aportes = 0.8 * $promedio_aportes;
								$ponderado_examen = 0.2 * $examen_quimestral;
								$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
								$suma_periodos += $calificacion_quimestral;
							} // while($periodo = parent::fetch_assoc($periodo_evaluacion))
						} // if($num_total_registros>0)

						// Calculo la suma y el promedio de los dos quimestres
						$promedio_periodos = $suma_periodos / $contador_periodos;
						$observacion = "";

						if ($promedio_periodos == 0) $contador_cero++;
						else if ($promedio_periodos >= 5 && $promedio_periodos < 7) {
							// Obtencion de la calificacion del examen supletorio

							if ($examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 2, $id_periodo_lectivo) >= 7)
								$promedio_periodos = 7;
							else
								$contador_supletorios++;
						} else if ($promedio_periodos > 0 && $promedio_periodos < 5) {
							// Obtencion de la calificacion del examen remedial

							if ($examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 3, $id_periodo_lectivo) >= 7)
								$promedio_periodos = 7;
							else
								$contador_remediales++;
						}

						$cadena .= "<td width=\"60px\" align=\"right\">" . number_format($promedio_periodos, 2) . "</td>\n";
						if ($contador_cero > 0 || $contador_supletorios > 0 || $contador_remediales > 0) {
							if ($contador_remediales == 1) { // Tiene que dar examen de gracia
								// Obtencion de la calificacion del examen de gracia

								if ($examen_supletorio = $funciones->obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, 4, $id_periodo_lectivo) >= 7) {
									$promedio_periodos = 7;
									$observacion = "APRUEBA";
								} else {
									$observacion = "NO APRUEBA";
								}
							}
						} else {
							$observacion = "APRUEBA";
						}
					} // while ($asignatura = parent::fetch_assoc($asignaturas))

				}

				$cadena .= "<td width=\"80px\">$observacion</td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // esto es para igualar las columnas

			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesAnuales($id_periodo_lectivo, $id_paralelo, $cantidad_registros, $numero_pagina)
	{
		// Cabecera de la tabla
		$cadena = "<table class=\"table table-striped table-hover fuente8\">\n";
		$cadena .= "<thead class=\"thead-dark fuente8\">\n";
		$cadena .= "<th>Nro.</th>\n";
		$cadena .= "<th>Nómina</th>\n";

		$consulta = parent::consulta("SELECT as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = " . $id_paralelo . " ORDER BY ac_orden");

		while ($titulo_asignatura = parent::fetch_assoc($consulta)) {
			// $ancho_col = ($tipo_reporte == 1) ? "60px" : "50px";
			$cadena .= "<th>" . $titulo_asignatura["as_abreviatura"] . "</th>\n";
		}

		$cadena .= "</thead>\n";
		$cadena .= "<tbody>\n";

		$inicio = ($numero_pagina - 1) * $cantidad_registros;

		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres, dg_abreviatura, es_retirado FROM sw_estudiante e, sw_estudiante_periodo_lectivo p, sw_def_genero dg WHERE dg.id_def_genero = e.id_def_genero AND e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND activo = 1 AND es_retirado <> 'S' ORDER BY es_apellidos, es_nombres ASC LIMIT $inicio, $cantidad_registros");

		$num_total_registros = parent::num_rows($consulta);

		if ($num_total_registros > 0) {
			$contador = $inicio;

			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$terminacion = ($paralelo["es_genero"] == "M") ? "O" : "A";
				$retirado = $paralelo["es_retirado"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td align=\"left\">$contador</td>\n";
				$cadena .= "<td align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				$asignaturas = parent::consulta("SELECT a.id_asignatura, as_abreviatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND a.id_tipo_asignatura = 1 AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");

				while ($asignatura = parent::fetch_assoc($asignaturas)) {
					$id_asignatura = $asignatura["id_asignatura"];

					$qry = parent::consulta("SELECT calcular_promedio_periodo_lectivo($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio");
					$registro = parent::fetch_assoc($qry);
					$promedio_anual = $registro["promedio"];

					$promedio_anual = $promedio_anual == 0 ? "" : $this->truncar($promedio_anual, 2);

					$cadena .= "<td align=\"left\">$promedio_anual</td>\n";
				}

				$cadena .= "</tr>\n";
			}
		}

		$cadena .= "</tbody>\n";
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesConsolidado($id_periodo_evaluacion, $cantidad_registros, $numero_pagina)
	{
		$inicio = ($numero_pagina - 1) * $cantidad_registros;
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = " . $this->id_paralelo . " ORDER BY es_apellidos, es_nombres ASC LIMIT $inicio, $cantidad_registros");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = $inicio;
			while ($paralelo = parent::fetch_assoc($consulta)) {
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];

				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$cadena .= "<td width=\"30px\">$contador</td>\n";
				$cadena .= "<td width=\"240px\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				$asignaturas = parent::consulta("SELECT id_asignatura FROM sw_paralelo_asignatura WHERE id_paralelo = " . $this->id_paralelo . " ORDER BY id_asignatura");
				$total_asignaturas = parent::num_rows($asignaturas);
				if ($total_asignaturas > 0) {
					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						// Aqui proceso los promedios de cada asignatura
						$id_asignatura = $asignatura["id_asignatura"];
						$aporte_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion AND p.id_periodo_evaluacion = $id_periodo_evaluacion");
						$num_total_registros = parent::num_rows($aporte_evaluacion);
						if ($num_total_registros > 0) {
							// Aqui calculo los promedios y desplegar en la tabla
							$contador_aportes = 0;
							$suma_promedios = 0;
							while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
								$contador_aportes++;
								$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion WHERE id_aporte_evaluacion = " . $aporte["id_aporte_evaluacion"]);
								$total_rubricas = parent::num_rows($rubrica_evaluacion);
								if ($total_rubricas > 0) {
									$suma_rubricas = 0;
									$contador_rubricas = 0;
									while ($rubricas = parent::fetch_assoc($rubrica_evaluacion)) {
										$contador_rubricas++;
										$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
										$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_evaluacion");
										$total_registros = parent::num_rows($qry);
										if ($total_registros > 0) {
											$rubrica_estudiante = parent::fetch_assoc($qry);
											$calificacion = $rubrica_estudiante["re_calificacion"];
										} else {
											$calificacion = 0;
										}
										$suma_rubricas += $calificacion;
									}
								}
								// Aqui calculo el promedio del aporte de evaluacion
								$promedio = $suma_rubricas / $contador_rubricas;
								if ($contador_aportes <= $num_total_registros - 1) {
									$suma_promedios += $promedio;
								} else {
									$examen_quimestral = $promedio;
								}
							}
						}
						// Aqui se calculan las calificaciones del periodo de evaluacion
						$promedio_aportes = $suma_promedios / ($contador_aportes - 1);
						$ponderado_aportes = 0.8 * $promedio_aportes;
						$ponderado_examen = 0.2 * $examen_quimestral;
						$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;
						$cadena .= "<td width=\"50px\" align=\"right\">" . number_format($calificacion_quimestral, 2) . "</td>\n";
					}
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesFaseProyecto($id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 c.id_curso, 
											 di.id_paralelo, 
											 di.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
								        FROM sw_distributivo di, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
								       WHERE di.id_paralelo = ep.id_paralelo 
									     AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
									     AND ep.id_estudiante = e.id_estudiante 
									     AND di.id_asignatura = a.id_asignatura 
									     AND di.id_paralelo = p.id_paralelo 
									     AND p.id_curso = c.id_curso 
									     AND di.id_paralelo = $this->id_paralelo
			                             AND di.id_asignatura = $this->id_asignatura
			                             AND es_retirado <> 'S'
									     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT a.id_aporte_evaluacion, 
															  a.id_tipo_aporte, 
															  ta_descripcion, 
															  ac.ap_estado, 
															  ap_ponderacion 
													     FROM sw_periodo_evaluacion p, 
															  sw_aporte_evaluacion a,
															  sw_tipo_aporte ta, 
															  sw_aporte_paralelo_cierre ac 
												        WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
														  AND ta.id_tipo_aporte = a.id_tipo_aporte 
													      AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
													      AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
													      AND ac.id_paralelo = $id_paralelo
														ORDER BY ap_orden");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				//
				if ($num_total_registros > 0) {
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_ponderados = 0;
					while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
						$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
						$id_tipo_aporte = $aporte["id_tipo_aporte"];
						$tipo_aporte = $aporte["ta_descripcion"];
						$estado_aporte = $aporte["ap_estado"];
						$ponderacion = $aporte["ap_ponderacion"];

						$qry = "SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$registro = parent::fetch_assoc($resultado);
						$promedio = $registro["promedio"];

						$qry = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a WHERE a.id_aporte_evaluacion = r.id_aporte_evaluacion AND a.id_aporte_evaluacion = $id_aporte_evaluacion";
						$resultado = parent::consulta($qry);
						$registro = parent::fetch_assoc($resultado);
						$id_rubrica_evaluacion = $registro["id_rubrica_evaluacion"];

						$promedio_ponderado = $promedio * $ponderacion;

						if ($tipo_aporte !== 'FASE_PRI') {
							$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";

							$suma_ponderados += $promedio_ponderado;

							$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio, '.') + 4);
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio_ponderado . "\" style=\"color:#666;\" /></td>\n";
						} else {
							$fase_proyecto = $promedio;

							$ponderado_fase = $ponderacion * $fase_proyecto;

							$nota_fase = ($fase_proyecto == 0) ? "" : substr($fase_proyecto, 0, strpos($fase_proyecto, '.') + 3);

							$nota_ponderado_fase = ($ponderado_fase == 0) ? "" : substr($ponderado_fase, 0, strpos($ponderado_fase, '.') + 4);

							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"faseproyecto_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_fase . "\"";

							if ($estado_aporte == 'A') {
								$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $id_tipo_aporte . "," . $fase_proyecto . "," . $ponderacion . ")\" /></td>\n";
							} else {
								$cadena .= " disabled /></td>\n";
							}

							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $nota_ponderado_fase . "\" style=\"color:#666;\" /></td>\n";
						}
					}

					$ponderado_aportes = $suma_ponderados;

					$calificacion_quimestral = $ponderado_aportes + $ponderado_fase;

					$calificacion_quimestral = ($calificacion_quimestral == 0) ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesParalelo($id_periodo_evaluacion, $tipo_reporte)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 c.id_curso, 
											 di.id_paralelo, 
											 di.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
								        FROM sw_distributivo di, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
								       WHERE di.id_paralelo = ep.id_paralelo 
									     AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
									     AND ep.id_estudiante = e.id_estudiante 
									     AND di.id_asignatura = a.id_asignatura 
									     AND di.id_paralelo = p.id_paralelo 
									     AND p.id_curso = c.id_curso 
									     AND di.id_paralelo = $this->id_paralelo
			                             AND di.id_asignatura = $this->id_asignatura
			                             AND es_retirado <> 'S'
									     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$cadena .= "<td width=\"5%\" class=\"text-center\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" class=\"text-center\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
				// Aqui se calculan los promedios de cada aporte de evaluacion
				$aporte_evaluacion = parent::consulta("SELECT a.id_aporte_evaluacion, 
															  a.id_tipo_aporte, 
															  ta_descripcion, 
															  ac.ap_estado, 
															  ap_ponderacion 
													     FROM sw_periodo_evaluacion p, 
															  sw_aporte_evaluacion a,
															  sw_tipo_aporte ta, 
															  sw_aporte_paralelo_cierre ac 
												        WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
														  AND ta.id_tipo_aporte = a.id_tipo_aporte 
													      AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
													      AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
													      AND ac.id_paralelo = $id_paralelo
														ORDER BY ap_orden");
				$num_total_registros = parent::num_rows($aporte_evaluacion);
				if ($num_total_registros > 0) {
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_ponderados = 0;
					while ($aporte = parent::fetch_assoc($aporte_evaluacion)) {
						$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
						$id_tipo_aporte = $aporte["id_tipo_aporte"];
						$tipo_aporte = $aporte["ta_descripcion"];
						$estado_aporte = $aporte["ap_estado"];
						$ponderacion = $aporte["ap_ponderacion"];

						$qry = "SELECT calcular_promedio_aporte($id_aporte_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS promedio";
						$resultado = parent::consulta($qry);
						$registro = parent::fetch_assoc($resultado);
						$promedio = $registro["promedio"];

						$qry = "SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a WHERE a.id_aporte_evaluacion = r.id_aporte_evaluacion AND a.id_aporte_evaluacion = $id_aporte_evaluacion";
						$resultado = parent::consulta($qry);
						$registro = parent::fetch_assoc($resultado);
						$id_rubrica_evaluacion = $registro["id_rubrica_evaluacion"];

						$promedio_ponderado = $promedio * $ponderacion;

						if ($tipo_aporte == 'PARCIAL' || $tipo_aporte == 'FASE_PRI') {
							$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";

							$suma_ponderados += $promedio_ponderado;

							$promedio_ponderado = $promedio_ponderado == 0 ? "" : substr($promedio_ponderado, 0, strpos($promedio, '.') + 4);
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoaportes_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio_ponderado . "\" style=\"color:#666;\" /></td>\n";
						} else {
							$examen_quimestral = $promedio;
						}
					}

					$ponderado_aportes = $suma_ponderados;

					$ponderado_examen = $ponderacion * $examen_quimestral;
					$calificacion_quimestral = $ponderado_aportes + $ponderado_examen;

					$nota_quimestral = ($examen_quimestral == 0) ? "" : substr($examen_quimestral, 0, strpos($examen_quimestral, '.') + 3);

					$ponderado_examen = ($ponderado_examen == 0) ? "" : substr($ponderado_examen, 0, strpos($ponderado_examen, '.') + 4);

					$calificacion_quimestral = ($calificacion_quimestral == 0) ? "" : substr($calificacion_quimestral, 0, strpos($calificacion_quimestral, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"examenquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_quimestral . "\"";

					if ($estado_aporte == 'A') {
						$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $id_tipo_aporte . "," . $examen_quimestral . "," . $ponderacion . ")\" /></td>\n";
					} else {
						// Verificar si tiene autorización para editar calificación
						$qry_autorizado = parent::consulta("SELECT * FROM sw_autorizacion WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
						$num_total_registros_autorizado = parent::num_rows($qry_autorizado);
						if ($num_total_registros_autorizado > 0) {
							$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $id_tipo_aporte . "," . $examen_quimestral . "," . $ponderacion . ")\" /></td>\n";
						} else {
							$cadena .= " disabled /></td>\n";
						}
					}

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoexamen_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $ponderado_examen . "\" style=\"color:#666;\" /></td>\n";

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionquimestral_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $calificacion_quimestral . "\" style=\"color:#666;\" /></td>\n";
				}
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarCalificacionesProyecto($id_periodo_lectivo, $tipo_aporte)
	{
		// $this->id_periodo_evaluacion : contiene el id_periodo_evaluacion a ser editado

		$qry = "SELECT tp_descripcion 
				  FROM sw_periodo_evaluacion pe, 
				       sw_tipo_periodo tp 
				 WHERE tp.id_tipo_periodo = pe.id_tipo_periodo 
				   AND pe.id_periodo_evaluacion = $this->id_periodo_evaluacion";

		$consulta = parent::consulta($qry);
		$registro = parent::fetch_object($consulta);
		$tipo_periodo_actual = $registro->tp_descripcion;

		$qry = "SELECT e.id_estudiante, 
					   c.id_curso, 
					   di.id_paralelo, 
					   di.id_asignatura, 
					   e.es_apellidos, 
					   e.es_nombres,
					   es_retirado,
					   dg_abreviatura,   
					   as_nombre, 
					   cu_nombre, 
					   pa_nombre,
					   id_tipo_asignatura 
				  FROM sw_distributivo di, 
					   sw_estudiante_periodo_lectivo ep, 
					   sw_estudiante e, 
					   sw_def_genero dg, 
					   sw_asignatura a, 
					   sw_curso c, 
					   sw_paralelo p 
				 WHERE di.id_paralelo = ep.id_paralelo 
				   AND di.id_periodo_lectivo = ep.id_periodo_lectivo 
				   AND ep.id_estudiante = e.id_estudiante 
				   AND dg.id_def_genero = e.id_def_genero 
				   AND di.id_asignatura = a.id_asignatura 
				   AND di.id_paralelo = p.id_paralelo 
				   AND p.id_curso = c.id_curso 
				   AND di.id_paralelo = $this->id_paralelo
				   AND di.id_asignatura = $this->id_asignatura
				   AND es_retirado <> 'S'
				   AND activo = 1 
				 ORDER BY es_apellidos, es_nombres ASC";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);

		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";

		// Aquí va el procedimiento para desplegar el listado de estudiantes...
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";

				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$retirado = $paralelos["es_retirado"];
				$es_genero = $paralelos["dg_abreviatura"];
				$terminacion = ($es_genero == "M") ? "O" : "A";

				$id_paralelo = $paralelos["id_paralelo"];
				$id_curso = $paralelos["id_curso"];
				$id_asignatura = $paralelos["id_asignatura"];

				$cadena .= "<td width=\"5%\" class=\"text-center\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" class=\"text-center\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Calcular las notas de bimestres, trimestres o quimestres
				$qry = "SELECT pe.id_periodo_evaluacion, 
							   tp_descripcion,  
							   pc.pe_ponderacion 
						  FROM sw_periodo_evaluacion pe,
						  	   sw_periodo_evaluacion_curso pc, 
							   sw_tipo_periodo tp 
						 WHERE pe.id_periodo_evaluacion = pc.id_periodo_evaluacion 
						   AND pe.id_periodo_lectivo = pc.id_periodo_lectivo 
						   AND tp.id_tipo_periodo = pe.id_tipo_periodo 
						   AND pe.id_tipo_periodo IN (1, 7, 8) 
						   AND pc.id_periodo_lectivo = $id_periodo_lectivo
						   AND pc.id_curso = $id_curso 
						 ORDER BY pc_orden ASC";
				// return $qry;
				$periodos_evaluacion = parent::consulta($qry);
				$num_total_registros = parent::num_rows($periodos_evaluacion);
				if ($num_total_registros > 0) {
					// Aqui calculo los promedios y desplegar en la tabla
					$suma_ponderados_subperiodos = 0;
					while ($periodo = parent::fetch_assoc($periodos_evaluacion)) {
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$ponderacion_subperiodo = $periodo["pe_ponderacion"];
						$tipo_periodo = $periodo["tp_descripcion"];

						// Aqui se calculan los promedios de cada aporte de evaluacion
						$qry = "SELECT a.id_aporte_evaluacion, 
									   id_tipo_aporte, 
									   ac.ap_estado, 
									   ap_ponderacion 
								  FROM sw_periodo_evaluacion p, 
									   sw_aporte_evaluacion a,  
									   sw_aporte_paralelo_cierre ac 
								 WHERE p.id_periodo_evaluacion = a.id_periodo_evaluacion 
								   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
								   AND p.id_periodo_evaluacion = $id_periodo_evaluacion 
								   AND ac.id_paralelo = $id_paralelo";
						// return $qry;
						$aportes_evaluacion = parent::consulta($qry);
						$num_total_registros = parent::num_rows($aportes_evaluacion);
						if ($num_total_registros > 0) {
							// Aqui calculo los promedios y desplegar en la tabla
							$contador_aportes = 0;
							$suma_promedios = 0;
							$suma_ponderados = 0;
							while ($aporte = parent::fetch_assoc($aportes_evaluacion)) {
								$contador_aportes++;
								$id_aporte_evaluacion = $aporte["id_aporte_evaluacion"];
								$tipo_aporte = $aporte["id_tipo_aporte"];
								$estado_aporte = $aporte["ap_estado"];
								$ponderacion = $aporte["ap_ponderacion"];

								$rubricas_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion 
																		   FROM sw_rubrica_evaluacion r,
																			    sw_asignatura a
																		  WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
																		    AND a.id_asignatura = $id_asignatura
																		    AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
								$total_rubricas = parent::num_rows($rubricas_evaluacion);
								if ($total_rubricas > 0) {
									$suma_rubricas = 0;
									$contador_rubricas = 0;
									while ($rubrica = parent::fetch_assoc($rubricas_evaluacion)) {
										$contador_rubricas++;
										$id_rubrica_evaluacion = $rubrica["id_rubrica_evaluacion"];

										$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $id_paralelo . " AND id_asignatura = " . $id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
										$total_registros = parent::num_rows($qry);
										if ($total_registros > 0) {
											$rubrica_estudiante = parent::fetch_assoc($qry);
											$calificacion = $rubrica_estudiante["re_calificacion"];
										} else {
											$calificacion = 0;
										}
										$suma_rubricas += $calificacion;
									} // rubricas
								}

								$promedio = $suma_rubricas / $contador_rubricas; // promedio del parcial
								$promedio_ponderado = $promedio * $ponderacion;  // ponderado del promedio del parcial
								$suma_promedios += $promedio; // suma de promedios de los parciales
								$suma_ponderados += $promedio_ponderado;  // suma de promedios ponderados de los parciales
								if ($tipo_periodo == "PROYECTO_FINAL") {
									$nota_proyecto = $promedio;
									$nota_proyecto_ponderada = $nota_proyecto * $ponderacion_subperiodo;
								} else if ($tipo_periodo == "EVAL_SUBNIVEL") {
									$nota_eval_subnivel = $promedio;
									$nota_eval_subnivel_ponderada = $nota_eval_subnivel * $ponderacion_subperiodo;
								}
							} // parciales

							if ($tipo_periodo != $tipo_periodo_actual) {
								if ($tipo_periodo == "PRINCIPAL") {
									// Aqui debo desplegar los promedios ponderados de los sub periodos 
									$promedio_subperiodo = $suma_ponderados;

									$promedio_subperiodo = $promedio_subperiodo == 0 ? "" : substr($promedio_subperiodo, 0, strpos($promedio_subperiodo, '.') + 3);

									$promedio_subperiodo_ponderado = $promedio_subperiodo * $ponderacion_subperiodo;

									$suma_ponderados_subperiodos += $promedio_subperiodo_ponderado;

									$subperiodo_ponderado = $promedio_subperiodo_ponderado == 0 ? "" : substr($promedio_subperiodo_ponderado, 0, strpos($promedio_subperiodo_ponderado, '.') + 4);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promediosubperiodo_" . $id_estudiante . "_" . $id_periodo_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio_subperiodo . "\" style=\"color:#666;\" /></td>\n";

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadosubperiodo_" . $id_estudiante . "_" . $id_periodo_evaluacion . "_" . $contador . "\" disabled value=\"" . $subperiodo_ponderado . "\" style=\"color:#666;\" /></td>\n";
								} else if ($tipo_periodo == "PROYECTO_FINAL") {
									$nota_proyecto = $nota_proyecto == 0 ? "" : substr($nota_proyecto, 0, strpos($nota_proyecto, '.') + 3);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"proyectofinal_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_proyecto . "\" disabled /></td>\n";

									$suma_ponderados_subperiodos += $nota_proyecto_ponderada;

									$nota_proyecto_ponderada = $nota_proyecto_ponderada == 0 ? "" : substr($nota_proyecto_ponderada, 0, strpos($nota_proyecto_ponderada, '.') + 4);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoproyecto_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $nota_proyecto_ponderada . "\" style=\"color:#666;\" /></td>\n";
								} else if ($tipo_periodo == "EVAL_SUBNIVEL") {
									$nota_eval_subnivel = $nota_eval_subnivel == 0 ? "" : substr($nota_eval_subnivel, 0, strpos($nota_eval_subnivel, '.') + 3);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"evalsubnivel_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_eval_subnivel . "\" disabled /></td>\n";

									$suma_ponderados_subperiodos += $nota_eval_subnivel_ponderada;

									$nota_eval_subnivel_ponderada = $nota_eval_subnivel_ponderada == 0 ? "" : substr($nota_eval_subnivel_ponderada, 0, strpos($nota_eval_subnivel_ponderada, '.') + 4);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoevalsubnivel_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $nota_eval_subnivel_ponderada . "\" style=\"color:#666;\" /></td>\n";
								}
							} else {
								if ($tipo_periodo == $tipo_periodo_actual && $tipo_periodo == "PROYECTO_FINAL") {
									$nota_proyecto = $nota_proyecto == 0 ? "" : substr($nota_proyecto, 0, strpos($nota_proyecto, '.') + 3);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"proyectofinal_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_proyecto . "\"";

									if ($estado_aporte == 'A') {
										$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacionProyecto(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $suma_ponderados . "," . $ponderacion_subperiodo . ")\" /></td>\n";
									} else {
										$cadena .= " disabled /></td>\n";
									}

									$suma_ponderados_subperiodos += $nota_proyecto_ponderada;

									$nota_proyecto_ponderada = $nota_proyecto_ponderada == 0 ? "" : substr($nota_proyecto_ponderada, 0, strpos($nota_proyecto_ponderada, '.') + 4);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoproyecto_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $nota_proyecto_ponderada . "\" style=\"color:#666;\" /></td>\n";
								} else if ($tipo_periodo == $tipo_periodo_actual && $tipo_periodo == "EVAL_SUBNIVEL") {
									$nota_eval_subnivel = $nota_eval_subnivel == 0 ? "" : substr($nota_eval_subnivel, 0, strpos($nota_eval_subnivel, '.') + 3);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio nota1 is-active\" id=\"evalsubnivel_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $id_paralelo . "_" . $id_asignatura . "_" . $contador . "\" value=\"" . $nota_eval_subnivel . "\"";

									if ($estado_aporte == 'A') {
										$cadena .= "onclick=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacionEvalSubnivel(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $suma_ponderados . "," . $ponderacion_subperiodo . ")\" /></td>\n";
									} else {
										$cadena .= " disabled /></td>\n";
									}

									$suma_ponderados_subperiodos += $nota_eval_subnivel_ponderada;

									$nota_eval_subnivel_ponderada = $nota_eval_subnivel_ponderada == 0 ? "" : substr($nota_eval_subnivel_ponderada, 0, strpos($nota_eval_subnivel_ponderada, '.') + 4);

									$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"ponderadoevalsubnivel_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $nota_eval_subnivel_ponderada . "\" style=\"color:#666;\" /></td>\n";
								}
							}
						}
					}

					// Promedio Final del Periodo Lectivo
					$puntaje_final = $suma_ponderados_subperiodos;

					$puntaje_final = $puntaje_final == 0 ? "" : substr($puntaje_final, 0, strpos($puntaje_final, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionperiodo_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $puntaje_final . "\" style=\"color:#666;\" /></td>\n";

					// Obtener la calificacion del examen supletorio
					$qry = "SELECT calcular_examen_supletorio($id_periodo_lectivo, $id_estudiante, $id_paralelo, $id_asignatura, 2) AS supletorio";
					$resultado = parent::consulta($qry);
					$calificacion = parent::fetch_assoc($resultado);
					$supletorio = $calificacion["supletorio"];

					$supletorio = $supletorio == 0 ? "" : substr($supletorio, 0, strpos($supletorio, '.') + 3);

					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"calificacionsupletorio_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $supletorio . "\" style=\"color:#666;\" /></td>\n";

					// OBSERVACION FINAL
					$observacion = "";
					$color = "#666";
					if ($retirado == "S")
						$observacion = "RETIRAD" . $terminacion;
					else if ($puntaje_final != "") {
						if ($puntaje_final >= 7) {
							$observacion = "APRUEBA";
							$color = "#008000";
						} else if ($puntaje_final > 4) {
							if ($supletorio == "") {
								$observacion = "SUPLETORIO";
								$color = "#ff8c00";
							} else {
								if ($supletorio >= 7) {
									$observacion = "APRUEBA";
									$color = "#008000";
								} else {
									$observacion = "NO APRUEBA";
									$color = "#FF0000";
								}
							}
						} else {
							$observacion = "NO APRUEBA";
							$color = "#FF0000";
						}
					}

					$cadena .= "<td width=\"120px\" align=\"left\"><input type=\"text\" class=\"inputMedio\" id=\"observacion_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $contador . "\" disabled value=\"" . $observacion . "\" style=\"color:$color;\" /></td>\n";
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		}

		$cadena .= "</table>";
		return $cadena;
	}

	function listarEstudiantesComportamientoAnual($id_periodo_lectivo, $id_paralelo)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($estudiante = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"45%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui va el codigo para determinar el total, el promedio y la equivalencia de cada quimestre

				$periodo_evaluacion = parent::consulta("SELECT id_periodo_evaluacion FROM sw_periodo_evaluacion WHERE id_periodo_lectivo = $id_periodo_lectivo AND id_tipo_periodo = 1");
				$num_total_registros = parent::num_rows($periodo_evaluacion);
				if ($num_total_registros > 0) {
					$suma_promedio = 0;
					while ($periodo = parent::fetch_assoc($periodo_evaluacion)) {
						$id_periodo_evaluacion = $periodo["id_periodo_evaluacion"];
						$resultado = parent::consulta("SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio");

						#echo "SELECT calcular_comp_insp_quimestre($id_periodo_evaluacion,$id_estudiante,$id_paralelo) AS promedio<br>";

						$registro = parent::fetch_assoc($resultado);
						$promedio = ceil($registro["promedio"]);
						$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cualitativo = $equivalencia["ec_equivalencia"];

						$cadena .= "<td width=\"15%\">" . $promedio_cualitativo . "</td>";

						$suma_promedio += $promedio;
					}
					$promedio_anual = ceil($suma_promedio / $num_total_registros);
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_anual");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td width=\"20%\">" . $promedio_cualitativo . "</td>";
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function listarEstudiantesCompInspector($id_paralelo, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($estudiante = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"45%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui se consultan los comportamientos de parciales ingresados por el Inspector
				$aportes_evaluacion = parent::consulta("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = $id_periodo_evaluacion AND id_tipo_aporte = 1");

				$suma_comportamiento_inspector = 0;
				$contador_aportes_evaluacion = 0;
				while ($aporte_evaluacion = parent::fetch_assoc($aportes_evaluacion)) {
					$contador_aportes_evaluacion++;
					$id_aporte_evaluacion = $aporte_evaluacion["id_aporte_evaluacion"];
					$query = parent::consulta("SELECT co_calificacion FROM sw_comportamiento_inspector WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_aporte_evaluacion = $id_aporte_evaluacion");
					$inspectores = parent::fetch_assoc($query);
					$promedio_inspector = $inspectores["co_calificacion"];
					if ($promedio_inspector == "") {
						$promedio_cuantitativo = 0;
					} else {
						$query = parent::consulta("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '$promedio_inspector'");
						$equivalencia = parent::fetch_assoc($query);
						$promedio_cuantitativo = $equivalencia["ec_correlativa"];
					}
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_cuantitativo");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td width=\"10%\" align=\"center\">" . $promedio_cualitativo . "</td>\n";
					$suma_comportamiento_inspector += $promedio_cuantitativo;
				}

				$promedio_comportamiento = ceil($suma_comportamiento_inspector / $contador_aportes_evaluacion);
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comportamiento");
				$equivalencia = parent::fetch_assoc($query);
				$promedio_cualitativo = $equivalencia["ec_equivalencia"];
				$cadena .= "<td width=\"20%\" align=\"center\">" . $promedio_cualitativo . "</td>\n";

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		}
		return $cadena;
	}

	function listarEstudiantesComportamiento($id_paralelo, $id_periodo_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo AND es_retirado = 'N' AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);

		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($estudiante = parent::fetch_assoc($consulta)) {
				$contador++;

				$cadena .= "<tr>\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$id_estudiante</td>\n";
				$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui se consultan los indices de evaluacion para el comportamiento

				$asignaturas = parent::consulta("SELECT a.id_asignatura FROM sw_asignatura a, sw_asignatura_curso ac, sw_paralelo p WHERE a.id_asignatura = ac.id_asignatura AND p.id_curso = ac.id_curso AND p.id_paralelo = $id_paralelo ORDER BY ac_orden");
				$total_asignaturas = parent::num_rows($asignaturas);

				if ($total_asignaturas > 0) {
					$suma_comp_asignatura = 0;
					$contador_asignaturas = 0;

					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];

						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = parent::consulta("SELECT calcular_comp_asignatura($id_periodo_evaluacion, $id_estudiante, $id_paralelo, $id_asignatura) AS calificacion");
						$calificaciones = parent::fetch_assoc($calificaciones);
						$calificacion = ceil($calificaciones["calificacion"]);

						$suma_comp_asignatura += $calificacion;
					}

					$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
					$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
					$equivalencia = parent::fetch_assoc($query);
					$promedio_cualitativo = $equivalencia["ec_equivalencia"];
					$cadena .= "<td>" . $promedio_cualitativo . "</td>\n";
				}

				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td colspan=\"4\" class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}

		return $cadena;
	}

	function listarEstudiantesComportamientoParciales($id_paralelo, $id_aporte_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
											 es_apellidos, 
											 es_nombres, 
											 quien_inserta_comp
										FROM sw_estudiante e, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_paralelo p, 
											 sw_curso c
									   WHERE e.id_estudiante = ep.id_estudiante 
									     AND p.id_paralelo = ep.id_paralelo
										 AND c.id_curso = p.id_curso
										 AND ep.es_retirado = 'N'
										 AND activo = 1   
									     AND ep.id_paralelo = $id_paralelo 
									   ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($estudiante = parent::fetch_assoc($consulta)) {
				$contador++;
				$cadena .= "<tr>\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$id_estudiante</td>\n";
				$cadena .= "<td>" . $apellidos . " " . $nombres . "</td>\n";

				// Aqui se calcula el promedio del comportamiento asignado por los docentes

				$asignaturas = parent::consulta("SELECT a.id_asignatura 
												   FROM sw_asignatura a, 
												   		sw_asignatura_curso ac, 
														sw_paralelo p 
												  WHERE a.id_asignatura = ac.id_asignatura 
												    AND p.id_curso = ac.id_curso 
													AND p.id_paralelo = $id_paralelo");
				$total_asignaturas = parent::num_rows($asignaturas);

				if ($total_asignaturas > 0) {
					$suma_comp_asignatura = 0;
					$contador_asignaturas = 0;

					while ($asignatura = parent::fetch_assoc($asignaturas)) {
						$contador_asignaturas++;
						$id_asignatura = $asignatura["id_asignatura"];

						// Aqui se consulta la calificacion del comportamiento ingresada por cada docente
						$calificaciones = parent::consulta("SELECT co_calificacion 
															  FROM sw_calificacion_comportamiento 
															 WHERE id_estudiante = $id_estudiante 
															   AND id_paralelo = $id_paralelo 
															   AND id_asignatura = $id_asignatura 
															   AND id_aporte_evaluacion = $id_aporte_evaluacion");
						if (parent::num_rows($calificaciones) > 0) {
							$calificaciones = parent::fetch_assoc($calificaciones);
							$calificacion = $calificaciones["co_calificacion"];
						} else
							$calificacion = 0;
						$suma_comp_asignatura += $calificacion;
					}
				}

				$promedio_comp = ceil($suma_comp_asignatura / $contador_asignaturas);
				//Aqui despliego el promedio del comportamiento asentado por los docentes
				//Primero obtengo la equivalencia del promedio en forma cualitativa
				$query = parent::consulta("SELECT ec_equivalencia FROM sw_escala_comportamiento WHERE ec_correlativa = $promedio_comp");
				$resultado = parent::fetch_assoc($query);
				$comp_docentes = $resultado["ec_equivalencia"];

				$cadena .= "<td>" . $comp_docentes . "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td colspan=\"4\" class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		return $cadena;
	}

	function listarEstudiantesComportamientoPorDocente($id_paralelo, $id_periodo_evaluacion, $id_asignatura)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, es_apellidos, es_nombres FROM sw_estudiante e, sw_estudiante_periodo_lectivo p WHERE e.id_estudiante = p.id_estudiante AND p.id_paralelo = $id_paralelo ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($estudiante = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $estudiante["id_estudiante"];
				$apellidos = $estudiante["es_apellidos"];
				$nombres = $estudiante["es_nombres"];
				$cadena .= "<td width=\"35px\">$contador</td>\n";
				$cadena .= "<td width=\"350px\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
				// Aqui se consultan los indices de evaluacion para el comportamiento
				$query = "SELECT id_indice_evaluacion FROM sw_indice_evaluacion_def ORDER BY ie_orden";
				$comportamiento = parent::consulta($query);
				$total_indices = parent::num_rows($comportamiento);
				if ($total_indices > 0) {
					$total = 0;
					$contador_indices = 0;
					while ($indice = parent::fetch_assoc($comportamiento)) {
						$id_indice_evaluacion = $indice["id_indice_evaluacion"];
						// Aqui se consulta la calificacion del comportamiento
						$query = "SELECT co_calificacion FROM sw_calificacion_comportamiento WHERE id_paralelo = $id_paralelo AND id_estudiante = $id_estudiante AND id_periodo_evaluacion = $id_periodo_evaluacion AND id_indice_evaluacion = $id_indice_evaluacion AND id_asignatura = $id_asignatura";
						$registro = parent::consulta($query);
						if (parent::num_rows($registro) > 0) {
							$nota_comportamiento = parent::fetch_assoc($registro);
							$calificacion = $nota_comportamiento["co_calificacion"];
						} else {
							$calificacion = 0;
						}
						$total += $calificacion;
						$contador_indices++;
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_" . $contador . "\" class=\"inputPequenio\" value=\"" . number_format($calificacion, 2) . "\" onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\" onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_indice_evaluacion . ")\" /></td>\n";
					}
				}
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"total_" . $contador . "\" class=\"inputPequenio\" value=\"" . number_format($total, 2) . "\" disabled style=\"color:#666;\" /></td>\n";

				$promedio = $total / $contador_indices;
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"promedio_" . $contador . "\" class=\"inputPequenio\" value=\"" . number_format($promedio, 2) . "\" disabled style=\"color:#666;\" /></td>\n";

				$equivalencia = equiv_comportamiento($promedio);
				$cadena .= "<td width=\"60px\"><input type=\"text\" id=\"equivalencia_" . $contador . "\" class=\"inputPequenio\" value=\"$equivalencia\" disabled style=\"color:#666;\" /></td>\n";

				$cadena .= "<td width=\"*\">&nbsp;</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr><td width=\"100%\" align=\"center\">No se han matriculado estudiantes en este paralelo...</td></tr>\n";
		}
		$cadena .= "</table>\n";
		return $cadena;
	}

	function listarCalificacionesAsignatura()
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, 
										     c.id_curso, 
											 d.id_paralelo, 
											 d.id_asignatura, 
											 e.es_apellidos, 
											 e.es_nombres, 
											 es_retirado, 
											 as_nombre, 
											 cu_nombre, 
											 pa_nombre,
											 id_tipo_asignatura 
										FROM sw_distributivo d, 
											 sw_estudiante_periodo_lectivo ep, 
											 sw_estudiante e, 
											 sw_asignatura a, 
											 sw_curso c, 
											 sw_paralelo p 
									   WHERE d.id_paralelo = ep.id_paralelo 
									     AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
										 AND ep.id_estudiante = e.id_estudiante 
										 AND d.id_asignatura = a.id_asignatura 
										 AND d.id_paralelo = p.id_paralelo 
										 AND p.id_curso = c.id_curso 
										 AND d.id_paralelo = " . $this->id_paralelo
			. " AND d.id_asignatura = " . $this->id_asignatura
			. " AND es_retirado <> 'S'
									     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros == 0) {
			//Busco en la tabla sw_paralelo_asignatura para periodos lectivos anteriores
			$consulta = parent::consulta("SELECT e.id_estudiante, 
										     	 c.id_curso, 
											 	 pa.id_paralelo, 
											 	 pa.id_asignatura, 
											 	 e.es_apellidos, 
											 	 e.es_nombres, 
											 	 es_retirado, 
											 	 as_nombre, 
											 	 cu_nombre, 
											 	 pa_nombre,
											 	 id_tipo_asignatura 
											FROM sw_paralelo_asignatura pa, 
											     sw_estudiante_periodo_lectivo ep, 
											     sw_estudiante e, 
											 	 sw_asignatura a, 
											 	 sw_curso c, 
											 	 sw_paralelo p 
									   	   WHERE pa.id_paralelo = ep.id_paralelo 
									     	 AND pa.id_periodo_lectivo = ep.id_periodo_lectivo 
										 	 AND ep.id_estudiante = e.id_estudiante 
										 	 AND pa.id_asignatura = a.id_asignatura 
										 	 AND pa.id_paralelo = p.id_paralelo 
										 	 AND p.id_curso = c.id_curso 
										 	 AND pa.id_paralelo = " . $this->id_paralelo
				. " AND pa.id_asignatura = " . $this->id_asignatura
				. " AND es_retirado <> 'S'
										     AND activo = 1 ORDER BY es_apellidos, es_nombres ASC");
		}
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$id_asignatura = $paralelos["id_asignatura"];
				$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
				//Aca vamos a obtener el estado del aporte de evaluacion
				$query = parent::consulta("SELECT ac.ap_estado
											 FROM sw_aporte_evaluacion a,
												  sw_aporte_paralelo_cierre ac
											WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
											  AND ac.id_paralelo = " . $this->id_paralelo .
					" AND a.id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
				//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
				if ($id_tipo_asignatura == 1) { //CUANTITATIVA
					// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
					$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion, 
																   id_tipo_aporte, 
																   ac.ap_estado 
															  FROM sw_rubrica_evaluacion r, 
															       sw_aporte_evaluacion a, 
																   sw_aporte_paralelo_cierre ac,
																   sw_asignatura asignatura
															 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
															   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
															   AND asignatura.id_asignatura = $id_asignatura
															   AND r.id_aporte_evaluacion = " . $this->id_aporte_evaluacion
						. " AND ac.id_paralelo = " . $this->id_paralelo);
					$num_total_registros = parent::num_rows($rubrica_evaluacion);
					if ($num_total_registros > 0) {
						$suma_rubricas = 0;
						$contador_rubricas = 0;
						while ($rubricas = parent::fetch_assoc($rubrica_evaluacion)) {
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
							$num_total_registros = parent::num_rows($qry);
							$rubrica_estudiante = parent::fetch_assoc($qry);
							if ($num_total_registros > 0) {
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}
							$suma_rubricas += $calificacion;
							$calificacion = ($calificacion == 0) ? "" : $calificacion;
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"" . $calificacion . "\" disabled /></td>\n";
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" id=\"promedio_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
					} else {
						$cadena .= "<tr>\n";
						$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
						$cadena .= "</tr>\n";
					}
				} else { //CUALITATIVA
					// Aqui va el codigo para obtener la calificacion cualitativa
					$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
					$num_total_registros = parent::num_rows($qry);
					$cualitativa = parent::fetch_assoc($qry);
					if ($num_total_registros > 0) {
						$calificacion = $cualitativa["rc_calificacion"];
					} else {
						$calificacion = '';
					}
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"" . $calificacion . "\" disabled /></td>\n";
				}

				// Aqui va el codigo para obtener el comportamiento
				$qry = parent::consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
				$num_total_registros = parent::num_rows($qry);
				$comportamiento = parent::fetch_assoc($qry);
				if ($num_total_registros > 0) {
					$calificacion = $comportamiento["co_cualitativa"];
				} else {
					$calificacion = '';
				}
				$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio\" value=\"" . $calificacion . "\" disabled /></td>\n";
				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function listarEstudiantesParalelo()
	{
		$qry = "SELECT e.id_estudiante, 
						c.id_curso, 
						d.id_paralelo, 
						d.id_asignatura, 
						e.es_apellidos, 
						e.es_nombres, 
						es_retirado, 
						as_nombre, 
						cu_nombre, 
						pa_nombre,
						id_tipo_asignatura 
				FROM sw_distributivo d, 
						sw_estudiante_periodo_lectivo ep, 
						sw_estudiante e, 
						sw_asignatura a, 
						sw_curso c, 
						sw_paralelo p 
				WHERE d.id_paralelo = ep.id_paralelo 
					AND d.id_periodo_lectivo = ep.id_periodo_lectivo 
					AND ep.id_estudiante = e.id_estudiante 
					AND d.id_asignatura = a.id_asignatura 
					AND d.id_paralelo = p.id_paralelo 
					AND p.id_curso = c.id_curso 
					AND d.id_paralelo = $this->id_paralelo
					AND d.id_asignatura = $this->id_asignatura
					AND es_retirado <> 'S' 
					AND activo = 1 ORDER BY es_apellidos, es_nombres ASC";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($paralelos = parent::fetch_assoc($consulta)) {
				$contador++;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$id_estudiante = $paralelos["id_estudiante"];
				$apellidos = $paralelos["es_apellidos"];
				$nombres = $paralelos["es_nombres"];
				$retirado = $paralelos["es_retirado"];

				$id_paralelo = $paralelos["id_paralelo"];
				$id_asignatura = $paralelos["id_asignatura"];
				$id_tipo_asignatura = $paralelos["id_tipo_asignatura"];
				$cadena .= "<td width=\"5%\" class=\"text-center\">$contador</td>\n";
				$cadena .= "<td width=\"5%\" class=\"text-center\">$id_estudiante</td>\n";
				$cadena .= "<td width=\"30%\" align=\"left\">" . $apellidos . " " . $nombres . "</td>\n";
				//Aca vamos a obtener el estado del aporte de evaluacion
				$query = parent::consulta("SELECT ac.ap_estado 
											 FROM sw_aporte_evaluacion a,
												  sw_aporte_paralelo_cierre ac,
												  sw_periodo_lectivo pl,
												  sw_paralelo p, 
												  sw_curso c 
											WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
											  AND pl.id_periodo_lectivo = p.id_periodo_lectivo
											  AND ac.id_paralelo = p.id_paralelo
											  AND c.id_curso = p.id_curso 
											  AND p.id_paralelo = " . $this->id_paralelo .
					" AND a.id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
				$resultado = parent::fetch_assoc($query);
				$estado_aporte = $resultado["ap_estado"];
				$sql = "SELECT qc.nombre FROM sw_periodo_lectivo pl, sw_quien_inserta_comp qc WHERE qc.id = pl.quien_inserta_comp_id AND pl.id_periodo_lectivo = $_SESSION[id_periodo_lectivo]";
				$resultado = parent::consulta($sql);
				$quien_inserta_comportamiento = $resultado->fetch_object()->nombre;
				//Aqui vamos a diferenciar asignaturas CUANTITATIVAS de CUALITATIVAS
				if ($id_tipo_asignatura == 1) { //CUANTITATIVA
					// Aqui se consultan las rubricas definidas para el aporte de evaluacion elegido
					$rubrica_evaluacion = parent::consulta("SELECT id_rubrica_evaluacion, 
																   id_tipo_aporte, 
																   ac.ap_estado, 
																   ap_ponderacion 
															  FROM sw_rubrica_evaluacion r, 
															       sw_aporte_evaluacion a, 
																   sw_aporte_paralelo_cierre ac,
																   sw_asignatura asignatura
															 WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion 
															   AND r.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion 
															   AND r.id_tipo_asignatura = asignatura.id_tipo_asignatura
															   AND asignatura.id_asignatura = $id_asignatura
															   AND r.id_aporte_evaluacion = " . $this->id_aporte_evaluacion
						. " AND ac.id_paralelo = " . $this->id_paralelo);
					$num_total_registros = parent::num_rows($rubrica_evaluacion);
					if ($num_total_registros > 0) {
						$suma_rubricas = 0;
						$contador_rubricas = 0;
						while ($rubricas = parent::fetch_assoc($rubrica_evaluacion)) {
							$contador_rubricas++;
							$id_rubrica_evaluacion = $rubricas["id_rubrica_evaluacion"];
							$tipo_aporte = $rubricas["id_tipo_aporte"];
							$estado_aporte = $rubricas["ap_estado"];

							$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_rubrica_personalizada = " . $id_rubrica_evaluacion);
							$num_total_registros = parent::num_rows($qry);
							$rubrica_estudiante = parent::fetch_assoc($qry);
							if ($num_total_registros > 0) {
								$calificacion = $rubrica_estudiante["re_calificacion"];
							} else {
								$calificacion = 0;
							}

							$suma_rubricas += $calificacion;

							$calificacion = $calificacion == 0 ? "" : $calificacion;
							$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"puntaje_" . $id_rubrica_evaluacion . "_" . $id_estudiante . "_" . $this->id_paralelo . "_" . $id_asignatura . "_" . $tipo_aporte . "_" . $contador . "\" class=\"inputPequenio nota" . $contador_rubricas . "\" value=\"" . $calificacion . "\"";
							if ($estado_aporte == 'A' && $retirado == 'N') {
								$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\"  onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $calificacion . ")\" /></td>\n";
							} else {
								// Verificar si tiene autorización para editar calificación
								$qry_autorizado = parent::consulta("SELECT * FROM sw_autorizacion WHERE id_estudiante = " . $id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
								$num_total_registros_autorizado = parent::num_rows($qry_autorizado);
								if ($num_total_registros_autorizado > 0) {
									$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'num')\"  onblur=\"editarCalificacion(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $id_rubrica_evaluacion . "," . $tipo_aporte . "," . $calificacion . ")\" /></td>\n";
								} else {
									$cadena .= " disabled /></td>\n";
								}
							}
						}
						$promedio = $suma_rubricas / $contador_rubricas;
						$promedio = $promedio == 0 ? "" : $this->truncar($promedio, 2);
						$promedio = $promedio == 0 ? "" : substr($promedio, 0, strpos($promedio, '.') + 3);
						$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" class=\"inputPequenio promedio\" id=\"promedio_" . $id_rubrica_evaluacion . "_" . $contador . "\" disabled value=\"" . $promedio . "\" style=\"color:#666;\" /></td>\n";
					} else {
						$cadena .= "<tr>\n";
						$cadena .= "<td>No se han definido r&uacute;bricas para este aporte de evaluaci&oacute;n...</td>\n";
						$cadena .= "</tr>\n";
					}
				} else { //CUALITATIVA
					// Obtener las referencias cualitativas

					// Aqui va el codigo para obtener la calificacion cualitativa
					$qry = parent::consulta("SELECT rc_calificacion FROM sw_rubrica_cualitativa WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
					$num_total_registros = parent::num_rows($qry);
					$cualitativa = parent::fetch_assoc($qry);
					if ($num_total_registros > 0) {
						$calificacion = $cualitativa["rc_calificacion"];
					} else {
						$calificacion = " ";
					}
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"cualitativa_" . $id_estudiante . "_" . $this->id_paralelo . "_" . $id_asignatura . "_" . $this->id_aporte_evaluacion . "_" . $contador . "\" class=\"inputPequenio nota1\" value=\"" . $calificacion . "\"";
					if ($estado_aporte == 'A' && $retirado == 'N') {
						$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionCualitativa(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $this->id_aporte_evaluacion . ",'" . $calificacion . "')\" /></td>\n";
					} else {
						$cadena .= " disabled /></td>\n";
					}
				}

				if (strtolower($quien_inserta_comportamiento) == 'docente') { // Ingresan los docentes
					// Aqui va el codigo para obtener el comportamiento
					$qry = parent::consulta("SELECT co_cualitativa FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $paralelos["id_estudiante"] . " AND id_paralelo = " . $this->id_paralelo . " AND id_asignatura = " . $this->id_asignatura . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
					$num_total_registros = parent::num_rows($qry);
					$comportamiento = parent::fetch_assoc($qry);
					if ($num_total_registros > 0) {
						$calificacion = $comportamiento["co_cualitativa"];
					} else {
						$calificacion = '';
					}
					$cadena .= "<td width=\"60px\" align=\"left\"><input type=\"text\" id=\"comportamiento_" . $contador . "\" class=\"inputPequenio\" value=\"" . $calificacion . "\"";
					if ($estado_aporte == 'A' && $retirado == 'N') {
						$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacionComportamiento(this," . $id_estudiante . "," . $id_paralelo . "," . $id_asignatura . "," . $this->id_aporte_evaluacion . ")\" /></td>\n";
					} else {
						$cadena .= " disabled /></td>\n";
					}
				}

				$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}
}
