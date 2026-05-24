<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cursos
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
                        <form id="frm-curso" action="<?php echo RUTA_URL; ?>/cursos/update" method="post">
                            <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $datos['curso']->id_curso ?>">
                            <div class="form-group">
                                <label for="cu_nombre" class="control-label">Nombre:</label>
                                <input type="text" name="cu_nombre" id="cu_nombre" value="<?php echo $datos['curso']->cu_nombre ?>" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="cu_shortname" class="control-label">Nombre Corto:</label>
                                <input type="text" name="cu_shortname" id="cu_shortname" value="<?php echo $datos['curso']->cu_shortname ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="cu_abreviatura" class="control-label">Abreviatura:</label>
                                <input type="text" name="cu_abreviatura" id="cu_abreviatura" value="<?php echo $datos['curso']->cu_abreviatura ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="es_bach_tecnico" class="control-label">¿Es Bachillerato Técnico?</label>
                                <select name="es_bach_tecnico" id="es_bach_tecnico" class="form-control">
                                    <option value="0" <?= $datos['curso']->es_bach_tecnico == 0 ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= $datos['curso']->es_bach_tecnico == 1 ? 'selected' : '' ?>>Sí</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="es_intensivo" class="control-label">¿Es intensivo?</label>
                                <select name="es_intensivo" id="es_intensivo" class="form-control">
                                    <option value="0" <?= ($datos['curso']->es_intensivo == 0) ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= ($datos['curso']->es_intensivo == 1) ? 'selected' : '' ?>>Sí</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_especialidad" class="control-label">Especialidad:</label>
                                <select name="id_especialidad" id="id_especialidad" class="form-control">
                                    <?php foreach ($datos['especialidades'] as $especialidad) : ?>
                                        <option value="<?= $especialidad->id_especialidad; ?>" <?= ($datos['curso']->id_especialidad == $especialidad->id_especialidad) ? 'selected' : '' ?>><?= $especialidad->es_figura; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/cursos" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-curso');
    });
</script>