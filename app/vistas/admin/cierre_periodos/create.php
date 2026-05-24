<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cierre de Periodos
        <small>Crear</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box-header with-border mb-5">
                        <div id="titulo" class="box-title">Nuevo Cierre de Periodo</div>
                    </div>
                    <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                    <form id="frm-cierre-periodo" action="<?php echo RUTA_URL; ?>/cierre_periodos/insert" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="id_paralelo">Paralelo:</label>
                            <select name="id_paralelo" id="id_paralelo" class="form-control" required>
                                <?php foreach ($datos['paralelos'] as $paralelo) : ?>
                                    <option value="<?= $paralelo->id_paralelo; ?>">
                                        <?= "[" . $paralelo->es_figura . "] " . $paralelo->cu_nombre . " " . $paralelo->pa_nombre . " (" . $paralelo->jo_nombre . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_aporte_evaluacion">Aporte de Evaluación:</label>
                            <select name="id_aporte_evaluacion" id="id_aporte_evaluacion" class="form-control" required>
                                <?php foreach ($datos['aportes_evaluacion'] as $aporte_evaluacion) : ?>
                                    <option value="<?= $aporte_evaluacion->id_aporte_evaluacion; ?>">
                                        <?= $aporte_evaluacion->ap_nombre; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ap_fecha_apertura">Fecha de apertura:</label>
                            <div class="controls">
                                <div class="input-group date">
                                    <input type="text" name="ap_fecha_apertura" id="ap_fecha_apertura" class="form-control" required>
                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#ap_fecha_apertura').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ap_fecha_cierre">Fecha de cierre:</label>
                            <div class="controls">
                                <div class="input-group date">
                                    <input type="text" name="ap_fecha_cierre" id="ap_fecha_cierre" class="form-control" required>
                                    <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#ap_fecha_cierre').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                            <a href="<?php echo RUTA_URL; ?>/cierre_periodos" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                        </div>
                    </form>
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
        Biblioteca.validacionGeneral('frm-cierre-periodo');
        $("#ap_fecha_apertura").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });

        $("#ap_fecha_cierre").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
        });
    });
</script>