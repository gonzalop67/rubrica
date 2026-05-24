<?php
class Dia_semana
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerDiaSemana($id_dia_semana)
    {
        $this->db->query("SELECT * FROM sw_dia_semana WHERE id_dia_semana = $id_dia_semana");

        return $this->db->registro();
    }

    public function obtenerDiasSemana($id_periodo_lectivo)
    {
        $this->db->query("SELECT * 
                            FROM sw_dia_semana 
                           WHERE id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY ds_ordinal");

        return $this->db->registros();
    }

    public function listarDiasSemana($id_periodo_lectivo)
    {
        $this->db->query("SELECT * 
                            FROM sw_dia_semana 
                           WHERE id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY ds_ordinal");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        if ($num_rows > 0) {
            foreach ($registros as $row) {
                $id = $row->id_dia_semana;
                $nombre = $row->ds_nombre;
                $orden = $row->ds_ordinal;
                $cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$nombre</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning btn-sm item-edit\" data=\"$id\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/dias_semana/delete\" class=\"btn btn-danger btn-sm item-delete\" data=\"$id\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='3' align='center'>No se han definido días de la semana para este periodo lectivo...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function obtenerDiasSemanaPorParalelo($id_paralelo)
    {
        $this->db->query("
            SELECT DISTINCT(d.id_dia_semana) 
              FROM sw_horario h,
                   sw_dia_semana d
             WHERE d.id_dia_semana = h.id_dia_semana
               AND id_paralelo = $id_paralelo
             ORDER BY ds_ordinal
        ");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        if ($num_rows > 0) {
            $cadena = "<tr>\n";
            $cadena .= "<th>HORA</th>\n";
            foreach ($registros as $row) {
                $id_dia_semana = $row->id_dia_semana;
                $this->db->query("SELECT ds_nombre FROM sw_dia_semana WHERE id_dia_semana = $id_dia_semana");
                $nombre_dia = $this->db->registro();
                $cadena .= "<th>$nombre_dia->ds_nombre</th>\n";
            }
            $cadena .= "</tr>\n";
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<th align='center'>No se han asociado Dias de la semana a este Paralelo...</th>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function existeNombreDiaSemana($ds_nombre, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_dia_semana WHERE ds_nombre = '$ds_nombre' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarDiaSemana($datos)
    {
        // Obtener el secuencial del dia de la semana del periodo lectivo correspondiente
        $id_periodo_lectivo = $datos['id_periodo_lectivo'];
        $ds_nombre = strtoupper($_POST['ds_nombre']);

        $this->db->query("SELECT MAX(ds_ordinal) AS maximo FROM sw_dia_semana WHERE id_periodo_lectivo = $id_periodo_lectivo");
        $record = $this->db->registro();

        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query("INSERT INTO sw_dia_semana SET id_periodo_lectivo = :id_periodo_lectivo, ds_nombre = :ds_nombre, ds_ordinal = :ds_ordinal");

        // Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
        $this->db->bind(':ds_nombre', $ds_nombre);
        $this->db->bind(':ds_ordinal', $maximo_orden);

        $this->db->execute();

        // Asociar el nuevo dia_semana con las horas_clase definidas en la tabla sw_hora_dia
        $this->db->query("SELECT MAX(id_dia_semana) AS max_id FROM sw_dia_semana");
        $record = $this->db->registro();

        $id_dia_semana = $record->max_id;

        $this->db->query("SELECT id_hora_clase FROM sw_hora_clase WHERE id_periodo_lectivo = :id_periodo_lectivo");
        // Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);

        $registros = $this->db->registros();

        foreach ($registros as $row) {
            $id_hora_clase = $row->id_hora_clase;
            $this->db->query("INSERT INTO sw_hora_dia SET id_dia_semana = :id_dia_semana, id_hora_clase = :id_hora_clase");

            // Vincular valores
            $this->db->bind(':id_dia_semana', $id_dia_semana);
            $this->db->bind(':id_hora_clase', $id_hora_clase);

            $this->db->execute();
        }
    }

    public function actualizarDiaSemana($datos)
    {
        $this->db->query("UPDATE sw_dia_semana SET ds_nombre = :ds_nombre WHERE id_dia_semana = :id_dia_semana");

        // Vincular valores
        $this->db->bind(':id_dia_semana', $datos['id_dia_semana']);
        $this->db->bind(':ds_nombre', $datos['ds_nombre']);

        $this->db->execute();
    }

    public function eliminarDiaSemana($id_dia_semana)
    {
        $this->db->query("DELETE FROM sw_dia_semana WHERE id_dia_semana = :id_dia_semana");

        // Vincular valores
        $this->db->bind(':id_dia_semana', $id_dia_semana);

        $this->db->execute();
    }

    public function actualizarOrden($id_dia_semana, $ds_ordinal)
    {
        $this->db->query('UPDATE `sw_dia_semana` SET `ds_ordinal` = :ds_ordinal WHERE `id_dia_semana` = :id_dia_semana');

        //Vincular valores
        $this->db->bind(':id_dia_semana', $id_dia_semana);
        $this->db->bind(':ds_ordinal', $ds_ordinal);

        echo $this->db->execute();
    }
}
