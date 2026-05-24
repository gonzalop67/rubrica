<?php
class Malla_curricular
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerItemMalla($id)
    {
        $this->db->query("SELECT * FROM sw_malla_curricular WHERE id_malla_curricular = $id");
        return $this->db->registro();
    }

    public function listarAsignaturasAsociadas($id_curso)
    {
        $this->db->query("
            SELECT m.*, 
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
               AND m.id_curso = $id_curso
             ORDER BY ac_orden
        ");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        $suma_horas = 0;
        if ($num_rows > 0) {
            foreach ($registros as $row) {
                $cadena .= "<tr>\n";
                $id = $row->id_malla_curricular;
                $presenciales = $row->ma_horas_presenciales;
                $autonomas = $row->ma_horas_autonomas;
                $tutorias = $row->ma_horas_tutorias;
                $suma_horas = $suma_horas + $presenciales + $tutorias;
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>" . $row->as_nombre . "</td>\n";
                $cadena .= "<td>" . $row->cu_nombre . "</td>\n";
                $cadena .= "<td>$presenciales</td>\n";
                $cadena .= "<td>$autonomas</td>\n";
                $cadena .= "<td>$tutorias</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning btn-sm item-edit\" data=\"$id\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/mallas_curriculares/delete\" class=\"btn btn-danger btn-sm item-delete\" data=\"$id\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='7' align='center'>No se han definido items asociados a este curso...</td>\n";
            $cadena .= "</tr>\n";
        }
        $datos = array(
            'cadena' => $cadena,
            'total_horas' => $suma_horas
        );
        return json_encode($datos);
    }

    public function existeAsociacion($id_curso, $id_asignatura)
    {
        $this->db->query("SELECT * FROM sw_malla_curricular WHERE id_curso = $id_curso AND id_asignatura = $id_asignatura");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function getMallaIdCursoAsignatura($id_curso, $id_asignatura)
    {
        $this->db->query("SELECT id_malla_curricular 
                            FROM sw_malla_curricular
                           WHERE id_curso = $id_curso 
                             AND id_asignatura = $id_asignatura");
        
        return $this->db->registro()->id_malla_curricular;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_malla_curricular SET id_periodo_lectivo = :id_periodo_lectivo, id_curso = :id_curso, id_asignatura = :id_asignatura, ma_horas_presenciales = :ma_horas_presenciales, ma_horas_autonomas = :ma_horas_autonomas, ma_horas_tutorias = :ma_horas_tutorias, ma_subtotal = :ma_subtotal");

        //Vincular los valores
        $this->db->bind('id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind('id_curso', $datos['id_curso']);
        $this->db->bind('id_asignatura', $datos['id_asignatura']);
        $this->db->bind('ma_horas_presenciales', $datos['ma_horas_presenciales']);
        $this->db->bind('ma_horas_autonomas', $datos['ma_horas_autonomas']);
        $this->db->bind('ma_horas_tutorias', $datos['ma_horas_tutorias']);
        $this->db->bind('ma_subtotal', $datos['ma_subtotal']);

        $this->db->execute();
    }

    public function actualizarAsociacion($datos)
    {
        $this->db->query("UPDATE sw_malla_curricular SET ma_horas_presenciales = :ma_horas_presenciales, ma_horas_autonomas = :ma_horas_autonomas, ma_horas_tutorias = :ma_horas_tutorias, ma_subtotal = :ma_subtotal WHERE id_malla_curricular = :id_malla_curricular");

        //Vincular los valores
        $this->db->bind('id_malla_curricular', $datos['id_malla_curricular']);
        $this->db->bind('ma_horas_presenciales', $datos['ma_horas_presenciales']);
        $this->db->bind('ma_horas_autonomas', $datos['ma_horas_autonomas']);
        $this->db->bind('ma_horas_tutorias', $datos['ma_horas_tutorias']);
        $this->db->bind('ma_subtotal', $datos['ma_subtotal']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id_malla_curricular)
    {
        $this->db->query("DELETE FROM sw_malla_curricular WHERE id_malla_curricular = $id_malla_curricular");
        return $this->db->execute();
    }
}
