<?php
class Area
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerArea($id_area)
    {
        $this->db->query("SELECT * FROM sw_area WHERE id_area = $id_area");
        return $this->db->registro();
    }

    public function obtenerAreas()
    {
        $this->db->query("SELECT * FROM sw_area ORDER BY ar_nombre");
        return $this->db->registros();
    }

    public function existeNombreArea($ar_nombre)
    {
        $this->db->query("SELECT id_area FROM sw_area WHERE ar_nombre = '$ar_nombre'");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarArea($datos)
    {
        $this->db->query('INSERT INTO sw_area (ar_nombre) VALUES (:ar_nombre)');

        //Vincular valores
        $this->db->bind(':ar_nombre', $datos['ar_nombre']);

        return $this->db->execute();
    }

    public function actualizarArea($datos)
    {
        $this->db->query('UPDATE sw_area SET ar_nombre = :ar_nombre WHERE id_area = :id_area');

        //Vincular valores
        $this->db->bind(':id_area', $datos['id_area']);
        $this->db->bind(':ar_nombre', $datos['ar_nombre']);

        return $this->db->execute();
    }

    public function eliminarArea($id_area)
    {
        $this->db->query('DELETE FROM sw_area WHERE id_area = :id_area');

        //Vincular valores
        $this->db->bind(':id_area', $id_area);

        return $this->db->execute();
    }
}