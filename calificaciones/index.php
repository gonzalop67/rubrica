<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>R&uacute;brica Web 2.0</title>
	<style>
		#tabla_navegacion,
		#tabla_paginacion {
			border: none;
		}

		.is-active {
			border: 1px solid black;
		}
	</style>
	<script type="text/javascript" src="js/funciones.js"></script>
	<script type="text/javascript" src="js/keypress.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			//$("#save_all").hide();

			// Determinar si está activada la opción de copiar y pegar
			// para la institución educativa
			$.ajax({
				url: "calificaciones/obtener_estado_copiar_y_pegar.php",
				type: "POST",
				dataType: "json",
				success: function(resp) {
					//console.log(resp.in_copiar_y_pegar);
					$("#in_copiar_y_pegar").val(resp.in_copiar_y_pegar);
					if (resp.in_copiar_y_pegar == 1) {
						$("#btn-guardar").show();
					} else {
						$("#btn-guardar").hide();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// Otro manejador error
					//console.log(jqXHR.responseText);
				}
			});

			cargarPeriodosEvaluacion();
			cargarAsignaturasDocente();

			$("#cboPeriodosEvaluacion").change(function(e) {
				cargarAportesEvaluacion();
				$("#div_estado_rubrica").html("");
				$("#div_fecha_cierre").html("");
				$("#mensaje_rubrica").html("");
				$("#ver_reporte").hide();
				$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
				$("#paginacion_estudiantes").html("");
				$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
			});

			$("#cboAportesEvaluacion").change(function(e) {
				$("#div_estado_rubrica").html("");
				$("#div_fecha_cierre").html("");
				$("#mensaje_rubrica").html("");
				document.getElementById('id_aporte_evaluacion').value = $(this).val();
				$("#mensaje_rubrica").html("");
				$("#ver_reporte").hide();
				//$("#save_all").hide();
				$("#num_estudiantes").html("N&uacute;mero de Estudiantes encontrados:&nbsp;");
				$("#paginacion_estudiantes").html("");
				$("#lista_estudiantes_paralelo").addClass("error");
				$("#lista_estudiantes_paralelo").html("Debe seleccionar una asignatura...");
				$("#tituloNomina").html("NOMINA DE ESTUDIANTES");
			});
		});

		function mostrarEstadoRubrica(id_paralelo) {
			var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();

			//Este código es para deshabilitar o habilitar el botón de Guardar de acuerdo al estado de cierre
			//del periodo y del paralelo asociados.

			$.ajax({
				url: "calificaciones/obtener_estado_cierre.php",
				type: "POST",
				data: {
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_paralelo: id_paralelo
				},
				dataType: "json",
				success: function(resp) {
					//console.log(resp);
					if (resp.ap_estado == "A") {
						$("#save_all").attr("disabled", false);
					} else {
						$("#save_all").attr("disabled", true);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// Otro manejador error
					//console.log(jqXHR.responseText);
				}
			});

			$.post("calificaciones/obtener_fecha_apertura_aporte.php", {
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_paralelo: id_paralelo
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error al obtener la fecha de apertura...");
					} else {
						if (resultado == "2") {
							// swal("Mensaje", "El Aporte de Evaluación no se encuentra Abierto todavía...", "info");
							Swal.fire({
								icon: "info",
								title: "Mensaje",
								text: "El Aporte de Evaluación no se encuentra Abierto todavía...",
							});
						} else if (resultado == "3") {
							// swal("Mensaje", "No se encuentra definida la fecha de apertura...", "info");
							Swal.fire({
								icon: "info",
								title: "Mensaje",
								text: "No se encuentra definida la fecha de apertura...",
							});
						}
					}
				}
			);

			$.post("calificaciones/mostrar_estado_rubrica.php", {
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_paralelo: id_paralelo
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#div_estado_rubrica").html(resultado);
					}
				}
			);

			$.post("calificaciones/obtener_fecha_cierre_aporte.php", {
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_paralelo: id_paralelo
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#div_fecha_cierre").html(resultado);
					}
				}
			);

		}

		function mostrarTitulosRubricas(alineacion, id_asignatura, id_curso) {
			var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
			var id_aporte_evaluacion = $("#cboAportesEvaluacion").val();
			$.post("calificaciones/mostrar_titulos_rubricas.php", {
					id_periodo_evaluacion: id_periodo_evaluacion,
					id_aporte_evaluacion: id_aporte_evaluacion,
					alineacion: alineacion,
					id_asignatura: id_asignatura,
					id_curso: id_curso
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#txt_rubricas").html(resultado);
						//$("#btn-guardar").show();
					}
				}
			);
		}

		function sel_texto(input) {
			$(input).select();
		}

		function cargarPeriodosEvaluacion() {
			$.get("scripts/cargar_periodos_evaluacion_principales.php", {},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#cboPeriodosEvaluacion").append(resultado);
						$("#lista_estudiantes_paralelo").addClass("error");
						$("#lista_estudiantes_paralelo").html("Debe elegir un per&iacute;odo de evaluaci&oacute;n...");
					}
				}
			);
		}

		function cargarAportesEvaluacion() {
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			document.getElementById("cboAportesEvaluacion").options.length = 1;
			$.get("scripts/cargar_aportes_evaluacion.php", {
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado) {
					if (resultado == false) {
						$("#lista_estudiantes_paralelo").addClass("error");
						$("#lista_estudiantes_paralelo").html("No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...");
					} else {
						$("#cboAportesEvaluacion").append(resultado);
						$("#lista_estudiantes_paralelo").addClass("error");
						$("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
					}
				}
			);
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

		function cargarAsignaturasDocente() {
			contarAsignaturasDocente(); //Esta funcion desencadena las demas funciones de paginacion
		}

		function contarAsignaturasDocente() {
			$("#img_loader").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
			$.post("calificaciones/contar_asignaturas_docente.php", {},
				function(resultado) {
					$("#img_loader").html("");
					if (resultado == false) {
						alert("Error: No se han asignado asignaturas al docente...");
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

		function cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
			contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura); //Esta funcion desencadena las demas funciones de paginacion 
		}

		function contarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
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
						//Listar los estudiantes del paralelo
						listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
					}
				}
			);
		}

		function mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura, id_curso) {

			$.post("calificaciones/mostrar_leyendas_rubricas.php", {
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_asignatura: id_asignatura,
					id_curso: id_curso
				},
				function(resultado) {
					$("#leyendas_rubricas").html(resultado);
				}
			);
		}

		function listarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura) {
			var id_aporte_evaluacion = document.getElementById("id_aporte_evaluacion").value;
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			$("#lista_estudiantes_paralelo").empty();
			$("#img_loader_estudiantes").html("<img src='./imagenes/ajax-loader.gif' alt='Procesando...'>");
			$.post("calificaciones/listar_estudiantes_paralelo.php", {
					id_curso: id_curso,
					id_paralelo: id_paralelo,
					id_asignatura: id_asignatura,
					id_aporte_evaluacion: id_aporte_evaluacion,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado) {
					$("#img_loader_estudiantes").html("");
					// console.log(resultado);
					//anadir el resultado al DOM
					$("#lista_estudiantes_paralelo").html(resultado);
					document.getElementById("mensaje_rubrica").innerHTML = "";
					//adicionar el evento de escucha para "pegar" desde el portapapeles
					var id_primer_input;
					var frmFormulario = document.forms["formulario_rubrica"];
					for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
						var objElemento = frmFormulario.elements[iCont];
						if (objElemento.type == 'text') {
							var id_elem = objElemento.id;
							var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
							var campos = id_elem.split("_");
							if (fila_elem == 1 && (campos[0] == 'puntaje' || campos[0] == 'examenquimestral' || campos[0] == 'proyectofinal' || campos[0] == 'cualitativa')) {
								id_primer_input = id_elem;
								break;
							}
						}
					}
					//console.log(resultado);
					document.getElementById(id_primer_input).addEventListener('paste', handlePaste);
				}
			);
		}

		function seleccionarParalelo(id_curso, id_paralelo, id_asignatura, asignatura, curso, paralelo) {
			mostrarTitulosRubricas("center", id_asignatura, id_curso);
			document.getElementById("id_asignatura").value = id_asignatura;
			document.getElementById("id_paralelo").value = id_paralelo;
			document.getElementById("id_periodo_evaluacion").value = document.getElementById("cboPeriodosEvaluacion").value;
			document.getElementById("id_aporte_evaluacion").value = document.getElementById("cboAportesEvaluacion").value;
			var id_aporte_evaluacion = document.getElementById("cboAportesEvaluacion").value;
			var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
			if (id_periodo_evaluacion == 0) {
				// swal("Mensaje", "Debe elegir un periodo de evaluación", "info");
				Swal.fire({
					icon: "info",
					title: "Mensaje",
					text: "Debe elegir un periodo de evaluación",
				});
			} else if (id_aporte_evaluacion == 0 && $('#div_combo_aportes').is(':visible')) {
				// swal("Mensaje", "Debe elegir un aporte de evaluación", "info");
				Swal.fire({
					icon: "info",
					title: "Mensaje",
					text: "Debe elegir un aporte de evaluación",
				});
			} else {
				mostrarEstadoRubrica(id_paralelo);
				// mostrarLeyendasRubricas(id_aporte_evaluacion, id_asignatura, id_curso);
				$("#mensaje").html("");
				document.getElementById("tituloNomina").innerHTML = "NOMINA DE ESTUDIANTES [" + asignatura + " - " + curso + " " + paralelo + "]";
				$("#lista_estudiantes_paralelo").removeClass("error");
				//Aqui va la llamada a ajax para recuperar la nómina de estudiantes con sus respectivas calificaciones
				//Primero debo determinar si se trata del examen quimestral de Proyectos Escolares
				$.ajax({
					url: "scripts/verificar_si_es_examen_proyectos.php",
					type: "POST",
					data: {
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: $("#cboAportesEvaluacion").val()
					},
					dataType: "json",
					success: function(resp) {
						if (!(resp.ap_tipo == "2" && resp.id_tipo_asignatura == "2")) {

							//No se trata del examen de proyectos: se consultan las calificaciones
							cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura);
							$("#ver_reporte").css("display", "block");

						} else {

							//Se trata del examen quimestral de Proyectos Escolares
							var error = '<span class="rojo">' +
								'Para la apreciación quimestral, se debe tomar en cuenta todas las apreciaciones parciales.' +
								'<br>(Fuente: Instructivo de Calificaciones 2019-2020)' +
								'</span>';
							$("#lista_estudiantes_paralelo").html(error);
							$("#lista_estudiantes_paralelo").fadeIn("slow");

						}
					}
				});
			}
		}

		function trunc(x, posiciones = 0) {
			var s = x.toString()
			var l = s.length
			var decimalLength = s.indexOf('.') + 1
			var numStr = s.substr(0, decimalLength + posiciones)
			return Number(numStr)
		}

		function editarCalificacion(obj, id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, tipo_aporte, calificacion_bd, ponderacion) {
			var calificacion = obj.value;
			var puntaje = 0;
			var id = obj.id;
			var fila = id.substr(id.lastIndexOf("_") + 1);
			var suma_total_aporte = 0;
			var promedio_aporte = 0;
			var contador_calificaciones = 0;
			var suma_ponderados = 0;
			var ponderado_examen = 0;
			var frmFormulario = document.forms["formulario_rubrica"];
			calificacion = replace(calificacion);
			console.log(tipo_aporte);
			//Validacion de la calificacion
			if (calificacion == "")
				//alert("El campo no puede ser nulo...");
				calificacion = 0;
			if (calificacion < 0 || calificacion > 10) {
				// swal("La calificacion debe estar en el rango de 0 a 10", "Se ha producido un error en el ingreso de calificaciones", "error");
				Swal.fire({
					icon: "error",
					title: "La calificacion debe estar en el rango de 0 a 10",
					text: "Se ha producido un error en el ingreso de calificaciones"
				});
				if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "")
					document.getElementById(id).value = calificacion_bd;
				else
					document.getElementById(id).value = "";
			} else {
				//Aqui va el codigo para calcular el promedio
				for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
					var objElemento = frmFormulario.elements[iCont];
					if (objElemento.type == 'text') {
						var id_elem = objElemento.id;
						var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
						var campos = id_elem.split("_");
						if (fila_elem == fila) {
							if (tipo_aporte == 1) {
								if (campos[0] == "puntaje") {
									//Aqui calculo la suma de las calificaciones de cada estudiante
									objElemento.value = replace(objElemento.value);
									puntaje = objElemento.value == "" ? 0 : objElemento.value;
									suma_total_aporte += parseFloat(puntaje);
									contador_calificaciones++;
								} else {
									//Aqui calculo el promedio del aporte y salto
									promedio_aporte = suma_total_aporte / contador_calificaciones;
									//console.log(promedio_aporte);
									if (!isNaN(promedio_aporte)) {
										if (promedio_aporte !== 0) {
											document.getElementById("promedio_" + campos[1] + "_" + fila_elem).value = trunc(promedio_aporte, 2);
										} else {
											document.getElementById("promedio_" + campos[1] + "_" + fila_elem).value = "";
										}
										break;
									}
								}
							} else if (tipo_aporte == 2 || tipo_aporte == 6) { // examen_sub_periodo o fase_proyecto
								if (id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoaportes" || id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoexamen") {
									//Aqui calculo la suma de los ponderados de los aportes y del examen
									puntaje = objElemento.value == "" ? 0 : objElemento.value;
									suma_ponderados += parseFloat(puntaje);
									//console.log('suma_ponderados = '+suma_ponderados);
								} else if (id_elem.substr(0, id_elem.indexOf("_")) == "examenquimestral" || id_elem.substr(0, id_elem.indexOf("_")) == "faseproyecto") {
									//Aqui calculo el ponderado del examen quimestral
									objElemento.value = replace(objElemento.value);
									ponderado_examen = parseFloat(objElemento.value) * parseFloat(ponderacion);
									if (!isNaN(ponderado_examen))
										document.getElementById("ponderadoexamen_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(ponderado_examen, 3);
									else
										document.getElementById("ponderadoexamen_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "";
								} else if (id_elem.substr(0, id_elem.indexOf("_")) == "calificacionquimestral") {
									//console.log('suma_ponderados = '+suma_ponderados);
									document.getElementById("calificacionquimestral_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(suma_ponderados, 2);
								}
							}
						}
					}
				}
				$.post("calificaciones/editar_calificacion.php", {
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_rubrica_personalizada: id_rubrica_personalizada,
						re_calificacion: calificacion
					},
					function(resultado) {
						if (resultado) { // Solo si existe resultado
							$("#mensaje_rubrica").html(resultado);
						}
					}
				);
			}
		}

		function editarCalificacionProyecto(obj, id_estudiante, id_paralelo, id_asignatura, id_rubrica_personalizada, tipo_aporte, calificacion_bd, ponderacion_subperiodo) {
			var calificacion = obj.value;
			var puntaje = 0;
			var id = obj.id;
			var fila = id.substr(id.lastIndexOf("_") + 1);
			var suma_ponderados = 0;
			var ponderado_proyecto = 0;
			var frmFormulario = document.forms["formulario_rubrica"];
			calificacion = replace(calificacion);
			//Validacion de la calificacion
			if (calificacion == "")
				calificacion = 0;
			if (calificacion < 0 || calificacion > 10) {
				// swal("La calificacion debe estar en el rango de 0 a 10", "Se ha producido un error en el ingreso de calificaciones", "error");
				Swal.fire({
					icon: "error",
					title: "La calificacion debe estar en el rango de 0 a 10",
					text: "Se ha producido un error en el ingreso de calificaciones"
				});
				if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "")
					document.getElementById(id).value = calificacion_bd;
				else
					document.getElementById(id).value = "";
			} else {
				//Aqui va el codigo para calcular el promedio
				for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
					var objElemento = frmFormulario.elements[iCont];
					if (objElemento.type == 'text') {
						var id_elem = objElemento.id;
						var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
						var campos = id_elem.split("_");
						if (fila_elem == fila) {
							if (tipo_aporte == 5) {
								//Se trata del proyecto integrador o interdisciplinario
								if (id_elem.substr(0, id_elem.indexOf("_")) == "ponderadosubperiodo" || id_elem.substr(0, id_elem.indexOf("_")) == "ponderadoproyecto") {
									//Aqui calculo la suma de los ponderados de los subperiodos y del proyecto
									puntaje = objElemento.value == "" ? 0 : objElemento.value;
									suma_ponderados += parseFloat(puntaje);
								} else if (campos[0] == "proyectofinal") {
									//Aqui calculo el ponderado del examen quimestral
									objElemento.value = replace(objElemento.value);
									ponderado_proyecto = parseFloat(objElemento.value) * parseFloat(ponderacion_subperiodo);
									if (!isNaN(ponderado_proyecto))
										document.getElementById("ponderadoproyecto_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(ponderado_proyecto, 3);
									else
										document.getElementById("ponderadoproyecto_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "";
								} else if (id_elem.substr(0, id_elem.indexOf("_")) == "calificacionperiodo") {
									//console.log('suma_ponderados = '+suma_ponderados);
									document.getElementById("calificacionperiodo_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = trunc(suma_ponderados, 2);
									suma_ponderados = parseFloat(suma_ponderados);
									if (suma_ponderados >= 7) {
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "APRUEBA";
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).style.color = "#008000";
									} else if (suma_ponderados > 4) {
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "SUPLETORIO";
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).style.color = "#ff8c00";
									} else {
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).value = "NO APRUEBA";
										document.getElementById("observacion_" + campos[1] + "_" + campos[2] + "_" + fila_elem).style.color = "#ff0000";
									}
								}
							}
						}
					}
				}
				// console.log(calificacion);
				$.post("calificaciones/editar_calificacion.php", {
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_rubrica_personalizada: id_rubrica_personalizada,
						re_calificacion: calificacion
					},
					function(resultado) {
						if (resultado) { // Solo si existe resultado
							$("#mensaje_rubrica").html(resultado);
						}
					}
				);
			}
		}

		function replace(str) {
			let str_aux = "";
			for (let i = 0; i < str.length; i++) {
				const element = str[i];
				if (element === ",") {
					str_aux += ".";
				} else {
					str_aux += element;
				}
			}
			return str_aux;
		}

		function editarCalificacionComportamiento(obj, id_estudiante, id_paralelo, id_asignatura, id_aporte_evaluacion) {
			var str = obj.value;
			var id = obj.id;
			var fila = id.substr(id.indexOf("_") + 1);
			//Validacion de la calificacion
			str = eliminaEspacios(str);
			var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
			var idx = permitidos.indexOf(str);
			//alert(str);
			if (str != '') {
				if (idx == -1) {
					alert("La calificacion debe estar en el rango de A a E");
					obj.value = "";
				} else {
					$.post("docentes/editar_calificacion_comportamiento.php", {
							id_estudiante: id_estudiante,
							id_paralelo: id_paralelo,
							id_asignatura: id_asignatura,
							id_aporte_evaluacion: id_aporte_evaluacion,
							co_calificacion: str.toUpperCase()
						},
						function(resultado) {
							if (resultado) { // Solo si existe resultado
								//alert(resultado);
								$("#mensaje_rubrica").html(resultado);
							}
						}
					);
				}
			} else {
				$.post("docentes/eliminar_calificacion_comportamiento.php", {
						id_estudiante: id_estudiante,
						id_paralelo: id_paralelo,
						id_asignatura: id_asignatura,
						id_aporte_evaluacion: id_aporte_evaluacion
					},
					function(resultado) {
						if (resultado) { // Solo si existe resultado
							//alert(resultado);
							$("#mensaje_rubrica").html(resultado);
						}
					}
				);
			}
		}

		function editarCalificacionCualitativa(obj, id_estudiante, id_paralelo, id_asignatura, id_aporte_evaluacion) {
			var str = obj.value;
			var id = obj.id;
			var fila = id.substr(id.indexOf("_") + 1);
			str = str.toUpperCase();
			//Validacion de la calificacion
			str = eliminaEspacios(str);
			var permitidos = ['EX', 'MB', 'B', 'R'];
			var idx = permitidos.indexOf(str);
			if (str != '') {
				if (idx == -1) {
					alert("La calificacion debe estar en el conjunto EX MB B R");
					obj.value = "";
				}
			}
		}

		function handlePaste(e) {
			var clipboardData, pastedData;

			// Stop data actually being pasted into div
			e.stopPropagation();
			e.preventDefault();

			// Get pasted data via clipboard API
			clipboardData = e.clipboardData || window.clipboardData;
			pastedData = clipboardData.getData('Text');

			// Do whatever with pasteddata
			pegarDatos(pastedData);
		}

		function pegarDatos(data) {
			var rows = data.split("\n");
			var fila = 0;
			var x, z, suma, promedio, nota;
			//alert('Cantidad de filas: ' + rows.length);
			for (var y in rows) {
				var cells = rows[y].split("\t");
				if (fila < rows.length) {
					suma = 0;
					is_number = 1;
					for (var x in cells) {
						z = parseInt(x) + 1;
						//alert('.nota'+parseInt(z)+':eq('+y+')');
						//alert(cells[x]);
						$('.nota' + parseInt(z) + ':eq(' + y + ')').val(cells[x]);
						nota = cells[x].replace("", 0);
						if (!isNaN(nota)) suma += parseFloat(nota);
						else is_number = 0;
					}
					//Lo siguiente solo funciona para los parciales
					if (is_number == 1) {
						x++;
						promedio = trunc(parseFloat(suma / x), 2);
						if (promedio != 0)
							$('.promedio:eq(' + fila + ')').val(promedio);
						else
							$('.promedio:eq(' + fila + ')').val("");
					}
					fila++;
				}
			}
		}

		function guardarTabla() {
			var frmFormulario = document.forms["formulario_rubrica"];
			var puntaje = 0;
			var id_estudiante, id_paralelo, id_asignatura;
			for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
				var objElemento = frmFormulario.elements[iCont];
				if (objElemento.type == 'text') {
					var id_elem = objElemento.id;
					var campos = id_elem.split("_");
					if (campos[0] == "puntaje" || campos[0] == "examenquimestral") {
						puntaje = objElemento.value == "" ? 0 : objElemento.value;
						puntaje = replace(puntaje);
						//console.log('puntaje: '+puntaje);
						id_rubrica_evaluacion = campos[1];
						id_estudiante = campos[2];
						id_paralelo = campos[3];
						id_asignatura = campos[4];

						//Aqui procesamos la calificacion dentro de la BD
						$.post("calificaciones/editar_calificacion.php", {
								id_estudiante: id_estudiante,
								id_paralelo: id_paralelo,
								id_asignatura: id_asignatura,
								id_rubrica_personalizada: id_rubrica_evaluacion,
								re_calificacion: puntaje
							},
							function(resultado) {
								if (resultado) { // Solo si existe resultado
									//console.log(resultado);
								}
							}
						);
					} else if (campos[0] == "cualitativa") {
						//Aqui procesamos la calificacion cualitativa
						puntaje = objElemento.value == " " ? 0 : objElemento.value;
						puntaje = replace(puntaje);
						//console.log('puntaje: '+puntaje);
						id_estudiante = campos[1];
						id_paralelo = campos[2];
						id_asignatura = campos[3];
						id_aporte_evaluacion = campos[4];
						if (puntaje !== 0) {
							//Almacena la calificación cualitativa en la base de datos
							$.post("docentes/editar_calificacion_cualitativa.php", {
									id_estudiante: id_estudiante,
									id_paralelo: id_paralelo,
									id_asignatura: id_asignatura,
									id_aporte_evaluacion: id_aporte_evaluacion,
									rc_calificacion: puntaje
								},
								function(resultado) {
									//alert(resultado);
									$("#mensaje_rubrica").html(resultado);
								}
							);
						} else {
							//Elimina la calificación cualitativa de la base de datos
							$.post("docentes/eliminar_calificacion_cualitativa.php", {
									id_estudiante: id_estudiante,
									id_paralelo: id_paralelo,
									id_asignatura: id_asignatura,
									id_aporte_evaluacion: id_aporte_evaluacion
								},
								function(resultado) {
									//alert(resultado);
									$("#mensaje_rubrica").html(resultado);
								}
							);
						}
					}
				}
			}
			// swal("Calificaciones ingresadas satisfactoriamente...", "Felicitaciones!", "success");
			Swal.fire({
				icon: "success",
				title: "Calificaciones ingresadas satisfactoriamente...",
				text: "Felicitaciones!"
			});
		}
	</script>
