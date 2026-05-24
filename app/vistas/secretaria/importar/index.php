<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Importar desde CSV
        <small>Paralelos</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-info">
        <div class="box-header with-border">
            <div style="margin-top: 10px;">
                <form id="form_import" action="<?= RUTA_URL ?>/importar/importar" method="POST" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                        <div class="col-sm-5">
                            <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                <?php foreach ($datos['paralelos'] as $v) : ?>
                                    <option value="<?= $v->id_paralelo; ?>"><?= $v->cu_nombre . " " . $v->pa_nombre . " - " . $v->es_figura . " - " . $v->jo_nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span id="mensaje1" style="color: #e73d4a"></span>
                        </div>

                        <div class="col-md-4">
                            <input type="file" name="student_file" id="student_file" class="form-control fuente9" />
                        </div>

                        <div class="col-sm-2">
                            <button type="submit" id="import_csv" name="import_csv" class="btn btn-info btn-sm"><i class="fa fa-upload"></i> Importar desde CSV</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $("#form_import").submit(function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const method = $(this).attr('method');

            let cont_errores = 0;

            const csv_file = document.forms['form_import']['student_file'];
            const validExt = ["csv", "CSV"];

            if (csv_file.value != '') {
                const file_ext = csv_file.value.substring(csv_file.value.lastIndexOf('.') + 1);

                const result = validExt.includes(file_ext);

                if (result == false) {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "Debe cargar un archivo de tipo CSV.",
                    });
                    cont_errores++;
                    return false;
                } else {
                    const CurrentFileSize = parseFloat(csv_file.files[0].size / (1024 * 1024));
                    if (CurrentFileSize >= 1) {
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "El archivo CSV debe tener un tamaño máximo de 1 Mb. Tamaño actual: " + CurrentFileSize.toPrecision(4) + " Mb.",
                        });
                        cont_errores++;
                        return false;
                    }
                }

            } else {
                Swal.fire({
                    icon: "error",
                    title: "¡Error!",
                    text: "Debe cargar un archivo de tipo CSV.",
                });
                cont_errores++;
                return false;
            }

            if (cont_errores == 0) {

                $.ajax({
                    url: url,
                    method: method,
                    data: new FormData(this),
                    contentType: false, // The content type used when sending data to the server.  
                    cache: false, // To unable request pages to be cached  
                    processData: false, // To send DOMDocument or non processed data file it is set to false 
                    dataType: 'html',
                    success: function(response) {
                        console.log(response);
                        /* Swal.fire({
                            icon: response.tipo_mensaje,
                            title: response.titulo,
                            text: response.mensaje,
                        }); */
                    }
                });

            }
        });
    });
</script>