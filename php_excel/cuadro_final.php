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
		height: 36px;
	}
</style>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			<?php echo "REPORTES " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
		</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box box-solid">
			<div class="box-body">
				<div class="barra_principal">
					<form id="formulario_periodo" action="php_excel/reporte_cuadro_final.php" method="post">
						<table id="tabla_navegacion">
							<tr>
								<td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
								<td>
									<select id="id_paralelo" name="id_paralelo" class="fuente9">
										<option value="">Seleccione...</option>
									</select>
								</td>
								<td>
									&nbsp;
									<button type="submit" id="export_to_excel" class="btn btn-primary btn-sm">
										<i class="fa fa-file-excel-o"></i> Exportar a Excel
									</button>
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div id="mensaje" style="font-size: 12px; margin-top: 2px;" class="text-center"></div>
			</div>
		</div>
	</section>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		cargarParalelos();
	});

	function cargarParalelos() {
		$.get("scripts/cargar_paralelos_especialidad.php", {},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$("#id_paralelo").append(resultado);
				}
			}
		);
	}

	$("#formulario_periodo").submit(function(e) {

		let id_paralelo = $("#id_paralelo").val();

		if (id_paralelo == 0) {
			swal("Mensaje", "Debe seleccionar un paralelo...", "error");
			return false;
		} else {
			return true;
		}
	});
</script>