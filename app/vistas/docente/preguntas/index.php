<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Categorías
        <small>Seleccione la categoría para añadir y editar preguntas</small>
    </h1>
</section>

<!-- Cargar los datos de las variables de sesion en inputs de tipo hidden -->
<?php
$id_usuario = $_SESSION["id_usuario"];
?>

<input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-solid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Categoría</th>
                                    <th>Duración en minutos</th>
                                    <th>Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_categories">
                                <!-- Aqui vamos a poblar las categorias ingresadas en la BDD mediante AJAX  -->
                            </tbody>
                        </table>
                        <div class="text-center">
                            <ul class="pagination" id="pagination"></ul>
                        </div>
                        <input type="hidden" id="pagina_actual">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        pagination(1);
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        id_usuario = $("#id_usuario").val();
        var url = "<?php echo RUTA_URL; ?>/categorias/paginar_select";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                partida: partida,
                id_usuario: id_usuario
            },
            success: function(data) {
                var array = eval(data);
                $("#tbody_categories").html(array[0]);
                $("#pagination").html(array[1]);
            }
        });
        return false;
    }
</script>