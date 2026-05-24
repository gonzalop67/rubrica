<?php
class Asignatura
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function contarAsignaturasDocente($id_usuario, $id_periodo_lectivo)
    {
        $this->db->query("SELECT COUNT(*) AS num_registros 
                            FROM sw_distributivo di,
                                 sw_asignatura a 
                           WHERE a.id_asignatura = di.id_asignatura
                             AND id_usuario = $id_usuario 
                             AND id_periodo_lectivo = $id_periodo_lectivo 
                             AND as_curricular = 1");
        $registro = $this->db->registro();
        return $registro->num_registros;
    }

    public function paginarAsignaturasPorDocente($cantidad_registros, $total_registros, $paginaActual)
    {
        $nroPaginas = ceil($total_registros / $cantidad_registros);

        $lista = '';
        $tabla = '';

        if ($total_registros > 0) {
            if ($paginaActual == 1) {
                $lista = $lista . '<li><a href="javascript:;" disabled>‹‹</a></li>';
                $lista = $lista . '<li><a href="javascript:;" disabled>‹</a></li>';
            } else {
                $lista = $lista . '<li><a href="javascript:pagination(1);">‹‹</a></li>';
                $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual - 1) . ');">‹</a></li>';
            }
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
            $lista = $lista . '<li><a href="javascript:;" disabled>››</a></li>';
        } else {
            $lista = $lista . '<li><a href="javascript:pagination(' . ($paginaActual + 1) . ');">›</a></li>';
            $lista = $lista . '<li><a href="javascript:pagination(' . $nroPaginas . ');">››</a></li>';
        }

        if ($paginaActual <= 1) {
            $limit = 0;
        } else {
            $limit = $cantidad_registros * ($paginaActual - 1);
        }

        $id_usuario = $_SESSION['id_usuario'];
        $id_periodo_lectivo = $_SESSION['id_periodo_lectivo'];

        $this->db->query("SELECT c.id_curso, 
                                 d.id_paralelo, 
                                 d.id_asignatura, 
                                 as_nombre, 
                                 es_figura, 
                                 cu_nombre, 
                                 pa_nombre 
                            FROM sw_asignatura a, 
                                 sw_distributivo d, 
                                 sw_paralelo pa, 
                                 sw_curso c, 
                                 sw_especialidad e 
                           WHERE a.id_asignatura = d.id_asignatura 
                             AND d.id_paralelo = pa.id_paralelo 
                             AND pa.id_curso = c.id_curso 
                             AND c.id_especialidad = e.id_especialidad 
                             AND d.id_usuario = $id_usuario
                             AND d.id_periodo_lectivo = $id_periodo_lectivo
                             AND as_curricular = 1
                           ORDER BY c.id_curso, pa.id_paralelo, as_nombre ASC 
                           LIMIT $limit, $cantidad_registros");
        $consulta = $this->db->registros();
        $num_total_registros = $this->db->rowCount();

        if ($num_total_registros > 0) {
            $contador = $limit;
            foreach ($consulta as $v) {
                $contador++;
                $fondolinea = ($contador % 2 == 0) ? "itemParTabla" : "itemImparTabla";
                $tabla .= "<tr class=\"$fondolinea\" onmouseover=\"className='itemEncimaTabla'\" onmouseout=\"className='$fondolinea'\">\n";
                $id_curso = $v->id_curso;
                $codigo = $v->id_paralelo;
                $id_asignatura = $v->id_asignatura;
                $nombre = $v->as_nombre;
                $curso = $v->cu_nombre . " " . $v->es_figura;
                $paralelo = $v->pa_nombre;
                $tabla .= "<td>$contador</td>\n";
                $tabla .= "<td>$nombre</td>\n";
                $tabla .= "<td>$curso</td>\n";
                $tabla .= "<td>$paralelo</td>\n";
                $tabla .= "<td><a href=\"#\" class=\"btn btn-primary btn-sm\" onclick=\"seleccionarParalelo(" . $id_curso . "," . $codigo . "," . $id_asignatura . ",'" . $nombre . "','" . $curso . "','" . $paralelo . "')\" title=\"Seleccionar\"><span class=\"fa fa-check\"></span></a></td>\n";
                $tabla .= "</tr>\n";
            }
        } else {
            $tabla .= "<tr>\n";
            $tabla .= "<td>No se han asociado asignaturas a este docente...</td>\n";
            $tabla .= "</tr>\n";
        }

        $array = array(
            0 => $tabla,
            1 => $lista
        );

        return json_encode($array);
    }

    public function obtenerAsignatura($id)
    {
        $this->db->query("SELECT * FROM sw_asignatura WHERE id_asignatura = $id");
        return $this->db->registro();
    }

    public function obtenerAsignaturas()
    {
        $this->db->query("SELECT a.*, 
                                 ar_nombre, 
                                 ta_descripcion 
                            FROM sw_asignatura a,
                                 sw_area ar, 
                                 sw_tipo_asignatura ta  
                           WHERE ar.id_area = a.id_area 
                             AND ta.id_tipo_asignatura = a.id_tipo_asignatura 
                           ORDER BY ar_nombre, as_nombre");

        return $this->db->registros();
    }

    public function obtenerAsignaturasDocente($id_usuario, $id_periodo_lectivo)
    {
        $this->db->query("SELECT DISTINCT(a.id_asignatura),
                                 as_nombre
                            FROM sw_asignatura a,
                                 sw_distributivo d
                           WHERE a.id_asignatura = d.id_asignatura
                             AND d.id_usuario = $id_usuario
                             AND d.id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY as_nombre");

        return $this->db->registros();
    }

    public function existeNombreAsignatura($as_nombre)
    {
        $this->db->query("SELECT id_asignatura FROM sw_asignatura WHERE as_nombre = '$as_nombre'");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function buscarAsignatura($patron)
    {
        $this->db->query("SELECT a.*, 
                                 ar_nombre, 
                                 ta_descripcion 
                            FROM sw_asignatura a,
                                 sw_area ar, 
                                 sw_tipo_asignatura ta  
                           WHERE ar.id_area = a.id_area 
                             AND ta.id_tipo_asignatura = a.id_tipo_asignatura
                             AND (as_nombre LIKE '%" . $patron . "%' 
                              OR ar_nombre LIKE '%" . $patron . "%' 
                              OR as_abreviatura LIKE '%" . $patron . "%') 
                           ORDER BY ar_nombre, as_nombre");
        $asignaturas = $this->db->registros();
        $error = 0;
        if (count($asignaturas) == 0) {
            $error = 1;
        }
        $array = array(
            0 => json_encode($asignaturas),
            1 => $error
        );

        return json_encode($array);
    }

    public function insertarAsignatura($datos)
    {
        $this->db->query('INSERT INTO sw_asignatura (id_area, id_tipo_asignatura, as_nombre, as_abreviatura, as_shortname) VALUES (:id_area, :id_tipo_asignatura, :as_nombre, :as_abreviatura, :as_shortname)');

        //Vincular valores
        $this->db->bind(':id_area', $datos['id_area']);
        $this->db->bind(':id_tipo_asignatura', $datos['id_tipo_asignatura']);
        $this->db->bind(':as_nombre', $datos['as_nombre']);
        $this->db->bind(':as_abreviatura', $datos['as_abreviatura']);
        $this->db->bind(':as_shortname', $datos['as_shortname']);

        return $this->db->execute();
    }

    public function actualizarAsignatura($datos)
    {
        $this->db->query('UPDATE sw_asignatura SET id_area = :id_area, id_tipo_asignatura = :id_tipo_asignatura, as_nombre = :as_nombre, as_abreviatura = :as_abreviatura, as_shortname = :as_shortname WHERE id_asignatura = :id_asignatura');

        //Vincular valores
        $this->db->bind(':id_asignatura', $datos['id_asignatura']);
        $this->db->bind(':id_area', $datos['id_area']);
        $this->db->bind(':id_tipo_asignatura', $datos['id_tipo_asignatura']);
        $this->db->bind(':as_nombre', $datos['as_nombre']);
        $this->db->bind(':as_abreviatura', $datos['as_abreviatura']);
        $this->db->bind(':as_shortname', $datos['as_shortname']);

        return $this->db->execute();
    }

    public function eliminarAsignatura($id_asignatura)
    {
        $this->db->query('DELETE FROM sw_asignatura WHERE id_asignatura = :id_asignatura');

        //Vincular valores
        $this->db->bind(':id_asignatura', $id_asignatura);

        return $this->db->execute();
    }
}
