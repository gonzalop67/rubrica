<?php

class asistencias extends MySQL
{
	
        var $code = "";
        var $id_estudiante = "";
        var $id_asignatura = "";
        var $id_paralelo = "";
        var $id_dia_semana = "";
        var $id_hora_clase = "";
        var $id_inasistencia = "";
        var $ae_fecha = ""; // campo ae_fecha en la tabla sw_asistencia_estudiante
        var $at_fecha = ""; // campo at_fecha en la tabla sw_asistencia_tutor
        var $at_justificacion = ""; // campo at_justificacion en la tabla sw_asistencia_tutor
        // Para actualizar el estado de las asistencias
        var $in_abreviatura = "";

        function obtenerInasistenciaParaleloTutor()
        {
                $consulta = parent::consulta("SELECT e.id_estudiante, "
                                                . "a.id_asistencia_tutor, "
                                                . "e.es_apellidos, "
                                                . "e.es_nombres, "
                                                . "i.in_abreviatura, "
                                                . "a.at_justificacion "
                                                . "FROM sw_estudiante e, "
                                                . "sw_asistencia_tutor a, "
                                                . "sw_inasistencia i "
                                                . "WHERE e.id_estudiante = a.id_estudiante "
                                                . " AND i.id_inasistencia = a.id_inasistencia "
                                                . " AND i.in_abreviatura IN ('I', 'J') "
                                                . " AND a.id_paralelo = " . $this->id_paralelo
                                                . " AND a.at_fecha = '" . $this->at_fecha . "'" 
                                                . " ORDER BY es_apellidos, es_nombres ASC");

                $num_rows = parent::num_rows($consulta);
                $cadena = "";

                if($num_rows > 0) {
                        $contador = 0;
                        while($estudiante = parent::fetch_assoc($consulta)) {
                                $contador++;
                                $id_estudiante = $estudiante["id_estudiante"];
                                $apellidos = $estudiante["es_apellidos"];
                                $nombres = $estudiante["es_nombres"];
                                $nombre_completo = $apellidos . " " . $nombres;
                                $codigo = $estudiante["id_asistencia_tutor"];
                                $justificacion = $estudiante["at_justificacion"];
                                $cadena .= "<tr>\n";
                                $cadena .= "<td width=\"5%\">$contador</td>\n";
                                $cadena .= "<td width=\"5%\">$codigo</td>\n";
                                $cadena .= "<td width=\"30%\">$nombre_completo</td>\n";
                                $checked = ($estudiante["in_abreviatura"]=='J') ? "checked" : "";
                                $disabled = ($estudiante["in_abreviatura"]=='I') ? "disabled" : "";
                                $cadena .= "<td width=\"10%\" align=\"left\"><input type=\"checkbox\" id=\"chkasistencia_" . $contador . "\" $checked onclick=\"editar_justificacion(this,". $codigo . ")\"></td>\n";
                                $cadena .= "<td width=\"30%\" align=\"left\"><input type=\"textbox\" id=\"justificacion_" . $contador . "\" $disabled size=\"50\" value=\"$justificacion\"></td>\n";
                                $cadena .= "<td width=\"20%\" align=\"left\"><button type=\"button\" id=\"save_" . $contador ."\" class=\"btn btn-success btn-xs\" $disabled onclick=\"actualizar_justificacion(this,". $codigo . ")\">Guardar</button></td>\n";
                                $cadena .= "</tr>\n";
                        }
                } else {
                        $cadena .= "<tr>\n";
                        $cadena .= "<td colspan=\"6\" class=\"text-center\">No se han ingresado asistencias para la fecha seleccionada...</td>\n";
                        $cadena .= "</tr>\n";
                }

                return $cadena;
        }

