<?php
class Cierre_periodo
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerCierrePeriodo($id_aporte_paralelo_cierre)
    {
        $this->db->query("SELECT * FROM sw_aporte_paralelo_cierre WHERE id_aporte_paralelo_cierre =  $id_aporte_paralelo_cierre");

        return $this->db->registro();
    }

    public function obtenerCierresPeriodos($id_periodo_lectivo)
    {
        $this->db->query("
            SELECT ac.*, 
                   ap_nombre, 
                   cu_nombre,
                   pa_nombre,
                   es_figura,
                   jo_nombre 
              FROM sw_aporte_paralelo_cierre ac,
                   sw_aporte_evaluacion ap,
                   sw_periodo_evaluacion pe,
                   sw_tipo_aporte ta, 
                   sw_paralelo pa,
                   sw_curso cu,
                   sw_especialidad es,
                   sw_jornada jo 
             WHERE ap.id_aporte_evaluacion = ac.id_aporte_evaluacion 
               AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion
               AND ta.id_tipo_aporte = ap.id_tipo_aporte
               AND pa.id_paralelo = ac.id_paralelo
               AND cu.id_curso = pa.id_curso
               AND es.id_especialidad = cu.id_especialidad
               AND jo.id_jornada = pa.id_jornada 
               AND pa.id_periodo_lectivo = $id_periodo_lectivo
             ORDER BY pa_orden, 
                      pe.id_periodo_evaluacion, 
                      ta.id_tipo_aporte, 
                      ap.id_aporte_evaluacion
        ");

        return $this->db->registros();
    }

    public function existeAsociacion($id_paralelo, $id_aporte_evaluacion)
    {
        $this->db->query("SELECT * FROM sw_aporte_paralelo_cierre WHERE id_paralelo = $id_paralelo AND id_aporte_evaluacion = $id_aporte_evaluacion");
        $this->db->registros();

        return $this->db->rowCount() > 0;
    }

    public function buscarCierresPeriodos($patron)
    {
        $this->db->query("SELECT ac.*, 
                                 ap_nombre, 
                                 cu_nombre,
                                 pa_nombre,
                                 es_figura,
                                 jo_nombre 
                            FROM sw_aporte_paralelo_cierre ac,
                                 sw_aporte_evaluacion ap,
                                 sw_periodo_evaluacion pe,
                                 sw_tipo_aporte ta, 
                                 sw_paralelo pa,
                                 sw_curso cu,
                                 sw_especialidad es,
                                 sw_jornada jo 
                           WHERE ap.id_aporte_evaluacion = ac.id_aporte_evaluacion 
                             AND pe.id_periodo_evaluacion = ap.id_periodo_evaluacion
                             AND ta.id_tipo_aporte = ap.id_tipo_aporte
                             AND pa.id_paralelo = ac.id_paralelo
                             AND cu.id_curso = pa.id_curso
                             AND es.id_especialidad = cu.id_especialidad
                             AND jo.id_jornada = pa.id_jornada 
                             AND (ap.ap_nombre LIKE '%" . $patron . "%'
                                 OR cu_nombre LIKE '%" . $patron . "%'
                                 OR es_figura LIKE '%" . $patron . "%')
                           ORDER BY pa_orden, 
                                 pe.id_periodo_evaluacion, 
                                 ta.id_tipo_aporte, 
                                 ap.id_aporte_evaluacion");

        return $this->db->registros();
    }

    public function insertarAsociacion($datos)
    {
        $this->db->query("INSERT INTO sw_aporte_paralelo_cierre 
                                  SET id_paralelo = :id_paralelo, 
                                      id_aporte_evaluacion = :id_aporte_evaluacion, 
                                      ap_fecha_apertura = :ap_fecha_apertura, 
                                      ap_fecha_cierre = :ap_fecha_cierre");
        //Vincular valores
        $this->db->bind('id_paralelo', $datos['id_paralelo']);
        $this->db->bind('id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
        $this->db->bind('ap_fecha_apertura', $datos['ap_fecha_apertura']);
        $this->db->bind('ap_fecha_cierre', $datos['ap_fecha_cierre']);

        $this->db->execute();
    }

    public function actualizarEstadoCierrePeriodo($datos)
    {
        $this->db->query("UPDATE sw_aporte_paralelo_cierre 
                             SET ap_estado = :ap_estado
                           WHERE id_aporte_paralelo_cierre = :id");
        //Vincular valores
        $this->db->bind('id', $datos['id_aporte_paralelo_cierre']);
        $this->db->bind('ap_estado', $datos['ap_estado']);

        $this->db->execute();
    }

    public function actualizarCierrePeriodo($datos)
    {
        $this->db->query("UPDATE sw_aporte_paralelo_cierre 
                             SET ap_fecha_apertura = :ap_fecha_apertura, 
                                 ap_fecha_cierre = :ap_fecha_cierre
                           WHERE id_aporte_paralelo_cierre = :id");
        //Vincular valores
        $this->db->bind('id', $datos['id_aporte_paralelo_cierre']);
        $this->db->bind('ap_fecha_apertura', $datos['ap_fecha_apertura']);
        $this->db->bind('ap_fecha_cierre', $datos['ap_fecha_cierre']);

        $this->db->execute();

        $fechaactual = Date("Y-m-d H:i:s");

        if ($fechaactual > $datos['ap_fecha_apertura']) { 
            // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'A'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        } else {
            // Si la fecha actual es menor a la fecha de cierre, actualizo el estado en [C]errado
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'C'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        }

        if ($fechaactual > $datos['ap_fecha_cierre']) { 
            // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'C'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        } else {
            // Si la fecha actual es menor a la fecha de cierre, actualizo el estado en [C]errado
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'A'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        }
    }

    public function actualizarFechaApertura($datos)
    {
        $this->db->query("UPDATE sw_aporte_paralelo_cierre 
                             SET ap_fecha_apertura = :ap_fecha_apertura
                           WHERE id_aporte_paralelo_cierre = :id");
        //Vincular valores
        $this->db->bind('id', $datos['id_aporte_paralelo_cierre']);
        $this->db->bind('ap_fecha_apertura', $datos['ap_fecha_apertura']);

        $this->db->execute();

        $fechaactual = Date("Y-m-d H:i:s");

        if ($fechaactual > $datos['ap_fecha_apertura']) { 
            // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'A'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        } else {
            // Si la fecha actual es menor a la fecha de cierre, actualizo el estado en [C]errado
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'C'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        }
    }

    public function actualizarFechaCierre($datos)
    {
        $this->db->query("UPDATE sw_aporte_paralelo_cierre 
                             SET ap_fecha_cierre = :ap_fecha_cierre
                           WHERE id_aporte_paralelo_cierre = :id");
        //Vincular valores
        $this->db->bind('id', $datos['id_aporte_paralelo_cierre']);
        $this->db->bind('ap_fecha_cierre', $datos['ap_fecha_cierre']);

        $this->db->execute();

        $fechaactual = Date("Y-m-d H:i:s");

        if ($fechaactual > $datos['ap_fecha_cierre']) { 
            // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [C]errado
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'C'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        } else {
            // Si la fecha actual es menor a la fecha de cierre, actualizo el estado en [A]bierto
            $data = [
                'id_aporte_paralelo_cierre' => $datos['id_aporte_paralelo_cierre'],
                'ap_estado'                 => 'A'
            ];
            $this->actualizarEstadoCierrePeriodo($data);
        }
    }
}