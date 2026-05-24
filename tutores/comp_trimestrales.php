<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Comportamiento Quimestral
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
                </div>
                <div id="tituloNomina" class="header2"> NOMINA DE ESTUDIANTES </div>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nro.</th>
							<th>Id</th>
							<th>N&oacute;mina</th>
							<th>COMPORTAMIENTO</th>
						</tr>
					</thead>
					<tbody id="tbl_estudiantes">
						<!-- Aquí va la lista de estudiantes obtenida de la base de datos -->
					</tbody>
				</table>
            </div>
            <div class="box-footer">
				<form id="formulario_comportamiento" action="reportes/reporte_comportamiento.php" method="post" target="_blank">
                    <div id="ver_reporte" style="text-align:center;margin-top:2px;display:none">
                        <input id="id_paralelo" name="id_paralelo" type="hidden" />
                        <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                        <input class="btn btn-primary btn-xs" type="submit" value="Reporte en PDF" />
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<script>
    $(document).ready(function(){
        $("#tbl_estudiantes").html('<tr><td colspan="4" class="text-center text-danger">Debe seleccionar un per&iacute;odo de evaluaci&oacute;n...</td></tr>');
        cargarPeriodosEvaluacion();
        $("#cboPeriodosEvaluacion").change(function(e){
			e.preventDefault();
			var id_periodo_evaluacion = $(this).find('option:selected').val();
			var id_paralelo_tutor = $("#id_paralelo_tutor").val();
			if(id_periodo_evaluacion==0) {
				alert("Debe seleccionar un periodo de evaluacion...");
				$("#ver_reporte").css("display","none");
				$("#cboPeriodosEvaluacion").focus();
			} else {
				// Aqui va el codigo para desplegar las calificaciones del comportamiento
				// alert("id_paralelo_tutor = " + id_paralelo_tutor)
				listarEstudiantesParalelo(id_paralelo_tutor);
				$("#ver_reporte").css("display","block");
			}
		});
    });
    function cargarPeriodosEvaluacion()
	{
		$.get("scripts/cargar_periodos_evaluacion_principales.php", { },
			function(resultado)
			{
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#cboPeriodosEvaluacion").append(resultado);
				}
			}
		);
	}
    function listarEstudiantesParalelo(id_paralelo)
	{
		var id_periodo_evaluacion = document.getElementById("cboPeriodosEvaluacion").value;
		document.getElementById("id_paralelo").value = id_paralelo;
		document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
		if(id_periodo_evaluacion==0) {
            $("#tbl_estudiantes").html('<tr><td colspan="4" class="text-center text-danger">Debe escoger un per&iacute;odo de evaluaci&oacute;n...</td></tr>');
		} else if(id_paralelo==0) {
            $("#tbl_estudiantes").html('<tr><td colspan="4" class="text-center text-danger">Debe escoger un paralelo...</td></tr>');
		} else {
            $("#tbl_estudiantes").html('<tr><td colspan="4" class="text-center"><img src="imagenes/ajax-loader-blue.GIF" alt="procesando..." /></td></tr>');
			$.post("tutores/listar_estudiantes_comp_trimestral.php", 
				{ 
					id_paralelo: id_paralelo,
					id_periodo_evaluacion: id_periodo_evaluacion
				},
				function(resultado)
				{
					// console.log(resultado)
					$("#tbl_estudiantes").html(resultado);
					// $("#ver_reporte").css("display","block");
				}
			);
		}
	}
</script>