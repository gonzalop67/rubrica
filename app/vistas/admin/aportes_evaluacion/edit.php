<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Aportes de Evaluación
        <small>Editar</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-warning">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-body">
                        <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                        </div>
                        <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                        <form id="frm-aporte-evaluacion" action="<?php echo RUTA_URL; ?>/aportes_evaluacion/update" method="post" autocomplete="off">
                            <input type="hidden" name="id_aporte_evaluacion" id="id_aporte_evaluacion" value="<?= $datos['aporte_evaluacion']->id_aporte_evaluacion ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_nombre">Nombre:</label>
                                        <input type="text" name="ap_nombre" id="ap_nombre" value="<?= $datos['aporte_evaluacion']->ap_nombre ?>" class="form-control text-uppercase" autofocus required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_abreviatura">Abreviatura:</label>
                                        <input type="text" name="ap_abreviatura" id="ap_abreviatura" value="<?= $datos['aporte_evaluacion']->ap_abreviatura ?>" class="form-control text-uppercase" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_descripcion">Descripción:</label>
                                        <input type="text" name="ap_descripcion" id="ap_descripcion" value="<?= $datos['aporte_evaluacion']->ap_descripcion ?>" class="form-control text-uppercase" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_ponderacion">Ponderación:</label>
                                        <input type="number" min="0.01" max="1.00" step="0.01" name="ap_ponderacion" id="ap_ponderacion" value="<?= $datos['aporte_evaluacion']->ap_ponderacion ?>" class="form-control text-uppercase" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_fecha_apertura">Fecha de inicio:</label>
                                        <input type="text" name="ap_fecha_apertura" id="ap_fecha_apertura" class="form-control" value="<?= $datos['aporte_evaluacion']->ap_fecha_apertura ?>" placeholder="aaaa-mm-dd" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_fecha_cierre">Fecha de fin:</label>
                                        <input type="text" name="ap_fecha_cierre" id="ap_fecha_cierre" class="form-control" value="<?= $datos['aporte_evaluacion']->ap_fecha_cierre ?>" placeholder="aaaa-mm-dd" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="id_periodo_evaluacion">Periodo:</label>
                                        <select name="id_sub_periodo_evaluacion" id="id_sub_periodo_evaluacion" class="form-control">
                                            <?php foreach ($datos['periodos_evaluacion'] as $v) : ?>
                                                <option value="<?= $v->id_sub_periodo_evaluacion ?>" <?= ($datos['aporte_evaluacion']->id_sub_periodo_evaluacion == $v->id_sub_periodo_evaluacion) ? 'selected' : '' ?>>
                                                    <?= $v->pe_nombre ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="id_tipo_aporte">Tipo:</label>
                                        <select name="id_tipo_aporte" id="id_tipo_aporte" class="form-control">
                                            <?php foreach ($datos['tipos_aporte'] as $v) : ?>
                                                <option value="<?= $v->id_tipo_aporte ?>" <?= ($datos['aporte_evaluacion']->id_tipo_aporte == $v->id_tipo_aporte) ? 'selected' : '' ?>>
                                                    <?= $v->ta_descripcion ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/aportes_evaluacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-aporte-evaluacion');
        $("#ap_fecha_apertura").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
        $("#ap_fecha_cierre").datepicker({
            dateFormat: 'yy-mm-dd',
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1
        });
    });
</script>