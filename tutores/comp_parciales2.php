<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Comportamiento de Parciales
			<small id="nombreParalelo"></small>
		</h1>
	</section>
	<section class="content">
		<!-- Default box -->
		<div class="box box-solid">
			<div class="box-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="cboPeriodosEvaluacion">Período: </label>
							<select name="cboPeriodosEvaluacion" id="cboPeriodosEvaluacion">
								<option value="0">Seleccione...</option>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="cboAportesEvaluacion">Aporte: </label>
							<select name="cboAportesEvaluacion" id="cboAportesEvaluacion">
								<option value="0">Seleccione...</option>
							</select>
						</div>
					</div>
				</div>
				<div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nro.</th>
							<th>Id</th>
							<th>N&oacute;mina</th>
							<th>COMPORTAMIENTO</th>
							<th>EQUIVALENCIA</th>
						</tr>
					</thead>
					<tbody id="tbl_estudiantes">
						<!-- Aquí va la lista de estudiantes obtenida de la base de datos -->
					</tbody>
				</table>
			</div>
			<div class="box-footer">
				<form id="formulario_comportamiento" action="reportes/reporte_comportamiento_parciales.php" method="post" target="_blank">
					<div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
						<input id="id_paralelo" name="id_paralelo" type="hidden" />
						<input id="id_aporte_evaluacion" name="id_aporte_evaluacion" type="hidden" />
						<input class="btn btn-primary btn-xs" type="submit" value="Reporte en PDF" />
					</div>
				</form>
			</div>
		</div>
	</section>
</div>
<!-- /.content-wrapper -->

<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="js/keypress.js"></script>

<script>
	$(document).ready(function() {
		$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>');
		$.ajaxSetup({
			error: function(xhr) {
				alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			}
		});
		cargarPeriodosEvaluacion();
		$("#cboPeriodosEvaluacion").change(function(e) {
			e.preventDefault();
			var id_periodo_evaluacion = $(this).find('option:selected').val();
			if (id_periodo_evaluacion == 0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display", "none");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				cargarAportesEvaluacion();
			}
		});
		$("#cboAportesEvaluacion").change(function(e) {
			e.preventDefault();
			var id_aporte_evaluacion = $(this).find('option:selected').val();
			document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
			var id_paralelo_tutor = $("#id_paralelo_tutor").val();
			// Aqui va el codigo para desplegar las calificaciones del comportamiento
			listarEstudiantesParalelo(id_paralelo_tutor, id_aporte_evaluacion);
			$("#ver_reporte").css("display", "block");
		});
	});

	function cargarPeriodosEvaluacion() {
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

	function cargarAportesEvaluacion() {
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("cboAportesEvaluacion").options.length = 1;
		$.get("scripts/cargar_aportes_principales_evaluacion.php", {
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado) {
				if (resultado == false) {
					$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">No existen aportes de evaluaci&oacute;n asociados a este peri&oacute;do de evaluaci&oacute;n...</td>');
				} else {
					$("#cboAportesEvaluacion").append(resultado);
					$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">Debe elegir un aporte de evaluaci&oacute;n...</td>');
				}
			}
		);
	}

	function sel_texto(input) {
		$(input).select();
	}

	function listarEstudiantesParalelo(id_paralelo, id_aporte_evaluacion) {
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		if (id_periodo_evaluacion == 0) {
			$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">Debe escoger un per&iacute;odo de evaluaci&oacute;n...</td>');
		} else if (id_paralelo == 0) {
			$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">No se ha pasado correctamente el id_paralelo...</td>');
		} else if (id_aporte_evaluacion == 0) {
			$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center text-danger">No se ha pasado correctamente el id_aporte_evaluacion...</td>');
		} else {
			$("#tbl_estudiantes").html('<tr><td colspan="5" class="text-center"><img src="imagenes/ajax-loader-red-dog.GIF" alt="procesando..."></td></tr>');
			console.log(id_paralelo + ',' + id_aporte_evaluacion)
			$.ajax({
				type: "post",
				url: "tutores/listar_comportamiento.php",
				data: {
					id_paralelo: id_paralelo,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				dataType: "html",
				success: function(response) {
					// console.log(response)
					$("#tbl_estudiantes").html(response)
				}
			});
		}
	}

	function editarCalificacion(obj, id_estudiante, id_paralelo, id_aporte_evaluacion) {
		var str = obj.value;
		var id = obj.id;
		var fila = id.substr(id.indexOf("_") + 1);

		//Validacion de la calificacion
		str = eliminaEspacios(str);
		var permitidos = ['a', 'b', 'c', 'd', 'e', 'A', 'B', 'C', 'D', 'E'];
		var idx = permitidos.indexOf(str);
		if (str != '') {
			if (idx == -1) {
				alert("La calificacion debe estar en el rango de A a E");
				obj.value = "";
			} else {
				$.post("inspeccion/obtener_escala_comportamiento.php", {
						co_calificacion: document.getElementById("puntaje_" + fila).value
					},
					function(resultado) {
						if (resultado == false) { // Si existe algun error
							alert(resultado);
						} else {
							console.log(resultado);
							var JSONEscalaComportamiento = eval('(' + resultado + ')');
							var id_escala_comportamiento = JSONEscalaComportamiento.id_escala_comportamiento;
							document.getElementById('equivalencia_' + fila).value = JSONEscalaComportamiento.ec_relacion;
							$.post("tutores/editar_calificacion.php", {
									id_estudiante: id_estudiante,
									id_paralelo: id_paralelo,
									id_escala_comportamiento: id_escala_comportamiento,
									id_aporte_evaluacion: id_aporte_evaluacion,
									co_calificacion: str.toUpperCase()
								},
								function(resultado) {
									if (resultado) { // Solo si existe resultado
										$("#mensaje_rubrica").html(resultado);
									}
								}
							);
						}
					}
				);
			}
		} else {
			//Aqui va el codigo para eliminar la calificacion del comportamiento
			$.post("tutores/obtener_id_comportamiento_tutor.php", {
					id_paralelo: id_paralelo,
					id_estudiante: id_estudiante,
					id_aporte_evaluacion: id_aporte_evaluacion
				},
				function(resultado) {
					if (resultado == false) { // Si existe algun error
						alert(resultado);
					} else {
						//Aqui va el codigo para recuperar el id_comportamiento_tutor
						var JSONEscalaComportamiento = eval('(' + resultado + ')');
						var id_comportamiento_tutor = JSONEscalaComportamiento.id_comportamiento_tutor;
						if (id_comportamiento_tutor != 0) {
							$.post("tutores/eliminar_comportamiento_tutor.php", {
									id_comportamiento_tutor: id_comportamiento_tutor
								},
								function(resultado) {
									if (resultado == false) {
										alert(resultado);
									} else {
										//Aqui va el mensaje de exito o fracaso al eliminar el registro
										$("#mensaje_rubrica").html(resultado);
										document.getElementById('equivalencia_' + fila).value = '';
									}
								}
							);
						}
					}
				}
			);
		}
	}
</script>