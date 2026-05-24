<?php
class Subperiodo_evaluacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM sw_sub_periodo_evaluacion ORDER BY pe_orden ASC");
        return $this->db->registros();
    }

}