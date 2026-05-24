<?php

class cursos extends MySQL
{
	var $code = "";
	var $id_curso = 0;
	var $id_especialidad = 0;
	var $cu_nombre = "";
	var $bol_proyectos = "";
	var $ins_comp = "";
	var $cu_abreviatura = "";
	var $cu_shortname = "";
	var $id_periodo_lectivo = "";
	var $id_jornada = "";
	var $bach_tecnico = "";
	var $es_intensivo = "";
	var $es_fin_subnivel = "";
	// Variables para asociar cursos superiores
	var $id_curso_inferior = "";
	var $id_curso_superior = "";

	function obtenerCurso()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_curso = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerNombreCurso($id_curso, $tipo)
	{
		$consulta = parent::consulta("SELECT cu_nombre, es_nombre, es_figura FROM sw_curso cu, sw_especialidad es WHERE cu.id_especialidad = es.id_especialidad AND cu.id_curso = $id_curso");
		$resultado = parent::fetch_object($consulta);
		if ($tipo == 1) {
			return $resultado->cu_nombre . " DE " . $resultado->es_figura;
		} else {
			return $resultado->cu_nombre . " DE " . $resultado->es_nombre . ": " . $resultado->es_figura;
		}
	}

	function obtenerBolProyectos($id_curso)
	{
		$consulta = parent::consulta("SELECT bol_proyectos FROM sw_curso WHERE id_curso = $id_curso");
		$resultado = parent::fetch_object($consulta);
		return $resultado->bol_proyectos;
	}

