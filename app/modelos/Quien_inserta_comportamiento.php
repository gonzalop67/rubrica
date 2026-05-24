<?php
class Quien_inserta_comportamiento
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM sw_quien_inserta_comp ORDER BY id ASC");

        return $this->db->registros();
    }
}