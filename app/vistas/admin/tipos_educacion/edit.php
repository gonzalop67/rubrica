<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Niveles de Educación
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
                        <form id="frm-nivel-educacion" action="<?php echo RUTA_URL; ?>/tipos_educacion/update" method="post">
                            <input type="hidden" name="id_nivel_educacion" id="id_nivel_educacion" value="<?php echo $datos['tipo_educacion']->id_nivel_educacion ?>">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" name="nombre" id="nombre" value="<?php echo $datos['tipo_educacion']->nombre ?>" class="form-control" autofocus required>
                            </div>
                            <div class="form-group">
                                <label for="es_bachillerato">¿Es Bachillerato?:</label>
                                <select name="es_bachillerato" id="es_bachillerato" class="form-control">
                                    <option value="1" <?php echo ($datos['tipo_educacion']->es_bachillerato == 1) ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?php echo ($datos['tipo_educacion']->es_bachillerato == 0) ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-warning"><i class="fa fa-pencil"></i> Actualizar</button>
                                <a href="<?php echo RUTA_URL; ?>/tipos_educacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-nivel-educacion');
    });
</script>