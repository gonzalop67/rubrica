<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Definir Horarios
		</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box box-solid">
			<div class="box-body">
				<table class="tabla_navegacion" cellpadding="0" cellspacing="0">
					<tr>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td colspan="3"><span class="fuente9">&nbsp;Horario:&nbsp;</span>
							<select id="cboHorarios" class="fuente9" required autofocus>
								<option value="0"> Seleccione... </option>
							</select>
						</td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td colspan="3"><span class="fuente9">&nbsp;Paralelos:&nbsp;</span>
							<select id="cboParalelos" class="fuente9">
								<option value="0"> Seleccione... </option>
							</select>
						</td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td valign="top">
							<div id="asociar"> <a href="#" class="btn btn-success btn-xs"> Asociar </a> </div>
						</td>
						<td width="*">&nbsp; </td> <!-- Esto es para igualar las columnas -->
					</tr>
					<tr>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td><span class="fuente9">&nbsp;D&iacute;as:</span></td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td><span class="fuente9">&nbsp;Horas Clase:</span></td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td><span class="fuente9">&nbsp;Asignaturas:</span></td>
						<td width="*">&nbsp; </td> <!-- Esto es para igualar las columnas -->
					</tr>
					<tr>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td> <select id="lstDiasSemana" class="fuente9" size="7"> </select> </td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td> <select id="lstHorasClase" class="fuente9" size="7"> </select> </td>
						<td class="fuente9" valign="top">&nbsp;</td>
						<td valign="top"><select id="lstAsignaturas" class="fuente9" size="7"> </select></td>
						<td width="*">&nbsp; </td> <!-- Esto es para igualar las columnas -->
					</tr>
				</table>
				<div id="mensaje" class="error"></div>
				<div id="pag_asociacion">
					<!-- Aqui va la paginacion del horario semanal del paralelo elegido -->
					<div id="titulo_dia" class="header2" style="margin-top:2px;"> HORARIO DIARIO </div>
					<div id="tabla" class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th width="15%" class="text-left">
										<input id="check_all" type="checkbox" style="vertical-align: middle;">
										<button type="button" name="delete_all" id="delete_all" class="btn btn-danger btn-xs">Eliminar</button>
									</th>
									<th width="15%" class="text-left">Hora Clase</th>
									<th width="35%" class="text-left">Asignatura</th>
									<th width="35%" class="text-left">Docente</th>
								</tr>
							</thead>
							<tbody id="lista_asignaturas_asociadas">

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		cargar_paralelos();
		// cargarDiasSemana();
		cargarHorarios();

		$("#lista_asignaturas_asociadas").html("<tr><td colspan='4' class='text-center'>Debe seleccionar un paralelo... </td></tr>");

		$("#cboHorarios").change(function() {
			var id_horario_def = $(this).val();
			if (id_horario_def == "") {
				Swal.fire({
					title: Error,
					text: "Debe elegir un horario...",
					icon: "error"
				});
			} else {
				$("#lista_asignaturas_asociadas").html("");
				cargarDiasSemana();
			}
		});

		//If check_all checked then check all table rows
		$("#check_all").on("click", function() {
			if ($("input:checkbox").prop("checked")) {
				$("input:checkbox[name='row-check']").prop("checked", true);
			} else {
				$("input:checkbox[name='row-check']").prop("checked", false);
			}
		});
		// Check each table row checkbox
		$("input:checkbox[name='row-check']").on("change", function() {
			var total_check_boxes = $("input:checkbox[name='row-check']").length;
			var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;

			// If all checked manually then check check_all checkbox
			if (total_check_boxes === total_checked_boxes) {
				$("#check_all").prop("checked", true);
			} else {
				$("#check_all").prop("checked", false);
			}
		});
		$('#delete_all').click(function() {
			var checkbox = $('.delete_checkbox:checked');
			if (checkbox.length > 0) {
				var checkbox_value = [];
				$(checkbox).each(function() {
					checkbox_value.push($(this).val());
				});

				$.ajax({
					url: "horarios/eliminar_asociacion.php",
					method: "POST",
					data: {
						checkbox_value: checkbox_value
					},
					success: function() {
						listar_asignaturas_asociadas();
						var total_check_boxes = $("input:checkbox[name='row-check']").length;
						var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;
						//alert(total_checked_boxes);
						if (total_check_boxes === total_checked_boxes) {
							$("#check_all").prop("checked", false);
						}
					}
				});
			} else {
				alert("Seleccione al menos un registro");
			}
		});
		$("#cboParalelos").change(function(e) {
			e.preventDefault();
			if ($(this).val == "") {
				$("#lista_asignaturas_asociadas").html("<tr><td colspan='4' class='text-center'>Debe seleccionar un paralelo... </td></tr>");
			} else {
				$("#lista_asignaturas_asociadas").html("<tr><td colspan='4' class='text-center'>Debe seleccionar un día de la semana... </td></tr>");
				cargar_asignaturas_asociadas();
				listar_asignaturas_asociadas();
			}
		});
		$("#lstDiasSemana").click(function(e) {
			e.preventDefault();
			$("#titulo_dia").html("HORARIO DEL " + $("#lstDiasSemana option:selected").text());
			// cargar_horas_clase();
			listar_asignaturas_asociadas();
		});
		$("#asociar").click(function(e) {
			e.preventDefault();
			asociar_hora_asignatura();
		});
	});

	function cargarHorarios() {
		var request = $.ajax({
			url: "dias_semana/cargar_titulos_horarios.php",
			method: "get",
			dataType: "html"
		});

		request.done(function(data) {
			$("#cboHorarios").append(data);
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

	function cargarDiasSemana() {
		var id_horario_def = $("#cboHorarios").val();

		$.post("scripts/cargar_dias_semana.php", {
				id_horario_def: id_horario_def
			},
			function(resultado) {
				// Validación limpia: verifica si el resultado viene vacío, nulo o falso
				if (!resultado) {
					Swal.fire("Error", "No se pudieron cargar los días de la semana.", "error");
				} else {
					// CORREGIDO: Vaciamos por completo el select eliminando las opciones anteriores
					$("#lstDiasSemana").empty().append(resultado);
				}
			}
		);

		$.post("scripts/cargar_horas_clase.php", {
				id_horario_def: id_horario_def
			},
			function(resultado) {
				if (!resultado) {
					Swal.fire("Error", "No se pudieron cargar las horas de clase.", "error");
				} else {
					// CORREGIDO: Limpia el select, añade la opción inicial y luego inyecta las horas con sus recreos calculados
					$("#lstHorasClase").empty().append(resultado);
				}
			}
		);
	}

	function cargar_asignaturas_asociadas() {
		$.get("horarios/cargar_asignaturas_asociadas.php", {
				id_paralelo: document.getElementById("cboParalelos").value
			},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					document.getElementById("lstAsignaturas").length = 0;
					$('#lstAsignaturas').append(resultado);
				}
			});
	}

	function cargar_horas_clase() {
		var id_dia_semana = document.getElementById("lstDiasSemana").value;
		var id_horario_def = document.getElementById("cboHorarios").value;

		document.getElementById("lstHorasClase").length = 0;

		$.ajax({
			type: "POST",
			url: "scripts/cargar_horas_clase.php",
			data: "id_dia_semana=" + id_dia_semana + "&id_horario_def=" + id_horario_def,
			success: function(resultado) {
				// console.log(resultado);
				$("#lstHorasClase").append(resultado);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
			}
		});
	}

	function listar_asignaturas_asociadas(iDesplegar) {
		var id_paralelo = document.getElementById("cboParalelos").value;
		var id_dia_semana = document.getElementById("lstDiasSemana").value;
		var id_horario_def = document.getElementById("cboHorarios").value;
		if (id_paralelo == 0) {
			$("#lista_horario_diario").html("Debe elegir un paralelo...");
			$("#cboParalelos").focus();
		} else if (id_dia_semana == "") {
			$("#lista_horario_diario").html("Debe elegir un d&iacute;a de la semana...");
			$("#cboParalelos").focus();
		} else {
			$("#lista_horario_diario").html("<img src='imagenes/ajax-loader.gif' alt='procesando...'>");
			$.post("horarios/listar_asignaturas_asociadas.php", {
					id_paralelo: id_paralelo,
					id_dia_semana: id_dia_semana,
					id_horario_def: id_horario_def
				},
				function(resultado) {
					$("#mensaje").html("");
					$("#tabla tbody").html(resultado);
				}
			);
		}
	}

	function asociar_hora_asignatura() {
		// Captura de valores unitarios limpia (ya no devuelven arrays)
		var id_paralelo = $("#cboParalelos").val();
		var id_dia_semana = $("#lstDiasSemana").val();
		var id_asignatura = $("#lstAsignaturas").val();
		var id_hora_clase = $("#lstHorasClase").val();
		var id_horario_def = $("#cboHorarios").val();
		var $mensaje = $("#mensaje");

		// Validaciones de formulario
		if (id_paralelo == 0 || id_paralelo === null) {
			$mensaje.html("Debe elegir un Paralelo...");
			$("#cboParalelos").focus();
		} else if (id_dia_semana === null || id_dia_semana === "") {
			$mensaje.html("Debe elegir un D&iacute;a de la Semana...");
			$("#lstDiasSemana").focus();
		} else if (id_hora_clase === null || id_hora_clase === "") {
			$mensaje.html("Debe elegir una Hora Clase...");
			$("#lstHorasClase").focus();
		} else if (id_asignatura === null || id_asignatura === "") {
			$mensaje.html("Debe elegir una Asignatura...");
			$("#lstAsignaturas").focus();
		} else if (id_horario_def == 0 || id_horario_def === null) {
			$mensaje.html("Debe elegir un Horario...");
			$("#cboHorarios").focus();
		} else {
			// Inicio de procesos AJAX
			$mensaje.hide().html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />").show();

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
						Swal.fire({
							title: "Error",
							text: "Ya existe una Asignatura asociada en esta Hora Clase...",
							icon: "error"
						});
					} else {
						// Comprobar cruce de horario
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
								$mensaje.hide().html("");
								if (JSONResultadoCruce.errorno == 2) {
									Swal.fire({
										title: "Error",
										text: "No se ha designado un docente para la asignatura seleccionada.",
										icon: "error"
									});
								} else if (JSONResultadoCruce.errorno == 1) {
									Swal.fire({
										title: "¿Existe cruce de horario?",
										text: "Existe un cruce de horario asociado con esta Hora Clase. ¿Desea asociar la asignatura de todas formas?",
										icon: "warning",
										showCancelButton: true,
										confirmButtonColor: "#3085d6",
										cancelButtonColor: "#d33",
										confirmButtonText: "Sí, asociar",
										cancelButtonText: "Cancelar"
									}).then((result) => {
										if (result.isConfirmed) {
											ejecutar_insercion(id_paralelo, id_hora_clase, id_asignatura, id_dia_semana, id_horario_def);
										}
									});
								} else {
									ejecutar_insercion(id_paralelo, id_hora_clase, id_asignatura, id_dia_semana, id_horario_def);
								}
							}
						});
					}
				}
			});
		}
	}

	function ejecutar_insercion(paralelo, hora, asignatura, dia, horario) {
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
				$("#mensaje").html(resultado);
				listar_asignaturas_asociadas(false);

				Swal.fire({
					title: "¡Guardado!",
					text: "La asignatura se ha asociado correctamente.",
					icon: "success",
					timer: 2000,
					showConfirmButton: false
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Swal.fire({
					title: "Error de servidor",
					text: xhr.status + " - " + thrownError,
					icon: "error"
				});
			}
		});
	}

	function eliminarHorario(id_horario) {
		if (id_horario == "") {
			document.getElementById("mensaje").innerHTML = "No se ha pasado correctamente el par&aacute;metros id_horario...";
		} else {
			$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$.ajax({
				type: "POST",
				url: "horarios/eliminar_asociacion.php",
				data: "id_horario=" + id_horario,
				success: function(resultado) {
					$("#mensaje").html(resultado);
					listar_asignaturas_asociadas(true);
				}
			});
		}
	}
</script>