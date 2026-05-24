<?php
class Horario
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function existeAsociacion($id_paralelo, $id_dia_semana, $id_hora_clase)
    {
        $this->db->query("SELECT * FROM sw_horario WHERE id_paralelo = $id_paralelo AND id_dia_semana = $id_dia_semana AND id_hora_clase = $id_hora_clase");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeDocente($id_paralelo, $id_asignatura)
    {
        $this->db->query("SELECT id_usuario FROM sw_distributivo WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function comprobarCruce($id_paralelo, $id_asignatura, $id_dia_semana, $id_hora_clase)
    {
        //Primero obtengo el id_usuario de la hora clase en el horario
        $this->db->query("SELECT id_usuario FROM sw_distributivo WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");
        
        $usuario = $this->db->registro();
        $id_usuario = $usuario->id_usuario;

        //Ahora hay que verificar si existe otra hora dentro del horario para el mismo docente
		$this->db->query("SELECT id_horario FROM sw_horario WHERE id_dia_semana = $id_dia_semana AND id_hora_clase = $id_hora_clase AND id_usuario = $id_usuario");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function getIdUsuario($id_paralelo, $id_asignatura)
    {
        $this->db->query("SELECT id_usuario FROM sw_distributivo WHERE id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura");

        $usuario = $this->db->registro();
        return $usuario->id_usuario;
    }

    public function listarHorario($id_paralelo, $id_dia_semana)
    {
        $this->db->query("
            SELECT id_horario,
                   hc_nombre,
                   DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio, 
				   DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
                   CONCAT(us_titulo,' ',us_apellidos,' ',us_nombres) AS docente,  
                   as_nombre
              FROM sw_horario ho,
                   sw_hora_clase hc,
                   sw_asignatura a,
                   sw_usuario u
             WHERE hc.id_hora_clase = ho.id_hora_clase
               AND a.id_asignatura = ho.id_asignatura
               AND u.id_usuario = ho.id_usuario
               AND id_paralelo = $id_paralelo
               AND id_dia_semana = $id_dia_semana
             ORDER BY hc_ordinal
        ");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        if ($num_rows > 0) {
            foreach ($registros as $row) {
                $id = $row->id_horario;
                $hora = $row->hc_nombre . " (" . $row->hora_inicio . " - " . $row->hora_fin . ")";
                $asignatura = $row->as_nombre;
                $docente = $row->docente;
                $cadena .= "<tr>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/horarios/delete\" class=\"btn btn-danger btn-xs item-delete\" data=\"$id\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "<td>$hora</td>\n";
                $cadena .= "<td>$asignatura</td>\n";
                $cadena .= "<td>$docente</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='4' align='center'>No se han asociado horas clase para el paralelo y día de la semana elegidos...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_horario SET id_asignatura = :id_asignatura, id_paralelo = :id_paralelo, id_dia_semana = :id_dia_semana, id_hora_clase = :id_hora_clase, id_usuario = :id_usuario");

        // Vincular los valores
        $this->db->bind('id_asignatura', $datos['id_asignatura']);
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_dia_semana', $datos['id_dia_semana']);
        $this->db->bind('id_hora_clase', $datos['id_hora_clase']);
        $this->db->bind('id_usuario', $datos['id_usuario']);

        $this->db->execute();
    }

    public function eliminarAsociacion($id)
    {
        $this->db->query("DELETE FROM sw_horario WHERE id_horario = $id");

        $this->db->execute();
    }
}