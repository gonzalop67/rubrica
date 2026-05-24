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
    <div id="titulo_pagina">
        <?php echo "REPORTES " . $_SESSION['titulo_pagina'] . " A EXCEL" ?>
    </div>
    <div class="barra_principal">
        <form id="formulario_periodo" action="reportes_a_excel/reporte_sub_periodo.php" method="post">
            <table id="tabla_navegacion">
                <tr>
                    <td class="fuente9">&nbsp;Periodo: &nbsp;</td>
                    <td>
                        <select id="id_periodo_evaluacion" name="id_periodo_evaluacion" class="fuente9" required>
                            <option value="">Seleccione...</option>
                        </select>
                    </td>
                    <td class="fuente9">&nbsp;Paralelo: &nbsp;</td>
                    <td>
                        <select id="id_paralelo" name="id_paralelo" class="fuente9" required>
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
</div>

<script type="text/javascript">
    $(document).ready(function() {
        cargarParalelos();
        cargarPeriodosEvaluacion();
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

    function cargarPeriodosEvaluacion() {
        $.get("scripts/cargar_periodos_evaluacion_principales.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#id_periodo_evaluacion").append(resultado);
                }
            }
        );
    }
</script>