<div class="container">
    <div id="parcialesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones de Sub-Periodo</h4>
            </div>
            <div class="panel-body">
                <form id="form_parciales" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="font:10pt helvetica;">Sub Periodo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select id="cboPeriodosEvaluacion" class="form-control" style="font:10pt helvetica;">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span id="mensaje1" class="help-desk"></span>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <div id="img_loader" class="text-center">
                    <!-- Aqui va la imagen del loader... -->
                </div>
                <div id="tbl_notas" class="table-responsive">
                    <!-- Aqui van las calificaciones del estudiante -->
                </div>
                <form id="form_califica" name="form_califica" target="_blank" action="../dompdf/resumen_periodo.php" method="post">
                    <input type="hidden" id="idestudiante" name="idestudiante" value="<?php echo $id_estudiante; ?>" />
                    <input type="hidden" id="idperiodolectivo" name="idperiodolectivo" value="<?php echo $id_periodo_lectivo; ?>" />
                    <input type="hidden" id="idparalelo" name="idparalelo" value="<?php echo $id_paralelo; ?>">
                    <input type="hidden" id="idperiodoevaluacion" name="idperiodoevaluacion" value="0" />
                    <div id="btn_enviar_pdf" class="row" style="margin-top: 4px;" id="botones_edicion">
                        <div class="col-sm-12 text-center">
                            <button type="submit" class="btn btn-primary fuente9">Exportar a PDF</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#btn_enviar_pdf").hide();
        cargarPeriodosEvaluacion();
        $("#cboPeriodosEvaluacion").change(function(e){
            if($(this).val()==0){
                $("#mensaje1").html("Debe seleccionar un Quimestre...");
                $("#tbl_notas").html("");
                $("#btn_enviar_pdf").hide();
            }else{
                $("#mensaje1").html("");
                obtenerDetallePeriodoEvaluacion();
            }
		});
    });
    function cargarPeriodosEvaluacion()
	{
		$.get("cargar_periodos_evaluacion_principales.php", { },
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
    function obtenerDetallePeriodoEvaluacion()
    {
        var id_estudiante = $("#idestudiante").val();
        var id_paralelo = $("#idparalelo").val();
        var id_periodo_evaluacion = $("#cboPeriodosEvaluacion").val();
        $("#idperiodoevaluacion").val($("#cboPeriodosEvaluacion").val());

        //Obtener los titulos de los insumos de evaluacion
        $("#img_loader").html("<img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' />");
        //console.log(id_estudiante); console.log(id_paralelo); console.log(id_periodo_evaluacion);
        
        $.post("../scripts/obtener_calificaciones_quimestrales_id.php", 
			{
				id_periodo_evaluacion: id_periodo_evaluacion,
                id_estudiante: id_estudiante,
                id_paralelo: id_paralelo
			},
			function(resultado)
			{
                $("#img_loader").html("");
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
                    $("#tbl_notas").html(resultado);
				}
			}
		);
        $("#btn_enviar_pdf").show(); 
    }
</script>