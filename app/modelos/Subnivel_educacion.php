<?php
class Subnivel_educacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM sw_sub_nivel_educacion ORDER BY orden ASC");
        return $this->db->registros();
    }

    public function obtener($id)
    {
        $this->db->query("SELECT * FROM sw_sub_nivel_educacion WHERE id = $id");
        return $this->db->registro();
    }

    public function existeNombre($nombre)
    {
        $this->db->query("SELECT * FROM sw_sub_nivel_educacion WHERE nombre = '$nombre'");
        $this->db->registro();

        return $this->db->rowCount();
    }

    public function insertar($datos)
    {
        $this->db->query(
            "INSERT INTO sw_sub_nivel_educacion (
                            institucion_id, 
                            nombre, 
                            es_bachillerato) VALUES (
                            :institucion_id, 
                            :nombre, 
                            :es_bachillerato)"
        );

        //Vincular valores
        $this->db->bind(':institucion_id', $datos['institucion_id']);
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':es_bachillerato', $datos['es_bachillerato']);

        $this->db->execute();
    }

    public function actualizar($datos)
    {
        $this->db->query(
            "UPDATE sw_sub_nivel_educacion SET
                    nombre = :nombre, 
                    es_bachillerato = :es_bachillerato
              WHERE id = :id"
        );

        //Vincular valores
        $this->db->bind(':id', $datos['id']);
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':es_bachillerato', $datos['es_bachillerato']);

        $this->db->execute();
    }

    public function eliminar($id)
    {
        $this->db->query('DELETE FROM `sw_sub_nivel_educacion` WHERE `id` = :id');

        //Vincular valores
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }
}
