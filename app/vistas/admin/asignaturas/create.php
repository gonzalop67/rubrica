<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Asignaturas
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
                        <form id="frm-asignatura" action="<?php echo RUTA_URL; ?>/asignaturas/insert" method="post">
                            <div class="form-group">
                                <label for="as_nombre">Nombre:</label>
                                <input type="text" name="as_nombre" id="as_nombre" value="" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="as_shortname">Alias:</label>
                                <input type="text" name="as_shortname" id="as_shortname" value="" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="as_abreviatura">Abreviatura:</label>
                                <input type="text" name="as_abreviatura" id="as_abreviatura" value="" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="id_tipo_asignatura">Tipo de Asignatura:</label>
                                <select name="id_tipo_asignatura" id="id_tipo_asignatura" class="form-control">
                                    <?php foreach ($datos['tiposAsignatura'] as $tipo_asignatura) : ?>
                                        <option value="<?= $tipo_asignatura->id_tipo_asignatura; ?>">
                                            <?= $tipo_asignatura->ta_descripcion; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_area">Areas:</label>
                                <select name="id_area" id="id_area" class="form-control">
                                    <?php foreach ($datos['areas'] as $area) : ?>
                                        <option value="<?= $area->id_area; ?>">
                                            <?= $area->ar_nombre; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                <a href="<?php echo RUTA_URL; ?>/asignaturas" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-asignatura');
    });
</script>