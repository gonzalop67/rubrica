<?php
class Hora_dia
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerHorasClase($id_dia_semana)
    {
        $this->db->query("
            SELECT id_hora_dia,
                   hc.id_hora_clase,
                   ds_nombre, 
                   hc_nombre,
                   DATE_FORMAT(hc_hora_inicio,'%H:%i') AS hc_hora_inicio,
                   DATE_FORMAT(hc_hora_fin,'%H:%i') AS hc_hora_fin
              FROM sw_hora_dia hd,
                   sw_dia_semana ds,
                   sw_hora_clase hc
             WHERE ds.id_dia_semana = hd.id_dia_semana
               AND hc.id_hora_clase = hd.id_hora_clase
               AND hd.id_dia_semana =  $id_dia_semana
             ORDER BY hc_ordinal
        ");

        return $this->db->registros();
    }

    public function existeAsociacion($id_dia_semana, $id_hora_clase)
    {
        $this->db->query("SELECT * FROM sw_hora_dia WHERE id_dia_semana = $id_dia_semana AND id_hora_clase = $id_hora_clase");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_hora_dia SET id_dia_semana = :id_dia_semana, id_hora_clase = :id_hora_clase");

        // Vincular los datos
        $this->db->bind('id_dia_semana', $datos['id_dia_semana']);
        $this->db->bind('id_hora_clase', $datos['id_hora_clase']);

        return $this->db->execute();
    }

    public function eliminarAsociacion($id_hora_dia)
    {
        $this->db->query("DELETE FROM sw_hora_dia WHERE id_hora_dia = $id_hora_dia");

        return $this->db->execute();
    }

}