        function listarInasistenciaParaleloTutor()
        {
                // Primero genero las asistencia para que despues puedan ser editadas
                $consulta = parent::consulta("SELECT * 
                                                FROM sw_asistencia_tutor 
                                                WHERE id_paralelo = " . $this->id_paralelo 
                                                ." AND at_fecha = '" . $this->at_fecha . "'");
                $num_rows = parent::num_rows($consulta);

                if ($num_rows == 0) {
                        // Genero las asistencias para editarlas
                        $consulta = parent::consulta("SELECT e.id_estudiante
                                                        FROM sw_estudiante e, 
                                                             sw_estudiante_periodo_lectivo ep 
                                                       WHERE e.id_estudiante = ep.id_estudiante 
                                                         AND es_retirado = 'N' 
                                                         AND activo = 1
                                                         AND ep.id_paralelo = " . $this->id_paralelo);
                        while($estudiante = parent::fetch_assoc($consulta)) {
                                // Aqui se genera la asistencia
                                $insertar = parent::consulta("INSERT INTO sw_asistencia_tutor 
                                                                SET id_estudiante = " . $estudiante["id_estudiante"]
                                                                .", id_paralelo = " . $this->id_paralelo
                                                                .", id_inasistencia = 1"
                                                                .", at_fecha = '" . $this->at_fecha . "'");
                        }
                }

                $consulta = parent::consulta("SELECT e.id_estudiante, "
                                                . "ep.id_paralelo, "
                                                . "e.es_apellidos, "
                                                . "e.es_nombres, "
                                                . "es_retirado "
                                                . "FROM sw_estudiante_periodo_lectivo ep, "
                                                . "sw_estudiante e "
                                                . "WHERE e.id_estudiante = ep.id_estudiante "
                                                . "AND ep.id_paralelo = " . $this->id_paralelo 
                                                . " AND es_retirado = 'N'"
                                                . " AND activo = 1"
                                                . " ORDER BY es_apellidos, es_nombres ASC");
                $num_total_registros = parent::num_rows($consulta);

                $cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
                if($num_total_registros > 0) {
                        $contador = 0;
                        while($paralelo = parent::fetch_assoc($consulta)) {
                                $contador++;
                                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                                $id_estudiante = $paralelo["id_estudiante"];
                                $apellidos = $paralelo["es_apellidos"];
                                $nombres = $paralelo["es_nombres"];
                                $retirado = $paralelo["es_retirado"];
                                $id_paralelo = $paralelo["id_paralelo"];
                                $cadena .= "<td width=\"5%\">$contador</td>\n";	
                                $cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
                                $cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

                                // Aqui va el nuevo codigo para recuperar las asistencias
                                $query = parent::consulta("SELECT i.id_inasistencia, 
                                                                  in_abreviatura, 
                                                                  id_asistencia_tutor
                                                             FROM sw_asistencia_tutor a, 
                                                                  sw_inasistencia i 
                                                            WHERE i.id_inasistencia = a.id_inasistencia
                                                              AND id_estudiante = $id_estudiante 
                                                              AND id_paralelo = " . $this->id_paralelo
                                                           ." AND at_fecha = '" . $this->at_fecha . "'");

                                $resultado = parent::fetch_assoc($query);
                                $codigo = $resultado["id_asistencia_tutor"];
                                $checked= ($resultado["in_abreviatura"]=='P') ? "checked" : "";

                                $cadena .= "<td width=\"8%\" align=\"center\"><input type=\"checkbox\" name=\"chkasistencia_" . $contador . "\" $checked onclick=\"actualizar_asistencia(this,". $codigo . ")\"></td>\n";
                                $cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
                        
                        }
                }
                else {
                        $cadena .= "<tr>\n";	
                        $cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
                        $cadena .= "</tr>\n";
                }
                $cadena .= "</table>";
                return $cadena;
        }

        function listarInasistenciaParalelo()
        {
                // Primero genero las asistencias para que despues puedan ser editadas
                $consulta = parent::consulta("SELECT * 
                                                FROM sw_asistencia_estudiante 
                                                WHERE id_paralelo = " . $this->id_paralelo 
                                                ." AND id_asignatura = " . $this->id_asignatura
                                                ." AND id_hora_clase = " . $this->id_hora_clase
                                                ." AND ae_fecha = '" . $this->ae_fecha . "'");
                $num_rows = parent::num_rows($consulta);

                if ($num_rows == 0) {
                        // Genero las asistencias para editarlas
                        $consulta = parent::consulta("SELECT e.id_estudiante
                                                        FROM sw_estudiante e, 
                                                                sw_estudiante_periodo_lectivo ep 
                                                        WHERE e.id_estudiante = ep.id_estudiante 
                                                                AND es_retirado = 'N'
                                                                AND activo = 1  
                                                                AND ep.id_paralelo = " . $this->id_paralelo);
                        while($estudiante = parent::fetch_assoc($consulta)) {
                                // Aqui se genera la asistencia
                                $insertar = parent::consulta("INSERT INTO sw_asistencia_estudiante 
                                                                SET id_hora_clase = " . $this->id_hora_clase
                                                        .", id_estudiante = " . $estudiante["id_estudiante"]
                                                        .", id_asignatura = " . $this->id_asignatura 
                                                        .", id_paralelo = " . $this->id_paralelo
                                                        .", id_inasistencia = 1"
                                                        .", ae_fecha = '" . $this->ae_fecha . "'");
                        }
                }

                $consulta = parent::consulta("SELECT e.id_estudiante, "
                                                . "d.id_paralelo, "
                                                . "d.id_asignatura, "
                                                . "e.es_apellidos, "
                                                . "e.es_nombres, "
                                                . "es_retirado "
                                                . "FROM sw_distributivo d, "
                                                . "sw_estudiante_periodo_lectivo ep, "
                                                . "sw_estudiante e, "
                                                . "sw_asignatura a, "
                                                . "sw_paralelo p "
                                                . "WHERE d.id_paralelo = ep.id_paralelo "
                                                . "AND d.id_periodo_lectivo = ep.id_periodo_lectivo "
                                                . "AND ep.id_estudiante = e.id_estudiante "
                                                . "AND d.id_asignatura = a.id_asignatura "
                                                . "AND d.id_paralelo = p.id_paralelo "
                                                . "AND d.id_paralelo = " . $this->id_paralelo 
                                                . " AND d.id_asignatura = " . $this->id_asignatura
                                                . " AND es_retirado = 'N'"
                                                . " AND activo = 1"
                                                . " ORDER BY es_apellidos, es_nombres ASC"); //LIMIT $inicio, $cantidad_registros
                $num_total_registros = parent::num_rows($consulta);
                $cadena = "<table id=\"tabla_calificaciones\" class=\"fuente8\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
                if($num_total_registros > 0) {
                        $contador = 0;
                        while($paralelos = parent::fetch_assoc($consulta)) {
                                $contador++;
                                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                                $cadena .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                                $id_estudiante = $paralelos["id_estudiante"];
                                $apellidos = $paralelos["es_apellidos"];
                                $nombres = $paralelos["es_nombres"];
                                /* $retirado = $paralelos["es_retirado"];
                                $id_paralelo = $paralelos["id_paralelo"];
                                $id_asignatura = $paralelos["id_asignatura"]; */
                                $cadena .= "<td width=\"5%\">$contador</td>\n";	
                                $cadena .= "<td width=\"5%\">$id_estudiante</td>\n";	
                                $cadena .= "<td width=\"30%\" align=\"left\">".$apellidos." ".$nombres."</td>\n";

                                // Aqui va el nuevo codigo para recuperar las asistencias
                                $query = parent::consulta("SELECT i.id_inasistencia, 
                                                        in_abreviatura, 
                                                        id_asistencia_estudiante
                                                        FROM sw_asistencia_estudiante a, 
                                                        sw_inasistencia i 
                                                        WHERE i.id_inasistencia = a.id_inasistencia
                                                        AND id_estudiante = $id_estudiante 
                                                        AND id_paralelo = " . $this->id_paralelo
                                                        ." AND id_asignatura = " . $this->id_asignatura
                                                        ." AND id_hora_clase = " . $this->id_hora_clase
                                                        ." AND ae_fecha = '" . $this->ae_fecha . "'");

                                $resultado = parent::fetch_assoc($query);
                                $codigo = $resultado["id_asistencia_estudiante"];
                                $checked= ($resultado["in_abreviatura"]=='P') ? "checked" : "";

                                $cadena .= "<td width=\"8%\" align=\"center\"><input type=\"checkbox\" name=\"chkasistencia_" . $contador . "\" $checked onclick=\"actualizar_asistencia(this,". $codigo . ")\"></td>\n";
                                $cadena .= "<td width=\"*\">&nbsp;</td>\n"; // Esto es para igualar el espaciado entre columnas
                        
                        }
                }
                else {
                        $cadena .= "<tr>\n";	
                        $cadena .= "<td>No se han matriculado estudiantes en este paralelo...</td>\n";
                        $cadena .= "</tr>\n";
                }
                $cadena .= "</table>";
                return $cadena;
        }

        function actualizarJustificacionEstudianteTutor()
        {
                $qry = "SELECT id_inasistencia FROM sw_inasistencia WHERE in_abreviatura = '" . $this->in_abreviatura . "'";
                $consulta = parent::consulta($qry);
                $resultado = parent::fetch_assoc($consulta);
                $id_inasistencia = $resultado["id_inasistencia"];

                $qry = "UPDATE sw_asistencia_tutor SET ";
                $qry .= "id_inasistencia = $id_inasistencia, ";
                $qry .= "at_justificacion = '" . $this->at_justificacion . "'";
                $qry .= " WHERE id_asistencia_tutor = " . $this->code;
                $consulta = parent::consulta($qry);
                
                // Aqui vamos a actualizar las inasistencias registradas por los docentes
                $qry = "SELECT * FROM sw_asistencia_tutor WHERE id_asistencia_tutor = " . $this->code;
                $consulta = parent::consulta($qry);
                $resultado = parent::fetch_assoc($consulta);
                $id_estudiante = $resultado["id_estudiante"];
                $id_paralelo = $resultado["id_paralelo"];
                $ae_fecha = $resultado["at_fecha"];

                $qry = "UPDATE sw_asistencia_estudiante SET id_inasistencia = $id_inasistencia WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND ae_fecha = '$ae_fecha'";
                $consulta = parent::consulta($qry);
                $mensaje = "Inasistencia actualizada exitosamente...";

                return $mensaje;
        }

        function actualizarInasistenciaEstudianteTutor()
        {
                $qry = "SELECT id_inasistencia FROM sw_inasistencia WHERE in_abreviatura = '" . $this->in_abreviatura . "'";
                $consulta = parent::consulta($qry);
                $resultado = parent::fetch_assoc($consulta);
                $id_inasistencia = $resultado["id_inasistencia"];

                $qry = "UPDATE sw_asistencia_tutor SET ";
                $qry .= "id_inasistencia = $id_inasistencia ";
                $qry .= " WHERE id_asistencia_tutor = " . $this->code;
                $consulta = parent::consulta($qry);
                $mensaje = "Inasistencia actualizada exitosamente...";
                if (!$consulta)
                $mensaje = "No se pudo actualizar la Inasistencia...Error: " . mysqli_error($this->conexion);
                return $mensaje;
        }

        function actualizarInasistenciaEstudiante()
        {
                $qry = "SELECT id_inasistencia FROM sw_inasistencia WHERE in_abreviatura = '" . $this->in_abreviatura . "'";
                $consulta = parent::consulta($qry);
                $resultado = parent::fetch_assoc($consulta);
                $id_inasistencia = $resultado["id_inasistencia"];

                $qry = "UPDATE sw_asistencia_estudiante SET ";
                $qry .= "id_inasistencia = $id_inasistencia ";
                $qry .= " WHERE id_asistencia_estudiante = " . $this->code;
                $consulta = parent::consulta($qry);
                $mensaje = "Inasistencia actualizada exitosamente...";
                if (!$consulta)
                $mensaje = "No se pudo actualizar la Inasistencia...Error: " . mysqli_error($this->conexion);
                return $mensaje;
        }

        function eliminarInasistencia()
        {
                $qry = "DELETE FROM sw_inasistencia WHERE id_inasistencia = ". $this->code;
                $consulta = parent::consulta($qry);
                $mensaje = "Inasistencia eliminada exitosamente...";
                if (!$consulta)
                        $mensaje = "No se pudo eliminar la Inasistencia...Error: " . mysqli_error($this->conexion);
                return $mensaje;
        }
		
}
?>