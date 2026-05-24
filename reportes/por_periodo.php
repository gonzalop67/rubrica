<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>R&uacute;brica Web 2.0</title>
	<style>
		table {
			border: none;
		}
	</style>
	<script type="text/javascript" src="js/funciones.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			cargarPeriodosEvaluacionPrincipales();
			cargarAsignaturasDocente();
			$("#paginacion_estudiantes").hide();
		});

		function cargarPeriodosEvaluacionPrincipales() {
			$.get("scripts/cargar_periodos_evaluacion_principales.php", {},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#cboPeriodosEvaluacion").append(resultado);
					}
				}
			);
		}

		function cargarAsignaturasDocente() {
			contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
		}

		function contarAsignaturasDocente() {
			$.post("calificaciones/contar_asignaturas_docente.php", {},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						var JSONNumRegistros = eval('(' + resultado + ')');
						var total_registros = JSONNumRegistros.num_registros;
						$("#num_asignaturas").html("N&uacute;mero de Asignaturas encontradas: " + total_registros);
						paginarAsignaturasDocente(4, 1, total_registros);
					}
				}
			);
		}

		function paginarAsignaturasDocente(cantidad_registros, num_pagina, total_registros) {
			$.post("calificaciones/paginar_asignaturas_docente.php", {
					cantidad_registros: cantidad_registros,
					num_pagina: num_pagina,
					total_registros: total_registros
				},
				function(resultado) {
					$("#paginacion_asignaturas").html(resultado);
				}
			);
			listarAsignaturasDocente(num_pagina);
		}

		function listarAsignaturasDocente(numero_pagina) {
			$.post("scripts/cargar_asignaturas_docente.php", {
					cantidad_registros: 4,
					numero_pagina: numero_pagina
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#lista_asignaturas").html(resultado);
					}
				}
			);
		}

		function cargarEstudiantesParalelo(id_paralelo, id_asignatura) {
			contarEstudiantesParalelo(id_paralelo,
				id_asignatura); //Esta funcion desencadena las demas funciones de paginacion 
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
						// paginarEstudiantesParalelo(1, total_registros, id_paralelo, id_asignatura);
					}
				}
			);
		}

		function paginarEstudiantesParalelo(numero_pagina, total_registros, id_paralelo, id_asignatura) {
			$("#paginacion_estudiantes").html("");
			document.getElementById("numero_pagina").value = numero_pagina;
			$.post("calificaciones/paginar_estudiantes_paralelo.php", {
					cantidad_registros: 7,
					numero_pagina: numero_pagina,
					total_registros: total_registros,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura
				},
				function(resultado) {
					$("#paginacion_estudiantes").html(resultado);
				}
			);
			listarEstudiantesParalelo(numero_pagina, id_paralelo, id_asignatura);
		}

		function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo) {
			document.getElementById("id_asignatura").value = id_asignatura;
			document.getElementById("id_paralelo").value = id_paralelo;
			document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			if (id_periodo_evaluacion == 0) {
				$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
				document.getElementById("cboPeriodosEvaluacion").focus();
			} else {
				$("#lista_estudiantes_paralelo").html("");
				$("#tituloNomina").html("CALIFICACIONES SUBPERIODO [" + asignatura + " - " + curso + " " + paralelo + "]");
				contarEstudiantesParalelo(id_paralelo, id_asignatura);
				//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
				obtenerDetallePeriodoEvaluacion(id_paralelo, id_asignatura);
			}
		}

		function obtenerDetallePeriodoEvaluacion(id_paralelo, id_asignatura) {
			//Obtener los titulos de los insumos de evaluacion
			document.getElementById("id_asignatura").value = id_asignatura;
			document.getElementById("id_paralelo").value = id_paralelo;
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
			$("#tbl_notas").html(
				"<div style='text-align: center'><img src='imagenes/ajax-loader.gif' alt='procesando...' /></div>");
			$("#ver_reporte").css("display", "none");
			$.post("reportes/obtener_detalle_periodo_evaluacion.php", {
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado) {
					//console.log(resultado);
					$("#tbl_notas").html(resultado);
					$("#ver_reporte").css("display", "block");
				}
			);
		}

		function listarEstudiantesParalelo(numero_pagina, id_paralelo, id_asignatura) {
			document.getElementById("id_asignatura").value = id_asignatura;
			document.getElementById("id_paralelo").value = id_paralelo;
			document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			$("#lista_estudiantes_paralelo").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			$("#ver_reporte").css("display", "none");
			$.post("reportes/listar_calificaciones_periodo.php", {
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado) {
					$("#lista_estudiantes_paralelo").html(resultado);
					$("#ver_reporte").css("display", "block");
				}
			);
		}
	</script>
</head>

<body>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				<?php echo "REPORTE DE CALIFICACIONES " . $_SESSION['titulo_pagina'] ?>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box box-solid">
				<div class="box-body">
					<div id="barra_principal">
						<table id="tabla_navegacion" cellpadding="0" cellspacing="0">
							<tr>
								<td width="5%" class="fuente9 text-right">Per&iacute;odo:&nbsp; </td>
								<td width="5%">
									<select id="cboPeriodosEvaluacion" class="fuente8">
										<option value="0"> Seleccione... </option>
									</select>
								</td>
								<td width="*">
									<div id="img-loader-principal" class="boton"> </div>
								</td>
							</tr>
						</table>
						<input id="numero_pagina" type="hidden" />
					</div>
					<div id="pag_asignaturas">
						<!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
						<div id="total_registros" class="paginacion">
							<table class="fuente8" width="100%" cellspacing=4 cellpadding=0>
								<tr>
									<td>
										<div id="num_asignaturas">&nbsp;N&uacute;mero de Asignaturas encontradas:&nbsp;</div>
									</td>
									<td>
										<div id="paginacion_asignaturas">
											<!-- Aqui va la paginacion de asignaturas -->
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div class="header2"> LISTA DE ASIGNATURAS ASOCIADAS </div>
						<div class="cabeceraTabla">
							<table class="fuente8" width="100%" cellspacing=0 cellpadding=0>
								<tr class="cabeceraTabla">
									<td width="5%">Nro.</td>
									<td width="39%" class="text-left">Asignatura</td>
									<td width="32%" class="text-left">Curso</td>
									<td width="6%" class="text-left">Paralelo</td>
									<td width="18%" class="text-center">Acciones</td>
								</tr>
							</table>
						</div>
						<div id="lista_asignaturas" class="text-center"> </div>
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
						<div id="tbl_notas" class="table-responsive">
							<!-- Aquí van las notas generadas dinámicamente mediante AJAX -->
						</div>
						<form id="formulario_periodo" target="_blank" action="reportes/reporte_quimestral_docente.php"
							method="post">
							<div id="lista_estudiantes_paralelo" style="text-align:center"> </div>
							<div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
								<input id="id_asignatura" name="id_asignatura" type="hidden" />
								<input id="id_paralelo" name="id_paralelo" type="hidden" />
								<input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
								<input type="submit" value="Ver Reporte" />
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
</body>

</html>