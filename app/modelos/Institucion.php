<?php
class Institucion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    function obtenerDatosInstitucion()
    {
        $this->db->query("SELECT * FROM sw_institucion where id_institucion = 1");
        return $this->db->registro();
    }

    public function obtenerNombreInstitucion()
    {
        $this->db->query("SELECT in_nombre FROM sw_institucion WHERE id_institucion = 1");
        return $this->db->registro()->in_nombre;
    }

    public function obtenerUrlInstitucion()
    {
        $this->db->query("SELECT in_url FROM sw_institucion WHERE id_institucion = 1");
        return $this->db->registro()->in_url;
    }

    public function obtener_copiar_y_pegar()
    {
        $this->db->query("SELECT in_copiar_y_pegar FROM sw_institucion WHERE id_institucion = 1");
        return $this->db->registro()->in_copiar_y_pegar;
    }

    public function actualizarInstitucion($datos)
    {
        $this->db->query('UPDATE sw_institucion SET in_nombre = :in_nombre, in_direccion = :in_direccion, in_telefono = :in_telefono, in_regimen = :in_regimen, in_nom_rector = :in_nom_rector, in_genero_rector = :in_genero_rector, in_nom_vicerrector = :in_nom_vicerrector, in_nom_secretario = :in_nom_secretario, in_genero_secretario = :in_genero_secretario, in_url = :in_url, in_amie = :in_amie, in_ciudad = :in_ciudad, in_logo = :in_logo WHERE id_institucion = 1');

        //Vincular valores
        $this->db->bind(':in_nombre', $datos['in_nombre']);
        $this->db->bind(':in_direccion', $datos['in_direccion']);
        $this->db->bind(':in_telefono', $datos['in_telefono']);
        $this->db->bind(':in_regimen', $datos['in_regimen']);
        $this->db->bind(':in_nom_rector', $datos['in_nom_rector']);
        $this->db->bind(':in_genero_rector', $datos['in_genero_rector']);
        $this->db->bind(':in_nom_vicerrector', $datos['in_nom_vicerrector']);
        $this->db->bind(':in_nom_secretario', $datos['in_nom_secretario']);
        $this->db->bind(':in_genero_secretario', $datos['in_genero_secretario']);
        $this->db->bind(':in_url', $datos['in_url']);
        $this->db->bind(':in_amie', $datos['in_amie']);
        $this->db->bind(':in_ciudad', $datos['in_ciudad']);
        $this->db->bind(':in_logo', $datos['in_logo']);

        //Ejecutar
        try {
            $this->db->execute();
            echo "Actualizaci&oacute;n realizada exitosamente.";
        } catch (PDOException $e) {
            echo "No se pudo realizar la actualizaci&oacute;n. Error: " . $e->getMessage();
        }
    }

    public function actualizar_copiar_y_pegar($in_copiar_y_pegar)
    {
        $this->db->query('UPDATE sw_institucion SET in_copiar_y_pegar = :in_copiar_y_pegar WHERE id_institucion = 1');
        //Vincular valores
        $this->db->bind(':in_copiar_y_pegar', $in_copiar_y_pegar);
        $this->db->execute();
    }
}
