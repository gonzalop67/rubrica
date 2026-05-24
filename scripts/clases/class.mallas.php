<?php

class mallas extends MySQL
{
	var $code = "";
    var $id_periodo_lectivo = "";
    var $id_curso = "";
	var $id_paralelo = "";
	var $id_asignatura = "";
	var $ma_horas_presenciales = 0;
	var $ma_horas_autonomas = 0;
	var $ma_horas_tutorias = 0;
    var $ma_subtotal = 0;
    
    function obtenerItemMalla()
	{
		$consulta = parent::consulta("SELECT * FROM sw_malla_curricular WHERE id_malla_curricular = " . $this->code);
		return json_encode(parent::fetch_assoc($consulta));
	}
    
    function insertarMalla()
    {
        $consulta = parent::consulta("SELECT * FROM sw_malla_curricular WHERE id_curso = "
                                     . $this->id_curso
                                     . " AND id_asignatura = "
                                     . $this->id_asignatura);
        $num_total_registros = parent::num_rows($consulta);
        if($num_total_registros > 0) {
            $mensaje = "Ya existe la asociacion entre el curso y asignatura escogidos.";
        } else {
            // Procedimiento para insertar un item en la malla curricular
            $qry = "INSERT INTO sw_malla_curricular(id_periodo_lectivo,
                                                    id_curso,
                                                    id_asignatura,
                                                    ma_horas_presenciales,
                                                    ma_horas_autonomas,
                                                    ma_horas_tutorias,
                                                    ma_subtotal) VALUES(";
            // id_periodo_lectivo
            $qry .= $this->id_periodo_lectivo . ",";
            // id_curso
            $qry .= $this->id_curso . ",";
            // id_asignatura
            $qry .= $this->id_asignatura . ",";
            // ma_horas_presenciales
            $qry .= $this->ma_horas_presenciales . ",";
            // ma_horas_autonomas
            $qry .= $this->ma_horas_autonomas . ",";
            // ma_horas_tutorias
            $qry .= $this->ma_horas_tutorias . ",";
            // ma_subtotal
            $qry .= $this->ma_subtotal . ")";
            $consulta = parent::consulta($qry);
            $mensaje = "Insercion exitosa.";
        }
        return $mensaje;
    }

    function actualizarMalla()
    {
        // Procedimiento para actualizar un item en la malla curricular
        $qry = "UPDATE sw_malla_curricular SET";
        // id_periodo_lectivo
        // $qry .= " id_periodo_lectivo = " . $this->id_periodo_lectivo . ",";
        // id_curso
        // $qry .= " id_curso = " . $this->id_curso . ",";
        // id_asignatura
        // $qry .= " id_asignatura = " . $this->id_asignatura . ",";
        // ma_horas_presenciales
        $qry .= " ma_horas_presenciales = " . $this->ma_horas_presenciales . ",";
        // ma_horas_autonomas
        $qry .= " ma_horas_autonomas = " . $this->ma_horas_autonomas . ",";
        // ma_horas_tutorias
        $qry .= " ma_horas_tutorias = " . $this->ma_horas_tutorias . ",";
        // ma_subtotal
        $qry .= " ma_subtotal = " . $this->ma_subtotal;
        $qry .= " WHERE id_malla_curricular = " . $this->code;
        $consulta = parent::consulta($qry);
        $mensaje = "Actualizacion exitosa.";
        return $mensaje;
    }
    function eliminarMalla()
    {
        $consulta = parent::consulta("DELETE FROM sw_malla_curricular WHERE id_malla_curricular = " . $this->code);
        $mensaje = "Item de la Malla Curricular eliminado exitosamente.";
        return $mensaje;
    }
    function listarMalla()
	{
		$consulta = parent::consulta("SELECT m.*, 
                                             as_nombre, 
                                             cu_nombre,
                                             ac_orden 
                                        FROM sw_malla_curricular m, 
                                             sw_curso c, 
                                             sw_asignatura_curso ac, 
                                             sw_asignatura a 
                                       WHERE c.id_curso = m.id_curso 
                                         AND c.id_curso = ac.id_curso 
                                         AND a.id_asignatura = m.id_asignatura 
                                         AND m.id_asignatura = ac.id_asignatura 
                                         AND m.id_curso = " . $this->id_curso 
                                   . " ORDER BY ac_orden");
		$num_total_registros = parent::num_rows($consulta);
		$cadena = "";
        $suma_horas = 0;
		if($num_total_registros > 0)
		{
            $suma_horas = 0;
            $contador = 0;
			while($malla = parent::fetch_assoc($consulta))
			{
                $contador++;
				$cadena .= "<tr>\n";
				$code = $malla["id_malla_curricular"];
				$asignatura = $malla["as_nombre"];
				$curso = $malla["cu_nombre"];
                $presenciales = $malla["ma_horas_presenciales"];
                $autonomas = $malla["ma_horas_autonomas"];
                $tutorias = $malla["ma_horas_tutorias"];
                $suma_horas = $suma_horas + $presenciales + $tutorias;
                $cadena .= "<td>$contador</td>\n";
				$cadena .= "<td>$code</td>\n";
				$cadena .= "<td>$asignatura</td>\n";
                $cadena .= "<td>$curso</td>\n";
                $cadena .= "<td>$presenciales</td>\n";
                $cadena .= "<td>$autonomas</td>\n";
                $cadena .= "<td>$tutorias</td>\n";
                $cadena .= "<td style='text-align:center'>\n";
                $cadena .= "<div class='btn-group'>\n";
				$cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $code . ")\" data-toggle=\"modal\" data-target=\"#editarItemMallaModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
				$cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarItemMalla(" . $code . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
				$cadena .= "</div>\n";
                $cadena .= "</td>\n";
				$cadena .= "</tr>\n";	
			}
		}
		else {
			$cadena .= "<tr>\n";	
			$cadena .= "<td colspan='8' align='center'>No se han definido items asociados a este curso...</td>\n";
			$cadena .= "</tr>\n";	
        }
        $datos = array('cadena' => $cadena, 
				       'total_horas' => $suma_horas);
        return json_encode($datos);
	}
}