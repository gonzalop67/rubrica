<?php
class Tipo_aporte
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTiposAporteEvaluacion()
    {
        $this->db->query("SELECT *
                            FROM sw_tipo_aporte
                        ORDER BY id_tipo_aporte ASC");
        return $this->db->registros();
    }

}