<?php
class Def_genero
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerDefGeneros()
    {
        $this->db->query("SELECT * FROM sw_def_genero ORDER BY dg_nombre");
        return $this->db->registros();
    }

}