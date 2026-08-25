<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>Diseñador de Horarios Visual</h1>
	</section>

	<!-- Main content -->
	<section class="content">
		<!-- selectores principales requeridos -->
		<div class="box box-solid" style="padding: 15px;">
			<label>Horario:</label>
			<!-- MODIFICADO: Se añade onchange nativo -->
			<select id="cboHorarios" class="fuente9" onchange="cambioHorarioGlobal(this.value)">
				<option value="0">Seleccione un horario...</option>
			</select>

			<label style="margin-left:15px;">Paralelo:</label>
			<!-- MODIFICADO: Se añade onchange nativo -->
			<select id="cboParalelos" class="fuente9" onchange="cambioParaleloGlobal(this.value)">
				<option value="0">Seleccione un paralelo...</option>
			</select>
		</div>

		<!-- CONTENEDOR PRINCIPAL: Flexbox garantiza alturas idénticas -->
		<div style="display: flex; gap: 20px; margin-top: 15px; align-items: stretch;">

			<!-- BARRA LATERAL: Asignaturas arrastrables -->
			<div class="box box-solid contenedor-materias-fijo">
				<div class="box-header with-border">
					<h4 class="box-title" style="font-size: 16px; font-weight: bold;">Asignaturas</h4>
					<p class="text-muted" style="margin-bottom: 0;"><small>Arrastra una materia al tablero:</small></p>
				</div>
				<!-- Contenedor interno con scroll dedicado -->
				<div id="lista-asignaturas" class="lista-materias-scroll">
					<p class="text-muted text-center"><small>Selecciona un paralelo para ver sus materias...</small></p>
				</div>
			</div>

			<!-- TABLERO: Matriz de Días y Horas -->
			<div style="width: 80%; display: flex; flex-direction: column;">
				<table class="table table-bordered text-center" style="background: #fff; margin-bottom: 0; height: 100%;">
					<thead>
						<tr style="background: #e9ecef;">
							<th width="15%">Hora / Clase</th>
							<th>Lunes</th>
							<th>Martes</th>
							<th>Miércoles</th>
							<th>Jueves</th>
							<th>Viernes</th>
						</tr>
					</thead>
					<tbody>
						<!-- Las filas se inyectan dinámicamente aquí por tu AJAX -->
					</tbody>
				</table>
			</div>

		</div>

	</section>
</div>
<style>
	/* Estilos visuales para cuando una materia vuela sobre una celda */
	.celda-horario {
		height: 60px;
		background: #fafafa;
		border: 2px dashed #ddd;
		transition: all 0.2s;
		vertical-align: middle !important;
	}

	.celda-horario.drag-over {
		background: #e0f2fe;
		border-color: #38bdf8;
	}

	/* Estilo opcional para que los elementos arrastrables luzcan consistentes */
	.materia-item {
		background: #3c8dbc;
		color: #fff;
		padding: 8px;
		margin-bottom: 5px;
		cursor: move;
		border-radius: 3px;
		user-select: none;
	}

	/* Contenedor de la barra lateral ajustado a flex */
	.contenedor-materias-fijo {
		width: 20%;
		margin-bottom: 0;
		display: flex;
		flex-direction: column;
		background: #f4f4f4;
		border-radius: 5px;
		border: 1px solid #d2d6de;
	}

	/* Encabezado fijo de la barra lateral */
	.contenedor-materias-fijo .box-header {
		background: #fafafa;
		padding: 12px;
		border-bottom: 1px solid #f4f4f4;
	}

	/* CORRECCIÓN DE TAMAÑO: Scroll interno automático acoplado a la tabla */
	.lista-materias-scroll {
		padding: 15px;
		overflow-y: auto;
		flex-grow: 1;
		/* Altura mínima inicial por si la tabla está vacía */
		min-height: 250px;
		/* Altura máxima dinámica calculada en base al tamaño de la pantalla */
		max-height: calc(100vh - 280px);
	}

	/* Opcional: Estilizar la barra de scroll para que luzca más moderna */
	.lista-materias-scroll::-webkit-scrollbar {
		width: 6px;
	}

	.lista-materias-scroll::-webkit-scrollbar-track {
		background: #f1f1f1;
		border-radius: 3px;
	}

	.lista-materias-scroll::-webkit-scrollbar-thumb {
		background: #c1c1c1;
		border-radius: 3px;
	}

	.lista-materias-scroll::-webkit-scrollbar-thumb:hover {
		background: #a8a8a8;
	}

	/* Forzar a la tabla a mantener columnas proporcionales */
	table.table {
		table-layout: fixed !important;
		width: 100% !important;
	}

	/* Configuración de la celda constructora */
	.table-bordered>tbody>tr>td.celda-horario {
		padding: 4px !important;
		vertical-align: stretch !important;
		/* IMPORTANTE: Hace que todas las celdas de la fila se estiren al mismo alto */
		height: 1px;
		/* Truco CSS para que los hijos con height: 100% calculen bien su tamaño */
	}

	/* Tarjeta adaptable que hereda el 100% del alto de la celda más grande */
	.materia-asignada {
		color: #fff;
		background-color: #222d32 !important;
		border-top: 3px solid #3c8dbc;
		border-radius: 4px;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
		padding: 6px 4px;
		height: 100%;
		/* Ocupa todo el alto de la fila estirada */
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		/* Centra el contenido verticalmente en celdas estiradas */
		gap: 4px;
		transition: background-color 0.2s;
	}

	.materia-asignada:hover {
		background-color: #2c3b41 !important;
	}
