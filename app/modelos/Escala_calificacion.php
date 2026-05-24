<?php
class Escala_calificacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerEscalaCalificacion($id)
    {
        $this->db->query("SELECT *
                            FROM sw_escala_calificaciones
                           WHERE id_escala_calificaciones = $id");
        return $this->db->registro();
    }

    public function obtenerEscalasCalificaciones($id_periodo_lectivo)
    {
        $this->db->query("SELECT *
                            FROM sw_escala_calificaciones
                           WHERE id_periodo_lectivo = $id_periodo_lectivo
                           ORDER BY ec_orden");
        return $this->db->registros();
    }

    public function existeCualitativa($ec_cualitativa, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_escala_calificaciones WHERE ec_cualitativa = '$ec_cualitativa' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeCuantitativa($ec_cuantitativa, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_escala_calificaciones WHERE ec_cuantitativa = '$ec_cuantitativa' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeNotaMinima($ec_nota_minima, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_escala_calificaciones WHERE ec_nota_minima = '$ec_nota_minima' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeNotaMaxima($ec_nota_maxima, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_escala_calificaciones WHERE ec_nota_maxima = '$ec_nota_maxima' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }
    
    public function existeEquivalencia($ec_equivalencia, $id_periodo_lectivo)
    {
        $this->db->query("SELECT * FROM sw_escala_calificaciones WHERE ec_equivalencia = '$ec_equivalencia' AND id_periodo_lectivo = $id_periodo_lectivo");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarEscalaCalificacion($datos)
    {
        //Obtener el máximo ec_orden
        $id_periodo_lectivo = $datos['id_periodo_lectivo'];
        $this->db->query("SELECT MAX(ec_orden) AS max_orden FROM sw_escala_calificaciones WHERE id_periodo_lectivo = $id_periodo_lectivo");
        $record = $this->db->registro();

        $maximo_orden = $this->db->rowCount() == 0 ? 1 : $record->maximo + 1;

        $this->db->query("INSERT INTO sw_escala_calificaciones (id_periodo_lectivo, ec_cualitativa, ec_cuantitativa, ec_nota_minima, ec_nota_maxima, ec_equivalencia, ec_orden) VALUES (:id_periodo_lectivo, :ec_cualitativa, :ec_cuantitativa, :ec_nota_minima, :ec_nota_maxima, :ec_equivalencia, :ec_orden)");

        //Vincular valores
        $this->db->bind(':id_periodo_lectivo', $id_periodo_lectivo);
        $this->db->bind(':ec_cualitativa', $datos['ec_cualitativa']);
        $this->db->bind(':ec_cuantitativa', $datos['ec_cuantitativa']);
        $this->db->bind(':ec_nota_minima', $datos['ec_nota_minima']);
        $this->db->bind(':ec_nota_maxima', $datos['ec_nota_maxima']);
        $this->db->bind(':ec_equivalencia', $datos['ec_equivalencia']);
        $this->db->bind(':ec_orden', $maximo_orden);

        return $this->db->execute();
    }

    public function actualizarEscalaCalificacion($datos)
    {
        $this->db->query("UPDATE sw_escala_calificaciones SET ec_cualitativa = :ec_cualitativa, ec_cuantitativa = :ec_cuantitativa, ec_nota_minima = :ec_nota_minima, ec_nota_maxima = :ec_nota_maxima, ec_equivalencia = :ec_equivalencia WHERE id_escala_calificaciones = :id_escala_calificaciones");

        //Vincular valores
        $this->db->bind(':id_escala_calificaciones', $datos['id_escala_calificaciones']);
        $this->db->bind(':ec_cualitativa', $datos['ec_cualitativa']);
        $this->db->bind(':ec_cuantitativa', $datos['ec_cuantitativa']);
        $this->db->bind(':ec_nota_minima', $datos['ec_nota_minima']);
        $this->db->bind(':ec_nota_maxima', $datos['ec_nota_maxima']);
        $this->db->bind(':ec_equivalencia', $datos['ec_equivalencia']);

        return $this->db->execute();
    }

    public function eliminarEscalaCalificacion($id)
    {
        $this->db->query("DELETE FROM sw_escala_calificaciones WHERE id_escala_calificaciones = :id_escala_calificaciones");

        //Vincular valores
        $this->db->bind(':id_escala_calificaciones', $id);

        return $this->db->execute();
    }

    public function actualizarOrden($id_escala_calificaciones, $ec_orden)
    {
        $this->db->query('UPDATE `sw_escala_calificaciones` SET `ec_orden` = :ec_orden WHERE `id_escala_calificaciones` = :id_escala_calificaciones');

        //Vincular valores
        $this->db->bind(':id_escala_calificaciones', $id_escala_calificaciones);
        $this->db->bind(':ec_orden', $ec_orden);

        echo $this->db->execute();
    }
}