<?php
class Categoria
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtener($id_categoria)
    {
        $this->db->query("SELECT * FROM sw_categoria WHERE id_categoria = $id_categoria");
        return $this->db->registro();
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM sw_categoria ORDER BY nombre");
        return $this->db->registros();
    }

    public function existeNombre($nombre)
    {
        $this->db->query("SELECT * FROM sw_categoria WHERE nombre = '$nombre'");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertar($datos)
    {
        $this->db->query('INSERT INTO sw_categoria (nombre) VALUES (:nombre)');

        //Vincular valores
        $this->db->bind(':nombre', $datos['nombre']);

        return $this->db->execute();
    }

    public function actualizar($datos)
    {
        $this->db->query('UPDATE sw_categoria SET nombre = :nombre WHERE id_categoria = :id_categoria');

        //Vincular valores
        $this->db->bind(':id_categoria', $datos['id_categoria']);
        $this->db->bind(':nombre', $datos['nombre']);

        return $this->db->execute();
    }

    public function eliminar($id_categoria)
    {
        $this->db->query('DELETE FROM sw_categoria WHERE id_categoria = :id_categoria');

        //Vincular valores
        $this->db->bind(':id_categoria', $id_categoria);

        return $this->db->execute();
    }
}