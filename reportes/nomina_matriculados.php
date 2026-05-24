<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reporte
            <small>Matriculados</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <div style="margin-top: 10px;">
                    <form id="form_id" action="reportes/reporte_nomina_matriculados.php" target="_blank" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label for="cboParalelos" class="col-sm-1 control-label text-right">Paralelo:</label>

                            <div class="col-sm-9">
                                <select class="form-control fuente9" name="cboParalelos" id="cboParalelos">
                                    <!-- Aquí vamos a poblar los datos mediante AJAX -->
                                </select>
                                <span id="mensaje1" style="color: #e73d4a"></span>
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-primary">Ver Reporte</button>
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
                    $("#cboParalelos").append(resultado);
                }
            }
        );
    }
</script>