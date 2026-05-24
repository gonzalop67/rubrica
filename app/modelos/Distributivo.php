<?php
class Distributivo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarAsignaturasAsociadas($id_usuario, $id_periodo_lectivo)
    {
        $this->db->query("SELECT d.*,
                                 m.*, 
                                 pa_nombre,
                                 cu_abreviatura,
                                 es_abreviatura, 
                                 as_nombre,
                                 pa_orden,
                                 ac_orden 
                            FROM sw_distributivo d, 
                                 sw_malla_curricular m,
                                 sw_paralelo p, 
                                 sw_curso c,
                                 sw_especialidad e, 
                                 sw_asignatura_curso ac, 
                                 sw_asignatura a 
                           WHERE m.id_malla_curricular = d.id_malla_curricular
                             AND e.id_especialidad = c.id_especialidad
                             AND c.id_curso = p.id_curso 
                             AND p.id_paralelo = d.id_paralelo 
                             AND c.id_curso = ac.id_curso 
                             AND a.id_asignatura = d.id_asignatura 
                             AND d.id_asignatura = ac.id_asignatura 
                             AND d.id_usuario = $id_usuario 
                             AND d.id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY pa_orden, ac_orden");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        $suma_horas_presenciales = 0;
        $suma_horas_tutorias = 0;
        $suma_horas_totales = 0;
        if ($num_rows > 0) {
            foreach ($registros as $row) {
                $cadena .= "<tr>\n";
                $id = $row->id_distributivo;
                $paralelo = $row->cu_abreviatura . " " . $row->pa_nombre . " " . $row->es_abreviatura;
                $asignatura = $row->as_nombre;
                $presenciales = $row->ma_horas_presenciales;
                $autonomas = $row->ma_horas_autonomas;
                $tutorias = $row->ma_horas_tutorias;
                $suma_horas_presenciales = $suma_horas_presenciales + $presenciales;
                $suma_horas_tutorias = $suma_horas_tutorias + $tutorias;
                $suma_horas_totales = $suma_horas_totales + $presenciales + $tutorias;
                $subtotal = $presenciales + $tutorias;
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$paralelo</td>\n";
                $cadena .= "<td>$asignatura</td>\n";
                $cadena .= "<td>$presenciales</td>\n";
                $cadena .= "<td>$autonomas</td>\n";
                $cadena .= "<td>$tutorias</td>\n";
                $cadena .= "<td>$subtotal</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/distributivos/delete\" class=\"btn btn-danger btn-sm item-delete\" data=\"$id\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='8' align='center'>No se han definido items en el distributivo para este docente...</td>\n";
            $cadena .= "</tr>\n";
        }
        $datos = array(
            'cadena' => $cadena,
            'horas_presenciales' => $suma_horas_presenciales,
            'horas_tutorias' => $suma_horas_tutorias,
            'total_horas' => $suma_horas_totales
        );
        return json_encode($datos);
    }

    public function existeAsociacion($id_paralelo, $id_asignatura)
    {
        $this->db->query("SELECT * FROM sw_distributivo WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_distributivo SET id_periodo_lectivo = :id_periodo_lectivo, id_malla_curricular = :id_malla_curricular, id_paralelo = :id_paralelo, id_asignatura = :id_asignatura, id_usuario = :id_usuario");

        // Vincular valores
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind('id_malla_curricular', $datos['id_malla_curricular']);
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_asignatura', $datos['id_asignatura']);
        $this->db->bind('id_usuario', $datos['id_usuario']);

        return $this->db->execute();
    }

    public function eliminarAsociacion($id_distributivo)
    {
        $this->db->query("DELETE FROM sw_distributivo WHERE id_distributivo = $id_distributivo");
        return $this->db->execute();
    }
}
