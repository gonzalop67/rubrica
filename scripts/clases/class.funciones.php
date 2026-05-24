<?php

class funciones extends MySQL
{
	static public function obtenerFechaCierreSupRemGracia($id_paralelo, $pe_principal, $id_periodo_lectivo)
	{
		// Obtencion de la fecha de cierre del aporte indicado por el campo pe_principal
		$qry = parent::consulta("SELECT ac.ap_fecha_cierre 
        						   FROM sw_periodo_evaluacion p, 
             							sw_aporte_evaluacion a, 
             							sw_aporte_paralelo_cierre ac,  
             							sw_paralelo pa 
       							  WHERE a.id_periodo_evaluacion = p.id_periodo_evaluacion 
         							AND a.id_aporte_evaluacion = ac.id_aporte_evaluacion  
         							AND pa.id_paralelo = ac.id_paralelo 
         							AND pa.id_paralelo = $id_paralelo 
         							AND id_tipo_periodo = $pe_principal 
         							AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($qry);
		return $registro["ap_fecha_cierre"];
	}
	
	static public function existeExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $id_tipo_periodo, $id_periodo_lectivo)
	{
		$consulta = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $id_tipo_periodo AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($consulta);
		$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
		
		$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
		return (parent::num_rows($qry) > 0);		
	}
	
	static public function obtenerExamenSupRemGracia($id_estudiante, $id_paralelo, $id_asignatura, $pe_principal, $id_periodo_lectivo)
	{
		$qry = parent::consulta("SELECT id_rubrica_evaluacion FROM sw_rubrica_evaluacion r, sw_aporte_evaluacion a, sw_periodo_evaluacion p WHERE r.id_aporte_evaluacion = a.id_aporte_evaluacion AND a.id_periodo_evaluacion = p.id_periodo_evaluacion AND p.id_tipo_periodo = $pe_principal AND p.id_periodo_lectivo = $id_periodo_lectivo");
		$registro = parent::fetch_assoc($qry);
		$id_rubrica_personalizada = $registro["id_rubrica_evaluacion"];
		
		$qry = parent::consulta("SELECT re_calificacion FROM sw_rubrica_estudiante WHERE id_estudiante = $id_estudiante AND id_paralelo = $id_paralelo AND id_asignatura = $id_asignatura AND id_rubrica_personalizada = $id_rubrica_personalizada");
	
		if($qry) {
			$rubrica_estudiante = parent::fetch_assoc($qry);
			$calificacion = $rubrica_estudiante["re_calificacion"];
		} else {
			$calificacion = 0;
		}
		
		return $calificacion;
	}

}
?>