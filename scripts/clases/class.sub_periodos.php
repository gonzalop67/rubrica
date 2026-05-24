<?php

class sub_periodos extends MySQL
{
    var $code = "";
    var $pe_nombre = "";
    var $pe_abreviatura = "";
    var $id_tipo_periodo = "";

    function existeNombreSubPeriodo($nombre)
    {
        $consulta = parent::consulta("SELECT * FROM sw_sub_periodo_evaluacion WHERE pe_nombre = '$nombre'");
        return (parent::num_rows($consulta) > 0);
    }

    function existeAbreviaturaSubPeriodo($abreviatura)
    {
        $consulta = parent::consulta("SELECT * FROM sw_sub_periodo_evaluacion WHERE pe_abreviatura = '$abreviatura'");
        return (parent::num_rows($consulta) > 0);
    }

    function cargar_subperiodos_evaluacion()
    {
        $consulta = parent::consulta("SELECT * FROM sw_sub_periodo_evaluacion ORDER BY pe_orden ASC");
        $num_total_registros = parent::num_rows($consulta);
        $cadena = "";
        if ($num_total_registros > 0) {
            $contador = 0;
            while ($sub_periodo = parent::fetch_assoc($consulta)) {
                $contador++;
                $id = $sub_periodo["id_sub_periodo_evaluacion"];
                $name = $sub_periodo["pe_nombre"];
                $abrev = $sub_periodo["pe_abreviatura"];
                $orden = $sub_periodo["pe_orden"];
                $cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
                $cadena .= "<td>$contador</td>\n";
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$name</td>\n";
                $cadena .= "<td>$abrev</td>";
                $cadena .= "<td>\n";
                $cadena .= "<div class='btn-group'>\n";
                $cadena .= "<button class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $id . ")\" data-toggle=\"modal\" data-target=\"#editarSubperiodoModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></button>\n";
                $cadena .= "<button class=\"btn btn-danger\" onclick=\"eliminarSubPeriodo(" . $id . ")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></button>\n";
                $cadena .= "</div>";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='100%' align='center'>No se han definido Subperiodos de Evaluación...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    function obtenerSubPeriodo()
    {
        $consulta = parent::consulta("SELECT * FROM sw_sub_periodo_evaluacion WHERE id_sub_periodo_evaluacion = $this->code");
        return json_encode(parent::fetch_assoc($consulta));
    }

    function insertarSubPeriodo()
    {
        if ($this->existeNombreSubPeriodo($this->pe_nombre)) {
            //Mensaje de operación fallida
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe un subperiodo de evaluación con ese nombre en la base de datos.",
                'estado' => 'error'
            ];
        } else if ($this->existeAbreviaturaSubPeriodo($this->pe_abreviatura)) {
            //Mensaje de operación fallida
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "Ya existe un subperiodo de evaluación con esa abreviatura en la base de datos.",
                'estado' => 'error'
            ];
        } else {
            try {
                $consulta = parent::consulta("SELECT MAX(pe_orden) AS max_orden FROM sw_sub_periodo_evaluacion");
                if (empty($consulta)) {
                    $max_orden = 1;
                } else {
                    $registro = parent::fetch_object($consulta);
                    $max_orden = $registro->max_orden + 1;
                }
                $qry = "INSERT INTO sw_sub_periodo_evaluacion (id_tipo_periodo, pe_nombre, pe_abreviatura, pe_orden) VALUES (";
                $qry .= $this->id_tipo_periodo . ",";
                $qry .= "'" . strtoupper($this->pe_nombre) . "',";
                $qry .= "'" . strtoupper($this->pe_abreviatura) . "',";
                $qry .= $max_orden . ")";

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

    function actualizarSubPeriodo()
    {
        $qry = "UPDATE sw_sub_periodo_evaluacion SET ";
        $qry .= "pe_nombre = '" . strtoupper($this->pe_nombre) . "', ";
        $qry .= "pe_abreviatura = '" . strtoupper($this->pe_abreviatura) . "', ";
        $qry .= "id_tipo_periodo = " . $this->id_tipo_periodo;
        $qry .= " WHERE id_sub_periodo_evaluacion = " . $this->code;

        $consulta = parent::consulta("SELECT * FROM sw_sub_periodo_evaluacion WHERE id_sub_periodo_evaluacion = $this->code");
        $registro = parent::fetch_object($consulta);
        $nombreActual = $registro->pe_nombre;
        $abreviaturaActual = $registro->pe_abreviatura;

        if ($nombreActual != $this->pe_nombre && $this->existeNombreSubperiodo($this->pe_nombre)) {
            //Mensaje de operación exitosa
            $datos = [
                'titulo' => "¡Ocurrió un error inesperado!",
                'mensaje' => "Ya existe un subperiodo de evaluación con este nombre.",
                'estado' => 'error'
            ];
        } else if ($abreviaturaActual != $this->pe_abreviatura && $this->existeAbreviaturaSubperiodo($this->pe_abreviatura)) {
            //Mensaje de operación exitosa
            $datos = [
                'titulo' => "¡Ocurrió un error inesperado!",
                'mensaje' => "Ya existe un subperiodo de evaluación con esta abreviatura.",
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

    function eliminarSubperiodo()
    {
        // Primero compruebo si no existen aportes de evaluación asociados
        $qry = "SELECT id_aporte_periodo FROM sw_aporte_periodo WHERE id_sub_periodo_evaluacion = " . $this->code;
        $consulta = parent::consulta($qry);
        $num_total_registros = parent::num_rows($consulta);
        if ($num_total_registros > 0) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se puede eliminar porque tiene aportes de evaluación asociados...",
                'estado' => 'error'
            ];
        } else {
            try {
                $qry = "DELETE FROM sw_sub_periodo_evaluacion WHERE id_sub_periodo_evaluacion =" . $this->code;
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