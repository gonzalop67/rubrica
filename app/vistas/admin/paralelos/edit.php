<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Paralelos
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
                        <form id="frm-paralelo" action="<?php echo RUTA_URL; ?>/paralelos/update" method="post">
                            <input type="hidden" name="id_paralelo" id="id_paralelo" value="<?= $datos['paralelo']->id_paralelo ?>">
                            <div class="form-group">
                                <label for="pa_nombre" class="control-label">Nombre:</label>
                                <input type="text" name="pa_nombre" id="pa_nombre" class="form-control" value="<?= $datos['paralelo']->pa_nombre ?>" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="id_curso" class="control-label">Curso:</label>
                                <select name="id_curso" id="id_curso" class="form-control">
                                    <?php foreach ($datos['cursos'] as $curso) : ?>
                                        <option value="<?= $curso->id_curso; ?>" <?= ($curso->id_curso == $datos['paralelo']->id_curso) ? 'selected' : '' ?>><?= "[" . $curso->es_figura . "] " . $curso->cu_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_jornada" class="control-label requerido">Jornada:</label>
                                <select name="id_jornada" id="id_jornada" class="form-control">
                                    <?php foreach ($datos['jornadas'] as $jornada) : ?>
                                        <option value="<?= $jornada->id_jornada; ?>" <?= ($jornada->id_jornada == $datos['paralelo']->id_jornada) ? 'selected' : '' ?>><?= $jornada->jo_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/paralelos" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-paralelo');
    });
</script>