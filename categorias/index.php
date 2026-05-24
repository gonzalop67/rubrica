<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Categorías
            <small>Listado</small>
        </h1>
    </section>

    <!-- Cargar los datos de las variables de sesion en inputs de tipo hidden -->
    <?php
    $id_usuario = $_SESSION["id_usuario"];
    $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];
    ?>


    <input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo; ?>">

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevaCategoriaModal"><i class="fa fa-plus-circle"></i> Nueva Categoria</span>
                <hr>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Categoría</th>
                                    <th>Duración en minutos</th>
                                    <th>Opciones</th>
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
    </section>
</div>

<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>

<script type="text/javascript">
    $(document).ready(function() {
        pagination(1);
    });

    function insertarCategoria() {
        let cont_errores = 0;
        let id_usuario = $("#id_usuario").val();
        let category = $("#category").val().trim();
        let exam_time_in_minutes = $("#exam_time_in_minutes").val().trim();

        var reg_numero = /^([0-9]{1,3})$/i;

        if (category == "") {
            $("#mensaje1").html("Debe ingresar el nombre de la categoria...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (exam_time_in_minutes == "") {
            $("#mensaje2").html("Debe ingresar el tiempo en minutos...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else if (!reg_numero.test(exam_time_in_minutes)) {
            $("#mensaje2").html("El tiempo en minutos debe contener al menos un caracter numerico.");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "categorias/insertar_categoria.php",
                data: {
                    id_usuario: id_usuario,
                    category: category.toUpperCase(),
                    exam_time_in_minutes: exam_time_in_minutes
                },
                dataType: "json",
                success: function(r) {
                    // console.log(r);
                    pagination(1);
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevaCategoriaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "categorias/obtener_categoria.php",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                $("#id_categoria").val(r.id);
                $("#category_u").val(r.category);
                $("#exam_time_in_minutes_u").val(r.exam_time_in_minutes);
            }
        });
    }

    function actualizarCategoria() {
        let cont_errores = 0;
        let id_usuario = $("#id_usuario").val();
        let id_categoria = $("#id_categoria").val();
        let category = $("#category_u").val().trim();
        let exam_time_in_minutes = $("#exam_time_in_minutes_u").val().trim();

        var reg_numero = /^([0-9]{1,3})$/i;

        if (category == "") {
            $("#mensaje3").html("Debe ingresar el nombre de la categoria...");
            $("#mensaje3").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje3").fadeOut();
        }

        if (exam_time_in_minutes == "") {
            $("#mensaje4").html("Debe ingresar el tiempo en minutos...");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else if (!reg_numero.test(exam_time_in_minutes)) {
            $("#mensaje4").html("El tiempo en minutos debe contener al menos un caracter numerico.");
            $("#mensaje4").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje4").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "categorias/update_category.php",
                data: {
                    id: id_categoria,
                    id_usuario: id_usuario,
                    category: category.toUpperCase(),
                    exam_time_in_minutes: exam_time_in_minutes
                },
                dataType: "json",
                success: function(r) {
                    // console.log(r);
                    pagination(1);
                    // swal(r.titulo, r.mensaje, r.estado);
                    Swal.fire({
                        icon: r.estado,
                        title: r.titulo,
                        text: r.mensaje
                    });
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarCategoriaModal").modal('hide');
                }
            });
        }

        return false;
    }

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        id_usuario = $("#id_usuario").val();
        var url = "categorias/paginar_categorias.php";
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
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
        return false;
    }

    function eliminarCategoria(id_categoria) {
        
        Swal.fire({
            title: "¿Está seguro que quiere eliminar el registro?",
            text: "No podrá recuperar el registro que va a ser eliminado!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, elimínelo!",
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "post",
                    url: "categorias/eliminar_categoria.php",
                    data: {
                        id_categoria: id_categoria
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.estado == "success") {
                            Swal.fire({
                                icon: response.estado,
                                title: response.titulo,
                                text: response.mensaje
                            });
                            var pagina_actual = $("#pagina_actual").val();
                            pagination(pagina_actual);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        });
    }
</script>