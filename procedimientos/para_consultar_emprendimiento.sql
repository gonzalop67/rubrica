SELECT 
mo_nombre,
pe_anio_inicio, 
pe_anio_fin, 
cu_abreviatura, 
es_figura, 
pa_nombre,
es_retirado
FROM `sw_estudiante_periodo_lectivo` pe, 
`sw_periodo_lectivo` pl,
`sw_modalidad` mo,
`sw_paralelo` pa,
`sw_curso` cu,
`sw_especialidad` es 
WHERE pl.id_periodo_lectivo = pe.id_periodo_lectivo 
AND mo.id_modalidad = pl.id_modalidad
AND es.id_especialidad = cu.id_especialidad
AND cu.id_curso = pa.id_curso
AND pa.id_paralelo = pe.id_paralelo
AND `id_estudiante` = 2832