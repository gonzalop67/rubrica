<?php
class Tipo_asignatura
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTiposAsignatura()
    {
        $this->db->query("SELECT * FROM sw_tipo_asignatura ORDER BY id_tipo_asignatura");
        return $this->db->registros();
    }
}
