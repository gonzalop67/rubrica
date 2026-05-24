<?php
class Tipo_educacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerNivelEducacion($id)
    {
        $this->db->query("SELECT * FROM sw_sub_nivel_educacion WHERE id_nivel_educacion = $id");
        return $this->db->registro();
    }

    public function obtenerNivelesEducacion()
    {
        $this->db->query("
            SELECT *
              FROM sw_sub_nivel_educacion 
             ORDER BY orden
            ");
        return $this->db->registros();
    }

    public function obtenerNombreNivelEducacion($id_paralelo)
    {
        $this->db->query("SELECT te_nombre, 
                                 te_bachillerato 
                            FROM sw_tipo_educacion t, 
                                 sw_especialidad e, 
                                 sw_curso c, 
                                 sw_paralelo p 
                           WHERE t.id_tipo_educacion = e.id_tipo_educacion 
                             AND e.id_especialidad = c.id_especialidad 
                             AND c.id_curso = p.id_curso 
                             AND id_paralelo = $id_paralelo");
        return $this->db->registro()->te_nombre;
    }

    public function existeNombreNivelDeEducacion($te_nombre)
    {
        $this->db->query("SELECT id_tipo_educacion FROM sw_tipo_educacion WHERE te_nombre = '$te_nombre'");
        $nivelEducacion = $this->db->registro();
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarNivelEducacion($datos)
    {
        //Obtener el máximo te_orden
        $this->db->query("SELECT MAX(te_orden) AS maximo FROM sw_tipo_educacion");
        $registro = $this->db->registro();
        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $registro->maximo + 1;

        $this->db->query('INSERT INTO sw_tipo_educacion (id_periodo_lectivo, te_nombre, te_bachillerato, te_orden) VALUES (:id_periodo_lectivo, :te_nombre, :te_bachillerato, :te_orden)');

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $datos['id_periodo_lectivo']);
        $this->db->bind(':te_nombre', $datos['te_nombre']);
        $this->db->bind(':te_bachillerato', $datos['te_bachillerato']);
        $this->db->bind(':te_orden', $maximo_orden);

        return $this->db->execute();
    }

    public function actualizarNivelEducacion($datos)
    {
        $this->db->query('UPDATE sw_sub_nivel_educacion SET nombre = :nombre, es_bachillerato = :es_bachillerato WHERE id_nivel_educacion = :id_nivel_educacion');

        //Vincular valores
        $this->db->bind(':id_nivel_educacion', $datos['id_nivel_educacion']);
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':es_bachillerato', $datos['es_bachillerato']);

        return $this->db->execute();
    }

    public function eliminarNivelEducacion($id)
    {
		$this->db->query('DELETE FROM `sw_tipo_educacion` WHERE `id_tipo_educacion` = :id_tipo_educacion');
        
        //Vincular valores
        $this->db->bind(':id_tipo_educacion', $id);

		return $this->db->execute();
	}
}