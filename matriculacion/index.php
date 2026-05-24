<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>R&uacute;brica Web 2.0</title>
	<style>
		table {
			border: none;
			margin-top: 4px;
		}

		table td {
			padding-left: 2px;
			padding-top: 2px;
		}

		.ocultar {
			display: none;
		}

		.barra_principal {
			background: #f5f5f5;
			height: 32px;
		}

		.mayusculas {
			text-transform: uppercase;
		}
	</style>
	<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/funciones.js"></script>
	<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
	<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
	<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
	<script type="text/javascript">
		var verifica = true;
		var modo_actualizar = false;
		var periodo_terminado = '';

		$(document).ready(function() {
			cargar_paralelos();
			cargar_paralelos_vigentes();
			cargar_def_generos();
			cargar_tipos_documento();
			cargar_def_nacionalidades();

			// Determinar que opciones se tienen al momento de cargar...
			determinarOpciones();

			$("#img-loader-busqueda").hide();

			$("#id_paralelo").change(function() {
				if ($(this).val() != "") {
					contarEstudiantesParalelo($(this).val()); //Esta funcion desencadena las demas funciones de paginacion
					cargar_estudiantes_des_matriculados();
					if (periodo_terminado !== "T") {
						$("#new_student").show();
						$("#historico_estudiante").show();
						$("#inactive_students").show();
						$("#export_csv").show();
						$("#import_csv").show();
					}
					$("#id_paralelo_export").val($(this).val());
				} else {
					if (periodo_terminado !== "T") {
						$("#new_student").hide();
						$("#historico_estudiante").hide();
						$("#inactive_students").hide();
						$("#export_csv").hide();
						$("#import_csv").hide();
					}
					$("#tabla tbody").html("<div class='text-center'>Debe seleccionar un paralelo...</div>");
					$("#id_paralelo_export").val("");
				}
			});

			$("#datos_representante").click(function() {
				//Aqui se van a realizar las instrucciones necesarias para actualizar los datos del representante del estudiante
				$("#datos_estudiante").show();
				obtenerRepresentante();
				$(this).hide();
			});

			$("#datos_estudiante").click(function() {
				//Aqui se van a realizar las instrucciones necesarias para actualizar los datos del representante del estudiante
				$("#datos_representante").show();
				$("#formulario_representante").hide();
				$("#formulario_nuevo").show();
				$(this).hide();
				$("#datos_estudiante").show();
				$(this).hide();
			});

			$("#new_fec_nac").blur(function() {
				//Aqui se va a calcular la edad a partir de la fecha de nacimiento
				var hoy = new Date();
				var fec_nac = new Date($(this).val());
				var edad = hoy.getFullYear() - fec_nac.getFullYear();
				var m = hoy.getMonth() - fec_nac.getMonth();

				if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
					edad--;
				}

				$("#new_edad").val(edad);
			});

			$("#edit_fec_nac").blur(function() {
				//Aqui se va a calcular la edad a partir de la fecha de nacimiento
				var hoy = new Date();
				var fec_nac = new Date($(this).val());
				var edad = hoy.getFullYear() - fec_nac.getFullYear();
				var m = hoy.getMonth() - fec_nac.getMonth();

				if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
					edad--;
				}

				$("#edit_edad").val(edad);
			});

			$("#new_dni").blur(function() {
				//Aqui se va a determinar si el numero de cedula ya existe en la base de datos
				if (($(this).val() != "" && !modo_actualizar) || ($(this).val() != "" && modo_actualizar && $(this).val() != $("#cedula_anterior").val())) {
					$.post("matriculacion/determinar_cedula_repetida.php", {
							es_cedula: $(this).val()
						},
						function(resultado) {
							if (resultado != "") {
								alert(resultado);
							}
						}
					);
				}
			});

			$("#input_search").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: "matriculacion/buscar_estudiantes.php",
						type: 'POST',
						dataType: 'json',
						data: {
							valor: request.term
						},
						success: function(data) {
							response(data);
						},
						error: function(xhr, status, error) {
							alert(xhr.responseText);
							console.log(xhr.responseText);
						}
					});
				},
				minLength: 3,
				select: function(event, ui) {
					$("#id_estudiante").val(ui.item.id);
				},
			});

			$('#t_deleted tbody').on('click', '.item-deleted', function() {
				let id_estudiante_periodo_lectivo = $(this).attr('data');
				let id_paralelo = $("#id_paralelo").val();
				$.ajax({
					url: "matriculacion/volver_a_matricular.php",
					method: "post",
					data: {
						id_estudiante_periodo_lectivo: id_estudiante_periodo_lectivo
					},
					dataType: "json",
					success: function(resultado) {
						$('#deletedStudentModal').modal('hide');
						Swal.fire({
							title: resultado.titulo,
							text: resultado.mensaje,
							icon: resultado.tipo_mensaje
						});
						contarEstudiantesParalelo(id_paralelo);
						cargar_estudiantes_des_matriculados();
					},
					error: function(jqXHR, textStatus) {
						console.log(jqXHR.responseText);
					}
				});
			});

			$("#form-search").submit(function(e) {
				e.preventDefault();
				//Aqui va el codigo para buscar estudiantes antiguos
				var id_estudiante = document.getElementById("id_estudiante").value;
				var nombreEstudiante = document.getElementById("input_search").value;

				document.getElementById("nombre_estudiante").innerHTML = nombreEstudiante;

				if (id_estudiante == "" || id_estudiante == null) {

				} else {
					$.ajax({
						type: "POST",
						url: "matriculacion/obtener_historial_estudiante.php",
						data: "id_estudiante=" + id_estudiante,
						success: function(resultado) {
							$("#tbody_found").html(resultado);
						}
					});

					$("#storyStudentModal").modal('show');
				}

			});

			// $("#export_csv").click(function(){
			// 	const id_paralelo = $("#id_paralelo").val();
			// 	$.ajax({
			// 		type: "POST",
			// 		url: "matriculacion/exportar_csv.php",
			// 		data: "id_paralelo="+id_paralelo,
			// 		dataType: "html",
			// 		success: function (response) {
			// 			console.log(response);
			// 		}
			// 	});
			// })
		});

		function cargar_paralelos() {
			$.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$('#id_paralelo').append(resultado);
					// $('#edit_id_paralelo').append(resultado);
				}
			});
		}

		function cargar_paralelos_vigentes() {
			$.get("scripts/cargar_paralelos_vigentes.php", function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$('#edit_id_paralelo').append(resultado);
				}
			});
		}

		function cargar_def_generos() {
			$.get("scripts/cargar_definicion_generos.php", function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$('#new_genero').append(resultado);
					$('#edit_genero').append(resultado);
				}
			});
		}

		function cargar_def_nacionalidades() {
			$.get("scripts/cargar_definicion_nacionalidades.php", function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$('#new_nacionalidad').append(resultado);
					$('#edit_nacionalidad').append(resultado);
				}
			});
		}

		function cargar_tipos_documento() {
			$.get("scripts/cargar_tipos_documento.php", function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$('#new_id_tipo_documento').append(resultado);
					$('#edit_id_tipo_documento').append(resultado);
				}
			});
		}

		function contarEstudiantesParalelo(id_paralelo) {
			$.post("matriculacion/contar_estudiantes_por_genero.php", {
					id_paralelo: id_paralelo
				},
				function(resultado) {
					if (resultado == false) {
						alert("Error");
					} else {
						$("#contar_estudiantes_por_genero").html(resultado);
						listarEstudiantesParalelo(id_paralelo);
					}
				}
			);
		}

		function determinarOpciones() {
			$.ajax({
				url: "matriculacion/obtener_estado_periodo_lectivo.php",
				success: function(respuesta) {
					//alert(respuesta);
					// if (respuesta == "T") {
					// 	$("#new_student").hide();
					// 	$("#inactive_students").hide();
					// 	$("#import_csv").hide();
					// 	$("#historico_estudiante").hide();
					// }
					periodo_terminado = respuesta;
				}
			});
		}

		function listarEstudiantesParalelo(id_paralelo) {
			var id_paralelo = document.getElementById("id_paralelo").value;
			if (id_paralelo == 0) {
				document.getElementById("lista_estudiantes").innerHTML = "Debe elegir un paralelo...";
			} else {
				$.post("matriculacion/listar_estudiantes.php", {
						id_paralelo: id_paralelo
					},
					function(resultado) {
						// console.log(resultado);
						if (resultado == false) {
							alert("Error");
						} else {
							$("#tbody_estudiantes").html(resultado);
						}
					}
				);
			}
		}

		function limpiarRepresentante() {
			document.getElementById("re_cedula").value = "";
			document.getElementById("re_parentesco").value = "";
			document.getElementById("re_apellidos").value = "";
			document.getElementById("re_nombres").value = "";
			document.getElementById("re_email").value = "";
			document.getElementById("re_direccion").value = "";
			document.getElementById("re_sector").value = "";
			document.getElementById("re_telefono").value = "";
			document.getElementById("img-loader").innerHTML = "";
			document.getElementById("mensaje").innerHTML = "";
			$("#formulario_nuevo").hide();
		}

		function cargar_estudiantes_des_matriculados() {
			// Aquí vamos a recuperar los estudiantes "des-matriculados"
			var id_paralelo = $("#id_paralelo").val();
			//console.log(id_paralelo);
			var request = $.ajax({
				url: "matriculacion/estudiantes_des_matriculados.php",
				method: "post",
				data: {
					id_paralelo: id_paralelo
				},
				dataType: "html"
			});

			request.done(function(response) {
				$("#t_deleted tbody").html(response);
			});

			request.fail(function(jqXHR, textStatus) {
				console.log("Requerimiento fallido: " + jqXHR.responseText);
			});
		}

		function cedulaValida(cedula) {
			var total = 0;
			var longitud = cedula.length;
			var longcheck = longitud - 1;

			if (longitud != 10) {
				return false;
			}

			if (cedula !== "" && longitud === 10) {
				for (i = 0; i < longcheck; i++) {
					if (i % 2 === 0) {
						var aux = cedula.charAt(i) * 2;
						if (aux > 9) aux -= 9;
						total += aux;
					} else {
						total += parseInt(cedula.charAt(i)); // parseInt o concatenará en lugar de sumar
					}
				}

				total = total % 10 ? 10 - total % 10 : 0;

				return cedula.charAt(longitud - 1) == total;
			}
		}

		function calcularEdad(es_fec_nacim) {
			//Aqui se va a calcular la edad a partir de la fecha de nacimiento
			var hoy = new Date();
			var fec_nac = new Date(es_fec_nacim);
			var edad = hoy.getFullYear() - fec_nac.getFullYear();
			var m = hoy.getMonth() - fec_nac.getMonth();

			if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
				edad--;
			}

			return edad;
		}

		function insertarEstudiante() {
			let cont_errores = 0;
			let es_cedula = $("#new_dni").val().trim();
			let es_email = $("#new_email").val().trim();
			let es_sector = $("#new_sector").val().trim();
			let es_nombres = $("#new_nombres").val().trim();
			let id_paralelo = $("#id_paralelo").val().trim();
			let id_def_genero = $("#new_genero").val().trim();
			let es_telefono = $("#new_telefono").val().trim();
			let es_fec_nacim = $("#new_fec_nac").val().trim();
			let es_apellidos = $("#new_apellidos").val().trim();
			let es_direccion = $("#new_direccion").val().trim();
			let id_def_nacionalidad = $("#new_nacionalidad").val().trim();
			let id_tipo_documento = $("#new_id_tipo_documento").val().trim();

			var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
			var reg_nombres = /^([a-zA-Z ñáéíóúüÑÁÉÍÓÚÜ]{3,64})$/i;
			var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
			var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
			var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

			if (id_paralelo == "") {
				swal("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
				$("#mensaje1").html("Debe seleccionar un paralelo...");
				$("#mensaje1").fadeIn("slow");
				cont_errores++;
			} else {
				$("#mensaje1").fadeOut();
			}

			if (id_tipo_documento == "") {
				$("#mensaje2").html("Debe seleccionar un tipo de documento...");
				$("#mensaje2").fadeIn("slow");
				cont_errores++;
			} else {
				$("#mensaje2").fadeOut();
			}

			if (es_cedula == "") {
				$("#mensaje3").html("Debe ingresar el DNI...");
				$("#mensaje3").fadeIn();
				cont_errores++;
			} else if (es_cedula.length != 0 && id_tipo_documento == 1 && !reg_cedula.test(es_cedula)) {
				$("#mensaje3").html("El DNI del estudiante no tiene un formato válido.");
				$("#mensaje3").fadeIn();
				cont_errores++;
			} else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
				$("#mensaje3").html("La cédula ingresada no es válida.");
				$("#mensaje3").fadeIn("slow");
				cont_errores++;
			} else {
				$("#mensaje3").fadeOut();
			}

			if (es_apellidos == "") {
				$("#mensaje4").html("Debe ingresar los apellidos...");
				$("#mensaje4").fadeIn();
				cont_errores++;
			} else if (!reg_nombres.test(es_apellidos)) {
				$("#mensaje4").html("Caracteres especiales admitidos: ñ,á,é,í,ó,ú,ü,Ñ,Á,É,Í,Ó,Ú,Ü.");
				$("#mensaje4").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje4").fadeOut();
			}

			if (es_nombres == "") {
				$("#mensaje5").html("Debe ingresar los nombres...");
				$("#mensaje5").fadeIn();
				cont_errores++;
			} else if (!reg_nombres.test(es_nombres)) {
				$("#mensaje5").html("Caracteres especiales admitidos: ñ,á,é,í,ó,ú,ü,Ñ,Á,É,Í,Ó,Ú,Ü.");
				$("#mensaje5").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje5").fadeOut();
			}

			if (es_fec_nacim == "") {
				$("#mensaje6").html("Debe ingresar la fecha de nacimiento...");
				$("#mensaje6").fadeIn();
				cont_errores++;
			} else if (!reg_fecnac.test(es_fec_nacim)) {
				$("#mensaje6").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd");
				$("#mensaje6").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje6").fadeOut();
				$("#new_edad").val(calcularEdad(es_fec_nacim));
			}

			if (es_direccion == "") {
				$("#mensaje7").html("Debe ingresar la dirección de su domicilio...");
				$("#mensaje7").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje7").fadeOut();
			}

			if (es_sector == "") {
				$("#mensaje8").html("Debe ingresar el sector de su domicilio...");
				$("#mensaje8").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje8").fadeOut();
			}

			if (es_telefono == "") {
				$("#mensaje9").html("Debe ingresar el número de celular...");
				$("#mensaje9").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje9").fadeOut();
			}

			if (es_email.length != 0 && !reg_email.test(es_email)) {
				$("#mensaje10").html("Dirección de correo electrónico no válida.");
				$("#mensaje10").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje10").fadeOut();
			}

			if (id_def_genero == "") {
				$("#mensaje11").html("Debe seleccionar el género...");
				$("#mensaje11").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje11").fadeOut();
			}

			if (id_def_nacionalidad == "") {
				$("#mensaje12").html("Debe seleccionar la nacionalidad...");
				$("#mensaje12").fadeIn();
				cont_errores++;
			} else {
				$("#mensaje12").fadeOut();
			}

			if (cont_errores == 0) {
				$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
				$.ajax({
					type: "POST",
					url: "matriculacion/insertar_estudiante.php",
					data: {
						id_tipo_documento: id_tipo_documento,
						id_def_genero: id_def_genero,
						id_def_nacionalidad: id_def_nacionalidad,
						es_apellidos: es_apellidos.toUpperCase(),
						es_nombres: es_nombres.toUpperCase(),
						es_cedula: es_cedula.toUpperCase(),
						es_email: es_email.toLowerCase(),
						es_sector: es_sector.toUpperCase(),
						es_direccion: es_direccion.toUpperCase(),
						es_telefono: es_telefono,
						es_fec_nacim: es_fec_nacim,
						id_paralelo: id_paralelo
					},
					dataType: "json",
					success: function(r) {
						console.log(r);
						$("#img-loader").html("");
						contarEstudiantesParalelo(id_paralelo);
						//$("#formulario_nuevo").hide();
						//console.log(resultado);
						if (r.estado === "success") {
							$("#id_estudiante").val(r.id_estudiante);
							$("#form_insert")[0].reset();
							$('#newStudentModal').modal('hide');
							//Aqui va el proceso para ingresar los datos del representante
							//$("#btnEditarRepresentante").html("Ingresar");
							//$("#formulario_representante").show();
						}
						alert(r.mensaje);
						// swal(resultado.titulo, resultado.mensaje, resultado.estado);
						// toastr[r.estado](r.mensaje, r.titulo);
					},
					error: function(xhr, status, error) {
						console.log(xhr.responseText);
					}
				});
			}
		}

		function actualizarEstudiante() {
			let cont_errores = 0;
			let id_estudiante = $("#id_estudiante").val();
			let es_cedula = $("#edit_dni").val().trim();
			let es_email = $("#edit_email").val().trim();
			let es_sector = $("#edit_sector").val().trim();
			let es_nombres = $("#edit_nombres").val().trim();
			let id_paralelo = $("#id_paralelo").val().trim();
			let id_def_genero = $("#edit_genero").val().trim();
			let es_telefono = $("#edit_telefono").val().trim();
			let es_fec_nacim = $("#edit_fec_nac").val().trim();
			let es_apellidos = $("#edit_apellidos").val().trim();
			let es_direccion = $("#edit_direccion").val().trim();
			let edit_id_paralelo = $("#edit_id_paralelo").val().trim();
			let id_def_nacionalidad = $("#edit_nacionalidad").val().trim();
			let id_tipo_documento = $("#edit_id_tipo_documento").val().trim();

			var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
			var reg_nombres = /^([a-zA-Z ñáéíóúüÑÁÉÍÓÚÜ]{3,64})$/i;
			var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
			var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
			var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

			if (id_paralelo == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar un paralelo.", "error");
				cont_errores++;
			}

			if (id_tipo_documento == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar un tipo de documento...", "error");
				cont_errores++;
			}

			if (es_cedula == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el DNI...", "error");
				cont_errores++;
			} else if (es_cedula.length != 0 && id_tipo_documento == 1 && !reg_cedula.test(es_cedula)) {
				Swal.fire("Ocurrió un error inesperado!", "El DNI del estudiante no tiene un formato válido.", "error");
				cont_errores++;
			} else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
				Swal.fire("Ocurrió un error inesperado!", "La cédula ingresada no es válida.", "error");
				cont_errores++;
			}

			if (es_apellidos == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los apellidos...", "error");
				cont_errores++;
			} else if (!reg_nombres.test(es_apellidos)) {
				$("#mensaje4").html("Caracteres especiales admitidos: ñ,á,é,í,ó,ú,ü,Ñ,Á,É,Í,Ó,Ú,Ü.");
				$("#mensaje4").fadeIn();
				cont_errores++;
			}

			if (es_nombres == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los nombres...", "error");
				cont_errores++;
			} else if (!reg_nombres.test(es_nombres)) {
				$("#mensaje5").html("Caracteres especiales admitidos: ñ,á,é,í,ó,ú,ü,Ñ,Á,É,Í,Ó,Ú,Ü.");
				$("#mensaje5").fadeIn();
				cont_errores++;
			}

			if (es_fec_nacim == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la fecha de nacimiento...", "error");
				cont_errores++;
			} else if (!reg_fecnac.test(es_fec_nacim)) {
				Swal.fire("Ocurrió un error inesperado!", "La fecha de nacimiento debe tener el formato aaaa-mm-dd", "error");
				cont_errores++;
			}

			if (es_direccion == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la dirección de su domicilio...", "error");
				cont_errores++;
			}

			if (es_sector == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el sector de su domicilio...", "error");
				cont_errores++;
			}

			if (es_telefono == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el número de celular...", "error");
				cont_errores++;
			}

			if (es_email.length != 0 && !reg_email.test(es_email)) {
				Swal.fire("Ocurrió un error inesperado!", "Dirección de correo electrónico no válida.", "error");
				cont_errores++;
			}

			if (id_def_genero == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar el género...", "error");
				cont_errores++;
			}

			if (id_def_nacionalidad == "") {
				Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar la nacionalidad...", "error");
				cont_errores++;
			}

			if (cont_errores == 0) {
				// Aquí vamos a actualizar el estudiante mediante AJAX
				$.ajax({
					url: "matriculacion/actualizar_estudiante.php",
					method: "POST",
					data: {
						id_estudiante: id_estudiante,
						id_tipo_documento: id_tipo_documento,
						id_def_genero: id_def_genero,
						id_def_nacionalidad: id_def_nacionalidad,
						es_apellidos: es_apellidos.toUpperCase(),
						es_nombres: es_nombres.toUpperCase(),
						es_cedula: es_cedula.toUpperCase(),
						es_email: es_email.toLowerCase(),
						es_sector: es_sector.toUpperCase(),
						es_direccion: es_direccion.toUpperCase(),
						es_telefono: es_telefono,
						es_fec_nacim: es_fec_nacim,
						id_paralelo: edit_id_paralelo
					},
					success: function(resultado) {
						//alert(resultado);
						id_paralelo = document.getElementById("id_paralelo").value;
						contarEstudiantesParalelo(id_paralelo);
						Swal.fire({
							title: "Operación exitosa",
							text: resultado,
							icon: "success"
						});
						$("#form_edit")[0].reset();
						$('#editStudentModal').modal('hide');
					},
					error: function(xhr, status, error) {
						console.log(xhr.responseText);
					}
				});
			}
		}

		function setearIndice(nombreCombo, indice) {
			for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
				if (document.getElementById(nombreCombo).options[i].value == indice) {
					document.getElementById(nombreCombo).options[i].selected = indice;
				}
		}

		function obtenerRepresentante() {
			//Funcion para recuperar los datos del representante
			var id_estudiante = $("#id_estudiante").val();
			limpiarRepresentante();
			$.ajax({
				type: "POST",
				url: "matriculacion/obtener_representante.php",
				data: "id_estudiante=" + id_estudiante,
				success: function(resultado) {
					//Si todo ha ido bien, tenemos los datos del representante en formato JSON
					var JSONRepresentante = eval('(' + resultado + ')');
					if (JSONRepresentante) {
						//console.log(JSONRepresentante);
						//Aqui se va a pintar el representante recuperado
						document.getElementById("id_representante").value = JSONRepresentante.id_representante;
						document.getElementById("re_cedula").value = JSONRepresentante.re_cedula;
						document.getElementById("re_apellidos").value = JSONRepresentante.re_apellidos;
						document.getElementById("re_nombres").value = JSONRepresentante.re_nombres;
						document.getElementById("re_direccion").value = JSONRepresentante.re_direccion;
						document.getElementById("re_telefono").value = JSONRepresentante.re_telefono;
						document.getElementById("re_sector").value = JSONRepresentante.re_sector;
						document.getElementById("re_parentesco").value = JSONRepresentante.re_parentesco;
					}
					document.getElementById("re_cedula").focus();
				}
			});
			$("#formulario_representante").show();
		}

		function editarRepresentante() {
			// Procedimiento para actualizar los datos del representante
			var id_representante = $("#id_representante").val();
			var id_estudiante = $("#id_estudiante").val();
			var cedula = $("#re_cedula").val();
			es_cedula_anterior = cedula;
			var parentesco = $("#re_parentesco").val();
			var apellidos = $("#re_apellidos").val();
			var nombres = $("#re_nombres").val();
			var direccion = $("#re_direccion").val();
			var sector = $("#re_sector").val();
			var email = $("#re_email").val();
			var telefono = $("#re_telefono").val();
			var observacion = $("#re_observacion").val();
			// Elimino los espacios en blanco a la izquierda y a la derecha
			cedula = cedula.trim();
			parentesco = parentesco.trim();
			apellidos = apellidos.trim();
			nombres = nombres.trim();
			direccion = direccion.trim();
			sector = sector.trim();
			email = email.trim();
			telefono = telefono.trim();
			observacion = observacion.trim();

			if (parentesco == "") {
				alert("Debe ingresar el parentesco del representante...");
				$("#re_parentesco").focus();
				return false;
			} else if (apellidos == "") {
				alert("Debe ingresar los apellidos del representante...");
				$("#re_apellidos").focus();
				return false;
			} else if (nombres == "") {
				alert("Debe ingresar los nombres del representante...");
				$("#re_nombres").focus();
				return false;
			} else if (telefono == "") {
				alert("Debe ingresar el telefono del representante...");
				$("#re_telefono").focus();
				return false;
			}
			// Si se han ingresado los campos obligatorios se procede a la actualizacion de datos
			$("#img-loader").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
			console.log(id_estudiante);
			$.ajax({
				type: "POST",
				url: "matriculacion/actualizar_representante.php",
				data: "id_representante=" + id_representante +
					"&id_estudiante=" + id_estudiante +
					"&re_apellidos=" + apellidos +
					"&re_nombres=" + nombres +
					"&re_nombre_completo=" + apellidos + " " + nombres +
					"&re_cedula=" + cedula +
					"&re_email=" + email +
					"&re_direccion=" + direccion +
					"&re_sector=" + sector +
					"&re_telefono=" + telefono +
					"&re_observacion=" + observacion +
					"&re_parentesco=" + parentesco,
				success: function(resultado) {
					$("#img-loader").html("");
					$("#formulario_representante").hide();
					$("#datos_estudiante").hide();
					$("#datos_representante").hide();
					console.log(resultado);
				}
			});
		}

		function editarEstudiante(id_estudiante) {
			$.ajax({
				url: "matriculacion/obtener_estudiante.php",
				method: "post",
				data: {
					id_estudiante: id_estudiante
				},
				dataType: "json",
				success: function(data) {
					$("#id_estudiante").val(id_estudiante);
					setearIndice("edit_id_tipo_documento", data.id_tipo_documento);
					$("#edit_dni").val(data.es_cedula);
					$("#edit_apellidos").val(data.es_apellidos);
					$("#edit_nombres").val(data.es_nombres);
					$("#edit_fec_nac").val(data.es_fec_nacim);
					$("#edit_edad").val(calcularEdad(data.es_fec_nacim));
					$("#edit_direccion").val(data.es_direccion);
					$("#edit_sector").val(data.es_sector);
					$("#edit_telefono").val(data.es_telefono);
					$("#edit_email").val(data.es_email);
					setearIndice("edit_genero", data.id_def_genero);
					setearIndice("edit_nacionalidad", data.id_def_nacionalidad);

					$('#editStudentModal').modal('show');
				},
				error: function(jqXHR, textStatus) {
					console.log(jqXHR.responseText);
				}
			});
			//$("#editStudentModal").modal('show');
		}

		//calcular la edad de una persona
		//recibe la fecha como un string en formato AAAA-MM-DD
		//devuelve un entero con la edad. Devuelve false en caso de que la fecha sea incorrecta o mayor que el dia actual
		function calcular_edad(fecha) {
			//calculo la fecha de hoy
			hoy = new Date()
			//alert(hoy)

			//calculo la fecha que recibo
			//la descompongo en un array
			var array_fecha = fecha.split("-");

			if (array_fecha.length != 3)
				return false

			//compruebo que anio, mes, dia son correctos
			var anio
			anio = parseInt(array_fecha[0])
			if (isNaN(anio))
				return false

			var mes
			mes = parseInt(array_fecha[1])
			if (isNaN(mes))
				return false

			var dia
			dia = parseInt(array_fecha[2])
			if (isNaN(dia))
				return false

			//si el anio de la fecha que recibo no tiene 4 cifras retorna falso
			if (String(anio).length != 4)
				return false

			//resto los años de las dos fechas 
			edad = hoy.getFullYear() - anio - 1; //-1 porque no se si ha cumplido años ya este año 

			//si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido 
			if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0 
				return edad

			if (hoy.getMonth() + 1 - mes > 0)
				return edad + 1

			//entonces es que eran iguales. miro los dias 
			//si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido 
			if (hoy.getUTCDate() - dia >= 0)
				return edad + 1

			return edad
		}

		function salirRepresentante() {
			$("#mensaje").html("");
			$("#formulario_representante").css("display", "none");
			$("#datos_estudiante").hide();
			document.getElementById("estudiante_nuevo").focus();
			$("#formulario_representante").hide();
		}

		function quitarEstudiante(id_estudiante) {
			// Quitar al estudiante del paralelo

			var id_paralelo = $("#id_paralelo").val();

			if (id_estudiante == "") {
				$("#mensaje").html("No se ha pasado el parámetro de id_estudiante...");
				//salirEstudiante(false);
			} else {
				//
				Swal.fire({
					title: "¿Desea quitar este estudiante de este periodo lectivo?",
					icon: "warning",
					showCancelButton: true,
					confirmButtonColor: "#d33",
					cancelButtonColor: "#3085d6",
					confirmButtonText: "Sí, quítelo!",
					cancelButtonText: 'Cancelar',
				}).then((result) => {
					if (result.isConfirmed) {
						// Primero verificar si tiene calificaciones asociadas
						$.ajax({
							type: "POST",
							url: "matriculacion/verificar_si_tiene_notas.php",
							data: "id_estudiante=" + id_estudiante,
							dataType: "json",
							success: function(response) {
								if (!response.error) {
									// tiene calificaciones asociadas... preguntar si desea eliminar
									// permanentemente al estudiate...
									Swal.fire({
										title: "El estudiante no tiene calificaciones asociadas...",
										text: "¿Desea eliminar permanentemente a este estudiante? No podrá recuperar el registro que va a ser eliminado!",
										icon: "warning",
										showCancelButton: true,
										showDenyButton: true,
										confirmButtonText: "Sí, elimínelo!",
										cancelButtonText: "Cancelar",
										confirmButtonColor: "#d33",
										denyButtonColor: "#3085d6",
										denyButtonText: `No, manténgalo`
									}).then((result) => {
										if (result.isConfirmed) {
											alert('Estudiante será eliminado de la base de datos...');
											$.ajax({
												type: "POST",
												url: "matriculacion/eliminar_estudiante.php",
												data: "id_estudiante=" + id_estudiante,
												dataType: "json",
												success: function(resultado) {
													// alert(resultado);
													Swal.fire({
														title: resultado.titulo,
														text: resultado.mensaje,
														icon: resultado.icono
													});
													contarEstudiantesParalelo(id_paralelo);
													cargar_estudiantes_des_matriculados();
												}
											});
										} else if (result.isDenied) {
											alert('El estudiante solamente será des-matriculado pero seguirá existiendo su registro.');
											$.ajax({
												type: "POST",
												url: "matriculacion/quitar_estudiante.php",
												data: "id_estudiante=" + id_estudiante + "&id_paralelo=" + id_paralelo,
												dataType: "json",
												success: function(resultado) {
													// alert(resultado);
													Swal.fire({
														title: resultado.titulo,
														text: resultado.mensaje,
														icon: resultado.icono
													});
													contarEstudiantesParalelo(id_paralelo);
													cargar_estudiantes_des_matriculados();
												}
											});
										}
									});
								} else {
									$.ajax({
										type: "POST",
										url: "matriculacion/quitar_estudiante.php",
										data: "id_estudiante=" + id_estudiante + "&id_paralelo=" + id_paralelo,
										dataType: "json",
										success: function(resultado) {
											// alert(resultado);
											Swal.fire({
												title: resultado.titulo,
												text: resultado.mensaje,
												icon: resultado.icono
											});
											contarEstudiantesParalelo(id_paralelo);
											cargar_estudiantes_des_matriculados();
										}
									});
								}
							}
						});
					}
				});

				// if (confirm("¿Desea quitar este estudiante de este periodo lectivo?")) {
				// 	$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");

				// 	// Primero verifico que no tenga calificaciones
				// 	$.ajax({
				// 		type: "POST",
				// 		url: "matriculacion/verificar_si_tiene_notas.php",
				// 		data: "id_estudiante=" + id_estudiante,
				// 		dataType: "json",
				// 		success: function (response) {

				// 		}
				// 	});

				// 	// $.ajax({
				// 	// 	type: "POST",
				// 	// 	url: "matriculacion/quitar_estudiante.php",
				// 	// 	data: "id_estudiante=" + id_estudiante + "&id_paralelo=" + id_paralelo,
				// 	// 	success: function(resultado) {
				// 	// 		alert(resultado);
				// 	// 		contarEstudiantesParalelo(id_paralelo);
				// 	// 		cargar_estudiantes_des_matriculados();
				// 	// 	}
				// 	// });
				// }
			}
		}

		function seleccionarEstudiante() {
			var id_paralelo = $("#id_paralelo").val();
			var id_estudiante = $("#id_estudiante").val();
			var nombre_estudiante = $("#nombre_estudiante").val();

			// Primero debemos verificar si ya se ha matriculado al estudiante
			$.ajax({
				type: "POST",
				url: "matriculacion/verificar_estudiante_matriculado.php",
				data: "id_estudiante=" + id_estudiante,
				dataType: "json",
				success: function(resultado) {
					$("#img-loader").html("");
					if (!resultado.error) {
						//Aqui va el proceso para matricular al estudiante antiguo
						$.post("matriculacion/seleccionar_estudiante.php", {
								id_estudiante: id_estudiante,
								id_paralelo: id_paralelo,
								nombre_estudiante: nombre_estudiante
							},
							function(resultado) {
								if (resultado == false) {
									alert("Error");
								} else {
									// Mensaje de salida
									alert(resultado);
									contarEstudiantesParalelo(id_paralelo);
									$("#form-search")[0].reset();
									$("#id_estudiante").val("");
									$("#storyStudentModal").modal('hide');
								}
							}
						);
					} else {
						alert(resultado.mensaje);
					}
				}
			});
		}

		function actualizar_estado_retirado(obj, id_estudiante) {
			if (obj.checked) estado_retirado = "S";
			else estado_retirado = "N";
			$.ajax({
				type: "POST",
				url: "matriculacion/actualizar_estado_retirado.php",
				data: "id_estudiante=" + id_estudiante + "&es_retirado=" + estado_retirado,
				success: function(resultado) {
					// No desplega nada... esto es solo para ejecutar el codigo php
				}
			});
		}

		function certificadoMatricula(id_estudiante) {
			var id_paralelo = document.getElementById("id_paralelo").value;
			document.getElementById("id_paralelo_certificado").value = id_paralelo;
			document.getElementById("id_estudiante").value = id_estudiante;
			document.getElementById("formulario_certificado").submit();
		}
	</script>
