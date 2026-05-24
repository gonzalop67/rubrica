<?php
class Hora_clase
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function listarHorasClase($id_periodo_lectivo)
    {
        $this->db->query("SELECT * 
                            FROM sw_hora_clase 
                           WHERE id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY hc_ordinal");
        $registros = $this->db->registros();
        $num_rows = $this->db->rowCount();
        $cadena = "";
        if ($num_rows > 0) {
            foreach ($registros as $row) {
                $id = $row->id_hora_clase;
                $nombre = $row->hc_nombre;
                $orden = $row->hc_ordinal;
                $hora_inicio = $row->hc_hora_inicio;
                $hora_fin = $row->hc_hora_fin;
                $cadena .= "<tr data-index='$id' data-orden='$orden'>\n";
                $cadena .= "<td>$id</td>\n";
                $cadena .= "<td>$nombre</td>\n";
                $cadena .= "<td>$hora_inicio</td>\n";
                $cadena .= "<td>$hora_fin</td>\n";
                $cadena .= "<td>$orden</td>\n";
                $cadena .= "<td>\n";
                $cadena .= "<div class=\"btn-group\">\n";
                $cadena .= "<a href=\"javascript:;\" class=\"btn btn-warning btn-sm item-edit\" data=\"$id\" title=\"Editar\"><span class=\"fa fa-pencil\"></span></a>\n";
                $cadena .= "<a href=\"" . RUTA_URL . "/horas_clase/delete\" class=\"btn btn-danger btn-sm item-delete\" data=\"$id\" title=\"Eliminar\"><span class=\"fa fa-remove\"></span></a>\n";
                $cadena .= "</div>\n";
                $cadena .= "</td>\n";
                $cadena .= "</tr>\n";
            }
        } else {
            $cadena .= "<tr>\n";
            $cadena .= "<td colspan='6' align='center'>No se han definido horas clase para este periodo lectivo...</td>\n";
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function obtenerHorasClasePorParalelo($id_paralelo)
    {
        $this->db->query("
            SELECT DISTINCT(hc.id_hora_clase)
              FROM sw_horario ho, 
                   sw_hora_clase hc
             WHERE ho.id_hora_clase = hc.id_hora_clase
               AND id_paralelo = $id_paralelo
             ORDER BY hc_ordinal
        ");
        $cadena = "";
        $horas_clase = $this->db->registros();
        foreach ($horas_clase as $hora) {
            $cadena .= "<tr>\n";
            $id_hora_clase = $hora->id_hora_clase;
            // Consulto el nombre de la hora clase
            $this->db->query("SELECT hc_nombre FROM sw_hora_clase WHERE id_hora_clase = $id_hora_clase");
            $hora_clase = $this->db->registro();
            $cadena .= "<td class='text-center'><span style='font-size: 12pt'><strong>$hora_clase->hc_nombre</strong></span></td>\n";
            // Acá obtengo los dias de la semana asociados al paralelo
            $this->db->query("
                SELECT DISTINCT(d.id_dia_semana) 
                  FROM sw_horario h,
                       sw_dia_semana d
                 WHERE d.id_dia_semana = h.id_dia_semana
                   AND id_paralelo = $id_paralelo
                 ORDER BY ds_ordinal
            ");
            $dias_semana = $this->db->registros();
            foreach ($dias_semana as $dia) {
                $id_dia_semana = $dia->id_dia_semana;
                // Consulto la asignatura del dia y hora correspondientes
                $this->db->query("
                        SELECT a.id_asignatura,
                               as_nombre
                          FROM sw_horario ho, 
                               sw_hora_clase hc, 
                               sw_asignatura a
                         WHERE ho.id_hora_clase = hc.id_hora_clase 
                           AND ho.id_asignatura = a.id_asignatura 
                           AND ho.id_dia_semana = $id_dia_semana 
                           AND ho.id_hora_clase = $id_hora_clase 
                           AND id_paralelo = $id_paralelo
                    ");
                $asignatura = $this->db->registro();
                if ($this->db->rowCount() > 0) {
                    $id_asignatura = $asignatura->id_asignatura;
                    $cadena .= "<td>\n";
                    $cadena .= "<p>$asignatura->as_nombre</p>\n";
                    // Obtengo el docente que imparte esta asignatura
                    $this->db->query("
                        SELECT us_shortname 
                          FROM sw_asignatura a, 
                               sw_distributivo d,
                               sw_usuario u
                         WHERE a.id_asignatura = d.id_asignatura
                           AND u.id_usuario = d.id_usuario
                           AND d.id_asignatura = $id_asignatura
                           AND d.id_paralelo = $id_paralelo
                    ");
                    $docente = $this->db->registro();
                    $cadena .= "<p><em>$docente->us_shortname</em></p>\n";
                    $cadena .= "</td>\n";
                } else {
                    $cadena .= "<td>&nbsp;</td>\n";
                }
            }
            $cadena .= "</tr>\n";
        }
        return $cadena;
    }

    public function obtenerHoraClase($id_hora_clase)
    {
        $this->db->query("SELECT id_hora_clase,
                                 hc_nombre,
                                 DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hora_inicio,
                                 DATE_FORMAT(hc_hora_fin,'%H:%i') AS hora_fin,
                                 hc_ordinal 
                            FROM sw_hora_clase
                           WHERE id_hora_clase = $id_hora_clase");

        return $this->db->registro();
    }

    public function obtenerHorasClase($id_periodo_lectivo)
    {
        $this->db->query("SELECT id_hora_clase,
                                 hc_nombre,
                                 DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hc_hora_inicio,
                                 DATE_FORMAT(hc_hora_fin,'%H:%i') AS hc_hora_fin,
                                 hc_ordinal 
                            FROM sw_hora_clase
                           WHERE id_periodo_lectivo = $id_periodo_lectivo");

        return $this->db->registros();
    }

    public function existeNombreHoraClase($hc_nombre, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_hora_clase WHERE hc_nombre = '$hc_nombre' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarHoraClase($datos)
    {
        // Obtener el secuencial del dia de la semana del periodo lectivo correspondiente
        $id_periodo_lectivo = $datos['id_periodo_lectivo'];

        $this->db->query("SELECT MAX(hc_ordinal) AS maximo FROM sw_hora_clase WHERE id_periodo_lectivo = $id_periodo_lectivo");
        $record = $this->db->registro();

        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query("INSERT INTO sw_hora_clase SET id_periodo_lectivo = :id_periodo_lectivo, hc_nombre = :hc_nombre, hc_hora_inicio = :hc_hora_inicio, hc_hora_fin = :hc_hora_fin, hc_ordinal = :hc_ordinal");

        // Vincular los datos
        $this->db->bind('id_periodo_lectivo', $id_periodo_lectivo);
        $this->db->bind('hc_nombre', $datos['hc_nombre']);
        $this->db->bind('hc_hora_inicio', $datos['hc_hora_inicio']);
        $this->db->bind('hc_hora_fin', $datos['hc_hora_fin']);
        $this->db->bind('hc_ordinal', $maximo_orden);

        $this->db->execute();
    }

    public function actualizarHoraClase($datos)
    {
        $this->db->query("UPDATE sw_hora_clase SET hc_nombre = :hc_nombre, hc_hora_inicio = :hc_hora_inicio, hc_hora_fin = :hc_hora_fin WHERE id_hora_clase = :id_hora_clase");

        // Vincular los datos
        $this->db->bind('id_hora_clase', $datos['id_hora_clase']);
        $this->db->bind('hc_nombre', $datos['hc_nombre']);
        $this->db->bind('hc_hora_inicio', $datos['hc_hora_inicio']);
        $this->db->bind('hc_hora_fin', $datos['hc_hora_fin']);

        $this->db->execute();
    }

    public function eliminarHoraClase($id_hora_clase)
    {
        $this->db->query("DELETE FROM sw_hora_clase WHERE id_hora_clase = :id_hora_clase");

        // Vincular los datos
        $this->db->bind('id_hora_clase', $id_hora_clase);

        $this->db->execute();
    }

    public function actualizarOrden($id_hora_clase, $hc_ordinal)
    {
        $this->db->query('UPDATE `sw_hora_clase` SET `hc_ordinal` = :hc_ordinal WHERE `id_hora_clase` = :id_hora_clase');

        //Vincular valores
        $this->db->bind(':id_hora_clase', $id_hora_clase);
        $this->db->bind(':hc_ordinal', $hc_ordinal);

        echo $this->db->execute();
    }
}