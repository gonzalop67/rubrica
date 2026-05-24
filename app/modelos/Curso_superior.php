<?php
class Curso_superior
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerCursosSuperiores()
    {
        $this->db->query("SELECT *
                            FROM sw_curso_superior
                           ORDER BY id_curso_superior");
        return $this->db->registros();
    }
}