</head>

<body>
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Matriculación
				<small>Paralelos</small>
			</h1>
		</section>
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box box-info">
				<div class="box-header">
					<a id="new_student" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newStudentModal"><i class="fa fa-plus-circle"></i> Nuevo Estudiante</a>

					<a id="inactive_students" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deletedStudentModal"><i class="fa fa-gear"></i> Estudiantes Inactivos</a>

					<div id="search_student" class="box-tools fuente9">
						<form id="form-search" action="matriculacion/buscar_estudiantes_antiguos.php" method="POST">
							<div class="input-group input-group-sm" style="width: 400px;">
								<input type="text" name="input_search" id="input_search" class="form-control pull-right text-uppercase fuente9" placeholder="Ingrese Nombre para Histórico del Estudiante...">

								<div class="input-group-btn">
									<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form id="form_id" action="" class="form-horizontal">
								<div class="form-group">
									<label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

									<div class="col-sm-11">
										<select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
											<option value="">Seleccione...</option>
										</select>
										<span id="mensaje1" style="color: #e73d4a"></span>
									</div>
								</div>
							</form>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<div id="total_registros_estudiantes" class="paginacion">
								<table class="fuente8" width="100%" cellspacing=4 cellpadding=0>
									<tr>
										<td>
											<div id="contar_estudiantes_por_genero">
												<!-- Aqui va el conteo de estudiantes por genero -->
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table id="t_estudiantes" class="table fuente8">
								<thead>
									<tr>
										<th>#</th>
										<th>Id</th>
										<th>Mat.</th>
										<th>Apellidos</th>
										<th>Nombres</th>
										<th>DNI</th>
										<th>Fec.Nacim.</th>
										<th>Edad</th>
										<th>Género</th>
										<th>Desertor</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody id="tbody_estudiantes">
									<!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
								</tbody>
							</table>
						</div>
					</div>
					<form id="formulario_certificado" action="reportes/certificado_matricula.php" method="post" target="_blank">
						<div id="ver_reporte" style="text-align:left;margin-top:2px;display:none">
							<input id="id_paralelo_certificado" name="id_paralelo_certificado" type="hidden" />
							<input id="id_estudiante" name="id_estudiante" type="hidden" />
							<input type="submit" value="Ver Reporte" />
						</div>
					</form>
				</div>
			</div>
		</section>
	</div>
	<!-- New Student Modal -->
	<div class="modal fade" id="newStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title text-center" id="myModalLabel1">Estudiante Nuevo</h4>
				</div>
				<form id="form_insert" action="" method="post" autocomplete="off">
					<div class="modal-body fuente9">
						<div class="form-group row">
							<label for="new_id_tipo_documento" class="col-sm-2 col-form-label requerido_antes">Tipo de Documento:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="new_id_tipo_documento" name="new_id_tipo_documento" required>
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje2" style="color: #e73d4a"></span>
							</div>
							<label for="new_dni" class="col-sm-1 col-form-label requerido_despues">DNI:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="new_dni" name="new_dni" value="">
								<span id="mensaje3" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_apellidos" class="col-sm-2 col-form-label requerido_despues">Apellidos:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="new_apellidos" name="new_apellidos" value="">
								<span id="mensaje4" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_nombres" class="col-sm-2 col-form-label requerido_despues">Nombres:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="new_nombres" name="new_nombres" value="">
								<span id="mensaje5" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_fec_nac" class="col-sm-2 col-form-label requerido_antes">Fecha de nacimiento:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="new_fec_nac" name="new_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
								<span id="mensaje6" style="color: #e73d4a"></span>
							</div>

							<label for="new_edad" class="col-sm-1 col-form-label">Edad:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="new_edad" name="new_edad" value="" disabled>
								<span id="mensaje6" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_direccion" class="col-sm-2 col-form-label requerido_despues">Dirección:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="new_direccion" name="new_direccion" value="">
								<span id="mensaje7" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_sector" class="col-sm-2 col-form-label requerido_despues">Sector:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control mayusculas" id="new_sector" name="new_sector" value="">
								<span id="mensaje8" style="color: #e73d4a"></span>
							</div>
							<label for="new_telefono" class="col-sm-1 col-form-label requerido_despues">Celular:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="new_telefono" name="new_telefono" value="">
								<span id="mensaje9" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_email" class="col-sm-2 col-form-label">E-mail:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="new_email" name="new_email" value="">
								<span id="mensaje10" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="new_genero" class="col-sm-2 col-form-label requerido_despues">Género:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="new_genero" name="new_genero">
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje11" style="color: #e73d4a"></span>
							</div>
							<label for="new_nacionalidad" class="col-sm-2 col-form-label requerido_despues">Nacionalidad:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="new_nacionalidad" name="new_nacionalidad">
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje12" style="color: #e73d4a"></span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
						<button type="button" class="btn btn-success" onclick="insertarEstudiante()"><span class="glyphicon glyphicon-save"></span> Insertar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Student Modal -->
	<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title text-center" id="myModalLabel2">Editar Estudiante</h4>
				</div>
				<form id="form_edit" action="" method="post" autocomplete="off">
					<input type="hidden" name="id_estudiante" id="id_estudiante">
					<div class="modal-body fuente9">
						<div class="form-group row">
							<label for="edit_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="edit_id_tipo_documento" name="edit_id_tipo_documento" required>
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje2" style="color: #e73d4a"></span>
							</div>
							<label for="edit_dni" class="col-sm-1 col-form-label">DNI:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="edit_dni" name="edit_dni" value="">
								<span id="mensaje3" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="edit_apellidos" name="edit_apellidos" value="">
								<span id="mensaje4" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_nombres" class="col-sm-2 col-form-label">Nombres:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="edit_nombres" name="edit_nombres" value="">
								<span id="mensaje5" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="edit_fec_nac" name="edit_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
								<span id="mensaje6" style="color: #e73d4a"></span>
							</div>

							<label for="edit_edad" class="col-sm-1 col-form-label">Edad:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="edit_edad" name="edit_edad" value="" disabled>
								<span id="mensaje6" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_direccion" class="col-sm-2 col-form-label">Dirección:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control mayusculas" id="edit_direccion" name="edit_direccion" value="">
								<span id="mensaje7" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_sector" class="col-sm-2 col-form-label">Sector:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control mayusculas" id="edit_sector" name="edit_sector" value="">
								<span id="mensaje8" style="color: #e73d4a"></span>
							</div>
							<label for="edit_telefono" class="col-sm-1 col-form-label">Celular:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="edit_telefono" name="edit_telefono" value="">
								<span id="mensaje9" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_email" class="col-sm-2 col-form-label">E-mail:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="edit_email" name="edit_email" value="">
								<span id="mensaje10" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_genero" class="col-sm-2 col-form-label">Género:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="edit_genero" name="edit_genero">
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje11" style="color: #e73d4a"></span>
							</div>
							<label for="edit_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
							<div class="col-sm-4">
								<select class="form-control fuente9" id="edit_nacionalidad" name="edit_nacionalidad">
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje12" style="color: #e73d4a"></span>
							</div>
						</div>
						<div class="form-group row">
							<label for="edit_id_paralelo" class="col-sm-2 col-form-label">Nuevo Paralelo:</label>
							<div class="col-sm-10">
								<select class="form-control fuente9" id="edit_id_paralelo" name="edit_id_paralelo">
									<option value="">Seleccione...</option>
								</select>
								<span id="mensaje13" style="color: #e73d4a"></span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
						<button type="button" class="btn btn-success" onclick="actualizarEstudiante()"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Estudiantes Des-matriculados Modal -->
	<div class="modal fade" id="deletedStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title text-center" id="myModalLabel3">Estudiante Des-matriculados</h4>
				</div>
				<div class="col-md-12 table-responsive">
					<table id="t_deleted" class="table fuente8">
						<thead>
							<tr>
								<th>#</th>
								<th>Id</th>
								<th>Mat.</th>
								<th>Apellidos</th>
								<th>Nombres</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="tbody_deleted">
							<!-- Aquí se pintarán los registros recuperados de la BD mediante AJAX -->
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Histórico de un estudiante Modal -->
	<div class="modal fade" id="storyStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title text-center" id="myModalLabel4">Histórico del estudiante</h4>
					<h5 id="nombre_estudiante" class="modal-title text-center"></h5>
				</div>

				<div class="col-md-12 table-responsive">
					<table id="t_found" class="table table-striped table-hover fuente8">
						<thead>
							<tr>
								<th>Id</th>
								<th>Modalidad</th>
								<th>Periodo Lectivo</th>
								<th>Curso</th>
								<th>Paralelo</th>
								<th>Aprobado</th>
							</tr>
						</thead>
						<tbody id="tbody_found">

						</tbody>
					</table>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
					<button type="button" class="btn btn-success" onclick="seleccionarEstudiante()"><span class="glyphicon glyphicon-save"></span> Seleccionar</a>
				</div>
			</div>
		</div>
	</div>
</body>

</html>