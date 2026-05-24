<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Aportes de Evaluación
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
                        <form id="frm-aporte-evaluacion" action="<?php echo RUTA_URL; ?>/aportes_evaluacion/insert" method="post" autocomplete="off">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="requerido" for="id_tipo_aporte">Tipo:</label>
                                        <select name="id_tipo_aporte" id="id_tipo_aporte" class="form-control">
                                            <?php foreach ($datos['tipos_aporte'] as $v) : ?>
                                                <option value="<?= $v->id_tipo_aporte ?>">
                                                    <?= $v->ta_descripcion ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="requerido" for="ap_nombre">Nombre:</label>
                                        <input type="text" name="ap_nombre" id="ap_nombre" value="" class="form-control text-uppercase" autofocus required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="requerido" for="ap_abreviatura">Abreviatura:</label>
                                        <input type="text" name="ap_abreviatura" id="ap_abreviatura" value="" class="form-control text-uppercase" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="requerido" for="ap_descripcion">Descripción:</label>
                                        <textarea class="form-control text-uppercase" name="ap_descripcion" id="ap_descripcion" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_fecha_apertura">Fecha de inicio (aaaa-mm-dd):</label>
                                        <input type="text" name="ap_fecha_apertura" id="ap_fecha_apertura" class="form-control" value="" placeholder="aaaa-mm-dd" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="ap_fecha_cierre">Fecha de fin (aaaa-mm-dd):</label>
                                        <input type="text" name="ap_fecha_cierre" id="ap_fecha_cierre" class="form-control" value="" placeholder="aaaa-mm-dd" required>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="id_sub_periodo_evaluacion">Periodo:</label>
                                        <select name="id_sub_periodo_evaluacion" id="id_sub_periodo_evaluacion" class="form-control">
                                            <?php foreach ($datos['sub_periodos_evaluacion'] as $v) : ?>
                                                <option value="<?= $v->id_sub_periodo_evaluacion ?>">
                                                    <?= $v->pe_nombre ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    
                                </div>
                            </div> -->
                            <div class="form-group">
                                <button id="btn-save" type="submit" class="btn btn-success"><i class="fa-solid fa-cloud-arrow-up"></i> Guardar</button>
                                <a href="<?php echo RUTA_URL; ?>/aportes_evaluacion" class="btn btn-default"><i class="fa fa-backward"></i> Volver</a>
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
        Biblioteca.validacionGeneral('frm-aporte-evaluacion');
        // const dateInput1 = document.getElementById('ap_fecha_apertura');
        // dateInput1.addEventListener('input', (e) => {
        //     let value = e.target.value.replace(/\D/g, ''); // Remove non-numeric characters
        //     if (value.length > 4) value = value.slice(0, 4) + '-' + value.slice(4);
        //     if (value.length > 7) value = value.slice(0, 7) + '-' + value.slice(7, 10);
        //     e.target.value = value;
        // });
        // dateInput1.addEventListener('blur', (e) => {
        //     const value = e.target.value;
        //     const parts = value.split('-');
        //     if (parts.length === 3) {
        //         const [year, month, day] = parts.map(Number);
        //         if (!isValidDate(year, month, day)) {
        //             alert('Invalid date. Please enter a valid date in YYYY-MM-DD format.');
        //             e.target.value = '';
        //         }
        //     }
        // });
        // $("#ap_fecha_apertura").datepicker({
        //     dateFormat: 'yy-mm-dd',
        //     monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        //     dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        //     firstDay: 1
        // });
        // $("#ap_fecha_cierre").datepicker({
        //     dateFormat: 'yy-mm-dd',
        //     monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        //     dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        //     firstDay: 1
        // });
    });

    function isValidDate(year, month, day) {
        return !(month < 1 || month > 12 ||
            day < 1 || day > 31 ||
            year < 1000 || year > 9999);
    }
</script>