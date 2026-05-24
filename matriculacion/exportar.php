<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Exportar a CSV
            <small>Paralelos</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <div style="margin-top: 10px;">
                    <form id="form_id" action="matriculacion/exportar_csv.php" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                            <div class="col-sm-7">
                                <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                    <!-- Aquí se poblarán los datos mediante AJAX -->
                                </select>
                                <span id="mensaje" style="color: #e73d4a"></span>
                            </div>

                            <div class="col-sm-4">
                                <button type="submit" id="export_csv" name="export" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Exportar a CSV</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        cargar_paralelos();
    });

    function cargar_paralelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#id_paralelo').append(resultado);
            }
        });
    }
</script>