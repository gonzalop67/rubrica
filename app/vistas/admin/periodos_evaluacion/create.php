<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Periodos de Evaluación
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
                    <div class="panel-body">
                        <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                        </div>
                        <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                        <form id="frm_periodo_evaluacion" action="<?php echo RUTA_URL; ?>/periodos_evaluacion/insert" method="post">
                            <div class="form-group">
                                <label for="pe_nombre" class="control-label">Nombre:</label>
                                <input type="text" name="pe_nombre" id="pe_nombre" value="" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="pe_abreviatura" class="control-label">Abreviatura:</label>
                                <input type="text" name="pe_abreviatura" id="pe_abreviatura" value="" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="id_tipo_periodo" class="control-label">Tipo Periodo:</label>
                                <select name="id_tipo_periodo" id="id_tipo_periodo" class="form-control" required>
                                    <?php foreach ($datos['tipos_periodo'] as $tipo_periodo) : ?>
                                        <option value="<?= $tipo_periodo->id_tipo_periodo; ?>"><?= $tipo_periodo->tp_descripcion; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10">
                            <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                            <a href="<?php echo RUTA_URL; ?>/periodos_evaluacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('frm_periodo_evaluacion');
    });
</script>