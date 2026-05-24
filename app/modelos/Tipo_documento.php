<?php
class Tipo_documento
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTiposDocumento()
    {
        $this->db->query("SELECT * FROM sw_tipo_documento ORDER BY id_tipo_documento");
        return $this->db->registros();
    }

    public function obtenerIdTipoDocumento($td_nombre)
    {
        $this->db->query("SELECT id_tipo_documento FROM sw_tipo_documento WHERE td_nombre = '$td_nombre'");
        return $this->db->registro()->id_tipo_documento;
    }
}