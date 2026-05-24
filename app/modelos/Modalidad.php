<?php
class Modalidad
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerModalidades()
    {
        $this->db->query("SELECT * FROM sw_modalidad WHERE mo_activo = 1 ORDER BY mo_orden ASC");
        return $this->db->registros();
    }

    public function obtenerNombreModalidadPorIdPeriodoLectivo($id_periodo_lectivo)
    {
        $this->db->query("SELECT mo_nombre FROM sw_modalidad m, sw_periodo_lectivo p WHERE m.id_modalidad = p.id_modalidad AND p.id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registro()->mo_nombre;
    }

    public function existeModalidad($mo_nombre)
    {
        $query = "SELECT id_modalidad "
                . " FROM sw_modalidad "
                . "WHERE mo_nombre = '" . $mo_nombre ."'";

        $this->db->query($query);
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarModalidad($datos)
    {
        // Obtener el secuencial del campo mo_orden
        $this->db->query("SELECT MAX(mo_orden) AS max_orden FROM sw_modalidad");
        $mo_orden = $this->db->registro()->max_orden + 1;

        $this->db->query('INSERT INTO sw_modalidad (mo_nombre, mo_activo, mo_orden) VALUES (:mo_nombre, :mo_activo, :mo_orden)');

        //Vincular valores
        $this->db->bind(':mo_nombre', $datos['mo_nombre']);
        $this->db->bind(':mo_activo', $datos['mo_activo']);
        $this->db->bind(':mo_orden', $mo_orden);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Agregado con éxito!",
                'mensaje' => "Inserción realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la inserción. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        }
    }

    public function obtenerModalidadPorId($id)
    {
        $this->db->query("SELECT * FROM sw_modalidad WHERE id_modalidad = $id");
        return $this->db->registro();
    }

    public function actualizarModalidad($datos)
    {
        $this->db->query('UPDATE sw_modalidad SET mo_nombre = :mo_nombre, mo_activo = :mo_activo WHERE id_modalidad = :id_modalidad');

        //Vincular valores
        $this->db->bind(':mo_nombre', $datos['mo_nombre']);
        $this->db->bind(':mo_activo', $datos['mo_activo']);
        $this->db->bind(':id_modalidad', $datos['id_modalidad']);

        //Ejecutar
        try {
            $this->db->execute();
            $datos = [
                'titulo' => "¡Actualizado con éxito!",
                'mensaje' => "Actualización realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se pudo realizar la actualización. Error: " . $e->getMessage(),
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        }
    }

    public function eliminarModalidad($id)
    {
        $this->db->query('DELETE FROM sw_modalidad WHERE id_modalidad = :id_modalidad');
        
        //Vincular valores
        $this->db->bind(':id_modalidad', $id);

        try {
            $this->db->execute();

            $datos = [
                'titulo' => "¡Eliminado con éxito!",
                'mensaje' => "Eliminación realizada exitosamente.",
                'estado' => 'success'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } catch (PDOException $e) {
            $datos = [
                'titulo' => "¡Error!",
                'mensaje' => "No se puede realizar la eliminación porque la modalidad tiene periodos lectivos asociados.",
                'estado' => 'error'
            ];
            //Enviar el objeto en formato json
            return json_encode($datos);
        } 
    }

    public function paginacion($paginaActual)
    {
        $this->db->query("SELECT * FROM `sw_modalidad`");
        $consulta = $this->db->registros();
        $nroRegistros = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroRegistros / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroRegistros > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT * FROM `sw_modalidad` ORDER BY mo_orden LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $id = $v->id_modalidad;
                    $nombre = $v->mo_nombre;
                    $activo = $v->mo_activo == 1 ? 'Sí' : 'No';
                    $orden = $v->mo_orden;
                    $tabla .= "<tr data-index='$id' data-orden='$orden'>\n";
                    $tabla .= "<td>" . $id . "</td>\n";
                    $tabla .= "<td>" . $nombre . "</td>\n";
                    $tabla .= "<td>" . $activo . "</td>\n";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-warning\" onclick=\"obtenerDatos(" . $v->id_modalidad . ")\" data-toggle=\"modal\" data-target=\"#editarModalidadModal\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                    $tabla .= "<a href=\"javascript:;\" class=\"btn btn-danger\" onclick=\"eliminarModalidad(".$v->id_modalidad.")\" title=\"Eliminar\"><span class=\"fa fa-trash\"></span></a>\n";
                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<tr><td colspan='3' align='center'>No se han ingresado modalidades todavia...</td></tr>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }

    public function actualizarOrden($id_modalidad, $mo_orden)
    {
        $this->db->query('UPDATE `sw_modalidad` SET `mo_orden` = :mo_orden WHERE `id_modalidad` = :id_modalidad');

        //Vincular valores
        $this->db->bind(':id_modalidad', $id_modalidad);
        $this->db->bind(':mo_orden', $mo_orden);

        echo $this->db->execute();
    }
}
