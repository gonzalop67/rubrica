<?php
class Especialidad
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerEspecialidad($id)
    {
        $this->db->query("SELECT * FROM sw_especialidad WHERE id_especialidad = $id");
        return $this->db->registro();
    }

    public function obtenerEspecialidades()
    {
        $this->db->query("SELECT *
                            FROM sw_especialidad  
                           ORDER BY es_orden");
        return $this->db->registros();
    }

    public function obtenerEspecialidadesPorPeriodoId($id_periodo_lectivo)
    {
        $this->db->query("SELECT id_especialidad, 
                                 es_figura 
                            FROM sw_especialidad e, 
                                 sw_periodo_nivel pn 
                           WHERE e.id_nivel_educacion = pn.id_nivel_educacion 
                             AND id_periodo_lectivo = $id_periodo_lectivo 
                        ORDER BY es_orden ASC");
        return $this->db->registros();
    }

    public function existeNombreEspecialidad($es_nombre, $id_tipo_educacion)
    {
        $this->db->query("SELECT id_especialidad FROM sw_especialidad WHERE es_nombre = '$es_nombre' AND id_tipo_educacion = $id_tipo_educacion");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function existeFiguraEspecialidad($es_figura)
    {
        $this->db->query("SELECT id_especialidad FROM sw_especialidad WHERE es_figura = '$es_figura'");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function existeAbreviaturaEspecialidad($es_abreviatura)
    {
        $this->db->query("SELECT id_especialidad FROM sw_especialidad WHERE es_abreviatura = '$es_abreviatura'");
        $this->db->registro();

        return $this->db->rowCount() > 0;
    }

    public function insertarEspecialidad($datos)
    {
        //Obtener el máximo es_orden
        $this->db->query("SELECT MAX(es_orden) AS maximo FROM sw_especialidad");
        $record = $this->db->registro();
        $maximo_orden = ($this->db->rowCount() == 0) ? 1 : $record->maximo + 1;

        $this->db->query('INSERT INTO sw_especialidad (es_nombre, es_figura, es_abreviatura, es_orden) VALUES (:es_nombre, :es_figura, :es_abreviatura, :es_orden)');

        //Vincular valores
        $this->db->bind(':es_nombre', $datos['es_nombre']);
        $this->db->bind(':es_figura', $datos['es_figura']);
        $this->db->bind(':es_abreviatura', $datos['es_abreviatura']);
        $this->db->bind(':es_orden', $maximo_orden);

        return $this->db->execute();
    }

    public function actualizarEspecialidad($datos)
    {
        $this->db->query('UPDATE sw_especialidad SET es_nombre = :es_nombre, es_figura = :es_figura, es_abreviatura = :es_abreviatura WHERE id_especialidad = :id_especialidad');

        //Vincular valores
        $this->db->bind(':id_especialidad', $datos['id_especialidad']);
        $this->db->bind(':es_nombre', $datos['es_nombre']);
        $this->db->bind(':es_figura', $datos['es_figura']);
        $this->db->bind(':es_abreviatura', $datos['es_abreviatura']);

        return $this->db->execute();
    }

    public function eliminarEspecialidad($id)
    {
		$this->db->query('DELETE FROM `sw_especialidad` WHERE `id_especialidad` = :id_especialidad');
        
        //Vincular valores
        $this->db->bind(':id_especialidad', $id);

		return $this->db->execute();
	}
}