</head>

<body>
	<div id="pagina">
		<input type="hidden" id="in_copiar_y_pegar">
		<div id="titulo_pagina">
			<?php echo "INGRESAR CALIFICACIONES " . $_SESSION['titulo_pagina'] ?>
		</div>
		<div id="barra_principal">
			<table id="tabla_navegacion" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%" class="fuente9 text-right"> Per&iacute;odo:&nbsp; </td>
					<td width="5%">
						<select id="cboPeriodosEvaluacion" class="fuente8">
							<option value="0"> Seleccione... </option>
						</select>
					</td>
					<td width="5%" class="fuente9 text-right">
						<div id="label_combo_aportes"> Aporte:&nbsp; </div>
					</td>
					<td width="5%">
						<div id="div_combo_aportes">
							<select id="cboAportesEvaluacion" class="fuente8">
								<option value="0"> Seleccione... </option>
							</select>
						</div>
					</td>
					<td width="10%">
						<div id="div_estado_rubrica" style="padding-left: 4px;"> </div>
					</td>
					<td width="20%">
						<div id="div_fecha_cierre" style="padding-left: 4px;"> </div>
					</td>
					<td width="*">
						<div id="mensaje_rubrica" class="text-center text-danger"></div>
					</td>
				</tr>
			</table>
			<input id="id_estudiante" type="hidden" />
			<input id="id_rubrica_personalizada" type="hidden" />
			<input id="numero_pagina" type="hidden" />
		</div>
		<div id="mensaje" class="text-center text-danger"></div>
		<div id="img_loader" style="text-align:center"> </div>
		<div id="pag_asignaturas">
			<!-- Aqui va la paginacion de las asignaturas asociadas al docente -->
			<div id="total_registros" class="paginacion">
				<table id="tabla_paginacion" class="fuente8" width="100%" cellspacing=4 cellpadding=0>
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
				<table class="fuente8 cabeceraTabla" width="100%" cellspacing=0 cellpadding=0 style="border:none">
					<tr class="cabeceraTabla">
						<td width="5%">Nro.</td>
						<td width="39%" class="text-left">Asignatura</td>
						<td width="32%" class="text-left">Curso</td>
						<td width="6%" class="text-left">Paralelo</td>
						<td width="18%" class="text-center">Acciones</td>
					</tr>
				</table>
			</div>
			<div id="lista_asignaturas" style="text-align:center"> </div>
		</div>
		<!--<div id="mensaje_rubrica" class="error" style="text-align:center"></div>-->
		<div id="pag_nomina_estudiantes">
			<!-- Aqui va la paginacion de los estudiantes encontrados -->
			<div id="total_registros_estudiantes" class="paginacion" style="height:25px;">
				<table class="fuente8" width="100%" cellspacing=4 cellpadding=0 style="border:none">
					<tr>
						<td>
							<div id="num_estudiantes">&nbsp;N&uacute;mero de Estudiantes:&nbsp;</div>
						</td>
						<td>
							<div id="leyendas_rubricas">
								<!-- Aqui van las leyendas de las rubricas -->
							</div>
						</td>
						<td>
							<div id="btn-guardar" style="text-align: right; margin-right: 2px;">
								<button type="button" id="save_all" class="btn btn-success btn-xs" onclick="guardarTabla()" title="Guardar Todas las Calificaciones">Guardar</button>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
			<div class="cabeceraTabla">
				<table class="fuente8" width="100%" cellspacing=0 cellpadding=0 border=0>
					<tr class="cabeceraTabla">
						<td width="5%">Nro.</td>
						<td width="5%">Id.</td>
						<td width="30%" align="left">N&oacute;mina</td>
						<td width="60%" align="left">
							<div id="txt_rubricas">Calificaciones</div>
						</td>
					</tr>
				</table>
			</div>
			<!-- <div id="cabecera_tabla">
		  Aqui va la cabecera de la tabla 
	  </div> -->
			<!-- <form id="formulario_rubrica" action="reportes/reporte_por_aporte.php" method="post" target="_blank"> -->
			<form id="formulario_rubrica" action="php_excel/reporte_por_parcial_docente.php" method="post">
				<div id="img_loader_estudiantes" style="text-align:center"> </div>
				<div id="lista_estudiantes_paralelo" style="text-align:center; overflow:auto"> </div>
				<div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
					<input id="id_asignatura" name="id_asignatura" type="hidden" />
					<input id="id_paralelo" name="id_paralelo" type="hidden" />
					<input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
					<input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
					<input type="submit" value="Ver Reporte" />
				</div>
			</form>
		</div>
	</div>
</body>

</html>