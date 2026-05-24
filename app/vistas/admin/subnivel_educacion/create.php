<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sub Niveles de Educación
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

                        <?php
                        include RUTA_APP . "/vistas/inc/mensaje.php";
                        ?>

                        <form id="frm-sub-nivel-educacion" action="<?php echo RUTA_URL; ?>/subniveles_educacion/store" method="post">
                            <div class="form-group">
                                <label for="te_nombre">Nombre:</label>
                                <input type="text" name="te_nombre" id="te_nombre" value="<?php echo (isset($_SESSION['nombre'])) ? $_SESSION['nombre'] : '' ?>" class="form-control text-uppercase" autofocus required>
                                <?php if (isset($_SESSION['nombre'])) unset($_SESSION['nombre']) ?>
                            </div>
                            <div class="form-group">
                                <label for="te_bachillerato">¿Es Bachillerato?:</label>
                                <select name="te_bachillerato" id="te_bachillerato" class="form-control">
                                    <option value="1" <?php echo (isset($_SESSION['es_bachillerato']) && $_SESSION['es_bachillerato'] == 1) ? 'selected' : '' ?>>Sí</option>
                                    <option value="0" <?php echo (isset($_SESSION['es_bachillerato']) && $_SESSION['es_bachillerato'] == 0) ? 'selected' : '' ?>>No</option>
                                </select>
                                <?php if (isset($_SESSION['es_bachillerato'])) unset($_SESSION['es_bachillerato']) ?>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                <a href="<?php echo RUTA_URL; ?>/subniveles_educacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-sub-nivel-educacion');
    });
</script>