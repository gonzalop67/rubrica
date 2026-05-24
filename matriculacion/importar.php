<div class="content-wrapper">
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
                    <form id="form_import" action="matriculacion/importar_excel.php" method="POST" class="form-horizontal" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="id_paralelo" class="col-sm-1 control-label text-right">Paralelo:</label>

                            <div class="col-sm-5">
                                <select class="form-control fuente9" name="id_paralelo" id="id_paralelo">
                                    
                                </select>
                                <span id="mensaje1" style="color: #e73d4a"></span>
                            </div>

                            <div class="col-md-4">
                                <input type="file" name="student_file" id="student_file" class="form-control fuente9" />
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" id="import_csv" name="import_csv" class="btn btn-info btn-sm"><i class="fa fa-upload"></i> Importar desde CSV o EXCEL</button>
                            </div>
                        </div>
                    </form>
                    <div id="img_loader" class="text-center">
                        <!-- Aquí va el gif animado para indicar que está procesando... -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        cargar_paralelos();

        $("#form_import").submit(function(e) {

            e.preventDefault();

            const url = $(this).attr('action');
            const method = $(this).attr('method');

            let cont_errores = 0;

            const csv_file = document.forms['form_import']['student_file'];
            const validExt = ["csv", "CSV", "xls", "XLS", "xlsx", "XLSX"];

            if (csv_file.value != '') {
                const file_ext = csv_file.value.substring(csv_file.value.lastIndexOf('.') + 1);

                const result = validExt.includes(file_ext);

                if (result == false) {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "Debe cargar un archivo de tipo CSV, XLS o XLSX.",
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
                    text: "Debe cargar un archivo de tipo CSV o EXCEL.",
                });
                cont_errores++;
                return false;
            }

            if (cont_errores == 0) {

                $("#img_loader").html("<img src='imagenes/ajax-loader-blue.GIF' alt='procesando...' width='25'>");

                $.ajax({
                    url: url,
                    method: method,
                    data: new FormData(this),
                    contentType: false, // The content type used when sending data to the server.  
                    cache: false, // To unable request pages to be cached  
                    processData: false, // To send DOMDocument or non processed data file it is set to false 
                    dataType: 'html',
                    success: function(response) {
                        // console.log(response);
                        $("#img_loader").html("Se procesaron " + response + " registros.");
                    }
                });

            }
        });
    });

    function cargar_paralelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#id_paralelo').append(resultado);
            }
        });
    }
</script>