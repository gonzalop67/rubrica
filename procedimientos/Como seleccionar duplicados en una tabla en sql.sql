SELECT id_periodo_lectivo, id_tipo_periodo, COUNT(*)
FROM sw_equivalencia_supletorios
GROUP BY id_periodo_lectivo, id_tipo_periodo
HAVING COUNT(*) > 1;

-- Cédulas repetidas
SELECT id_estudiante, es_cedula, COUNT(*)
FROM sw_estudiante
GROUP BY id_estudiante, es_cedula
HAVING COUNT(*) > 1;

-- Notas repetidas
SELECT id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, COUNT(*)
FROM sw_rubrica_estudiante
GROUP BY id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada
HAVING COUNT(*) > 1;

-- Distributivo repetido
SELECT id_periodo_lectivo, id_paralelo, id_asignatura, id_usuario, COUNT(*)
FROM sw_distributivo
GROUP BY id_periodo_lectivo, id_paralelo, id_asignatura, id_usuario
HAVING COUNT(*) > 1;