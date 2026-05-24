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
            Reporte
            <small>Periodo Lectivo a Excel</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <div style="margin-top: 10px;">
                    <form id="form_id" action="reportes_a_excel/reporte_periodo_lectivo.php" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                            <div class="col-sm-9">
                                <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                    <!-- Aquí vamos a poblar los datos mediante AJAX -->
                                </select>
                                <span id="mensaje1" style="color: #e73d4a"></span>
                            </div>

                            <div class="col-sm-2">
                            <button type="submit" id="export_to_excel" class="btn btn-primary btn-sm">
                            <i class="fa fa-file-excel-o"></i> Exportar a Excel
                        </button>
                            </div>
                        </div>
                    </form>
                </div>
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
</script>