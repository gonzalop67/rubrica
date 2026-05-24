<?php
class Periodo_nivel
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerNivelesPeriodo($id)
    {
        $this->db->query("SELECT * FROM sw_periodo_nivel WHERE id_periodo_lectivo = $id");
        $registros = $this->db->registros();
        $array = array();
        foreach ($registros as $r) {
            array_push($array, $r->id_nivel_educacion);
        }
        return $array;
    }

    public function eliminarNivelesPeriodoLectivo($id_periodo_lectivo)
    {
        $this->db->query("DELETE FROM sw_periodo_nivel WHERE id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);

        $this->db->execute();
    }

    public function insertarNivelPeriodo($id_periodo_lectivo, $id_nivel_educacion)
    {
        $this->db->query("INSERT INTO sw_periodo_nivel (id_periodo_lectivo, id_nivel_educacion) VALUES (:id_periodo_lectivo, :id_nivel_educacion)");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
        $this->db->bind(':id_nivel_educacion', $id_nivel_educacion);

        $this->db->execute();
    }
}
