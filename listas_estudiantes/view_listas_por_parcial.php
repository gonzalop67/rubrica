<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			<?php echo "LISTAS DE ESTUDIANTES " . $_SESSION['titulo_pagina'] ?>
		</h1>
	</section>
	<!-- Main content -->
	<section class="content container-fluid">
		<!-- Default box -->
		<div class="box box-info">
			<div class="box-body">
				<form id="formulario_rubrica" action="reportes/listas_por_parcial.php" method="post" target="_blank">
					<div class="row">
						<div class="col-md-2 text-right">
							<label class="control-label" style="position:relative; top:7px;">Subperíodo de evaluación:</label>
						</div>
						<div class="col-md-10">
							<select class="form-control fuente9" id="cboPeriodosEvaluacion" required>
								<option value="">Seleccione...</option>
							</select>
							<span class="help-desk error" id="error-cboPeriodosEvaluacion"></span>
						</div>
					</div>
					<div class="row" style="margin-top: 3px;">
						<div class="col-md-2 text-right">
							<label class="control-label" style="position:relative; top:7px;">Aporte:</label>
						</div>
						<div class="col-md-10">
							<select class="form-control fuente9" id="cboAportesEvaluacion" required>
								<option value="">Seleccione...</option>
							</select>
							<span class="help-desk error" id="error-cboAportesEvaluacion"></span>
						</div>
					</div>
					<div class="row" style="margin-top: 3px;">
						<div class="col-md-2 text-right">
							<label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
						</div>
						<div class="col-md-10">
							<select class="form-control fuente9" id="cboParalelos" required>
								<option value="">Seleccione...</option>
							</select>
							<span class="help-desk error" id="error-cboParalelos"></span>
						</div>
					</div>
					<div class="row" style="margin-top: 3px;">
						<div class="col-md-2 text-right">
							<label class="control-label" style="position:relative; top:7px;">Asignatura:</label>
						</div>
						<div class="col-md-10">
							<select class="form-control fuente9" id="cboAsignaturas" required>
								<option value="">Seleccione...</option>
							</select>
							<span class="help-desk error" id="error-cboAsignaturas"></span>
						</div>
					</div>
					<div class="row" style="margin-top: 4px;">
						<div class="col-md-2">
						</div>
						<div class="col-md-10">
							<button type="submit" class="btn btn-primary btn-md">Ver Listado</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>

<script>
	$(document).ready(function() {
		cargarPeriodosEvaluacion();
		cargarParalelos();

		$("#cboPeriodosEvaluacion").change(function(e) {
			cargarAportesEvaluacion();
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
		$.get("scripts/cargar_aportes_evaluacion.php", {
				id_periodo_evaluacion: id_periodo_evaluacion
			},
			function(resultado) {
				if (resultado == false) {
					Swal.fire({
						title: "Error...",
						text: "No existen aportes de evaluación asociados a este periodo de evaluación...",
						icon: "success"
					});
				} else {
					$("#cboAportesEvaluacion").append(resultado);
					// $("#lista_estudiantes_paralelo").addClass("error");
					// $("#lista_estudiantes_paralelo").html("Debe elegir un aporte de evaluaci&oacute;n...");
				}
			}
		);
	}

	function cargarParalelos() {
		//
	}
</script>