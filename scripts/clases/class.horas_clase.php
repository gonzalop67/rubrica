<?php
class horas_clase extends MySQL {
    var $code = "";
    var $hc_nombre = "";
    var $hc_orden = "";
    var $hc_hora_fin = "";
    var $id_dia_semana = "";
    var $hc_hora_inicio = "";
    var $id_periodo_lectivo = "";
    var $id_horario_def = "";
    var $id_asignatura = "";
    var $id_paralelo = "";

    function listar_horas_clase() {
        $id_horario_def = intval($this->id_horario_def);
        $consulta = parent::consulta("SELECT * FROM sw_hora_clase WHERE id_horario_def = $id_horario_def ORDER BY hc_orden ASC");
        $num_total_registros = parent::num_rows($consulta);
        $cadena = "";
        
        if ($num_total_registros > 0) {
            while ($hora_clase = parent::fetch_assoc($consulta)) {
                $code = $hora_clase["id_hora_clase"];
                $name = htmlspecialchars($hora_clase["hc_nombre"], ENT_QUOTES, 'UTF-8');
                $hora_inicio = $hora_clase["hc_hora_inicio"];
                $hora_fin = $hora_clase["hc_hora_fin"];
                $ordinal = $hora_clase["hc_orden"];
                
                $cadena .= "<tr data-index='$code' data-orden='$ordinal'>\n";
                $cadena .= "<td>$code</td>\n";
                $cadena .= "<td>$name</td>\n";
                $cadena .= "<td>$hora_inicio</td>\n";
                $cadena .= "<td>$hora_fin</td>\n";
                $cadena .= "<td>$ordinal</td>\n";
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
            $cadena .= "<td colspan='6' align='center'>No se han definido Horas Clase para este periodo lectivo...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    function insertarHoraClase() {
        try {
            $id_horario_def = intval($this->id_horario_def);
            
            // Obtener el max_orden de la tabla sw_hora_clase
            $qry = "SELECT MAX(hc_orden) AS max_orden FROM sw_hora_clase WHERE id_horario_def = $id_horario_def";
            $consulta = parent::consulta($qry);
            
            if (parent::num_rows($consulta) > 0) {
                $registro = parent::fetch_object($consulta);
                $max_orden = $registro->max_orden + 1;
            } else {
                $max_orden = 1;
            }

            // Escapar cadenas de texto para evitar Inyección SQL
            $hc_nombre = parent::filtrar($this->hc_nombre);
            $hc_hora_inicio = parent::filtrar($this->hc_hora_inicio);
            $hc_hora_fin = parent::filtrar($this->hc_hora_fin);

            $qry = "INSERT INTO sw_hora_clase (id_horario_def, hc_nombre, hc_hora_inicio, hc_hora_fin, hc_orden) VALUES (";
            $qry .= "$id_horario_def, '$hc_nombre', '$hc_hora_inicio', '$hc_hora_fin', $max_orden)";
            parent::consulta($qry);

            // Obtener el id_hora_clase recién creado
            $qry = "SELECT MAX(id_hora_clase) AS max_id FROM sw_hora_clase";
            $consulta = parent::consulta($qry);
            $registro = parent::fetch_object($consulta);
            $max_id = $registro->max_id;

            // Asociar la nueva hora clase con los días de la semana
            $qry = "SELECT id_dia_semana FROM sw_dia_semana WHERE id_horario_def = $id_horario_def";
            $consulta = parent::consulta($qry);
            
            while ($row = parent::fetch_object($consulta)) {
                $qry = "INSERT INTO sw_hora_dia SET id_dia_semana = $row->id_dia_semana, id_hora_clase = $max_id, id_horario_def = $id_horario_def";
                parent::consulta($qry);
            }

            $datos = array(
                "titulo" => "Yes!",
                "mensaje" => "Hora Clase insertada exitosamente.",
                "tipo_mensaje" => "success"
            );
        } catch (Exception $e) {
            $datos = array(
                "titulo" => "Oops!",
                "mensaje" => "La Hora Clase no fue insertada. Error: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
        }
        return json_encode($datos);
    }

    function obtenerHoraClase() {
        $code = intval($this->code);
        $consulta = parent::consulta("SELECT id_hora_clase, hc_nombre, DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin, hc_orden FROM sw_hora_clase WHERE id_hora_clase = $code");
        return json_encode(parent::fetch_assoc($consulta));
    }

    function obtenerNombreHoraClase($id_hora_clase) {
        $id_hora_clase = intval($id_hora_clase);
        $consulta = parent::consulta("SELECT hc_nombre, DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin, ds_nombre FROM sw_hora_clase hc, sw_dia_semana di WHERE di.id_dia_semana = hc.id_dia_semana AND id_hora_clase = $id_hora_clase");
        $hora_clase = parent::fetch_assoc($consulta);
        
        if (!$hora_clase) return "No asignado";
        
        return $hora_clase["ds_nombre"] . " - " . $hora_clase["hc_nombre"] . " (" . $hora_clase["hora_inicio"] . " - " . $hora_clase["hora_fin"] . ")";
    }

    function actualizarHoraClase() {
        try {
            $code = intval($this->code);
            $hc_nombre = parent::filtrar($this->hc_nombre);
            $hc_hora_inicio = parent::filtrar($this->hc_hora_inicio);
            $hc_hora_fin = parent::filtrar($this->hc_hora_fin);

            $qry = "UPDATE sw_hora_clase SET ";
            $qry .= "hc_nombre = '$hc_nombre', ";
            $qry .= "hc_hora_inicio = '$hc_hora_inicio', ";
            $qry .= "hc_hora_fin = '$hc_hora_fin' ";
            $qry .= "WHERE id_hora_clase = $code";
            parent::consulta($qry);

            $datos = array(
                "titulo" => "Yes!",
                "mensaje" => "Hora Clase actualizada exitosamente.",
                "tipo_mensaje" => "success"
            );
        } catch (Exception $e) {
            $datos = array(
                "titulo" => "Oops!",
                "mensaje" => "La Hora Clase no fue actualizada. Error: " . $e->getMessage(),
                "tipo_mensaje" => "error"
            );
        }
        return json_encode($datos);
    }

    function eliminarHoraClase() {
        $code = intval($this->code);
        $qry = "SELECT * FROM sw_horario WHERE id_hora_clase = $code";
        $consulta = parent::consulta($qry);
        
        if (parent::num_rows($consulta) > 0) {
            $data = [
                'titulo' => 'Oh no!',
                'mensaje' => 'Existen registros relacionados en la tabla de horarios...',
                'estado' => 'error'
            ];
        } else {
            try {
                $qry = "DELETE FROM sw_hora_clase WHERE id_hora_clase = $code";
                parent::consulta($qry);
                $data = [
                    "titulo" => "Yes!",
                    "mensaje" => "Hora Clase eliminada exitosamente.",
                    "estado" => "success"
                ];
            } catch (Exception $e) {
                $data = [
                    "titulo" => "Oops!",
                    "mensaje" => "La Hora Clase no fue eliminada. Error: " . $e->getMessage(),
                    "tipo_mensaje" => "error"
                ];
            }
        }
        return json_encode($data);
    }

    function obtenerHorasClase() {
        // Casteo forzado a enteros para blindar la consulta contra SQL Injection
        $id_asignatura = intval($this->id_asignatura);
        $id_paralelo = intval($this->id_paralelo);
        $id_dia_semana = intval($this->id_dia_semana);
        $id_horario_def = intval($this->id_horario_def);

        $sql = "SELECT hc.id_hora_clase, hc.hc_nombre, DATE_FORMAT(hc.hc_hora_inicio,'%H:%i') AS hora_inicio, DATE_FORMAT(hc.hc_hora_fin,'%H:%i') AS hora_fin, di.ds_nombre 
                FROM sw_hora_clase hc, sw_horario ho, sw_dia_semana di 
                WHERE hc.id_hora_clase = ho.id_hora_clase 
                  AND di.id_dia_semana = ho.id_dia_semana 
                  AND ho.id_asignatura = $id_asignatura 
                  AND ho.id_paralelo = $id_paralelo 
                  AND ho.id_dia_semana = $id_dia_semana 
                  AND ho.id_horario_def = $id_horario_def 
                ORDER BY hc.hc_orden";
                
        $consulta = parent::consulta($sql);
        $cadena = "";
        $num_total_registros = parent::num_rows($consulta);
        
        if ($num_total_registros > 0) {
            while ($hora_clase = parent::fetch_assoc($consulta)) {
                $code = $hora_clase["id_hora_clase"];
                $name = $hora_clase["ds_nombre"] . " - " . $hora_clase["hc_nombre"] . " (" . $hora_clase["hora_inicio"] . " - " . $hora_clase["hora_fin"] . ")";
                $cadena .= "<option value=\"$code\">$name</option>";
            }
        }
        
        $datos = array(
            'num_registros' => $num_total_registros,
            'cadena' => $cadena
        );
        return json_encode($datos);
    }
}
?>
