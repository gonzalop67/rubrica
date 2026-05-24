<section class="content-header">
    <h1>
        Periodos Lectivos
        <small>Editar</small>
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
                    <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                    <div id="alert-success" class="alert alert-success alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_exito']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"><?php echo isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_exito'])) unset($_SESSION['mensaje_exito']) ?>
                    <div class="panel-body">
                        <form id="frm-update" action="<?= RUTA_URL . "/periodos_lectivos/update" ?>" method="post">
                            <input type="hidden" name="id_periodo_lectivo" id="id_periodo_lectivo" value="<?= $datos['periodo_lectivo']->id_periodo_lectivo ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pe_anio_inicio">Año Inicial:</label>
                                        <input type="text" name="pe_anio_inicio" id="pe_anio_inicio" value="<?= $datos['periodo_lectivo']->pe_anio_inicio ?>" class="form-control" required autofocus>
                                        <span id="error_pe_anio_inicio" class="help-block"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pe_anio_fin">Año Final:</label>
                                        <input type="text" name="pe_anio_fin" id="pe_anio_fin" value="<?= $datos['periodo_lectivo']->pe_anio_fin ?>" class="form-control" required>
                                        <span id="error_pe_anio_fin" class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div id="div_pe_fecha_inicio" class="form-group">
                                        <label for="pe_fecha_inicio">Fecha de inicio:</label>
                                        <!-- <div class="controls">
                                            <div class="input-group"> -->
                                        <!-- <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_inicio').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label> -->
                                        <input type="text" name="pe_fecha_inicio" id="pe_fecha_inicio" class="form-control date" value="<?= $datos['periodo_lectivo']->pe_fecha_inicio ?>" required>
                                        <!-- </div>
                                        </div> -->
                                    </div>
                                    <span id="span_pe_fecha_inicio" class="help-block"></span>
                                </div>
                                <div class="col-lg-6">
                                    <div id="div_pe_fecha_fin" class="form-group">
                                        <label for="pe_fecha_fin">Fecha de fin:</label>
                                        <!-- <div class="controls">
                                            <div class="input-group date"> -->
                                        <!-- <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_fin').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label> -->
                                        <input type="text" name="pe_fecha_fin" id="pe_fecha_fin" class="form-control" value="<?= $datos['periodo_lectivo']->pe_fecha_fin ?>" required>
                                        <span id="span_pe_fecha_fin" class="help-block"></span>
                                        <!-- </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pe_nota_minima" class="form-label fw-bold">Nota mínima:</label>
                                        <input type="number" min="0.01" step="0.01" class="form-control" name="pe_nota_minima" id="pe_nota_minima" value="<?= $datos['periodo_lectivo']->pe_nota_minima ?>" required>
                                        <span id="span_pe_nota_minima" class="help-block"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="pe_nota_aprobacion" class="form-label fw-bold">Nota aprobación:</label>
                                        <input name="pe_nota_aprobacion" type="number" min="7" max="10" step="0.01" class="form-control" id="pe_nota_aprobacion" value="<?= $datos['periodo_lectivo']->pe_nota_aprobacion ?>" required>
                                        <span id="span_pe_nota_aprobacion" class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="quien_inserta_comp_id ">¿Quién inserta el comportamiento?:</label>
                                        <select name="quien_inserta_comp_id" id="quien_inserta_comp_id" class="form-control" required>
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($datos['quien_inserta_comportamiento'] as $v): ?>
                                                <option value="<?= $v->id ?>" <?= $v->id == $datos['periodo_lectivo']->quien_inserta_comp_id ? 'selected' : '' ?>>
                                                    <?= $v->nombre ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span id="span_quien_inserta_comp_id" class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="niveles">Asociar Nivel de Educación:</label>
                                    <?php foreach ($datos['niveles_educacion'] as $v) : ?>
                                        <div>
                                            <input type="checkbox" name="niveles[]" value="<?= $v->id_nivel_educacion ?>" <?= (in_array($v->id_nivel_educacion, $datos['niveles_periodo'])) ? 'checked' : '' ?>>
                                            <?= $v->nombre ?>
                                        </div>
                                    <?php endforeach ?>
                                    <span id="error_niveles" style="color: red"></span>
                                </div>
                                <div class="col-lg-6">
                                    <label for="sub_periodos_evaluacion">Asociar Sub Periodos de Evaluación:</label>
                                    <?php foreach ($datos['sub_periodos_evaluacion'] as $v) : ?>
                                        <div>
                                            <input type="checkbox" name="sub_periodos[]" value="<?= $v->id_sub_periodo_evaluacion ?>" <?= (in_array($v->id_sub_periodo_evaluacion, $datos['sub_periodos_periodo'])) ? 'checked' : '' ?>>
                                            <?= $v->pe_nombre ?>
                                        </div>
                                    <?php endforeach ?>
                                    <span id="error_sub_periodos" style="color: red"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success">Actualizar</button>
                                <a href="<?php echo RUTA_URL . "/periodos_lectivos"; ?>" class="btn btn-primary">Regresar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('frm-update');

        $("#pe_fecha_inicio").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#pe_fecha_fin").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#frm-update").submit(function(e) {
            e.preventDefault();

            const url = $(this).attr("action");

            // Selecciona todos los checkboxes con el atributo name="niveles"
            let countNivelesChecked = $('input[type="checkbox"][name="niveles[]"]:checked').length;
            let countSubPeriodosChecked = $('input[type="checkbox"][name="sub_periodos[]"]:checked').length;

            if (countNivelesChecked == 0 || countSubPeriodosChecked == 0) {
                if (countNivelesChecked == 0) {
                    document.getElementById("error_niveles").innerHTML = "Debe seleccionar al menos un nivel de educación...";
                } else {
                    document.getElementById("error_niveles").innerHTML = "";
                }
                if (countSubPeriodosChecked == 0) {
                    document.getElementById("error_sub_periodos").innerHTML = "Debe seleccionar al menos un sub periodo de evaluación...";
                } else {
                    document.getElementById("error_sub_periodos").innerHTML = "";
                }
            } else {
                document.getElementById("error_niveles").innerHTML = "";
                document.getElementById("error_sub_periodos").innerHTML = "";

                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        // console.log(response);
                        location.reload();
                    }
                });
            }
        });
    });
</script>