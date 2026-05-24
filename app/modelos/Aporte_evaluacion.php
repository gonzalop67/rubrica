<?php
class Aporte_evaluacion
{
    private $db;

    public function __construct()
    {
        $this->db = new Base;
    }

    public function obtenerAporteEvaluacion($id)
    {
        $this->db->query("SELECT * FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id");
        return $this->db->registro();
    }

    public function obtenerAportesPorPeriodo($id_periodo_evaluacion)
    {
        $this->db->query("SELECT id_aporte_evaluacion, ap_nombre FROM sw_aporte_evaluacion WHERE id_periodo_evaluacion = " . $id_periodo_evaluacion . " ORDER BY id_aporte_evaluacion ASC");
        $registros = $this->db->registros();
		$num_total_registros = $this->db->rowCount();
		$cadena = "";
		if($num_total_registros>0)
		{
			foreach($registros as $aportes_evaluacion)
			{
				$code = $aportes_evaluacion->id_aporte_evaluacion;
				$name = $aportes_evaluacion->ap_nombre;
				$cadena .= "<option value=\"$code\">$name</option>";
			}
		}
		return $cadena;
    }

    public function obtenerAportesEvaluacion()
    {
        $this->db->query("SELECT a.*,  
                                 ta_descripcion 
                            FROM sw_aporte_evaluacion a, 
                                 sw_tipo_aporte ta
                           WHERE ta.id_tipo_aporte = a.id_tipo_aporte  
                           ORDER BY ap_orden");
        return $this->db->registros();
    }

    public function existeNombreAporte($ap_nombre, $id_sub_periodo_evaluacion)
    {
        $this->db->query("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE ap_nombre = '$ap_nombre' and id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion");
        $this->db->registro();

        return $this->db->rowCount();
    }

    public function existeAbreviaturaAporte($ap_abreviatura, $id_sub_periodo_evaluacion)
    {
        $this->db->query("SELECT id_aporte_evaluacion FROM sw_aporte_evaluacion WHERE ap_abreviatura = '$ap_abreviatura' and id_sub_periodo_evaluacion = $id_sub_periodo_evaluacion");
        $this->db->registro();

        return $this->db->rowCount();
    }

    // Para reportes de calificaciones de parciales
    public function obtenerNombreAporteEvaluacion($id_aporte_evaluacion)
    {
        $this->db->query("SELECT pe_nombre, ap_nombre FROM sw_periodo_evaluacion p, sw_aporte_evaluacion a WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion AND id_aporte_evaluacion = $id_aporte_evaluacion");
		$aporte_evaluacion = $this->db->registro();
		return $aporte_evaluacion->pe_nombre . " - " . $aporte_evaluacion->ap_nombre;
    }

    public function obtenerNombresInsumos($id_aporte_evaluacion, $id_asignatura)
    {
        $this->db->query("SELECT ru_nombre, ru_abreviatura FROM `sw_rubrica_evaluacion` r, `sw_asignatura` a WHERE a.id_tipo_asignatura = r.id_tipo_asignatura AND id_aporte_evaluacion = $id_aporte_evaluacion AND id_asignatura = $id_asignatura");
		return $this->db->registros();
    }

    public function mostrarEstadoRubrica($id_aporte_evaluacion, $id_paralelo)
    {
        // Y bueno primero tengo que "actualizar" el estado del aporte
		$this->db->query("SELECT ap_fecha_apertura, ap_fecha_cierre, ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo");
        $aporte = $this->db->registro();
		if($this->db->rowCount() > 0)
		{
			$f_apertura = $aporte->ap_fecha_apertura;
			$f_cierre = $aporte->ap_fecha_cierre;
			$estado = $aporte->ap_estado;
		}
		else
		{
			return "FECHAS: NO DEFINIDAS";
		}

        // A ver... ahora si voy a actualizar el estado...
		date_default_timezone_set('America/Guayaquil');
		$fechaactual = Date("Y-m-d H:i:s");
		
		if ($fechaactual > $f_apertura) { // Si la fecha actual es mayor a la fecha de apertura, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'A' WHERE id_paralelo = $id_paralelo AND id_aporte_evaluacion = $id_aporte_evaluacion";
			$this->db->query($qry);
            $this->db->execute();
		}

        if ($fechaactual > $f_cierre) { // Si la fecha actual es mayor a la fecha de cierre, actualizo el estado en [A]bierto
			$qry = "UPDATE sw_aporte_paralelo_cierre SET ap_estado = 'C' WHERE id_paralelo = $id_paralelo AND id_aporte_evaluacion = $id_aporte_evaluacion";
			$this->db->query($qry);
            $this->db->execute();
		}

        // Consulto el estado del aporte (A: Abierto; C: Cerrado)
		$this->db->query("SELECT ap_estado FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo");
        $aporte = $this->db->registro();
		if($this->db->rowCount() > 0)
		{
			$estado = $aporte->ap_estado;
			$estado = ($estado == 'A') ? 'ABIERTO' : 'CERRADO';
			return "ESTADO: " . $estado;
		}
		else
		{
			return "ESTADO: NO DEFINIDO";
		}
    }

    public function mostrarFechaCierre($id_aporte_evaluacion, $id_paralelo)
	{
		// Consulto el estado del aporte (A: Abierto; C: Cerrado)
		$this->db->query("SELECT ap_fecha_cierre FROM sw_aporte_paralelo_cierre WHERE id_aporte_evaluacion = $id_aporte_evaluacion AND id_paralelo = $id_paralelo");
        $aporte = $this->db->registro();
		if($this->db->rowCount() > 0)
		{
			$fecha = $aporte->ap_fecha_cierre;
			return "Fecha de cierre: " . $fecha . " 00H:00";
		}
		else
		{
			return "FECHA: NO DEFINIDA";
		}
	}

    public function mostrarLeyendasRubricas($id_aporte_evaluacion, $id_asignatura, $id_curso)
    {
        $this->db->query("SELECT ru_nombre, 
								 ru_abreviatura
							FROM sw_rubrica_evaluacion r,
								 sw_asignatura a
						   WHERE r.id_tipo_asignatura = a.id_tipo_asignatura
							 AND a.id_asignatura = $id_asignatura
			                 AND id_aporte_evaluacion = $id_aporte_evaluacion");
        $registros = $this->db->registros();
        $num_total_registros = $this->db->rowCount();
        $mensaje = "";
        if ($num_total_registros > 0) {
			foreach ($registros as $rubrica) {
				$mensaje .= $rubrica->ru_abreviatura . ": " . $rubrica->ru_nombre . ", ";
			}
		}
        $mensaje .= "PROM: PROMEDIO";
        $this->db->query("SELECT quien_inserta_comp FROM sw_curso WHERE id_curso = $id_curso");
		$resultado = $this->db->registro();
        if ($resultado->quien_inserta_comp == 0) {
			$mensaje .= ", COMP: COMPORTAMIENTO";
		}
        return $mensaje;
    }

    public function obtenerTipoAporte($id_aporte_evaluacion)
	{
		$this->db->query("SELECT id_tipo_aporte FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = $id_aporte_evaluacion");
		$aporte_evaluacion = $this->db->registro();
		return $aporte_evaluacion->id_tipo_aporte;
	}

    public function insertarAporteEvaluacion($datos)
    {
        $this->db->query("SELECT MAX(ap_orden) AS max_orden FROM sw_aporte_evaluacion a, sw_sub_periodo_evaluacion sp, sw_periodo_lectivo_sub_periodo ps WHERE sp.id_sub_periodo_evaluacion = a.id_sub_periodo_evaluacion AND sp.id_sub_periodo_evaluacion = ps.id_sub_periodo_evaluacion AND ps.id_periodo_lectivo = " . $_SESSION['id_periodo_lectivo']);
        $max_orden = $this->db->registro()->max_orden ? $this->db->registro()->max_orden + 1 : 1;
        // var_dump($max_orden);
        // die();
        $this->db->query('INSERT INTO sw_aporte_evaluacion (id_sub_periodo_evaluacion, id_tipo_aporte, ap_nombre, ap_abreviatura, ap_descripcion, ap_ponderacion, ap_fecha_apertura, ap_fecha_cierre, ap_orden) VALUES (:id_sub_periodo_evaluacion, :id_tipo_aporte, :ap_nombre, :ap_abreviatura, :ap_descripcion, :ap_ponderacion, :ap_fecha_apertura, :ap_fecha_cierre, :ap_orden)');

        //Vincular valores
        $this->db->bind(':id_sub_periodo_evaluacion', $datos['id_sub_periodo_evaluacion']);
        $this->db->bind(':id_tipo_aporte', $datos['id_tipo_aporte']);
        $this->db->bind(':ap_nombre', $datos['ap_nombre']);
        $this->db->bind(':ap_abreviatura', $datos['ap_abreviatura']);
        $this->db->bind(':ap_descripcion', $datos['ap_descripcion']);
        $this->db->bind(':ap_ponderacion', $datos['ap_ponderacion']);
        $this->db->bind(':ap_fecha_apertura', $datos['ap_fecha_apertura']);
        $this->db->bind(':ap_fecha_cierre', $datos['ap_fecha_cierre']);
        $this->db->bind(':ap_orden', $max_orden);

        $this->db->execute();

        //Obtener el máximo id_aporte_evaluacion
        $this->db->query("SELECT MAX(id_aporte_evaluacion) AS max_id FROM `sw_aporte_evaluacion`");
        $id_aporte_evaluacion = $this->db->registro()->max_id;

        // Asociar el aporte de evaluación creado con los paralelos creados para el cierre de periodos...
        $qry = "SELECT p.id_paralelo  
				  FROM `sw_paralelo` p  
				 WHERE p.id_periodo_lectivo = " . $datos['id_periodo_lectivo'];
        $this->db->query($qry);
        $paralelos = $this->db->registros();
        foreach ($paralelos as $paralelo) {
            $id_paralelo = $paralelo->id_paralelo;
            // Insertar la asociación para el futuro cierre...
            $qry = "SELECT * 
					  FROM `sw_aporte_paralelo_cierre` 
					 WHERE id_aporte_evaluacion = $id_aporte_evaluacion 
					   AND id_paralelo = $id_paralelo";
            $this->db->query($qry);
            // $asociacion = $this->db->registro();
            if ($this->db->rowCount() == 0) {
                $this->db->query("INSERT INTO `sw_aporte_paralelo_cierre` 
				                          SET id_aporte_evaluacion = :id_aporte_evaluacion, 
                                              id_paralelo = :id_paralelo, 
                                              ap_fecha_apertura = :ap_fecha_apertura, 
                                              ap_fecha_cierre = :ap_fecha_cierre, 
                                              ap_estado = :ap_estado");
                 
				//Vincular valores
                $this->db->bind(':id_aporte_evaluacion', $id_aporte_evaluacion);
                $this->db->bind(':id_paralelo', $id_paralelo);
                $this->db->bind(':ap_fecha_apertura', $datos['ap_fecha_apertura']);
                $this->db->bind(':ap_fecha_cierre', $datos['ap_fecha_cierre']);
                $this->db->bind(':ap_estado', 'C');
        
                $this->db->execute();
            }
        }
    }

    public function actualizarAporteEvaluacion($datos)
    {
        $this->db->query('UPDATE sw_aporte_evaluacion SET id_sub_periodo_evaluacion = :id_sub_periodo_evaluacion, id_tipo_aporte = :id_tipo_aporte, ap_nombre = :ap_nombre, ap_abreviatura = :ap_abreviatura, ap_descripcion = :ap_descripcion, ap_ponderacion = :ap_ponderacion, ap_fecha_apertura = :ap_fecha_apertura, ap_fecha_cierre = :ap_fecha_cierre WHERE id_aporte_evaluacion = :id_aporte_evaluacion');

        //Vincular valores
        $this->db->bind(':id_aporte_evaluacion', $datos['id_aporte_evaluacion']);
        $this->db->bind(':id_sub_periodo_evaluacion', $datos['id_sub_periodo_evaluacion']);
        $this->db->bind(':id_tipo_aporte', $datos['id_tipo_aporte']);
        $this->db->bind(':ap_nombre', $datos['ap_nombre']);
        $this->db->bind(':ap_abreviatura', $datos['ap_abreviatura']);
        $this->db->bind(':ap_descripcion', $datos['ap_descripcion']);
        $this->db->bind(':ap_ponderacion', $datos['ap_ponderacion']);
        $this->db->bind(':ap_fecha_apertura', $datos['ap_fecha_apertura']);
        $this->db->bind(':ap_fecha_cierre', $datos['ap_fecha_cierre']);

        return $this->db->execute();
    }

    public function eliminarAporteEvaluacion($id_aporte_evaluacion)
    {
        $this->db->query('DELETE FROM sw_aporte_evaluacion WHERE id_aporte_evaluacion = :id_aporte_evaluacion');

        //Vincular valores
        $this->db->bind(':id_aporte_evaluacion', $id_aporte_evaluacion);

        return $this->db->execute();
    }
}