</style>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var asignaturaArrastrada = null;
		var idAsignaturaArrastrada = null;

		// 1. DRAG START: Al empezar a arrastrar la materia, guardamos sus datos
		$(document).on('dragstart', '.materia-item', function(e) {
			asignaturaArrastrada = $(this).text().trim();
			// CORREGIDO: .attr('data-id') es más fiable para elementos inyectados dinámicamente
			idAsignaturaArrastrada = $(this).attr('data-id');
			$(this).css('opacity', '0.5');
		});

		$(document).on('dragend', '.materia-item', function() {
			$(this).css('opacity', '1');
		});

		// 2. Permitir que las celdas de la matriz acepten elementos encima
		$(document).on('dragover', '.celda-horario', function(e) {
			e.preventDefault(); // Obligatorio para permitir el drop
			$(this).addClass('drag-over');
		});

		$(document).on('dragleave', '.celda-horario', function() {
			$(this).removeClass('drag-over');
		});

		// 3. EVENTO DROP: Cuando sueltas la materia en la celda
		$(document).on('drop', '.celda-horario', function(e) {
			e.preventDefault();
			var $celda = $(this);
			$celda.removeClass('drag-over');

			var id_paralelo = $("#cboParalelos").val();
			var id_horario_def = $("#cboHorarios").val();

			// Busca el punto 3 (EVENTO DROP) en tu JS y pon estas dos líneas de captura:
			var id_dia_semana = $(this).closest('.celda-horario').attr('data-dia');
			var id_hora_clase = $(this).closest('.celda-horario').attr('data-hora');

			var id_asignatura = idAsignaturaArrastrada;
			var nombre_asignatura = asignaturaArrastrada;

			// Control de seguridad en el navegador antes de enviar al servidor
			if (!id_paralelo || id_paralelo == "0" || !id_horario_def || id_horario_def == "0") {
				Swal.fire("Atención", "Por favor seleccione Horario y Paralelo antes de diseñar.", "warning");
				return;
			}

			if (!id_asignatura || id_asignatura === "undefined") {
				Swal.fire("Error", "No se pudo recuperar el ID de la asignatura arrastrada. Intente arrastrar desde el texto.", "error");
				return;
			}

			// Validación AJAX: ¿Existe ya asociación en esa celda?
			$.ajax({
				type: "POST",
				url: "horarios/existe_asociacion.php",
				dataType: "json",
				data: {
					id_paralelo: id_paralelo,
					id_dia_semana: id_dia_semana,
					id_hora_clase: id_hora_clase,
					id_horario_def: id_horario_def
				},
				success: function(JSONResultado) {
					if (JSONResultado.error) {
						Swal.fire("Error", "Esta celda ya tiene una asignatura asignada.", "error");
					} else {
						// Validación AJAX: Comprobar cruce de horario
						$.ajax({
							type: "POST",
							url: "horarios/comprobar_cruce_horario.php",
							dataType: "json",
							data: {
								id_paralelo: id_paralelo,
								id_hora_clase: id_hora_clase,
								id_asignatura: id_asignatura,
								id_dia_semana: id_dia_semana,
								id_horario_def: id_horario_def
							},
							success: function(JSONResultadoCruce) {
								if (JSONResultadoCruce.errorno == 2) {
									Swal.fire("Error", "No se ha designado un docente para esta asignatura.", "error");
								} else if (JSONResultadoCruce.errorno == 1) {
									Swal.fire({
										title: "¿Existe cruce de horario?",
										text: "El docente tiene un cruce en esta hora. ¿Desea asociar de todas formas?",
										icon: "warning",
										showCancelButton: true,
										confirmButtonText: "Sí, asociar",
										cancelButtonText: "Cancelar"
									}).then((result) => {
										if (result.isConfirmed) {
											// LLAMADA CORREGIDA: Aseguramos pasar id_dia_semana correctamente
											ejecutar_insercion_visual($celda, id_paralelo, id_hora_clase, id_asignatura, id_dia_semana, id_horario_def, nombre_asignatura);
										}
									});
								} else {
									ejecutar_insercion_visual($celda, id_paralelo, id_hora_clase, id_asignatura, id_dia_semana, id_horario_def, nombre_asignatura);
								}
							}
						});
					}
				}
			});
		});

		// 4. INSERCIÓN: Guarda en base de datos y pinta en la matriz
		function ejecutar_insercion_visual($celda, paralelo, hora, asignatura, dia, horario, nombre_materia) {
			$.ajax({
				method: "POST",
				url: "horarios/insertar_asociacion.php",
				data: {
					id_paralelo: paralelo,
					id_hora_clase: hora,
					id_asignatura: asignatura,
					id_dia_semana: dia,
					id_horario_def: horario
				},
				success: function(resultado) {
					obtener_y_llenar_matriz();
					Swal.fire({
						title: "¡Asociado!",
						text: "Asignatura colocada correctamente.",
						icon: "success",
						timer: 1500,
						showConfirmButton: false
					});

					if (typeof listar_asignaturas_asociadas === "function") {
						listar_asignaturas_asociadas();
					}
				}
			});
		}

		// 6. ELIMINACIÓN DIRECTA DESDE LA CELDA
		$(document).on('click', '.btn-eliminar-celda', function(e) {
			e.stopPropagation();
			var dia = $(this).data('dia');
			var hora = $(this).data('hora');
			var paralelo = $("#cboParalelos").val();
			var horario = $("#cboHorarios").val();

			Swal.fire({
				title: "¿Retirar asignatura?",
				text: "Se eliminará la asignatura asignada a este casillero.",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: "Sí, quitar",
				cancelButtonText: "Cancelar"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: "horarios/eliminar_asociacion_celda.php",
						method: "POST",
						data: {
							id_paralelo: paralelo,
							id_dia_semana: dia,
							id_hora_clase: hora,
							id_horario_def: horario
						},
						success: function() {
							obtener_y_llenar_matriz();
							if (typeof listar_asignaturas_asociadas === "function") {
								listar_asignaturas_asociadas();
							}
						}
					});
				}
			});
		});

		// --- GESTIÓN DE CHECKBOXES ANTERIORES ---
		$("#check_all").on("click", function() {
			$("input:checkbox[name='row-check']").prop("checked", $(this).prop("checked"));
		});

		$(document).on("change", "input:checkbox[name='row-check']", function() {
			var total_check_boxes = $("input:checkbox[name='row-check']").length;
			var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;
			$("#check_all").prop("checked", total_check_boxes === total_checked_boxes);
		});

		$('#delete_all').click(function() {
			var checkbox = $('.delete_checkbox:checked');

			if (checkbox.length > 0) {
				var checkbox_value = [];

				// Recorrer los checkboxes seleccionados y almacenar sus IDs
				$(checkbox).each(function() {
					checkbox_value.push($(this).val());
				});

				// Petición AJAX para eliminar el lote completo en la base de datos
				$.ajax({
					url: "horarios/eliminar_asociacion.php",
					method: "POST",
					data: {
						checkbox_value: checkbox_value
					},
					success: function() {
						// Actualiza la lista inferior clásica si la función existe
						if (typeof listar_asignaturas_asociadas === "function") {
							listar_asignaturas_asociadas();
						}

						// Refresca la matriz visual para limpiar los bloques borrados
						obtener_y_llenar_matriz();

						// Desmarcar el checkbox maestro de la cabecera
						$("#check_all").prop("checked", false);
					}
				});
			} else {
				alert("Seleccione al menos un registro");
			}
		});

		// --- ENTRADA / INICIALIZACIÓN ---
		cargar_paralelos();
		cargarHorarios();

		// Se dibuja el estado de espera inicial en lugar de llamar a la función que bloqueaba
		$("table.table tbody").html('<tr><td colspan="6" class="text-center text-muted">Seleccione un horario para estructurar el tablero...</td></tr>');
	});

	function cargarHorarios() {
		$.ajax({
			url: "horarios/cargar_titulos_horarios.php",
			method: "get",
			dataType: "html",
			success: function(data) {
				$("#cboHorarios").append(data);
			}
		});
	}

	function cargar_paralelos() {
		$.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
			if (resultado == false) {
				alert("Error");
			} else {
				$('#cboParalelos').append(resultado);
			}
		});
	}

	function cargar_asignaturas_asociadas() {
		var idParalelo = $("#cboParalelos").val();
		if (!idParalelo || idParalelo == "0") return;

		var $contenedor = $('#lista-asignaturas');

		// LIMPIEZA: Muestra un mensaje visual de carga antes de la petición AJAX
		$contenedor.html(
			'<p class="text-muted text-center">' +
			'<i class="fa fa-refresh fa-spin"></i> <small>Cargando materias...</small>' +
			'</p>'
		);

		$.get("horarios/cargar_asignaturas_asociadas.php", {
			id_paralelo: idParalelo
		}, function(resultado) {
			if (resultado == false) {
				$contenedor.html('<p class="text-danger text-center"><small>Error al cargar las materias.</small></p>');
				alert("Error");
			} else {
				// Inyectar el HTML recibido desde el servidor PHP
				$contenedor.html(resultado);

				// GARANTÍA DRAG & DROP: Asegura el atributo obligatorio en cada materia cargada
				$contenedor.find('.materia-item').attr('draggable', 'true');
			}
		});
	}

	// ========================================================
	// FUNCIONES GLOBALES - REFACTORIZADAS Y SINCRONIZADAS
	// ========================================================

	// REGENERAR ESTRUCTURA: Genera la tabla usando los IDs reales de tu colegio (274 al 278)
	function cargar_estructura_horas() {
		var id_horario_def = $("#cboHorarios").val();
		var $tbody = $("table.table tbody");

		if (!id_horario_def || id_horario_def == "0") return;

		$tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Generando cuadrícula de horas...</td></tr>');

		$.ajax({
			url: "horarios/cargar_horas_clase.php",
			method: "GET",
			dataType: "json",
			data: {
				id_horario_def: id_horario_def
			},
			success: function(horas) {
				$tbody.empty();
				if (horas.length === 0) {
					$tbody.html('<tr><td colspan="6" class="text-center text-muted">No existen horas configuradas para este esquema.</td></tr>');
					return;
				}

				horas.forEach(function(hora) {
					// CORREGIDO: data-dia ahora contiene los IDs reales de tu BD (274, 275, 276, 277, 278)
					var filaHtml = '<tr>' +
						'<td class="align-middle"><strong>' + hora.hc_nombre + '</strong><br><small class="text-muted">' + hora.rango_tiempo + '</small></td>' +
						'<td class="celda-horario" style="width: 17%;" data-dia="274" data-hora="' + hora.id_hora_clase + '"></td>' + // Lunes
						'<td class="celda-horario" style="width: 17%;" data-dia="275" data-hora="' + hora.id_hora_clase + '"></td>' + // Martes
						'<td class="celda-horario" style="width: 17%;" data-dia="276" data-hora="' + hora.id_hora_clase + '"></td>' + // Miércoles
						'<td class="celda-horario" style="width: 17%;" data-dia="277" data-hora="' + hora.id_hora_clase + '"></td>' + // Jueves
						'<td class="celda-horario" style="width: 17%;" data-dia="278" data-hora="' + hora.id_hora_clase + '"></td>' + // Viernes
						'</tr>';
					$tbody.append(filaHtml);
				});

				if (typeof obtener_y_llenar_matriz === "function") {
					obtener_y_llenar_matriz();
				}
			},
			error: function() {
				$tbody.html('<tr><td colspan="6" class="text-center text-danger">Error al cargar la estructura horaria.</td></tr>');
			}
		});
	}

	// LLENAR MATRIZ: Lee las coordenadas mapeadas y pinta usando "as_nombre"
	function obtener_y_llenar_matriz() {
		var id_horario = $("#cboHorarios").val();
		var id_paralelo = $("#cboParalelos").val();
		var $tbody = $("table.table tbody");

		$tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Cargando matriz visual...</td></tr>');

		$.ajax({
			type: "POST",
			url: "horarios/cargar_matriz.php", // Llama a tu archivo de backend
			dataType: "json",
			data: {
				id_paralelo: id_paralelo,
				id_horario_def: id_horario
			},
			success: function(response) {
				$tbody.empty();

				if (!response.horas || response.horas.length === 0) {
					$tbody.html('<tr><td colspan="6" class="text-warning text-center">Este horario no tiene bloques de horas configurados.</td></tr>');
					return;
				}

				// 1. Dibujamos las filas basándonos en la definición de horas reales de la BDD
				$.each(response.horas, function(index, hora) {
					var filaHtml = `<tr>
                    <td style="vertical-align: middle; background: #fafafa; font-weight: bold; border-right: 2px solid #ddd;">
                        ${hora.nombre}<br>
                        <small class="text-muted">${hora.hora_inicio.substring(0, 5)} - ${hora.hora_fin.substring(0, 5)}</small>
                    </td>`;

					// 2. Creamos las 5 celdas de la semana (Días 1 al 5)
					for (var dia = 1; dia <= 5; dia++) {
						var contenidoCelda = "";

						// 3. Buscamos si en la lista de clases guardadas hay algo para esta hora y día
						if (response.clases && response.clases.length > 0) {
							$.each(response.clases, function(i, clase) {
								if (clase.id_hora_clase == hora.id_horario_detalle && clase.id_dia_semana == dia) {
									contenidoCelda = `
									<div class="materia-asignada" style="position: relative; padding-right: 15px;">
										<!-- La cruz pequeña en la esquina superior derecha -->
										<button type="button" class="btn-eliminar-celda" 
												data-dia="${dia}" 
												data-hora="${hora.id_horario_detalle}" 
												style="position: absolute; top: 2px; right: 4px; background: transparent; border: 0; color: #ff7675; font-size: 11px; font-weight: bold; padding: 0; line-height: 1;" 
												title="Quitar">&times;</button>
										
										<!-- Textos de la materia -->
										<span style="font-weight: bold; font-size: 11px; line-height: 1.2;">${clase.as_nombre}</span>
										<small style="font-size: 9px; display: block; opacity: 0.8; margin-top: 2px;">${clase.us_shortname}</small>
									</div>`;
									return false; // Romper $.each interno
								}
							});
						}

						// IMPORTANTE: Aquí inyectamos data-hora apuntando al id_horario_detalle real
						filaHtml += `
                        <td class="celda-horario" data-dia="${dia}" data-hora="${hora.id_horario_detalle}">
                            ${contenidoCelda}
                        </td>`;
					}

					filaHtml += `</tr>`;
					$tbody.append(filaHtml);
				});
			},
			error: function() {
				$tbody.html('<tr><td colspan="6" class="text-danger text-center">Error al procesar la matriz.</td></tr>');
			}
		});
	}

	function cambioHorarioGlobal(id_horario) {
		var id_paralelo = $("#cboParalelos").val();

		// Si limpia el horario o selecciona la opción por defecto
		if (!id_horario || id_horario === "0") {
			$("table.table tbody").html('<tr><td colspan="6" class="text-center text-muted">Seleccione un horario para estructurar el tablero...</td></tr>');
			$("#lista-asignaturas").html('<p class="text-muted text-center"><small>Selecciona un paralelo para ver sus materias...</small></p>');
			$("#cboParalelos").val("0");
			return;
		}

		// 1. ¡MÁS DIRECTO!: Estructura la cuadrícula de horas de inmediato, sin importar el paralelo
		cargar_estructura_horas();

		// 2. Si resulta que el paralelo YA estaba seleccionado de antes, refrescamos su contenido
		if (id_paralelo && id_paralelo !== "0") {
			if (typeof cargar_asignaturas_asociadas === "function") {
				cargar_asignaturas_asociadas();
			}
		} else {
			// 3. Si no hay paralelo, dejamos la barra lateral en espera amigable
			$("#lista-asignaturas").html('<p class="text-muted text-center"><small>Selecciona un paralelo para ver sus materias...</small></p>');
		}
	}

	function cambioParaleloGlobal(id_paralelo) {
		var id_horario = $("#cboHorarios").val();

		// Bloqueo de seguridad: No se puede escoger paralelo sin un horario base estructurado
		if (!id_horario || id_horario === "0") {
			Swal.fire("Atención", "Por favor, seleccione primero un Horario para poder cargar el paralelo.", "warning");
			$("#cboParalelos").val("0");
			return;
		}

		// Si limpia el paralelo seleccionado
		if (!id_paralelo || id_paralelo === "0") {
			$("#lista-asignaturas").html('<p class="text-muted text-center"><small>Selecciona un paralelo para ver sus materias...</small></p>');
			$(".celda-horario").html('');
			return;
		}

		// Cargar el tablero en lote si ambos campos son correctos
		obtener_y_llenar_matriz();
		if (typeof cargar_asignaturas_asociadas === "function") {
			cargar_asignaturas_asociadas();
		}
	}

	function cargar_estructura_horas() {
		var id_horario = $("#cboHorarios").val();
		var $tbody = $("table.table tbody");

		$tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Estructurando rangos de horas...</td></tr>');

		$.ajax({
			type: "POST",
			url: "horarios/obtener_solo_horas.php", // Un archivo ligero solo para las filas
			dataType: "json",
			data: {
				id_horario_def: id_horario
			},
			success: function(response) {
				$tbody.empty();

				if (!response || response.length === 0) {
					$tbody.html('<tr><td colspan="6" class="text-warning text-center">Este horario no tiene bloques de horas configurados.</td></tr>');
					return;
				}

				// Dibujamos las filas bases de la matriz vacía
				$.each(response, function(index, hora) {
					var filaHtml = `<tr>
                    <td style="vertical-align: middle; background: #fafafa; font-weight: bold; border-right: 2px solid #ddd;">
                        ${hora.nombre}<br>
                        <small class="text-muted">${hora.hora_inicio.substring(0, 5)} - ${hora.hora_fin.substring(0, 5)}</small>
                    </td>`;

					// Creamos los 5 casilleros de la semana vacíos pero con sus identificadores listos
					for (var dia = 1; dia <= 5; dia++) {
						filaHtml += `<td class="celda-horario" data-dia="${dia}" data-hora="${hora.id_horario_detail}"></td>`;
					}

					filaHtml += `</tr>`;
					$tbody.append(filaHtml);
				});
			},
			error: function() {
				$tbody.html('<tr><td colspan="6" class="text-danger text-center">Error al cargar el esqueleto de horas.</td></tr>');
			}
		});
	}
</script>