<?php
class Tipo_periodo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM sw_tipo_periodo ORDER BY id_tipo_periodo ASC");
        return $this->db->registros();
    }

}