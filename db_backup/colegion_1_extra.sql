
--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD PRIMARY KEY (`id_aporte_evaluacion`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD PRIMARY KEY (`id_aporte_paralelo_cierre`),
  ADD KEY `sw_aporte_paralelo_cierre_ibfk_1` (`id_aporte_evaluacion`),
  ADD KEY `sw_aporte_paralelo_cierre_ibfk_2` (`id_paralelo`);

--
-- Indices de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `id_area` (`id_area`);

--
-- Indices de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  ADD PRIMARY KEY (`id_asignatura_curso`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD PRIMARY KEY (`id_asistencia_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_asignatura`,`id_paralelo`,`id_inasistencia`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_inasistencia` (`id_inasistencia`),
  ADD KEY `sw_asistencia_estudiante_ibfk_1` (`id_hora_clase`);

--
-- Indices de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD PRIMARY KEY (`id_asistencia_tutor`),
  ADD KEY `fk_asistencia_tutor_estudiante` (`id_estudiante`),
  ADD KEY `fk_asistencia_tutor_paralelo` (`id_paralelo`),
  ADD KEY `id_asistencia_tutor_inasistencia` (`id_inasistencia`);

--
-- Indices de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  ADD PRIMARY KEY (`id_asociar_curso_superior`),
  ADD KEY `fk_curso_superior_periodo_lectivo_idx` (`id_periodo_lectivo`),
  ADD KEY `fk_curso_inferior_curso_idx` (`id_curso_inferior`),
  ADD KEY `fk_curso_superior_curso_idx` (`id_curso_superior`);

--
-- Indices de la tabla `sw_calificacion_comportamiento`
--
ALTER TABLE `sw_calificacion_comportamiento`
  ADD KEY `id_paralelo` (`id_paralelo`,`id_estudiante`,`id_aporte_evaluacion`,`id_asignatura`);

--
-- Indices de la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_question` (`id_question`);

--
-- Indices de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  ADD PRIMARY KEY (`id_club`);

--
-- Indices de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  ADD PRIMARY KEY (`id_club_docente`);

--
-- Indices de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Indices de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  ADD PRIMARY KEY (`id_comportamiento_inspector`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_indice_evaluacion` (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  ADD PRIMARY KEY (`id_comportamiento_tutor`);

--
-- Indices de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sw_config_rangos_supletorios_ibfk_1` (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_cuestionario`
--
ALTER TABLE `sw_cuestionario`
  ADD PRIMARY KEY (`id_cuestionario`);

--
-- Indices de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_especialidad` (`id_especialidad`);

--
-- Indices de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  ADD PRIMARY KEY (`id_curso_superior`);

--
-- Indices de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  ADD PRIMARY KEY (`id_def_genero`);

--
-- Indices de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  ADD PRIMARY KEY (`id_def_nacionalidad`);

--
-- Indices de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD PRIMARY KEY (`id_dia_semana`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- Indices de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD PRIMARY KEY (`id_distributivo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_malla_curricular` (`id_malla_curricular`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  ADD PRIMARY KEY (`id_equivalencia_supletorios`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  ADD PRIMARY KEY (`id_escala_calificaciones`);

--
-- Indices de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  ADD PRIMARY KEY (`id_escala_comportamiento`);

--
-- Indices de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  ADD PRIMARY KEY (`id_escala_proyectos`);

--
-- Indices de la tabla `sw_escala_supletorios`
--
ALTER TABLE `sw_escala_supletorios`
  ADD PRIMARY KEY (`id_escala_supletorios`);

--
-- Indices de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD PRIMARY KEY (`id_especialidad`),
  ADD KEY `id_tipo_educacion` (`id_tipo_educacion`);

--
-- Indices de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_def_genero` (`id_def_genero`),
  ADD KEY `id_def_nacionalidad` (`id_def_nacionalidad`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  ADD PRIMARY KEY (`id_estudiante_club`);

--
-- Indices de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD PRIMARY KEY (`id_estudiante_periodo_lectivo`),
  ADD KEY `id_estudiante` (`id_estudiante`,`id_periodo_lectivo`,`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  ADD PRIMARY KEY (`id_evaluacion_subnivel`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `sw_exam_category`
--
ALTER TABLE `sw_exam_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_exam_results`
--
ALTER TABLE `sw_exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD PRIMARY KEY (`id_feriado`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  ADD PRIMARY KEY (`id_foro`);

--
-- Indices de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_hora_clase` (`id_hora_clase`),
  ADD KEY `sw_horario_ibfk_1` (`id_asignatura`),
  ADD KEY `sw_horario_ibfk_2` (`id_dia_semana`),
  ADD KEY `sw_horario_ibfk_4` (`id_paralelo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- Indices de la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  ADD PRIMARY KEY (`id_horario_def`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  ADD PRIMARY KEY (`id_horario_examen`);

--
-- Indices de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  ADD PRIMARY KEY (`id_hora_clase`);

--
-- Indices de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD PRIMARY KEY (`id_hora_dia`),
  ADD KEY `sw_hora_dia_ibfk_1` (`id_dia_semana`),
  ADD KEY `sw_hora_dia_ibfk_2` (`id_hora_clase`),
  ADD KEY `id_horario_def` (`id_horario_def`);

--
-- Indices de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  ADD PRIMARY KEY (`id_inasistencia`);

--
-- Indices de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  ADD PRIMARY KEY (`id_leccionario`),
  ADD KEY `fk_leccionario_asignatura` (`id_asignatura`),
  ADD KEY `fk_leccionario_hora_clase` (`id_hora_clase`),
  ADD KEY `fk_leccionario_paralelo` (`id_paralelo`),
  ADD KEY `fk_leccionario_usuario` (`id_usuario`);

--
-- Indices de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD PRIMARY KEY (`id_malla_curricular`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD KEY `fk_menu_perfil_menu` (`id_menu`),
  ADD KEY `fk_menu_perfil_perfil` (`id_perfil`);

--
-- Indices de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  ADD PRIMARY KEY (`id_modalidad`);

--
-- Indices de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD PRIMARY KEY (`id_paralelo`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `sw_paralelo_jornada` (`id_jornada`),
  ADD KEY `sw_paralelo_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  ADD PRIMARY KEY (`id_paralelo_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  ADD PRIMARY KEY (`id_paralelo_inspector`);

--
-- Indices de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD PRIMARY KEY (`id_paralelo_tutor`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indices de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  ADD PRIMARY KEY (`id_periodo_estado`);

--
-- Indices de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD PRIMARY KEY (`id_periodo_evaluacion`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`),
  ADD KEY `fk_periodo_evaluacion_tipo_periodo` (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD PRIMARY KEY (`id_periodo_evaluacion_curso`),
  ADD KEY `id_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Indices de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD PRIMARY KEY (`id_periodo_lectivo`),
  ADD KEY `id_institucion` (`id_institucion`),
  ADD KEY `id_modalidad` (`id_modalidad`),
  ADD KEY `id_periodo_estado` (`id_periodo_estado`),
  ADD KEY `quien_inserta_comp_id` (`quien_inserta_comp_id`);

--
-- Indices de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `fk_menus_idx` (`id_menu`),
  ADD KEY `fk_perfiles_idx` (`id_perfil`);

--
-- Indices de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  ADD PRIMARY KEY (`id_plan_rubrica`);

--
-- Indices de la tabla `sw_promedio_anual`
--
ALTER TABLE `sw_promedio_anual`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  ADD PRIMARY KEY (`id_promocion_automatica`),
  ADD KEY `fk_promocion_automatica_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_category` (`id_category`);

--
-- Indices de la tabla `sw_quien_inserta_comp`
--
ALTER TABLE `sw_quien_inserta_comp`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD PRIMARY KEY (`id_representante`),
  ADD KEY `fk_representante_estudiante_idx` (`id_estudiante`);

--
-- Indices de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  ADD PRIMARY KEY (`id_rubrica_club`);

--
-- Indices de la tabla `sw_rubrica_docente`
--
ALTER TABLE `sw_rubrica_docente`
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_docente` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  ADD PRIMARY KEY (`id_rubrica_estudiante`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_paralelo` (`id_paralelo`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_rubrica_personalizada` (`id_rubrica_personalizada`);

--
-- Indices de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD PRIMARY KEY (`id_rubrica_evaluacion`),
  ADD KEY `id_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD KEY `id_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  ADD PRIMARY KEY (`id_rubrica_personalizada`),
  ADD KEY `id_rubrica_evaluacion` (`id_rubrica_evaluacion`,`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paralelo` (`id_paralelo`);

--
-- Indices de la tabla `sw_supletorios_temp`
--
ALTER TABLE `sw_supletorios_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  ADD PRIMARY KEY (`id_tema`),
  ADD KEY `id_foro` (`id_foro`);

--
-- Indices de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  ADD PRIMARY KEY (`id_tipo_aporte`);

--
-- Indices de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  ADD PRIMARY KEY (`id_tipo_asignatura`);

--
-- Indices de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- Indices de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD PRIMARY KEY (`id_tipo_educacion`),
  ADD KEY `sw_tipo_educacion_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  ADD PRIMARY KEY (`id_tipo_periodo`);

--
-- Indices de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_perfil` (`id_perfil`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Indices de la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD PRIMARY KEY (`id_usuario`,`id_perfil`),
  ADD KEY `sw_usuario_perfil_ibfk_2` (`id_perfil`);

--
-- Indices de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD PRIMARY KEY (`id_valor_mes`),
  ADD KEY `id_periodo_lectivo` (`id_periodo_lectivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  MODIFY `id_aporte_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT de la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  MODIFY `id_aporte_paralelo_cierre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1627;

--
-- AUTO_INCREMENT de la tabla `sw_area`
--
ALTER TABLE `sw_area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT de la tabla `sw_asignatura_curso`
--
ALTER TABLE `sw_asignatura_curso`
  MODIFY `id_asignatura_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1663;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  MODIFY `id_asistencia_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17872;

--
-- AUTO_INCREMENT de la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  MODIFY `id_asistencia_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11997;

--
-- AUTO_INCREMENT de la tabla `sw_asociar_curso_superior`
--
ALTER TABLE `sw_asociar_curso_superior`
  MODIFY `id_asociar_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT de la tabla `sw_club`
--
ALTER TABLE `sw_club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_club_docente`
--
ALTER TABLE `sw_club_docente`
  MODIFY `id_club_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `sw_comentario`
--
ALTER TABLE `sw_comentario`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_inspector`
--
ALTER TABLE `sw_comportamiento_inspector`
  MODIFY `id_comportamiento_inspector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3293;

--
-- AUTO_INCREMENT de la tabla `sw_comportamiento_tutor`
--
ALTER TABLE `sw_comportamiento_tutor`
  MODIFY `id_comportamiento_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `sw_cuestionario`
--
ALTER TABLE `sw_cuestionario`
  MODIFY `id_cuestionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `sw_curso_superior`
--
ALTER TABLE `sw_curso_superior`
  MODIFY `id_curso_superior` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sw_def_genero`
--
ALTER TABLE `sw_def_genero`
  MODIFY `id_def_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_def_nacionalidad`
--
ALTER TABLE `sw_def_nacionalidad`
  MODIFY `id_def_nacionalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  MODIFY `id_dia_semana` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  MODIFY `id_distributivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2044;

--
-- AUTO_INCREMENT de la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  MODIFY `id_equivalencia_supletorios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `sw_escala_calificaciones`
--
ALTER TABLE `sw_escala_calificaciones`
  MODIFY `id_escala_calificaciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `sw_escala_comportamiento`
--
ALTER TABLE `sw_escala_comportamiento`
  MODIFY `id_escala_comportamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_escala_proyectos`
--
ALTER TABLE `sw_escala_proyectos`
  MODIFY `id_escala_proyectos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_escala_supletorios`
--
ALTER TABLE `sw_escala_supletorios`
  MODIFY `id_escala_supletorios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  MODIFY `id_especialidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3677;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_club`
--
ALTER TABLE `sw_estudiante_club`
  MODIFY `id_estudiante_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  MODIFY `id_estudiante_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6767;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3938;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_anual`
--
ALTER TABLE `sw_estudiante_prom_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7906;

--
-- AUTO_INCREMENT de la tabla `sw_estudiante_prom_quimestral`
--
ALTER TABLE `sw_estudiante_prom_quimestral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6075;

--
-- AUTO_INCREMENT de la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  MODIFY `id_evaluacion_subnivel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_exam_category`
--
ALTER TABLE `sw_exam_category`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_exam_results`
--
ALTER TABLE `sw_exam_results`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  MODIFY `id_feriado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `sw_foro`
--
ALTER TABLE `sw_foro`
  MODIFY `id_foro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  MODIFY `id_horario_def` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_horario_examen`
--
ALTER TABLE `sw_horario_examen`
  MODIFY `id_horario_examen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_clase`
--
ALTER TABLE `sw_hora_clase`
  MODIFY `id_hora_clase` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  MODIFY `id_hora_dia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_inasistencia`
--
ALTER TABLE `sw_inasistencia`
  MODIFY `id_inasistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_institucion`
--
ALTER TABLE `sw_institucion`
  MODIFY `id_institucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_insumo`
--
ALTER TABLE `sw_insumo`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sw_jornada`
--
ALTER TABLE `sw_jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  MODIFY `id_leccionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  MODIFY `id_malla_curricular` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1333;

--
-- AUTO_INCREMENT de la tabla `sw_menu`
--
ALTER TABLE `sw_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT de la tabla `sw_modalidad`
--
ALTER TABLE `sw_modalidad`
  MODIFY `id_modalidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sw_notificacion`
--
ALTER TABLE `sw_notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  MODIFY `id_paralelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_asignatura`
--
ALTER TABLE `sw_paralelo_asignatura`
  MODIFY `id_paralelo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1186;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_inspector`
--
ALTER TABLE `sw_paralelo_inspector`
  MODIFY `id_paralelo_inspector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT de la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  MODIFY `id_paralelo_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=289;

--
-- AUTO_INCREMENT de la tabla `sw_perfil`
--
ALTER TABLE `sw_perfil`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_estado`
--
ALTER TABLE `sw_periodo_estado`
  MODIFY `id_periodo_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  MODIFY `id_periodo_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  MODIFY `id_periodo_evaluacion_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=770;

--
-- AUTO_INCREMENT de la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  MODIFY `id_periodo_lectivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `sw_permiso`
--
ALTER TABLE `sw_permiso`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `sw_plan_rubrica`
--
ALTER TABLE `sw_plan_rubrica`
  MODIFY `id_plan_rubrica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_promedio_anual`
--
ALTER TABLE `sw_promedio_anual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1460;

--
-- AUTO_INCREMENT de la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  MODIFY `id_promocion_automatica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `sw_quien_inserta_comp`
--
ALTER TABLE `sw_quien_inserta_comp`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `sw_respuesta`
--
ALTER TABLE `sw_respuesta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_club`
--
ALTER TABLE `sw_rubrica_club`
  MODIFY `id_rubrica_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5612;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_estudiante`
--
ALTER TABLE `sw_rubrica_estudiante`
  MODIFY `id_rubrica_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=931849;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  MODIFY `id_rubrica_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT de la tabla `sw_rubrica_personalizada`
--
ALTER TABLE `sw_rubrica_personalizada`
  MODIFY `id_rubrica_personalizada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sw_supletorios_temp`
--
ALTER TABLE `sw_supletorios_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT de la tabla `sw_tarea`
--
ALTER TABLE `sw_tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `sw_tema`
--
ALTER TABLE `sw_tema`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_aporte`
--
ALTER TABLE `sw_tipo_aporte`
  MODIFY `id_tipo_aporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_asignatura`
--
ALTER TABLE `sw_tipo_asignatura`
  MODIFY `id_tipo_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_documento`
--
ALTER TABLE `sw_tipo_documento`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  MODIFY `id_tipo_educacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `sw_tipo_periodo`
--
ALTER TABLE `sw_tipo_periodo`
  MODIFY `id_tipo_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sw_usuario`
--
ALTER TABLE `sw_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=610;

--
-- AUTO_INCREMENT de la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  MODIFY `id_valor_mes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `sw_aporte_evaluacion`
--
ALTER TABLE `sw_aporte_evaluacion`
  ADD CONSTRAINT `sw_aporte_evaluacion_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`);

--
-- Filtros para la tabla `sw_aporte_paralelo_cierre`
--
ALTER TABLE `sw_aporte_paralelo_cierre`
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_aporte_paralelo_cierre_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_asignatura`
--
ALTER TABLE `sw_asignatura`
  ADD CONSTRAINT `sw_asignatura_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `sw_area` (`id_area`);

--
-- Filtros para la tabla `sw_asistencia_estudiante`
--
ALTER TABLE `sw_asistencia_estudiante`
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_1` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_asistencia_estudiante_ibfk_2` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`);

--
-- Filtros para la tabla `sw_asistencia_tutor`
--
ALTER TABLE `sw_asistencia_tutor`
  ADD CONSTRAINT `fk_asistencia_tutor_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `fk_asistencia_tutor_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `id_asistencia_tutor_inasistencia` FOREIGN KEY (`id_inasistencia`) REFERENCES `sw_inasistencia` (`id_inasistencia`);

--
-- Filtros para la tabla `sw_choices`
--
ALTER TABLE `sw_choices`
  ADD CONSTRAINT `sw_choices_ibfk_1` FOREIGN KEY (`id_question`) REFERENCES `sw_questions` (`id`);

--
-- Filtros para la tabla `sw_config_rangos_supletorios`
--
ALTER TABLE `sw_config_rangos_supletorios`
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`),
  ADD CONSTRAINT `sw_config_rangos_supletorios_ibfk_2` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_cuestionario_paralelo`
--
ALTER TABLE `sw_cuestionario_paralelo`
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `sw_exam_category` (`id`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_cuestionario_paralelo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_curso`
--
ALTER TABLE `sw_curso`
  ADD CONSTRAINT `sw_curso_especialidad` FOREIGN KEY (`id_especialidad`) REFERENCES `sw_especialidad` (`id_especialidad`);

--
-- Filtros para la tabla `sw_dia_semana`
--
ALTER TABLE `sw_dia_semana`
  ADD CONSTRAINT `sw_dia_semana_ibfk_1` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`);

--
-- Filtros para la tabla `sw_distributivo`
--
ALTER TABLE `sw_distributivo`
  ADD CONSTRAINT `sw_distributivo_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_distributivo_ibfk_2` FOREIGN KEY (`id_malla_curricular`) REFERENCES `sw_malla_curricular` (`id_malla_curricular`),
  ADD CONSTRAINT `sw_distributivo_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_distributivo_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_distributivo_ibfk_5` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_equivalencia_supletorios`
--
ALTER TABLE `sw_equivalencia_supletorios`
  ADD CONSTRAINT `sw_equivalencia_supletorios_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_especialidad`
--
ALTER TABLE `sw_especialidad`
  ADD CONSTRAINT `sw_especialidad_tipo_educacion` FOREIGN KEY (`id_tipo_educacion`) REFERENCES `sw_tipo_educacion` (`id_tipo_educacion`);

--
-- Filtros para la tabla `sw_estudiante`
--
ALTER TABLE `sw_estudiante`
  ADD CONSTRAINT `sw_estudiante_ibfk_1` FOREIGN KEY (`id_def_genero`) REFERENCES `sw_def_genero` (`id_def_genero`),
  ADD CONSTRAINT `sw_estudiante_ibfk_2` FOREIGN KEY (`id_def_nacionalidad`) REFERENCES `sw_def_nacionalidad` (`id_def_nacionalidad`),
  ADD CONSTRAINT `sw_estudiante_ibfk_3` FOREIGN KEY (`id_tipo_documento`) REFERENCES `sw_tipo_documento` (`id_tipo_documento`);

--
-- Filtros para la tabla `sw_estudiante_periodo_lectivo`
--
ALTER TABLE `sw_estudiante_periodo_lectivo`
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_2` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_estudiante_periodo_lectivo_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_estudiante_promedio_parcial`
--
ALTER TABLE `sw_estudiante_promedio_parcial`
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_2` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_estudiante_promedio_parcial_ibfk_3` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`);

--
-- Filtros para la tabla `sw_evaluacion_subnivel`
--
ALTER TABLE `sw_evaluacion_subnivel`
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_2` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_4` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`),
  ADD CONSTRAINT `sw_evaluacion_subnivel_ibfk_5` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`);

--
-- Filtros para la tabla `sw_feriado`
--
ALTER TABLE `sw_feriado`
  ADD CONSTRAINT `sw_feriado_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_horario`
--
ALTER TABLE `sw_horario`
  ADD CONSTRAINT `sw_horario_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_horario_ibfk_2` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`),
  ADD CONSTRAINT `sw_horario_ibfk_3` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `sw_horario_ibfk_4` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `sw_horario_ibfk_5` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`),
  ADD CONSTRAINT `sw_horario_ibfk_6` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`);

--
-- Filtros para la tabla `sw_horario_def`
--
ALTER TABLE `sw_horario_def`
  ADD CONSTRAINT `sw_horario_def_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_hora_dia`
--
ALTER TABLE `sw_hora_dia`
  ADD CONSTRAINT `sw_hora_dia_ibfk_1` FOREIGN KEY (`id_dia_semana`) REFERENCES `sw_dia_semana` (`id_dia_semana`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_2` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sw_hora_dia_ibfk_3` FOREIGN KEY (`id_horario_def`) REFERENCES `sw_horario_def` (`id_horario_def`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_leccionario`
--
ALTER TABLE `sw_leccionario`
  ADD CONSTRAINT `fk_leccionario_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `fk_leccionario_hora_clase` FOREIGN KEY (`id_hora_clase`) REFERENCES `sw_hora_clase` (`id_hora_clase`),
  ADD CONSTRAINT `fk_leccionario_paralelo` FOREIGN KEY (`id_paralelo`) REFERENCES `sw_paralelo` (`id_paralelo`),
  ADD CONSTRAINT `fk_leccionario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`);

--
-- Filtros para la tabla `sw_malla_curricular`
--
ALTER TABLE `sw_malla_curricular`
  ADD CONSTRAINT `sw_malla_curricular_ibfk_1` FOREIGN KEY (`id_asignatura`) REFERENCES `sw_asignatura` (`id_asignatura`),
  ADD CONSTRAINT `sw_malla_curricular_ibfk_3` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_menu_perfil`
--
ALTER TABLE `sw_menu_perfil`
  ADD CONSTRAINT `fk_menu_perfil_menu` FOREIGN KEY (`id_menu`) REFERENCES `sw_menu` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_menu_perfil_perfil` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_paralelo`
--
ALTER TABLE `sw_paralelo`
  ADD CONSTRAINT `sw_paralelo_curso` FOREIGN KEY (`id_curso`) REFERENCES `sw_curso` (`id_curso`),
  ADD CONSTRAINT `sw_paralelo_jornada` FOREIGN KEY (`id_jornada`) REFERENCES `sw_jornada` (`id_jornada`),
  ADD CONSTRAINT `sw_paralelo_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_paralelo_tutor`
--
ALTER TABLE `sw_paralelo_tutor`
  ADD CONSTRAINT `sw_paralelo_tutor_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_periodo_evaluacion`
--
ALTER TABLE `sw_periodo_evaluacion`
  ADD CONSTRAINT `fk_periodo_evaluacion_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`),
  ADD CONSTRAINT `fk_periodo_evaluacion_tipo_periodo` FOREIGN KEY (`id_tipo_periodo`) REFERENCES `sw_tipo_periodo` (`id_tipo_periodo`);

--
-- Filtros para la tabla `sw_periodo_evaluacion_curso`
--
ALTER TABLE `sw_periodo_evaluacion_curso`
  ADD CONSTRAINT `sw_periodo_evaluacion_curso_ibfk_1` FOREIGN KEY (`id_periodo_evaluacion`) REFERENCES `sw_periodo_evaluacion` (`id_periodo_evaluacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sw_periodo_lectivo`
--
ALTER TABLE `sw_periodo_lectivo`
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_1` FOREIGN KEY (`id_modalidad`) REFERENCES `sw_modalidad` (`id_modalidad`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_2` FOREIGN KEY (`id_periodo_estado`) REFERENCES `sw_periodo_estado` (`id_periodo_estado`),
  ADD CONSTRAINT `sw_periodo_lectivo_ibfk_3` FOREIGN KEY (`quien_inserta_comp_id`) REFERENCES `sw_quien_inserta_comp` (`id`);

--
-- Filtros para la tabla `sw_promocion_automatica`
--
ALTER TABLE `sw_promocion_automatica`
  ADD CONSTRAINT `fk_promocion_automatica_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_questions`
--
ALTER TABLE `sw_questions`
  ADD CONSTRAINT `sw_questions_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `sw_exam_category` (`id`);

--
-- Filtros para la tabla `sw_representante`
--
ALTER TABLE `sw_representante`
  ADD CONSTRAINT `sw_representante_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `sw_estudiante` (`id_estudiante`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sw_rubrica_evaluacion`
--
ALTER TABLE `sw_rubrica_evaluacion`
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_1` FOREIGN KEY (`id_aporte_evaluacion`) REFERENCES `sw_aporte_evaluacion` (`id_aporte_evaluacion`),
  ADD CONSTRAINT `sw_rubrica_evaluacion_ibfk_2` FOREIGN KEY (`id_tipo_asignatura`) REFERENCES `sw_tipo_asignatura` (`id_tipo_asignatura`);

--
-- Filtros para la tabla `sw_tipo_educacion`
--
ALTER TABLE `sw_tipo_educacion`
  ADD CONSTRAINT `sw_tipo_educacion_periodo_lectivo` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);

--
-- Filtros para la tabla `sw_usuario_perfil`
--
ALTER TABLE `sw_usuario_perfil`
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `sw_usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `sw_usuario_perfil_ibfk_2` FOREIGN KEY (`id_perfil`) REFERENCES `sw_perfil` (`id_perfil`);

--
-- Filtros para la tabla `sw_valor_mes`
--
ALTER TABLE `sw_valor_mes`
  ADD CONSTRAINT `sw_valor_mes_ibfk_1` FOREIGN KEY (`id_periodo_lectivo`) REFERENCES `sw_periodo_lectivo` (`id_periodo_lectivo`);
