<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Horarios de Clases</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <form id="form_id" action="" class="form-horizontal">
                <div class="form-group">
                    <label for="id_paralelo" class="col-sm-2 control-label text-right">Paralelo:</label>

                    <div class="col-sm-10">
                        <select class="form-control fuente10" name="id_paralelo" id="id_paralelo">
                            <option value="">Seleccione...</option>
                            <?php foreach ($datos['paralelos'] as $v) : ?>
                                <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span id="mensaje1" style="color: #e73d4a"></span>
                    </div>
                </div>
            </form>
            <!-- Línea de división -->
            <hr>
            <!-- message -->
            <div id="message" class="fuente9 text-center">Debe seleccionar un paralelo...</div>
            <!-- table -->
            <table class="table table-bordered fuente9">
                <thead id="horario_cabecera">
                    <!-- Aqui desplegamos los dias de la semana de este paralelo -->
                </thead>
                <tbody id="horario_clases">
                    <!-- Aqui desplegamos las horas clase con su asignatura y docente -->
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });
        
        $("#id_paralelo").change(function(e) {
            e.preventDefault();
            if ($(this).val() == "") {
                $("#message").html("Debe seleccionar un paralelo...");
                $("#horario_cabecera").html("");
                $("#horario_clases").html("");
            } else {
                $("#text_message").html("");
                // Obtengo los días de la semana
                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/dias_semana/obtenerDiasSemanaPorParalelo",
                    data: {
                        id_paralelo: $(this).val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response) {
                        $("#message").html("");
                        $("#horario_cabecera").html(response);
                    }
                });
                // Luego las horas clase con sus asignaturas y docentes
                $.ajax({
                    url: "<?php echo RUTA_URL; ?>/horas_clase/obtenerHorasClasePorParalelo",
                    data: {
                        id_paralelo: $(this).val()
                    },
                    method: "POST",
                    type: "html",
                    success: function(response) {
                        $("#message").html("");
                        $("#horario_clases").html(response);
                    }
                });
            }
        });
    });
</script>