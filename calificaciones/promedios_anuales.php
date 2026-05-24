<style>
	table {
		border: none;
	}
</style>

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			<?php echo "REPORTES " . $_SESSION['titulo_pagina'] ?>
		</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box box-solid">
			<div class="box-body">
				<div id="barra_principal">
					<form id="formulario_periodo" action="php_excel/reporte_anual_excel.php" method="post" target="_self">
						<input id="id_paralelo" name="id_paralelo" type="hidden" />
						<input id="impresion_para_juntas" name="impresion_para_juntas" type="hidden" />
						<table id="tabla_navegacion" cellpadding="0" cellspacing="0">
							<tr>
								<td class="fuente9 text-right"> &nbsp;Paralelo:&nbsp; </td>
								<td>
									<select id="cboParalelos" class="fuente8">
										<option value="0"> Seleccione... </option>
									</select>
								</td>
								<td class="fuente9">
									&nbsp;
									<button type="submit" id="export_to_excel" class="btn btn-primary btn-sm">
										<i class="fa fa-file-excel-o"></i> Exportar a Excel
									</button>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		cargarParalelos();
		$("#cboParalelos").change(function(e) {
			var id_paralelo = $(this).val();
			document.getElementById("id_paralelo").value = id_paralelo;
			if (id_paralelo == 0)
				$("#export_to_excel").hide();
			else
				$("#export_to_excel").show();
		});
		/* $("#imprimir_para_juntas").click(function(e) {
			var chequeado = 0;
			var checkbox = document.getElementById("impresion_para_juntas");
			if ($(this).is(':checked'))
				chequeado = 1;
			else
				chequeado = 0;
			checkbox.value = chequeado;
		}); */
	});

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
</script>