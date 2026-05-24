<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Periodos Lectivos
            <small>Editar</small>

            <button class="btn btn-info pull-right" onclick="javascript:history.back()">Regresar</button>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Editar Periodo Lectivo</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-periodo-lectivo" action="periodos_lectivos/actualizar_periodo_lectivo.php" method="post">
                                <input type="hidden" name="id_periodo_lectivo" value="<?= $_GET['id_periodo_lectivo'] ?>">
                                <?php
                                //Obtener los datos del periodo lectivo
                                $consulta = $db->consulta("SELECT * FROM sw_periodo_lectivo WHERE id_periodo_lectivo = $_GET[id_periodo_lectivo]");
                                $periodo_lectivo = $db->fetch_object($consulta);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pe_prefijo">Prefijo:</label>
                                            <input type="text" name="pe_prefijo" id="pe_prefijo" value="<?= $periodo_lectivo->pe_prefijo ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pe_anio_inicio">Año Inicial:</label>
                                            <input type="text" name="pe_anio_inicio" id="pe_anio_inicio" value="<?= $periodo_lectivo->pe_anio_inicio ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pe_anio_fin">Año Final:</label>
                                            <input type="text" name="pe_anio_fin" id="pe_anio_fin" value="<?= $periodo_lectivo->pe_anio_fin ?>" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="div_pe_fecha_inicio" class="form-group">
                                            <label for="pe_fecha_inicio">Fecha de inicio:</label>
                                            <div class="controls">
                                                <div class="input-group">
                                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_inicio').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    <input type="text" class="form-control date" name="pe_fecha_inicio" id="pe_fecha_inicio" value="<?= $periodo_lectivo->pe_fecha_inicio ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="div_pe_fecha_fin" class="form-group">
                                            <label for="pe_fecha_fin">Fecha de fin:</label>
                                            <div class="controls">
                                                <div class="input-group date">
                                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_fin').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    <input type="text" name="pe_fecha_fin" id="pe_fecha_fin" class="form-control" value="<?= $periodo_lectivo->pe_fecha_fin ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pe_nota_minima" class="form-label fw-bold">Nota mínima:</label>
                                            <input type="number" min="0.01" step="0.01" class="form-control" name="pe_nota_minima" id="pe_nota_minima" value="<?= $periodo_lectivo->pe_nota_minima ?>" required>
                                            <span id="span_pe_nota_minima" class="help-block"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pe_nota_aprobacion" class="form-label fw-bold">Nota aprobación:</label>
                                            <input name="pe_nota_aprobacion" type="number" min="7" max="10" step="0.01" class="form-control" id="pe_nota_aprobacion" value="<?= $periodo_lectivo->pe_nota_aprobacion ?>" required>
                                            <span id="span_pe_nota_aprobacion" class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <?php
                                    //Obtener las modalidades
                                    $modalidades = $db->consulta("SELECT * FROM sw_modalidad WHERE mo_activo = 1 ORDER BY mo_orden ASC");
                                    ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_modalidad">Modalidad:</label>
                                            <select name="id_modalidad" id="id_modalidad" class="form-control" required disabled>
                                                <option value="">Seleccione...</option>
                                                <?php
                                                while ($modalidad = $db->fetch_object($modalidades)) {
                                                ?>
                                                    <option value="<?= $modalidad->id_modalidad ?>" <?= $modalidad->id_modalidad == $periodo_lectivo->id_modalidad ? 'selected' : '' ?>>
                                                        <?= $modalidad->mo_nombre ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <span id="span_id_modalidad" class="help-block"></span>
                                        </div>
                                    </div>
                                    <?php
                                    //Obtener quien inserta comportamiento
                                    $quien_inserta_comp = $db->consulta("SELECT * FROM sw_quien_inserta_comp ORDER BY id ASC");
                                    ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quien_inserta_comp_id ">¿Quién inserta el comportamiento?:</label>
                                            <select name="quien_inserta_comp_id" id="quien_inserta_comp_id" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <?php
                                                while ($v = $db->fetch_object($quien_inserta_comp)) {
                                                ?>
                                                    <option value="<?= $v->id ?>" <?= $v->id == $periodo_lectivo->quien_inserta_comp_id ? 'selected' : '' ?>>
                                                        <?= $v->nombre ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <span id="span_quien_inserta_comp_id" class="help-block"></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Acá van las nuevas tablas -->
                                <div class="form-group" style="margin-top: 4px;">
                                    <button id="btn-save" type="submit" class="btn btn-success">Actualizar</button>
                                    <button id="btn-ponderacion" class="btn btn-primary" onclick="actualizarPonderaciones(event)">Ponderaciones</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(function() {
        //Datemask yyyy/mm/dd
        $('#pe_fecha_inicio').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });

        $('#pe_fecha_fin').inputmask('yyyy/mm/dd', {
            'placeholder': 'aaaa/mm/dd'
        });

        $("#pe_fecha_inicio").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#pe_fecha_fin").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#frm-periodo-lectivo").submit(function(e) {
            e.preventDefault();

            const formulario = document.getElementById("frm-periodo-lectivo");

            // Cuenta todos los checkboxes con el atributo name="niveles"
            const countNivelesChecked = $(
                'input[type="checkbox"][name="niveles[]"]:checked'
            ).length;

            // Cuenta todos los checkboxes con el atributo name="sub_periodos"
            const countSubperiodosChecked = $(
                'input[type="checkbox"][name="sub_periodos[]"]:checked'
            ).length;

            var url;
            var pe_anio_inicio = $.trim($("#pe_anio_inicio").val());
            var pe_anio_fin = $.trim($("#pe_anio_fin").val());
            var pe_fecha_inicio = $.trim($("#pe_fecha_inicio").val());
            var pe_fecha_fin = $.trim($("#pe_fecha_fin").val());
            var pe_nota_minima = $.trim($("#pe_nota_minima").val());
            var pe_nota_aprobacion = $.trim($("#pe_nota_aprobacion").val());
            var id_modalidad = $("#id_modalidad").val();
            var quien_inserta_comp = $("#quien_inserta_comp").val();

            var fecha_actual = new Date(); //devuelve la fecha actual
            var anio_actual = fecha_actual.getFullYear();

            var fecha_inicio = new Date(pe_fecha_inicio);
            var fecha_fin = new Date(pe_fecha_fin);

            if (pe_anio_inicio == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año inicial.", "error");
            } else if (pe_anio_fin == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año final.", "error");
            } else if (pe_fecha_inicio == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la fecha de inicio.", "error");
            } else if (pe_fecha_fin == "") {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la fecha de fin.", "error");
            } else if (parseInt(pe_anio_inicio) > parseInt(anio_actual)) {
                Swal.fire("Ocurrió un error inesperado!", "El año inicial no puede ser mayor que el año actual.", "error");
            } else if (pe_anio_inicio.length != 4) {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año inicial.", "error");
            } else if (pe_anio_fin.length != 4) {
                Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año final.", "error");
            } else if (parseInt(pe_anio_inicio) > parseInt(pe_anio_fin)) {
                Swal.fire("Ocurrió un error inesperado!", "El año inicial no puede ser mayor que el año final.", "error");
            } else if (id_modalidad == 0) {
                Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar una modalidad.", "error");
            /*} else if (countNivelesChecked == 0) {
                Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar al menos un nivel de educación.", "error");
            } else if (countSubperiodosChecked == 0) {
                Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar al menos un subperiodo de evaluación.", "error");*/
            } else {

                if ($("#btn-save").html() == "Guardar")
                    url = "periodos_lectivos/insertar_periodo_lectivo.php";
                else if ($("#btn-save").html() == "Actualizar")
                    url = "periodos_lectivos/actualizar_periodo_lectivo.php";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {

                        // Maneja la respuesta del servidor aquí
                        console.log('Respuesta del servidor:', response);
                        //alert('¡Formulario enviado con éxito!');

                        Swal.fire({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Maneja los errores aquí
                        console.error('Error en la solicitud:', textStatus, errorThrown);
                        alert('Hubo un error al enviar el formulario.');
                    }
                });

            }
        });
    });

    function actualizarPonderaciones(e) {
        e.preventDefault();
        alert("Actualizando ponderaciones...");
    }
</script>