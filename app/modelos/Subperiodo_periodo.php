<?php
class Subperiodo_periodo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerSubPeriodosPeriodo($id)
    {
        $this->db->query("SELECT * FROM sw_periodo_lectivo_sub_periodo WHERE id_periodo_lectivo = $id");
        $registros = $this->db->registros();
        $array = array();
        foreach ($registros as $r) {
            array_push($array, $r->id_sub_periodo_evaluacion);
        }
        return $array;
    }

    public function eliminarSubPeriodosPeriodoLectivo($id_periodo_lectivo)
    {
        $this->db->query("DELETE FROM sw_periodo_lectivo_sub_periodo WHERE id_periodo_lectivo = :id_periodo_lectivo");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);

        $this->db->execute();
    }

    public function insertarSubPeriodoPeriodo($id_periodo_lectivo, $id_sub_periodo_evaluacion)
    {
        $this->db->query("INSERT INTO sw_periodo_lectivo_sub_periodo (id_periodo_lectivo, id_sub_periodo_evaluacion) VALUES (:id_periodo_lectivo, :id_sub_periodo_evaluacion)");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
        $this->db->bind(':id_sub_periodo_evaluacion', $id_sub_periodo_evaluacion);

        $this->db->execute();
    }
}