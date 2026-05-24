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
<div id="pagina">
	<div id="titulo_pagina">
		<?php echo "REPORTES " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
	</div>
	<div class="barra_principal">
		<form id="formulario_periodo" action="php_excel/reporte_padron_electoral.php" method="post">
			<table id="tabla_navegacion">
				<tr>
					<td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
					<td>
						<select id="cboParalelos" name="cboParalelos" class="fuente9">
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
<!-- <div id="pagina">
    <div id="titulo_pagina">
    	<?php echo "REPORTE " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div id="barra_principal">
      <form id="formulario_periodo" action="php_excel/reporte_padron_electoral.php" method="post">
          <table id="tabla_navegacion" border="0" cellpadding="0" cellspacing="0">
             <tr>
                <td width="5%" class="fuente9" align="right"> &nbsp;Paralelo:&nbsp; </td>
                <td width="5%"> <select id="cboParalelos" name="cboParalelos" class="fuente8"> <option value="0"> Seleccione... </option> </select> </td>
                <td width="*"> <input type="submit" value="Exportar a Excel" /> </td>
             </tr>
          </table>
      </form>
    </div>
   </div>
   <div id="mensaje" class="error"></div>
</div> -->
<script type="text/javascript">
	$(document).ready(function() {
		cargarParalelos();
	});

	function cargarParalelos() {
		$.get("scripts/cargar_paralelos.php", {},
			function(resultado) {
				if (resultado == false) {
					alert("Error");
				} else {
					$("#cboParalelos").append(resultado);
				}
			}
		);
	}

	$("#formulario_periodo").submit(function(e) {

		let id_paralelo = $("#cboParalelos").val();

		if (id_paralelo == 0) {
			swal("Mensaje", "Debe seleccionar un paralelo...", "error");
			return false;
		} else {
			return true;
		}
	});
</script>