	function eliminarCurso()
	{
		$qry = "SELECT id_paralelo FROM sw_paralelo WHERE id_curso = " . $this->code;
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		if ($num_total_registros > 0) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "No se puede eliminar porque tiene paralelos asociadas...",
				'estado' => 'error'
			];
		} else {
			try {
				$qry = "DELETE FROM sw_curso WHERE id_curso=" . $this->code;
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

	function existeCurso($campo, $valor, $id_especialidad)
	{
		$qry = "SELECT * FROM sw_curso WHERE $campo = '$valor' AND id_especialidad = $id_especialidad";
		$consulta = parent::consulta($qry);

		return parent::num_rows($consulta) > 0;
	}

	function cargarPeriodosEvaluacionCurso($id_curso)
	{
		$consulta = parent::consulta("SELECT id_periodo_evaluacion_curso, 
											 es_figura,
											 c.id_curso, 
											 cu_nombre, 
											 p.pe_nombre, 
											 pc.pe_ponderacion,
											 pc_orden  
										FROM sw_periodo_evaluacion_curso pc, 
											 sw_curso c, 
											 sw_especialidad e,
											 sw_periodo_evaluacion p 
									   WHERE e.id_especialidad = c.id_especialidad
									     AND pc.id_curso = c.id_curso 
										 AND pc.id_periodo_evaluacion = p.id_periodo_evaluacion 
										 AND pc.id_curso = $id_curso 
									ORDER BY pc_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		$contador = 0;
		if ($num_total_registros > 0) {
			while ($cursos = parent::fetch_assoc($consulta)) {
				$contador++;
				$code = $cursos["id_periodo_evaluacion_curso"];
				$orden = $cursos["pc_orden"];
				$cadena .= "<tr data-index = '$code' data-orden = '$orden'>\n";
				$id_curso = $cursos["id_curso"];
				$curso = $cursos["es_figura"] . " - " . $cursos["cu_nombre"];
				$periodo = $cursos["pe_nombre"];
				$ponderacion = $cursos["pe_ponderacion"];
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$curso</td>\n";
				$cadena .= "<td>$periodo</td>\n";
				$cadena .= "<td>$ponderacion</td>\n";
				$cadena .= "<td><button class='btn btn-danger' onclick=\"eliminarAsociacion(" . $code . "," . $id_curso . ")\"><span class='fa fa-trash'></span></button></td>";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='6' align='center'>No se han asociado periodos de evaluación a este curso...</td>\n";
			$cadena .= "</tr>\n";
		}
		$datos = array(
			'cadena' => $cadena,
			'total_periodos' => $contador
		);
		return json_encode($datos);
	}

	function insertarCurso()
	{
		// $qry = "SELECT secuencial_curso_especialidad(" . $this->id_especialidad . ") AS secuencial";
		$qry = "SELECT MAX(cu_orden) AS secuencial 
				  FROM sw_curso cu, 
				  	   sw_especialidad es, 
					   sw_tipo_educacion tp 
				 WHERE es.id_especialidad = cu.id_especialidad 
				   AND tp.id_tipo_educacion = es.id_tipo_educacion 
				   AND tp.id_periodo_lectivo = $this->id_periodo_lectivo";
		$consulta = parent::consulta($qry);
		if (parent::num_rows($consulta) == 0) {
			$secuencial = 1;
		} else {
			$secuencial = parent::fetch_object($consulta)->secuencial + 1;
		}

		$qry = "INSERT INTO sw_curso (id_especialidad, cu_nombre, cu_orden, cu_abreviatura, cu_shortname, es_bach_tecnico, es_intensivo, es_fin_subnivel) VALUES (";
		$qry .= $this->id_especialidad . ",";
		$qry .= "'" . $this->cu_nombre . "',";
		$qry .= $secuencial . ",";
		$qry .= "'" . $this->cu_abreviatura . "',";
		$qry .= "'" . $this->cu_shortname . "',";
		$qry .= $this->bach_tecnico . ",";
		$qry .= $this->es_intensivo . ",";
		$qry .= $this->es_fin_subnivel . ")";

		// return json_encode($qry);

		if ($this->existeCurso('cu_nombre', $this->cu_nombre, $this->id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($this->existeCurso('cu_abreviatura', $this->cu_abreviatura, $this->id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con esa abreviatura en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($this->existeCurso('cu_shortname', $this->cu_shortname, $this->id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con ese nombre corto en la Base de Datos.",
				'estado' => 'error'
			];
		} else {
			try {
				$consulta = parent::consulta($qry);

				//Mensaje de operación exitosa
				$datos = [
					'titulo' => "¡Agregado con éxito!",
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

	function actualizarCurso()
	{
		$cursoActual = parent::fetch_object(parent::consulta("SELECT * FROM sw_curso WHERE id_curso = $this->code"));
		$nombreActual = $cursoActual->cu_nombre;
		$abreviaturaActual = $cursoActual->cu_abreviatura;
		$nombreCortoActual = $cursoActual->cu_shortname;

		$id_especialidad = $cursoActual->id_especialidad;

		$qry = "UPDATE sw_curso SET ";
		$qry .= "id_especialidad = " . $id_especialidad . ",";
		$qry .= "cu_nombre = '" . $this->cu_nombre . "',";
		$qry .= "cu_abreviatura = '" . $this->cu_abreviatura . "',";
		$qry .= "cu_shortname = '" . $this->cu_shortname . "',";
		$qry .= "es_bach_tecnico = " . $this->bach_tecnico . ",";
		$qry .= "es_intensivo = " . $this->es_intensivo . ",";
		$qry .= "es_fin_subnivel = " . $this->es_fin_subnivel;

		$qry .= " WHERE id_curso = " . $this->code;

		$cursoActual = parent::fetch_object(parent::consulta("SELECT * FROM sw_curso WHERE id_curso = $this->code"));
		$nombreActual = $cursoActual->cu_nombre;
		$abreviaturaActual = $cursoActual->cu_abreviatura;
		$nombreCortoActual = $cursoActual->cu_shortname;

		if ($nombreActual != $this->cu_nombre && $this->existeCurso('cu_nombre', $this->cu_nombre, $id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con ese nombre en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($abreviaturaActual != $this->cu_abreviatura && $this->existeCurso('cu_abreviatura', $this->cu_abreviatura, $id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con esa abreviatura en la Base de Datos.",
				'estado' => 'error'
			];
		} else if ($nombreCortoActual != $this->cu_shortname && $this->existeCurso('cu_shortname', $this->cu_shortname, $id_especialidad)) {
			$datos = [
				'titulo' => "¡Error!",
				'mensaje' => "Ya existe un curso con ese nombre corto en la Base de Datos.",
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

		// $datos = [
		// 	'titulo' => "¡Error!",
		// 	'mensaje' => $qry,
		// 	'estado' => 'error'
		// ];

		return json_encode($datos);
	}

	function listarCursos()
	{
		$consulta = parent::consulta("SELECT * FROM sw_curso WHERE id_especialidad = " . $this->code . " ORDER BY id_curso ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if ($num_total_registros > 0) {
			$contador = 0;
			while ($cursos = parent::fetch_assoc($consulta)) {
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $cursos["id_curso"];
				$name = $cursos["cu_nombre"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"5%\">$code</td>\n";
				$cadena .= "<td width=\"72%\" align=\"left\">$name</td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"editarCurso(" . $code . ")\">editar</a></td>\n";
				$cadena .= "<td width=\"9%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarCurso(" . $code . ",'" . $name . "')\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td align='center'>No se han definido cursos asociados a esta especialidad...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarCursos()
	{
		$qry = "SELECT id_curso, 
					   cu_nombre, 
					   es_figura, 
					   te_nombre, 
					   cu_orden  
				  FROM sw_curso cu, 
				  	   sw_especialidad es, 
					   sw_tipo_educacion te 
				 WHERE es.id_especialidad = cu.id_especialidad 
				   AND te.id_tipo_educacion = es.id_tipo_educacion 
				   AND te.id_periodo_lectivo = $this->id_periodo_lectivo 
				 ORDER BY cu_orden";
		$consulta = parent::consulta($qry);
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($curso = parent::fetch_assoc($consulta)) {
				$id = $curso["id_curso"];
				$name = $curso["cu_nombre"];
				$figura = $curso["es_figura"];
				$nivel = $curso["te_nombre"];
				$orden = $curso["cu_orden"];
				$cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$name</td>\n";
				$cadena .= "<td>$figura</td>\n";
				$cadena .= "<td>$nivel</td>\n";
				$cadena .= "<td>\n";
				$cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarCursoModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarCurso(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>\n";
				$cadena .= "</td>\n";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han definido cursos asociados a esta especialidad...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function cargarCursosAsociados($id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT cs.* FROM sw_asociar_curso_superior cs, sw_curso c, sw_especialidad e WHERE c.id_curso = id_curso_inferior AND e.id_especialidad = c.id_especialidad AND id_periodo_lectivo = $id_periodo_lectivo ORDER BY c.id_especialidad, id_curso");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
		if ($num_total_registros > 0) {
			while ($curso = parent::fetch_assoc($consulta)) {
				$cadena .= "<tr>\n";
				$id = $curso["id_asociar_curso_superior"];
				$id_curso_inferior = $curso["id_curso_inferior"];
				$id_curso_superior = $curso["id_curso_superior"];
				$query = "SELECT es_figura, cu_nombre FROM sw_curso c, sw_especialidad e, sw_tipo_educacion t WHERE c.id_especialidad = e.id_especialidad AND e.id_tipo_educacion = t.id_tipo_educacion AND c.id_curso = $id_curso_inferior";
				$result = parent::consulta($query);
				$record = parent::fetch_assoc($result);
				$curso_inferior = "[" . $record["es_figura"] . "] " . $record["cu_nombre"];
				$query = "SELECT cs_nombre FROM sw_curso_superior WHERE id_curso_superior = $id_curso_superior";
				$result = parent::consulta($query);
				$record = parent::fetch_assoc($result);
				$curso_superior = $record["cs_nombre"];
				$cadena .= "<td>$id</td>\n";
				$cadena .= "<td>$curso_inferior</td>\n";
				$cadena .= "<td>$curso_superior</td>\n";
				$cadena .= "<td><button onclick='eliminarAsociacion(" . $id . ")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";
			}
		} else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se han asociado cursos superiores todav&iacute;a...</td>\n";
			$cadena .= "</tr>\n";
		}
		return $cadena;
	}

	function asociarCursoSuperior()
	{
		$id_curso_inferior = $this->id_curso_inferior;
		$id_curso_superior = $this->id_curso_superior;

		// Compruebo si ya existe la asociación
		try {
			$consulta = parent::consulta("SELECT * FROM sw_asociar_curso_superior WHERE id_curso_inferior = $id_curso_inferior AND id_curso_superior = $id_curso_superior");
			$num_total_registros = parent::num_rows($consulta);
			if ($num_total_registros > 0) {
				$mensaje = "Ya existe la asociaci&oacute;n seleccionada...";
			} else {
				// Inserto en la tabla sw_asociar_curso_superior la asociación seleccionada
				$qry = "INSERT INTO sw_asociar_curso_superior (id_curso_inferior, id_curso_superior, id_periodo_lectivo) VALUES (";
				$qry .= $id_curso_inferior . ",";
				$qry .= $id_curso_superior . ",";
				$qry .= $this->id_periodo_lectivo . ")";
				$consulta = parent::consulta($qry);
				$mensaje = "Curso Superior asociado exitosamente...";
			}
		} catch (Exception $e) {
			$mensaje = "Error en la consulta: " . $e->getMessage();
		}

		return $mensaje;
	}

	function eliminarCursoSuperior()
	{
		$qry = "DELETE FROM sw_asociar_curso_superior WHERE id_asociar_curso_superior = " . $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Curso Superior des-asociado exitosamente...";
		return $mensaje;
	}
}
