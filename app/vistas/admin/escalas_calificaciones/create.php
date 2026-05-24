<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Escalas de Calificaciones
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
                    <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
                    </div>
                    <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
                    <form id="frm-escalas-calificaciones" action="<?php echo RUTA_URL; ?>/escalas_calificaciones/insert" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="ec_cualitativa" class="control-label requerido">Escala Cualitativa:</label>
                            <input type="text" name="ec_cualitativa" id="ec_cualitativa" value="" class="form-control" autofocus required>
                        </div>
                        <div class="form-group">
                            <label for="ec_cuantitativa" class="control-label requerido">Escala Cuantitativa:</label>

                            <input type="text" name="ec_cuantitativa" id="ec_cuantitativa" value="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ec_nota_minima" class="control-label requerido">Nota Mínima:</label>

                            <input type="number" step="any" min="0" max="10" name="ec_nota_minima" id="ec_nota_minima" value="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ec_nota_maxima" class="control-label requerido">Nota Máxima:</label>

                            <input type="number" step="any" min="0" max="10" name="ec_nota_maxima" id="ec_nota_maxima" value="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ec_equivalencia" class="control-label requerido">Equivalencia:</label>

                            <input type="text" name="ec_equivalencia" id="ec_equivalencia" value="" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button id="btn-save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                            <a href="<?php echo RUTA_URL; ?>/escalas_calificaciones" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
                        </div>
                    </form>
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
        Biblioteca.validacionGeneral('frm-escalas-calificaciones');
    });
</script>