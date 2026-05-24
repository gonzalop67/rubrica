<div class="container">
    <div id="anualesApp" class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 style="font:11pt helvetica;">Calificaciones Anuales</h4>
            </div>
            <form id="form_califica" name="form_califica" action="../dompdf/resumen_anual.php" method="post" target="_blank">
                <div class="panel-body">
                    <div id="img_loader" class="text-center">
                        <!-- Aqui va la imagen del loader... -->
                    </div>
                    <div id="tbl_notas" class="table-responsive">
                        <!-- Aqui van las calificaciones del estudiante -->
                    </div>

                    <input type="hidden" id="idestudiante" name="idestudiante" value="<?php echo $id_estudiante; ?>" />
                    <input type="hidden" id="idperiodolectivo" name="idperiodolectivo" value="<?php echo $id_periodo_lectivo; ?>" />
                    <input type="hidden" id="idparalelo" name="idparalelo" value="<?php echo $id_paralelo; ?>">

                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-10 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Promedio General:</label>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px;">
                            <input type="text" class="form-control fuente9 text-right" id="promedio_general" value="0" disabled>
                        </div>
                    </div>
                </div>
                <div id="btn_enviar_pdf" class="row" style="margin-top: 4px; margin-bottom: 4px;" id="botones_edicion">
                    <div class="col-sm-12 text-center">
                        <button type="submit" class="btn btn-primary fuente9">Exportar a PDF</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#btn_enviar_pdf").hide();
        consultarEstudiante();
    });

    function consultarEstudiante() {
        var id_estudiante = $("#idestudiante").val();
        var id_paralelo = $("#idparalelo").val();

        //Obtener los titulos de los insumos de evaluacion
        $("#img_loader").html("<img src='../imagenes/ajax-loader-blue.GIF' alt='cargando...' />");
        // console.log(id_estudiante); console.log(id_paralelo);

        $.ajax({
            type: "post",
            url: "../scripts/obtener_calificaciones_anuales_id.php",
            data: {
                id_estudiante: id_estudiante,
                id_paralelo: id_paralelo
            },
            dataType: "json",
            success: function (resultado) {
                // console.log(resultado);
                $("#img_loader").html("");
                $("#tbl_notas").html(resultado.cadena);
                $("#promedio_general").val(resultado.promedio);
                $("#btn_enviar_pdf").show();
            }
        });   
    }
</script>