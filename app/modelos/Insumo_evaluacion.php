<?php
class Insumo_evaluacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerInsumoEvaluacion($id)
    {
        $this->db->query("SELECT * FROM sw_rubrica_evaluacion WHERE id_rubrica_evaluacion = $id");
        return $this->db->registro();
    }

    public function obtenerInsumosEvaluacion($id_periodo_lectivo)
    {
        $this->db->query("SELECT r.*, 
                                 pe_nombre, 
                                 ap_nombre 
                            FROM sw_sub_periodo_evaluacion p,
                                 sw_aporte_evaluacion a, 
                                 sw_rubrica_evaluacion r, 
                                 sw_periodo_lectivo_sub_periodo ps 
                           WHERE p.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion 
                             AND p.id_sub_periodo_evaluacion = a.id_sub_periodo_evaluacion 
                             AND a.id_aporte_evaluacion = r.id_aporte_evaluacion 
                             AND ps.id_periodo_lectivo = $id_periodo_lectivo");
        return $this->db->registros();
    }

    public function existeNombreInsumo($ru_nombre, $id_aporte_evaluacion)
    {
        $this->db->query("SELECT * FROM sw_rubrica_evaluacion WHERE ru_nombre = '$ru_nombre' AND id_aporte_evaluacion = $id_aporte_evaluacion");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeAbreviaturaInsumo($ru_abreviatura, $id_aporte_evaluacion)
    {
        $this->db->query("SELECT * FROM sw_rubrica_evaluacion WHERE ru_abreviatura = '$ru_abreviatura' AND id_aporte_evaluacion = $id_aporte_evaluacion");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function existeRubricaEstudiante($datos)
    {
        $this->db->query("SELECT * FROM sw_rubrica_estudiante WHERE id_estudiante = " . $datos['id_estudiante'] . " AND id_paralelo = " . $datos['id_paralelo'] . " AND id_asignatura = " . $datos['id_asignatura'] . " AND id_rubrica_personalizada = " . $datos['id_rubrica_personalizada']);
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarRubricaEstudiante($datos)
    {
        try {
            $this->db->query('INSERT INTO sw_rubrica_estudiante (id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, re_calificacion) VALUES (:id_estudiante, :id_paralelo, :id_asignatura, :id_rubrica_personalizada, :re_calificacion)');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_rubrica_personalizada', $datos['id_rubrica_personalizada']);
            $this->db->bind(':re_calificacion', $datos['re_calificacion']);

            $this->db->execute();
            return "Insertada la calificación exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo insertar la calificación exitosamente. Error: " . $th->getMessage();
        }
    }

    public function actualizarRubricaEstudiante($datos)
    {
        try {
            $this->db->query('UPDATE sw_rubrica_estudiante SET re_calificacion = :re_calificacion WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_rubrica_personalizada = :id_rubrica_personalizada');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_rubrica_personalizada', $datos['id_rubrica_personalizada']);
            $this->db->bind(':re_calificacion', $datos['re_calificacion']);

            $this->db->execute();
            return "Actualizada la calificación exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo actualizar la calificación exitosamente. Error: " . $th->getMessage();
        }
    }

    public function eliminarRubricaEstudiante($datos)
    {
        try {
            $this->db->query('DELETE FROM sw_rubrica_estudiante WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_rubrica_personalizada = :id_rubrica_personalizada');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_rubrica_personalizada', $datos['id_rubrica_personalizada']);

            $this->db->execute();
            return "Eliminada la calificación exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo eliminar la calificación exitosamente. Error: " . $th->getMessage();
        }
    }

    public function existeRubricaEstudianteComportamiento($datos)
    {
        $this->db->query("SELECT * FROM sw_calificacion_comportamiento WHERE id_estudiante = " . $datos['id_estudiante'] . " AND id_paralelo = " . $datos['id_paralelo'] . " AND id_asignatura = " . $datos['id_asignatura'] . " AND id_aporte_evaluacion = " . $datos['id_aporte_evaluacion']);
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function insertarRubricaEstudianteComportamiento($datos)
    {
        try {
            $this->db->query('INSERT INTO sw_calificacion_comportamiento (id_estudiante, id_paralelo, id_asignatura, id_aporte_evaluacion, co_cualitativa) VALUES (:id_estudiante, :id_paralelo, :id_asignatura, :id_aporte_evaluacion, :co_cualitativa)');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
            $this->db->bind(':co_cualitativa', $datos['co_cualitativa']);

            $this->db->execute();

            //Ahora actualizamos el campo co_calificacion con la calificacion correlativa de la tabla sw_escala_comportamiento
            $this->db->query("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '" . $datos['co_cualitativa'] . "'");

            $registro = $this->db->registro();

            $co_calificacion = $registro->ec_correlativa;

            $this->db->query("UPDATE sw_calificacion_comportamiento SET co_calificacion = :co_calificacion WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_aporte_evaluacion = :id_aporte_evaluacion");

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
            $this->db->bind(':co_calificacion', $co_calificacion);

            $this->db->execute();

            return "Insertada la calificación de comportamiento exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo insertar la calificación de comportamiento exitosamente. Error: " . $th->getMessage();
        }
    }

    public function actualizarRubricaEstudianteComportamiento($datos)
    {
        try {
            $this->db->query('UPDATE sw_calificacion_comportamiento SET co_cualitativa = :co_cualitativa WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_aporte_evaluacion = :id_aporte_evaluacion');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
            $this->db->bind(':co_cualitativa', $datos['co_cualitativa']);

            $this->db->execute();

            //Ahora actualizamos el campo co_calificacion con la calificacion correlativa de la tabla sw_escala_comportamiento
            $this->db->query("SELECT ec_correlativa FROM sw_escala_comportamiento WHERE ec_equivalencia = '" . $datos['co_cualitativa'] . "'");
            
            $registro = $this->db->registro();

            $co_calificacion = $registro->ec_correlativa;

            $this->db->query("UPDATE sw_calificacion_comportamiento SET co_calificacion = :co_calificacion WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_aporte_evaluacion = :id_aporte_evaluacion");

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
            $this->db->bind(':co_calificacion', $co_calificacion);

            $this->db->execute();

            return "Actualizada la calificación de comportamiento exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo actualizar la calificación de comportamiento exitosamente. Error: " . $th->getMessage();
        }
    }

    public function eliminarCalificacionComportamiento($datos)
    {
        try {
            $this->db->query('DELETE FROM sw_calificacion_comportamiento WHERE id_estudiante = :id_estudiante AND id_paralelo = :id_paralelo AND id_asignatura = :id_asignatura AND id_aporte_evaluacion = :id_aporte_evaluacion');

            //Vincular valores
            $this->db->bind(':id_estudiante', $datos['id_estudiante']);
            $this->db->bind(':id_paralelo', $datos['id_paralelo']);
            $this->db->bind(':id_asignatura', $datos['id_asignatura']);
            $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);

            $this->db->execute();
            return "Eliminada la calificación de comportamiento exitosamente.";
        } catch (\Throwable $th) {
            return "No se pudo eliminar la calificación de comportamiento exitosamente. Error: " . $th->getMessage();
        }
    }

    public function insertarInsumoEvaluacion($datos)
    {
        $this->db->query('INSERT INTO sw_rubrica_evaluacion (id_aporte_evaluacion, id_tipo_asignatura, ru_nombre, ru_abreviatura) VALUES (:id_aporte_evaluacion, :id_tipo_asignatura, :ru_nombre, :ru_abreviatura)');

        //Vincular valores
        $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
        $this->db->bind(':id_tipo_asignatura', $datos['id_tipo_asignatura']);
        $this->db->bind(':ru_nombre', $datos['ru_nombre']);
        $this->db->bind(':ru_abreviatura', $datos['ru_abreviatura']);

        return $this->db->execute();
    }

    public function actualizarInsumoEvaluacion($datos)
    {
        $this->db->query('UPDATE sw_rubrica_evaluacion SET id_aporte_evaluacion = :id_aporte_evaluacion, id_tipo_asignatura = :id_tipo_asignatura, ru_nombre = :ru_nombre, ru_abreviatura = :ru_abreviatura WHERE id_rubrica_evaluacion = :id_rubrica_evaluacion');

        //Vincular valores
        $this->db->bind(':id_rubrica_evaluacion', $datos['id_rubrica_evaluacion']);
        $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
        $this->db->bind(':id_tipo_asignatura', $datos['id_tipo_asignatura']);
        $this->db->bind(':ru_nombre', $datos['ru_nombre']);
        $this->db->bind(':ru_abreviatura', $datos['ru_abreviatura']);

        return $this->db->execute();
    }

    public function eliminarInsumoEvaluacion($id)
    {
        $this->db->query('DELETE FROM sw_rubrica_evaluacion WHERE id_rubrica_evaluacion = :id_rubrica_evaluacion');

        //Vincular valores
        $this->db->bind(':id_rubrica_evaluacion', $id);

        return $this->db->execute();
    }
}
