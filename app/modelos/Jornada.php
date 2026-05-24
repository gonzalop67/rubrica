<?php
class Jornada
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerJornada($id_jornada)
    {
        $this->db->query("SELECT * FROM sw_jornada WHERE id_jornada = $id_jornada");
        return $this->db->registro();
    }

    public function obtenerJornadas()
    {
        $this->db->query("SELECT * FROM `sw_jornada` ORDER BY `id_jornada` ASC");
        return $this->db->registros();
    }

    public function obtenerNombreJornada($id_paralelo)
    {
        $this->db->query("SELECT jo_nombre
                            FROM sw_jornada j, 
                                 sw_paralelo p
                           WHERE j.id_jornada = p.id_jornada 
                             AND id_paralelo = $id_paralelo");
        $registro = $this->db->registro();
        return $registro->jo_nombre;
    }

    public function obtenerJornadasPorPeriodoLectivo($id_periodo_lectivo)
    {
        $this->db->query("SELECT DISTINCT(id_jornada) AS id_jornada FROM sw_paralelo WHERE id_periodo_lectivo = $id_periodo_lectivo ORDER BY id_jornada ASC");
        return $this->db->registros();
    }

}