<link href="calendario/calendar-blue.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">
	<div id="titulo_pagina">
		<?php echo $_SESSION['titulo_pagina'] ?>
	</div>
	<div id="barra_principal">
		<table id="tabla_navegacion" style="border: none;" cellpadding="0" cellspacing="0">
			<tr>
				<td width="5%" class="fuente9" class="text-right"> Per&iacute;odo:&nbsp; </td>
				<td width="5%">
					<select id="cboPeriodosEvaluacion" class="fuente8">
						<option value="0"> Seleccione... </option>
					</select>
				</td>
				<td width="5%" class="fuente9" class="text-center"> &nbsp;Paralelo:&nbsp; </td>
				<td width="5%"> <select id="cboParalelo" class="fuente8">
						<option value="0"> Seleccione... </option>
					</select> </td>
				<td width="*">&nbsp;</td> <!-- Esto es para igualar las columnas -->
			</tr>
		</table>
	</div>
	<div id="pag_periodo_evaluacion">
		<!-- Aqui va la paginacion de los periodos de evaluacion encontrados -->
		<div class="header2"> LISTA DE APORTES DE EVALUACION EXISTENTES </div>
		<div id="tabla" class="table-responsive">
			<table class="table fuente8">
				<thead>
					<tr>
						<th width='2%'>
							<input id="check_all" name="main_checkbox" type="checkbox" style="vertical-align: middle;">
						</th>
						<th width='5%'>Nro.</th>
						<th width='5%'>Id.</th>
						<th width='10%'>Nombre</th>
						<th width='10%'>Fecha de Apertura</th>
						<th width='10%'>Fecha de Cierre</th>
						<th width='10%'>Estado</th>
						<th width='10%'>Acciones</th>
						<th width='38%'>
							<button type="button" class="btn btn-success btn-xs ocultar" data-toggle="modal" data-target="#procesarCierresModal" name="process_all" id="process_all">Procesar</button>
						</th>
					</tr>
				</thead>
				<tbody>
					<!-- Aquí se va a pintar los registros de forma dinámica mediante AJAX -->
				</tbody>
			</table>
		</div>
		<div id="lista_aportes_evaluacion" style="text-align:center"> </div>
	</div>
	<div id="mensaje" class="mensaje">Debe seleccionar un Per&iacute;odo de Evaluaci&oacute;n...</div>
</div>
<!-- Procesar Cierre Modal -->
<div class="modal fade" id="procesarCierresModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title text-center" id="myModalLabel2">Procesar Fecha de Cierre</h4>
			</div>
			<form id="form_process" action="" method="post" autocomplete="off">
				<input type="hidden" name="id_estudiante" id="id_estudiante">
				<div class="modal-body fuente9">
					<div class="form-group row">
						<label for="procesar_fecha_cierre" class="col-sm-2 col-form-label">Fecha de cierre:</label>
						<div class="col-sm-10">
							<input type="date" class="form-control" id="procesar_fecha_cierre" name="procesar_fecha_cierre" value="" placeholder="aaaa-mm-dd" maxlength="10">
							<span id="mensaje_error_fecha_cierre" style="color: #e73d4a"></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
					<button type="button" class="btn btn-success" onclick="procesarFechaCierre()"><span class="glyphicon glyphicon-save"></span> Procesar</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/lang/calendar-sp.js"></script>
