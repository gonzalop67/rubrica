<?php
class Asociar_curso_superior
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarCursosSuperiores($id_periodo_lectivo)
    {
        $this->db->query("SELECT cs.*
                            FROM sw_asociar_curso_superior cs,
                                 sw_curso c, 
                                 sw_especialidad e 
                           WHERE c.id_curso = cs.id_curso_inferior
                             AND e.id_especialidad = c.id_especialidad
                             AND cs.id_periodo_lectivo = $id_periodo_lectivo 
                           ORDER BY c.id_especialidad, id_curso");
        $rows = $this->db->registros();
        $num_rows = count($rows);

        $cadena = "";

        if ($num_rows > 0) {
            foreach ($rows as $row) {
                $cadena .= "<tr>\n";
                $id = $row->id_asociar_curso_superior;
                $id_curso_inferior = $row->id_curso_inferior;
                $id_curso_superior = $row->id_curso_superior;
                //Consultar los nombres de los cursos inferior y superior
                $this->db->query("SELECT es_figura, 
                                         cu_nombre 
                                    FROM sw_curso c, 
                                         sw_especialidad e, 
                                         sw_tipo_educacion t 
                                   WHERE c.id_especialidad = e.id_especialidad 
                                     AND e.id_tipo_educacion = t.id_tipo_educacion 
                                     AND c.id_curso = $id_curso_inferior");
                $record = $this->db->registro();
                $curso_inferior = "[" . $record->es_figura . "] " . $record->cu_nombre;
                $consulta = $this->db->query("
                    SELECT cs_nombre 
                      FROM sw_curso_superior 
                     WHERE id_curso_superior = $id_curso_superior
                ");
                $record = $this->db->registro();
                $curso_superior = $record->cs_nombre;
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$curso_inferior</td>\n";
                $cadena .= "<td>$curso_superior</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/cursos_superiores/delete/" . $id . "\" class=\"btn btn-danger btn-sm item-delete\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='4' align='center'>No se han asociado cursos superiores todavía...</td>\n";
            $cadena .= "</tr>\n";
        }

        return $cadena;
    }

    public function existeAsociacion($id_curso_inferior, $id_curso_superior)
    {
        $this->db->query("SELECT * FROM sw_asociar_curso_superior WHERE id_curso_inferior = $id_curso_inferior AND id_curso_superior = $id_curso_superior");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_asociar_curso_superior SET id_curso_inferior = :id_curso_inferior, id_curso_superior = :id_curso_superior, id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular los valores
        $this->db->bind('id_curso_inferior', $datos['id_curso_inferior']);
        $this->db->bind('id_curso_superior', $datos['id_curso_superior']);
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id)
    {
        $this->db->query("DELETE FROM sw_asociar_curso_superior WHERE id_asociar_curso_superior = :id");

        //Vincular los valores
        $this->db->bind('id', $id);

        $this->db->execute();
    }
}
