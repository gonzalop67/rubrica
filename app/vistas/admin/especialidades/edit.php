<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Especialidades
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
                        <form id="frm-especialidad" action="<?php echo RUTA_URL; ?>/especialidades/update" method="post">
                            <input type="hidden" name="id_especialidad" id="id_especialidad" value="<?php echo $datos['especialidad']->id_especialidad; ?>">
                            <div class="form-group">
                                <label for="es_nombre" class="col-sm-2 control-label">Nombre:</label>
                                <input type="text" name="es_nombre" id="es_nombre" value="<?php echo $datos['especialidad']->es_nombre; ?>" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="es_figura" class="col-sm-2 control-label">Figura Profesional:</label>
                                <input type="text" name="es_figura" id="es_figura" value="<?php echo $datos['especialidad']->es_figura; ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="es_abreviatura" class="col-sm-2 control-label">Abreviatura:</label>
                                <input type="text" name="es_abreviatura" id="es_abreviatura" value="<?php echo $datos['especialidad']->es_abreviatura; ?>" class="form-control" required>
                            </div>
                            <!-- <div class="form-group">
                                <label for="id_tipo_educacion" class="col-sm-2 control-label">Nivel de Educación:</label>
                                <select name="id_tipo_educacion" id="id_tipo_educacion" class="form-control" required>
                                    <?php foreach ($datos['tipos_educacion'] as $tipo_educacion) : ?>
                                        <option value="<?= $tipo_educacion->id_tipo_educacion; ?>" <?php echo ($datos['especialidad']->id_tipo_educacion == $tipo_educacion->id_tipo_educacion) ? 'selected' : '' ?>><?= $tipo_educacion->te_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div> -->
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/especialidades" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-especialidad');
    });
</script>