<style>
	table {
		border: none;
	}

	.text-right {
		text-align: right;
	}

	.text-left {
		text-align: left;
	}

	.text-center {
		text-align: center;
	}
</style>

<div class="content-wrapper">
	<!-- Main content -->
	<section class="content">
		<div id="titulo_pagina">
			<?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
		</div>
		<div id="barra_principal">
			<table id="tabla_navegacion" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%" class="fuente9" class="text-right">
						<div class="text-right">
							Paralelo: &nbsp;
						</div>
					</td>
					<td width="15%">
						<select id="cboParalelos" class="fuente8">
							<option value="0"> Seleccione... </option>
						</select>
					</td>
					<td width="5%" class="fuente9" class="text-right">
						<div class="text-right">
							Aporte:&nbsp;
						</div>
					</td>
					<td width="15%">
						<select id="cboAportesEvaluacion" class="fuente8">
							<option value="0"> Seleccione... </option>
						</select>
					</td>
					<td width="5%" class="fuente9"> &nbsp;Asignatura:&nbsp; </td>
					<td width="15%">
						<select id="cboAsignaturas" class="fuente8">
							<option value="0"> Seleccione... </option>
						</select>
					</td>
					<td width="*">&nbsp; </td>
				</tr>
			</table>
			<input id="numero_pagina" type="hidden" value="1" />
		</div>
		<div id="pag_nomina_estudiantes">
			<!-- Aqui va la paginacion de los estudiantes encontrados -->
			<div id="total_registros_estudiantes" class="paginacion">
				<table class="fuente8" width="100%" cellspacing=4 cellpadding=0>
					<tr>
						<td>
							<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes encontrados:&nbsp;</div>
						</td>
						<td>
							<div id="paginacion_estudiantes">
								<!-- Aqui va la paginacion de estudiantes -->
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
			<div class="cabeceraTabla">
				<table class="fuente8" width="100%" cellspacing=0 cellpadding=0>
					<tr class="cabeceraTabla">
						<td width="5%">Nro.</td>
						<td width="5%">Id.</td>
						<td width="30%" class="text-left">N&oacute;mina</td>
						<td width="60%" class="text-left">
							<div id="txt_rubricas">Calificaciones</div>
						</td>
						<!-- <td width="18%" align="center">Acciones</td> -->
					</tr>
				</table>
			</div>
			<div id="img_loader" style="display:none;text-align:center">
				<img src="imagenes/ajax-loader.gif" alt="Procesando...">
			</div>
			<div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
			<input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
		</div>
		<div id="mensaje" class="error" class="text-center"> </div>
	</section>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		cargarParalelos();
		$("#cboParalelos").change(function(e) {
			e.preventDefault();
			cargarPeriodosEvaluacion($(this).val());
			cargarAsignaturas($(this).val());
			$("#lista_estudiantes_paralelo").hide();
			$("#mensaje").html("Debe elegir una asignatura...");
		});
		$("#cboAportesEvaluacion").change(function(e) {
			e.preventDefault();
			// Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
			var codigos = $("#cboAportesEvaluacion").val();
			var array_codigos = codigos.split("*");

			var id_periodo_evaluacion = array_codigos[0];
			var id_aporte_evaluacion = array_codigos[1];
			seleccionarAporteEvaluacion(id_aporte_evaluacion);
		});
		$("#cboAsignaturas").change(function(e) {
			e.preventDefault();
			// Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
			var codigos = $("#cboAportesEvaluacion").val();
			var array_codigos = codigos.split("*");

			var id_periodo_evaluacion = array_codigos[0];
			var id_aporte_evaluacion = array_codigos[1];
			seleccionarAporteEvaluacion(id_aporte_evaluacion);
		});
		$("#mensaje").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
	});

	function cargarPeriodosEvaluacion(id_paralelo) {
		$.ajax({
			url: "calificaciones/cargar_aportes_evaluacion_paralelo.php",
			method: "post",
			data: {
				id_paralelo: id_paralelo
			},
			dataType: "html",
			success: function(resultado) {
				// console.log(resultado);
				$('#cboAportesEvaluacion option').remove();
				$('#cboAportesEvaluacion optgroup').remove();
				$("#cboAportesEvaluacion").append(resultado);
			},
			error: function(jqXHR, exception) {
				var msg = '';
				if (jqXHR.status === 0) {
					msg = 'Not connect.\n Verify Network.';
				} else if (jqXHR.status == 404) {
					msg = 'Requested page not found. [404]';
				} else if (jqXHR.status == 500) {
					msg = 'Internal Server Error [500].';
				} else if (exception === 'parsererror') {
					msg = 'Requested JSON parse failed.';
				} else if (exception === 'timeout') {
					msg = 'Time out error.';
				} else if (exception === 'abort') {
					msg = 'Ajax request aborted.';
				} else {
					msg = 'Uncaught Error.\n' + jqXHR.responseText;
				}
				console.log(msg);
			}
		});
	}

	function cargarParalelos() {
		$.get("scripts/cargar_paralelos_especialidad.php", {},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$("#cboParalelos").append(resultado);
				}
			}
		);
	}

	function cargarAsignaturas(id_paralelo) {
		$.post("scripts/cargar_asignaturas_por_paralelo.php", {
				id_paralelo: id_paralelo
			},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					document.getElementById("cboAsignaturas").length = 1;
					$("#cboAsignaturas").append(resultado);
				}
			}
		);
	}

	function listarAportesEvaluacion() {
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		$.post("calificaciones/listado_aportes_evaluacion.php", {
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$("#lista_periodos_evaluacion").html(resultado);
				}
			}
		);
	}

	function seleccionarAporteEvaluacion(id_aporte_evaluacion) {
		// Aqui va el codigo para presentar las calificaciones por aporte de evaluacion, asignatura y paralelo
		// Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
		var codigos = $("#cboAportesEvaluacion").val();
		var array_codigos = codigos.split("*");

		var id_periodo_evaluacion = array_codigos[0];
		var id_aporte_evaluacion = array_codigos[1];

		var id_paralelo = $("#cboParalelos").val();
		var id_asignatura = $("#cboAsignaturas").val();

		document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
		if (id_aporte_evaluacion == "") {
			$("#mensaje").html("No se ha pasado el par&aacute;metro (id_aporte_evaluacion)...");
		} else if (id_paralelo == 0) {
			$("#mensaje").html("Debe elegir un paralelo...");
			$("#cboParalelos").focus();
		} else if (id_asignatura == 0) {
			$("#mensaje").html("Debe elegir una asignatura...");
			$("#cboAsignaturas").focus();
		} else {
			mostrarTitulosRubricas(id_aporte_evaluacion);
			// Aqui va la llamada con AJAX al procedimiento que desplegara las calificaciones
			cargarEstudiantesParalelo(id_paralelo, id_asignatura);
		}
	}

	function mostrarTitulosRubricas(id_aporte_evaluacion) {
		// Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
		var codigos = $("#cboAportesEvaluacion").val();
		var array_codigos = codigos.split("*");

		var id_periodo_evaluacion = array_codigos[0];
		var id_aporte_evaluacion = array_codigos[1];

		var id_asignatura = $("#cboAsignaturas").val();
		var id_paralelo = $("#cboParalelos").val();
		$.ajax({
			type: "post",
			url: "scripts/obtener_id_curso_paralelo.php",
			data: {
				id_paralelo: id_paralelo
			},
			success: function(resultado) {
				var id_curso = resultado;
				$.post("calificaciones/mostrar_titulos_rubricas.php", {
						id_periodo_evaluacion: id_periodo_evaluacion,
						id_aporte_evaluacion: id_aporte_evaluacion,
						alineacion: "left",
						id_asignatura: id_asignatura,
						id_curso: id_curso
					},
					function(resultado) {
						if (resultado == false) {
							alert("Error");
						} else {
							$("#txt_rubricas").html(resultado);
						}
					}
				);
			},
			error: function(xhr, textStatus, error) {
				alert(xhr.responseText);
			}
		});
	}

	function cargarEstudiantesParalelo(id_paralelo, id_asignatura) {
		contarEstudiantesParalelo(id_paralelo, id_asignatura); //Esta funcion desencadena las demas funciones de paginacion 
	}

	function contarEstudiantesParalelo(id_paralelo, id_asignatura) {
		$.post("calificaciones/contar_estudiantes_paralelo.php", {
				id_paralelo: id_paralelo
			},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					var JSONNumRegistrosEstudiantes = eval('(' + resultado + ')');
					var total_registros = JSONNumRegistrosEstudiantes.num_registros;
					$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados: " + total_registros);
					listarEstudiantesParalelo(id_paralelo, id_asignatura);
				}
			}
		);
	}

	function listarEstudiantesParalelo(id_paralelo, id_asignatura) {
		// Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
		var codigos = $("#cboAportesEvaluacion").val();
		var array_codigos = codigos.split("*");

		var id_periodo_evaluacion = array_codigos[0];
		var id_aporte_evaluacion = array_codigos[1];

		$("#form_rubrica_estudiante").css("display", "none");
		$("#mensaje").html("");
		$("#img_loader").show();
		$("#lista_estudiantes_paralelo").hide();
		// Primero obtengo el id_curso asociado al id_paralelo actual
		$.ajax({
			url: "tutores/obtener_id_curso.php",
			method: "post",
			data: {
				id_paralelo: id_paralelo
			},
			type: "json",
			success: function(resultado) {
				// Obtengo el id_curso en formato json
				var id_curso = JSON.parse(resultado).id_curso;
				$.ajax({
					url: "calificaciones/listar_estudiantes_paralelo_tutor.php",
					method: "post",
					data: {
						id_curso: id_curso,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: id_aporte_evaluacion,
						id_periodo_evaluacion: id_periodo_evaluacion
					},
					type: "html",
					success: function(resultado) {
						console.log(resultado);
						$("#img_loader").hide();
						$("#lista_estudiantes_paralelo").show();
						$("#lista_estudiantes_paralelo").html(resultado);
					},
					error: function(xhr, status, error) {
						console.log(xhr.responseText);
					}
				});
			},
			error: function(xhr, status, error) {
				console.log(xhr.responseText);
			}
		});
	}
</script>