<script type="text/JavaScript" language="javascript" src="calendario/calendar-setup.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		cargarPeriodosEvaluacion();
		cargarParalelos();
		//If check_all checked then check all table rows
		$("#check_all").on("click", function() {
			if ($("input:checkbox").prop("checked")) {
				$("input:checkbox[name='row-check']").prop("checked", true);
			} else {
				$("input:checkbox[name='row-check']").prop("checked", false);
			}
			toggleBtnState();
		});
		// Check each table row checkbox
		$(document).on('change', 'input[type="checkbox"][name="row-check"]', function() {
			if ($('input[type="checkbox"][name="row-check"]').length == $(
					'input[type="checkbox"][name="row-check"]:checked').length) {
				$('input[type="checkbox"][name="main_checkbox"]').prop('checked', true);
			} else {
				$('input[type="checkbox"][name="main_checkbox"]').prop('checked', false);
			}
			toggleBtnState();
		});
		$("#cboPeriodosEvaluacion").change(function(e) {
			e.preventDefault();
			$("#tabla tbody").html("");
			$('#cboParalelo').val(0);
			$("#mensaje").html("Debe seleccionar un Paralelo...");
			$("#cboParalelo").focus();
		});
		$("#cboParalelo").change(function(e) {
			e.preventDefault();
			var id_paralelo = $("#cboParalelo").val();
			if (id_paralelo == 0) {
				$("#tabla tbody").html("");
				$("#mensaje").html("Debe seleccionar un Paralelo...");
			} else {
				// Aquí toca cargar los cierres definidos para el Paralelo y el periodo elegidos
				cargarAportesEvaluacion(false);
			}
		});
	});

	function sel_texto(input) {
		$(input).select();
	}

	function toggleBtnState() {
		let selectedItems = $('input[type="checkbox"][name="row-check"]:checked').length;

		if (selectedItems > 0) {
			$('button#process_all').text('Procesar (' + selectedItems + ')').removeClass('ocultar');
		} else {
			$('button#process_all').addClass('ocultar');
		}
	}

	function procesarFechaCierre() {
		let fecha_cierre = $("#procesar_fecha_cierre").val();
		if (fecha_cierre.trim() == "") {
			Swal.fire({
				title: "Oops!",
				text: "Debes ingresar una fecha de cierre!",
				icon: "error"
			});
		} else {
			let checkbox = $('input[type="checkbox"][name="row-check"]:checked');
			if (checkbox.length > 0) {
				var checkbox_value = [];
				$(checkbox).each(function() {
					checkbox_value.push($(this).val());
				});

				$.ajax({
					url: "aportes_evaluacion/procesar_fecha_cierre.php",
					method: "POST",
					data: {
						checkbox_value: checkbox_value,
						fecha_cierre: fecha_cierre
					},
					dataType: 'json',
					success: function(response) {
						//console.log(response.contador);
						cargarAportesEvaluacion(false);
						Swal.fire({
							title: "Muy bien!",
							text: "Se procesaron " + response.contador + " registros!",
							icon: "success"
						});
						$("#form_process")[0].reset();
						$('#procesarCierresModal').modal('hide');
						$('button#process_all').addClass('ocultar');
						$('input[type="checkbox"][name="main_checkbox"]').prop('checked', false);
					}
				});
			} else {
				alert("Seleccione al menos un registro");
			}
		}
	}

	function cargarPeriodosEvaluacion() {
		$.get("scripts/cargar_periodos_evaluacion.php", {},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$("#cboPeriodosEvaluacion").append(resultado);
				}
			}
		);
	}

	function cargarParalelos() {
		$.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
			if (resultado == false) {
				alert("Error");
			} else {
				$('#cboParalelo').append(resultado);
			}
		});
	}

	function cargarAportesEvaluacion(iDesplegar) {
		var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
		var id_paralelo = $("#cboParalelo").val();
		if (id_periodo_evaluacion == 0) {
			$("#mensaje").css("color", "red");
			$("#mensaje").html("Debe seleccionar un Per&iacute;odo de Evaluaci&oacute;n...");
			$("#lista_aportes_evaluacion").html("");
			$("#cboPeriodosEvaluacion").focus();
		} else if (id_paralelo == 0) {
			$("#mensaje").css("color", "red");
			$("#mensaje").html("Debe seleccionar un Paralelo...");
			$("#lista_aportes_evaluacion").html("");
			$("#cboParalelo").focus();
		} else {
			$.ajax({
				type: "POST",
				url: "scripts/listar_aportes_estados.php",
				data: "id_periodo_evaluacion=" + id_periodo_evaluacion + "&id_paralelo=" + id_paralelo,
				success: function(resultado) {
					if (!iDesplegar) $("#mensaje").html("");
					//$("#lista_aportes_evaluacion").html(resultado);
					$("#tabla tbody").html(resultado);
					$("#form_process")[0].reset();
					$('button#process_all').addClass('ocultar');
					$('input[type="checkbox"][name="main_checkbox"]').prop('checked', false);
				}
			});
		}
	}

	function actualizarAporteEvaluacion(id, nombre, fecha_apertura, fecha_cierre) {
		var txt_fecha_apertura = fecha_apertura.value;
		var txt_fecha_cierre = fecha_cierre.value;
		var id_paralelo = $("#cboParalelo").val();
		$("#mensaje").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
		$.ajax({
			type: "POST",
			url: "scripts/actualizar_fechas_aporte_evaluacion.php",
			data: "id_aporte_evaluacion=" + id + "&id_paralelo=" + id_paralelo + "&ap_nombre=" + nombre + "&fecha_apertura=" + txt_fecha_apertura + "&fecha_cierre=" + txt_fecha_cierre,
			success: function(resultado) {
				$("#mensaje").css("color", "blue");
				$("#mensaje").html(resultado);
				cargarAportesEvaluacion(true);
			}
		});
	}
</script>