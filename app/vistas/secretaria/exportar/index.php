<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Exportar a CSV
        <small>Paralelos</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <div style="margin-top: 10px;">
                <form id="form_id" action="<?= RUTA_URL ?>/exportar/exportar" method="POST" class="form-horizontal">
                    <div class="form-group">
                        <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                        <div class="col-sm-9">
                            <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                <?php foreach ($datos['paralelos'] as $v) : ?>
                                    <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" id="export_csv" name="export_csv" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Exportar a CSV</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>