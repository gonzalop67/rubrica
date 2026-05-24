<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Insumos de Evaluación
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
                        <form id="frm-insumo-evaluacion" action="<?php echo RUTA_URL; ?>/insumos_evaluacion/update" method="post">
                            <input type="hidden" name="id_rubrica_evaluacion" id="id_rubrica_evaluacion" value="<?= $datos['insumo_evaluacion']->id_rubrica_evaluacion ?>">
                            <div class="form-group">
                                <label for="ru_nombre">Nombre:</label>
                                <input type="text" name="ru_nombre" id="ru_nombre" value="<?= $datos['insumo_evaluacion']->ru_nombre ?>" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="ru_abreviatura">Abreviatura:</label>
                                <input type="text" name="ru_abreviatura" id="ru_abreviatura" value="<?= $datos['insumo_evaluacion']->ru_abreviatura ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="id_aporte_evaluacion">Aporte de Evaluación:</label>
                                <select name="id_aporte_evaluacion" id="id_aporte_evaluacion" class="form-control">
                                    <?php foreach ($datos['aportes_evaluacion'] as $v) : ?>
                                        <option value="<?= $v->id_aporte_evaluacion ?>" <?php echo ($datos['insumo_evaluacion']->id_aporte_evaluacion == $v->id_aporte_evaluacion
                                                                                                    ) ? 'selected' : '' ?>>
                                            <?= $v->ap_nombre . ' - ' . $v->pe_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_tipo_asignatura">Tipo de Asignatura:</label>
                                <select name="id_tipo_asignatura" id="id_tipo_asignatura" class="form-control">
                                    <?php foreach ($datos['tipos_asignatura'] as $v) : ?>
                                        <option value="<?= $v->id_tipo_asignatura ?>" <?php echo ($datos['insumo_evaluacion']->id_tipo_asignatura == $v->id_tipo_asignatura
                                                                                                    ) ? 'selected' : '' ?>>
                                            <?= $v->ta_descripcion ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/insumos_evaluacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-insumo-evaluacion');
    });
</script>