<div class="content-wrapper">
    <div id="reporteParcialesAutoridad" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>
                    <?php echo "REPORTE " . $_SESSION['titulo_pagina'] ?>
                </h4>
            </div>
            <div class="panel-body">
                <form id="formulario_periodo" action="php_excel/reporte_quimestral_autoridad.php" method="post" target="_self">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Periodo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboPeriodos" required>
                                <option value="">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 4px">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos" required>
                                <option value="">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje3"></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 4px">
                        <div class="col-sm-2">
                            &nbsp;
                        </div>
                        <div id="ver_reporte" class="col-sm-10">
                            <input id="id_paralelo" name="id_paralelo" type="hidden" />
                            <input id="id_periodo_evaluacion" name="id_periodo_evaluacion" type="hidden" />
                            <button type="submit" class="btn btn-primary">Ver Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        cargarParalelos();
        cargarPeriodosEvaluacion();
        $("#ver_reporte").hide();

        $("#cboPeriodos").change(function(e) {
            e.preventDefault();
            $("#ver_reporte").hide();
            $("#cboParalelos").val("");
            $("#id_periodo_evaluacion").val($(this).find(":selected").val());
        });

        $("#cboParalelos").change(function(e) {
            e.preventDefault();
            if ($("#cboPeriodos").val() == "") {
                alert("Debe seleccionar un Per&iacute;odo...");
                $("#cboPeriodos").focus();
            } else {
                $("#id_paralelo").val($(this).val());
                if ($(this).val() == 0) {
                    $("#ver_reporte").hide();
                } else {
                    $("#ver_reporte").show();
                }
            }
        });
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

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion_principales.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboPeriodos").append(resultado);
                }
            }
        );
    }
</script>