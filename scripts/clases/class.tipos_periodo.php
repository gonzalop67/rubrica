<?php

class tipos_periodo extends MySQL
{

    var $code = "";
    var $tp_descripcion = "";
    var $tp_slug = "";

    function existeTipoPeriodo($nombre)
    {
        $consulta = parent::consulta("SELECT * FROM sw_tipo_periodo WHERE tp_descripcion = '$nombre'");
        return (parent::num_rows($consulta) > 0);
    }

    function obtenerTipoPeriodo()
    {
        $consulta = parent::consulta("SELECT * FROM sw_tipo_periodo WHERE id_tipo_periodo = $this->code");
        return json_encode(parent::fetch_assoc($consulta));
    }

    function cargar_tipos_periodo()
    {
        $consulta = parent::consulta("SELECT * FROM sw_tipo_periodo ORDER BY id_tipo_periodo");
        $num_total_registros = parent::num_rows($consulta);
        $cadena = "";
        if ($num_total_registros > 0) {
            $contador = 0;
            while ($tipo_periodo = parent::fetch_assoc($consulta)) {
                $contador++;
                $id = $tipo_periodo["id_tipo_periodo"];
                $name = $tipo_periodo["tp_descripcion"];
                $slug = $tipo_periodo["tp_slug"];
                $cadena .= "<tr>\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$name</td>\n";
                $cadena .= "<td>$slug</td>";
                $cadena .= "<td>\n";
                $cadena .= "<div class='btn-group'>\n";
                $cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarTipoPeriodoModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
                $cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarTipoPeriodo(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
                $cadena .= "</div>";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='100%' align='center'>No se han definido Tipos de Periodo de Evaluación...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    function insertarTipoPeriodo()
    {
        if ($this->existeTipoPeriodo($this->tp_descripcion)) {
            //Mensaje de operación fallida
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe un tipo de periodo de evaluación con ese nombre en base de datos.",
                'estado' => 'error'
            ];
        } else {
            try {
                $qry = "INSERT INTO sw_tipo_periodo (tp_descripcion, tp_slug) VALUES (";
                $qry .= "'" . strtoupper($this->tp_descripcion) . "',";
                $qry .= "'" . strtolower($this->tp_slug) . "')";

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

    function actualizarTipoPeriodo()
    {
        $qry = "UPDATE sw_tipo_periodo SET ";
        $qry .= "tp_descripcion = '" . strtoupper($this->tp_descripcion) . "', ";
        $qry .= "tp_slug = '" . strtolower($this->tp_slug) . "'";
        $qry .= " WHERE id_tipo_periodo = " . $this->code;

        $consulta = parent::consulta("SELECT * FROM sw_tipo_periodo WHERE id_tipo_periodo = $this->code");
        $registro = parent::fetch_object($consulta);
        $nombreActual = $registro->tp_descripcion;

        if ($nombreActual != $this->tp_descripcion && $this->existeTipoPeriodo($this->tp_descripcion)) {
            //Mensaje de operación exitosa
            $datos = [
                'titulo' => "¡Ocurrió un error inesperado!",
                'mensaje' => "Ya existe un tipo de periodo de evaluación con este nombre.",
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

    function eliminarTipoPeriodo()
    {
        // Primero compruebo si no existen subperiodos de evaluación asociados
        $qry = "SELECT id_sub_periodo_evaluacion FROM sw_sub_periodo_evaluacion WHERE id_tipo_periodo = " . $this->code;
        $consulta = parent::consulta($qry);
        $num_total_registros = parent::num_rows($consulta);
        if ($num_total_registros > 0) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se puede eliminar porque tiene subperiodos de evaluación asociados...",
                'estado' => 'error'
            ];
        } else {
            try {
                $qry = "DELETE FROM sw_tipo_periodo WHERE id_tipo_periodo =" . $this->code;
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
}
