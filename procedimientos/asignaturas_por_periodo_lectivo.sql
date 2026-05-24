SELECT DISTINCT a.id_asignatura, 
       as_nombre 
  FROM sw_malla_curricular m, 
       sw_asignatura a 
 WHERE a.id_asignatura = m.id_asignatura 
   AND id_periodo_lectivo = 6 
   AND id_tipo_asignatura = 1
 ORDER BY as_nombre