<?php
class PeriodoLectivo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerPeriodosLectivos()
    {
        $this->db->query("SELECT p.*, pe_descripcion, mo_nombre FROM sw_periodo_lectivo p, sw_periodo_estado pe, sw_modalidad m WHERE pe.id_periodo_estado = p.id_periodo_estado AND m.id_modalidad = p.id_modalidad ORDER BY pe_fecha_inicio DESC");
        return $this->db->registros();
    }

    public function obtenerPeriodosL($id_modalidad)
    {
        $this->db->query("SELECT p.*, pe_descripcion FROM sw_periodo_lectivo p, sw_periodo_estado pe WHERE pe.id_periodo_estado = p.id_periodo_estado AND id_modalidad = $id_modalidad ORDER BY pe_fecha_inicio DESC");
        return $this->db->registros();
    }

    public function obtenerPeriodoLectivo($id)
    {
        $this->db->query("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $id");
        return $this->db->registro();
    }

    public function insertarPeriodoLectivo($datos)
    {
        $this->db->query(
            "INSERT INTO sw_periodo_lectivo (
                            id_periodo_estado, 
                            id_modalidad, 
                            pe_anio_inicio, 
                            pe_anio_fin, 
                            pe_fecha_inicio, 
                            pe_fecha_fin, 
                            pe_nota_minima, 
                            pe_nota_aprobacion, 
                            quien_inserta_comp_id) VALUES (
                            :id_periodo_estado, 
                            :id_modalidad, 
                            :pe_anio_inicio, 
                            :pe_anio_fin, 
                            :pe_fecha_inicio, 
                            :pe_fecha_fin, 
                            :pe_nota_minima, 
                            :pe_nota_aprobacion, 
                            :quien_inserta_comp_id)"
        );

        //Vincular valores
        $this->db->bind(':id_periodo_estado', 1);
        $this->db->bind(':id_modalidad', $datos['id_modalidad']);
        $this->db->bind(':pe_anio_inicio', $datos['pe_anio_inicio']);
        $this->db->bind(':pe_anio_fin', $datos['pe_anio_fin']);
        $this->db->bind(':pe_fecha_inicio', $datos['pe_fecha_inicio']);
        $this->db->bind(':pe_fecha_fin', $datos['pe_fecha_fin']);
        $this->db->bind(':pe_nota_minima', $datos['pe_nota_minima']);
        $this->db->bind(':pe_nota_aprobacion', $datos['pe_nota_aprobacion']);
        $this->db->bind(':quien_inserta_comp_id', $datos['quien_inserta_comp_id']);

        $this->db->execute();

        $this->db->query("SELECT MAX(id_periodo_lectivo) AS max_id FROM sw_periodo_lectivo");
        $id_periodo_lectivo = $this->db->registro()->max_id;

        // Asociar sub niveles de educación
        for ($i = 0; $i < count($datos['niveles']); $i++) {
            $this->db->query("INSERT INTO sw_periodo_nivel SET id_periodo_lectivo = :id_periodo_lectivo, id_nivel_educacion = :id_nivel_educacion");

            //Vincular valores
            $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
            $this->db->bind(':id_nivel_educacion', $datos['niveles'][$i]);

            $this->db->execute();
        }

        // Asociar subperiodos de evaluacion
        for ($i = 0; $i < count($datos['sub_periodos']); $i++) {
            $this->db->query("INSERT INTO sw_periodo_lectivo_sub_periodo SET id_periodo_lectivo = :id_periodo_lectivo, id_sub_periodo_evaluacion = :id_sub_periodo_evaluacion");

            //Vincular valores
            $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
            $this->db->bind(':id_sub_periodo_evaluacion', $datos['sub_periodos'][$i]);

            $this->db->execute();
        }
    }

    public function actualizarPeriodoLectivo($datos)
    {
        $this->db->query("UPDATE sw_periodo_lectivo SET 
                            pe_anio_inicio = :pe_anio_inicio, 
                            pe_anio_fin = :pe_anio_fin, 
                            pe_fecha_inicio = :pe_fecha_inicio, 
                            pe_fecha_fin = :pe_fecha_fin, 
                            pe_nota_minima = :pe_nota_minima, 
                            pe_nota_aprobacion = :pe_nota_aprobacion, 
                            quien_inserta_comp_id = :quien_inserta_comp_id 
                        WHERE id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind(':pe_anio_inicio', $datos['pe_anio_inicio']);
        $this->db->bind(':pe_anio_fin', $datos['pe_anio_fin']);
        $this->db->bind(':pe_fecha_inicio', $datos['pe_fecha_inicio']);
        $this->db->bind(':pe_fecha_fin', $datos['pe_fecha_fin']);
        $this->db->bind(':pe_nota_minima', $datos['pe_nota_minima']);
        $this->db->bind(':pe_nota_aprobacion', $datos['pe_nota_aprobacion']);
        $this->db->bind(':quien_inserta_comp_id', $datos['quien_inserta_comp_id']);

        $this->db->execute();
    }

    public function paginacion($paginaActual, $id_modalidad)
    {
        $this->db->query("SELECT * FROM `sw_periodo_lectivo` WHERE id_modalidad = $id_modalidad");
        $consulta = $this->db->registros();
        $nroPeriodosLectivos = $this->db->rowCount();

        $nroLotes = 5;
        $nroPaginas = ceil($nroPeriodosLectivos / $nroLotes);

        $lista = '';
        $tabla = '';

        if ($nroPeriodosLectivos > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ',' . $id_modalidad . ');">‹</a></li>';
            }

            for ($i = 1; $i <= $nroPaginas; $i++) {
                if ($i == $paginaActual) {
                    $lista = $lista . '<li class="active"><a href="javascript:pagination(' . $i . ',' . $id_modalidad . ');">' . $i . '</a></li>';
                } else {
                    $lista = $lista . '<li><a href="javascript:pagination(' . $i . ',' . $id_modalidad . ');">' . $i . '</a></li>';
                }
            }

            if ($paginaActual == $nroPaginas) {
                $lista = $lista . '<li><a href="javascript:;" disabled>›</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ',' . $id_modalidad . ');">›</a></li>';
            }

            if ($paginaActual <= 1) {
                $limit = 0;
            } else {
                $limit = $nroLotes * ($paginaActual - 1);
            }

            $this->db->query("SELECT pl.*, pe.pe_descripcion FROM `sw_periodo_lectivo` pl, `sw_periodo_estado` pe WHERE pe.id_periodo_estado = pl.id_periodo_estado AND id_modalidad = $id_modalidad ORDER BY pe_fecha_inicio DESC LIMIT $limit, $nroLotes");
            $consulta = $this->db->registros();
            $num_total_registros = $this->db->rowCount();

            if ($num_total_registros > 0) {
                $contador = $limit;
                foreach ($consulta as $v) {
                    $contador++;
                    $tabla .= "<tr>\n";
                    $code = $v->id_periodo_lectivo;
                    $pe_anio_inicio = $v->pe_anio_inicio;
                    $pe_anio_fin = $v->pe_anio_fin;
                    $pe_estado = $v->pe_descripcion;
                    $tabla .= "<td>$contador</td>";
                    $tabla .= "<td>$code</td>\n";
                    $tabla .= "<td>$pe_anio_inicio</td>\n";
                    $tabla .= "<td>$pe_anio_fin</td>\n";
                    $tabla .= "<td>$pe_estado</td>\n";
                    $tabla .= "<td>\n";
                    $tabla .= "<div class=\"btn-group\">\n";

                    /*<a href="<?php echo RUTA_URL; ?>/tipos_educacion/edit/<?php echo $v->id_nivel_educacion; ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>*/

                    $tabla .= "<a href='" . RUTA_URL . "/periodos_lectivos/edit/" . $code . "' class=\"btn btn-warning item-edit\" data=\"" . $code . "\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";

                    $tabla .= "</div>\n";
                    $tabla .= "</td>\n";
                    $tabla .= "</tr>\n";
                }
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<td colspan=\"6\" align=\"center\">A&uacute;n no se han definido periodos lectivos...</td>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }
}
