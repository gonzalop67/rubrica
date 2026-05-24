<?php
class Genero
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerGeneros()
    {
        $this->db->query("SELECT * FROM sw_def_genero ORDER BY id_def_genero");
        return $this->db->registros();
    }

    public function obtenerIdDefGenero($dg_nombre)
    {
        $this->db->query("SELECT id_def_genero FROM sw_def_genero WHERE dg_nombre = '$dg_nombre'");
        return $this->db->registro()->id_def_genero;
    }
}