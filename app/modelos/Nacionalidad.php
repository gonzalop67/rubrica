<?php
class Nacionalidad
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerNacionalidades()
    {
        $this->db->query("SELECT * FROM sw_def_nacionalidad ORDER BY id_def_nacionalidad");
        return $this->db->registros();
    }

    public function obtenerIdDefNacionalidad($dn_nombre)
    {
        $this->db->query("SELECT id_def_nacionalidad FROM sw_def_nacionalidad WHERE dn_nombre = '$dn_nombre'");
        return $this->db->registro()->id_def_nacionalidad;
    }
}