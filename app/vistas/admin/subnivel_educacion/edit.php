<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sub Niveles de Educación
        <small>Editar</small>
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
                        
                        <div id="alert-message" class="alert alert-<?= isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'danger' ?> alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje']) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <p><i class="icon fa fa-<?= isset($_SESSION['icono']) ? $_SESSION['icono'] : 'ban' ?>"></i> <span id="mensaje"><?php echo isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '' ?></span></p>
                        </div>
                        <?php if (isset($_SESSION['mensaje'])) unset($_SESSION['mensaje']) ?>
                        <?php if (isset($_SESSION['tipo'])) unset($_SESSION['tipo']) ?>
                        <?php if (isset($_SESSION['icono'])) unset($_SESSION['icono']) ?>

                        <form id="frm-sub-nivel-educacion" action="<?php echo RUTA_URL; ?>/subniveles_educacion/update" method="post">
                            <input type="hidden" name="id_sub_nivel_educacion" value="<?= $datos['subnivel']->id ?>">
                            <div class="form-group">
                                <label for="te_nombre">Nombre:</label>
                                <input type="text" name="te_nombre" id="te_nombre" value="<?php echo (isset($_SESSION['nombre'])) ? $_SESSION['nombre'] : $datos['subnivel']->nombre ?>" class="form-control text-uppercase" autofocus required>
                                <?php if (isset($_SESSION['nombre'])) unset($_SESSION['nombre']) ?>
                            </div>
                            <div class="form-group">
                                <label for="te_bachillerato">¿Es Bachillerato?:</label>
                                <select name="te_bachillerato" id="te_bachillerato" class="form-control">
                                    <option value="1" <?php echo (isset($_SESSION['es_bachillerato']) && $_SESSION['es_bachillerato'] == 1) ? 'selected' : ($datos['subnivel']->es_bachillerato == 1 ? 'selected' : '') ?>>Sí</option>
                                    <option value="0" <?php echo (isset($_SESSION['es_bachillerato']) && $_SESSION['es_bachillerato'] == 0) ? 'selected' : ($datos['subnivel']->es_bachillerato == 0 ? 'selected' : '') ?>>No</option>
                                </select>
                                <?php if (isset($_SESSION['es_bachillerato'])) unset($_SESSION['es_bachillerato']) ?>
                            </div>
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-pencil"></i> Actualizar</button>
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