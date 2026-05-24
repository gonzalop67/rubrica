<?php
class Def_nacionalidad
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerDefNacionalidades()
    {
        $this->db->query("SELECT * FROM sw_def_nacionalidad ORDER BY id_def_nacionalidad");
        return $this->db->registros();
    }

}