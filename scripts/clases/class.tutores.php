<?php

class tutores extends MySQL
{
	
	var $code = "";
	var $id_usuario = "";
	var $id_paralelo = "";
	var $id_estudiante = "";
	var $co_calificacion = "";
	var $id_periodo_lectivo = "";
	var $id_aporte_evaluacion = "";
	var $id_escala_comportamiento = "";
	
	function asociarParaleloTutor()
	{
		$qry = "INSERT INTO sw_paralelo_tutor (id_paralelo, id_usuario, id_periodo_lectivo) VALUES (";
		$qry .= $this->id_paralelo . ",";
		$qry .= $this->id_usuario . ",";
		$qry .= $this->id_periodo_lectivo . ")";
		$consulta = parent::consulta($qry);
		$mensaje = "Tutor asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo asociar el Tutor...Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}

	function eliminarParaleloTutor()
	{
		$qry = "DELETE FROM sw_paralelo_tutor WHERE id_paralelo_tutor =". $this->code;
		$consulta = parent::consulta($qry);
		$mensaje = "Tutor des-asociado exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo des-asociar el Tutor...Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}	

	function listarParalelosTutores()
	{
		$consulta = parent::consulta("SELECT id_paralelo_tutor, cu_nombre, es_figura, pa_nombre, us_titulo, us_fullname FROM sw_paralelo_tutor pt, sw_paralelo p, sw_curso c, sw_especialidad e, sw_usuario u WHERE pt.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND pt.id_usuario = u.id_usuario AND pt.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY c.id_especialidad, c.id_curso, pa_nombre ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "<table class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tutor = parent::fetch_assoc($consulta))
			{
				$contador += 1;
				$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$code = $tutor["id_paralelo_tutor"];
				$paralelo = $tutor["cu_nombre"] . " " . $tutor["pa_nombre"] . " - [" . $tutor["es_figura"] . "]";
				$tutor = $tutor["us_titulo"] . " " . $tutor["us_fullname"];
				$cadena .= "<td width=\"5%\">$contador</td>\n";
				$cadena .= "<td width=\"37%\" align=\"left\">$paralelo</td>\n";	
				$cadena .= "<td width=\"38%\" align=\"left\">$tutor</td>\n";
				$cadena .= "<td width=\"18%\" class=\"link_table\"><a href=\"#\" onclick=\"eliminarAsociacion(".$code.")\">eliminar</a></td>\n";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td>No se ha asociado tutores...</td>\n";
			$cadena .= "</tr>\n";
		}
		$cadena .= "</table>";
		return $cadena;
	}

	function cargarParalelosTutores()
	{
		$consulta = parent::consulta("SELECT id_paralelo_tutor, cu_nombre, es_figura, pa_nombre, us_titulo, us_fullname FROM sw_paralelo_tutor pt, sw_paralelo p, sw_curso c, sw_especialidad e, sw_usuario u WHERE pt.id_paralelo = p.id_paralelo AND p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND pt.id_usuario = u.id_usuario AND pt.id_periodo_lectivo = " . $this->id_periodo_lectivo . " ORDER BY pa_orden ASC");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = ""; $total_tutores = 0;
		if($num_total_registros>0)
		{
			$contador = 0;
			while($tutor = parent::fetch_assoc($consulta))
			{
				$total_tutores++;
				$contador++;
				$cadena .= "<tr>\n";
				$code = $tutor["id_paralelo_tutor"];
				$paralelo = $tutor["cu_nombre"] . " " . $tutor["pa_nombre"] . " - [" . $tutor["es_figura"] . "]";
				$tutor = $tutor["us_titulo"] . " " . $tutor["us_fullname"];
				$cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$paralelo</td>\n";
				$cadena .= "<td>$tutor</td>\n";
				$cadena .= "<td><button onclick='eliminarAsociacion(".$code.")' class='btn btn-block btn-danger'>Eliminar</button></td>";
				$cadena .= "</tr>\n";
			}
		}
		else {
			$cadena .= "<tr>\n";
			$cadena .= "<td colspan='4' align='center'>No se ha asociado tutores...</td>\n";
			$cadena .= "</tr>\n";
		}
		$datos = array('cadena' => $cadena, 
				       'total_tutores' => $total_tutores);
        return json_encode($datos);
	}

	function obtenerIdParaleloTutor($id_usuario, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_paralelo FROM sw_paralelo_tutor WHERE id_usuario = $id_usuario AND id_periodo_lectivo = $id_periodo_lectivo");
		return json_encode(parent::fetch_assoc($consulta));
	}

	function obtenerIdParalelo($id_usuario, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_paralelo FROM sw_paralelo_tutor WHERE id_usuario = $id_usuario AND id_periodo_lectivo = $id_periodo_lectivo");
		$resultado = parent::fetch_assoc($consulta);
		return $resultado["id_paralelo"];
	}

	function obtenerIdCurso($id_paralelo)
	{
		$consulta = parent::consulta("SELECT c.id_curso FROM sw_curso c, sw_paralelo p WHERE c.id_curso = p.id_curso AND id_paralelo = $id_paralelo");
		$resultado = parent::fetch_assoc($consulta);
		return $resultado["id_curso"];
	}

	function obtenerNombreParalelo($id_paralelo)
	{
		$consulta = parent::consulta("SELECT es_figura, cu_nombre, pa_nombre FROM sw_especialidad e, sw_curso c, sw_paralelo p WHERE p.id_curso = c.id_curso AND c.id_especialidad = e.id_especialidad AND p.id_paralelo = $id_paralelo");
		$paralelo = parent::fetch_assoc($consulta);
		return $paralelo["cu_nombre"] . " " . $paralelo["pa_nombre"] . " \"" . $paralelo["es_figura"] . "\"";
	}

	function listarComportamientosPorTutores($id_paralelo, $id_aporte_evaluacion)
	{
		$consulta = parent::consulta("SELECT e.id_estudiante, "
									. "       e.es_apellidos, "
									. "       e.es_nombres "
									. "  FROM sw_estudiante_periodo_lectivo ep, "
									. "       sw_estudiante e "
									. " WHERE ep.id_estudiante = e.id_estudiante "
									. "   AND ep.id_paralelo = " . $id_paralelo
									. "   AND es_retirado = 'N' "
									. "   AND activo = 1 "
									. " ORDER BY es_apellidos, es_nombres ASC");
		$num_total_registros = parent::num_rows($consulta);
		//$cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
		$cadena = "";
		if($num_total_registros > 0)
		{
			$contador = 0;
			while($paralelo = parent::fetch_assoc($consulta))
			{
				$contador++;
				//$fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
				//$cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
				$cadena .= "<tr>\n";
				$id_estudiante = $paralelo["id_estudiante"];
				$apellidos = $paralelo["es_apellidos"];
				$nombres = $paralelo["es_nombres"];
				$cadena .= "<td>$contador</td>\n";	
				$cadena .= "<td>$id_estudiante</td>\n";	
				$cadena .= "<td>".$apellidos." ".$nombres."</td>\n";
				$qry = parent::consulta("SELECT co_calificacion, "
										. "       id_escala_comportamiento "
										. "  FROM sw_comportamiento_tutor "
										. " WHERE id_estudiante = " . $paralelo["id_estudiante"]
										. "   AND id_paralelo = " . $id_paralelo
										. "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
				$num_total_registros = parent::num_rows($qry);
				$comportamiento_estudiante = parent::fetch_assoc($qry);
				if($num_total_registros>0) {
					$calificacion = $comportamiento_estudiante["co_calificacion"];
					$id_escala_comportamiento = $comportamiento_estudiante["id_escala_comportamiento"];
				} else {
					$calificacion = "";
					$id_escala_comportamiento = 0;
				}
				$cadena .= "<td><input type=\"text\" id=\"puntaje_".$contador."\" style=\"text-transform: uppercase;\" value=\"".$calificacion."\"";
				$qry = parent::consulta("SELECT ac.ap_estado
										   FROM sw_aporte_evaluacion a,
												sw_aporte_paralelo_cierre ac,
												sw_curso c,
												sw_paralelo p
										  WHERE a.id_aporte_evaluacion = ac.id_aporte_evaluacion
											AND c.id_curso = p.id_curso
											AND ac.id_paralelo = p.id_paralelo
											AND p.id_paralelo = $id_paralelo 
										    AND a.id_aporte_evaluacion = $id_aporte_evaluacion");
				$aporte = parent::fetch_assoc($qry);
				$estado_aporte = $aporte["ap_estado"];
				if($estado_aporte=='A') {
					$cadena .= " onfocus=\"sel_texto(this)\" onkeypress=\"return permite(event,'car')\" onblur=\"editarCalificacion(this,".$id_estudiante.",".$id_paralelo.",".$id_aporte_evaluacion.")\" /></td>\n";
				} else {
					$cadena .= " disabled /></td>\n";
				}
				$qry = parent::consulta("SELECT ec_relacion FROM sw_escala_comportamiento WHERE id_escala_comportamiento = $id_escala_comportamiento");
				if (!$qry) {
					die("NO SE HAN DEFINIDO LAS ESCALAS DE COMPORTAMIENTO!");
				} else if(parent::num_rows($qry) > 0) {
					$escala_comportamiento = parent::fetch_assoc($qry);
					$descripcion = $escala_comportamiento["ec_relacion"];
				} else {
					$descripcion = "";
				}
				$cadena .= "<td><input type=\"text\" id=\"equivalencia_".$contador."\" disabled value=\"".$descripcion."\" /></td>\n";
				//$cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan=\"4\" class=\"text-center\">No se han matriculado estudiantes en este paralelo...</td>\n";
			$cadena .= "</tr>\n";
		}
		//$cadena .= "</table>";	
		return $cadena;
	}

	function existeCalifComportamiento()
	{
        $consulta = parent::consulta("SELECT * FROM sw_comportamiento_tutor WHERE id_estudiante = " . $this->id_estudiante . " AND id_paralelo = " . $this->id_paralelo . " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion);
		$num_total_registros = parent::num_rows($consulta);
		return($num_total_registros > 0);
	}

	function obtenerIdComportamientoTutor($id_paralelo, $id_estudiante, $id_aporte_evaluacion) {
        $consulta = parent::consulta("SELECT id_comportamiento_tutor "
                                    . "  FROM sw_comportamiento_tutor "
                                    . " WHERE id_paralelo = " . $id_paralelo 
                                    . "   AND id_estudiante = " . $id_estudiante
                                    . "   AND id_aporte_evaluacion = " . $id_aporte_evaluacion);
        if(parent::num_rows($consulta) > 0) {
            return json_encode(parent::fetch_assoc($consulta));
        } else {
            return json_encode(array('id_comportamiento_tutor' => 0));
        }
    }

	function insertarCalifComportamiento()
	{
		$qry = "INSERT INTO sw_comportamiento_tutor SET ";
		$qry .= "id_estudiante = " . $this->id_estudiante . ",";
		$qry .= "id_paralelo = " . $this->id_paralelo . ",";
        $qry .= "id_escala_comportamiento = " . $this->id_escala_comportamiento . ",";
		$qry .= "id_aporte_evaluacion = " . $this->id_aporte_evaluacion . ",";
		$qry .= "co_calificacion = '" . $this->co_calificacion . "'";
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n insertada exitosamente...";
		if (!$consulta)
            $mensaje = "No se pudo realizar la inserci&oacute;n... Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}

	function actualizarCalifComportamiento()
	{
		$qry = "UPDATE sw_comportamiento_tutor SET ";
		$qry .= "co_calificacion = '" . $this->co_calificacion . "', ";
		$qry .= "id_escala_comportamiento = " . $this->id_escala_comportamiento;
		$qry .= " WHERE id_estudiante = " . $this->id_estudiante;
		$qry .= " AND id_paralelo = " . $this->id_paralelo;
		$qry .= " AND id_aporte_evaluacion = " . $this->id_aporte_evaluacion;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n actualizada exitosamente...";
		if (!$consulta)
			$mensaje = "No se pudo realizar la actualizaci&oacute;n... Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}

	function eliminarCalifComportamiento($id_comportamiento_tutor)
	{
		$qry = "DELETE FROM sw_comportamiento_tutor";
		$qry .= " WHERE id_comportamiento_tutor = " . $id_comportamiento_tutor;
		$consulta = parent::consulta($qry);
		$mensaje = "Calificaci&oacute;n eliminada exitosamente...";
		if (!$consulta)
            $mensaje = "No se pudo realizar la eliminaci&oacute;n... Error: " . mysqli_error($this->conexion);
		return $mensaje;
	}

}
?>