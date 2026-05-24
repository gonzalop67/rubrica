<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Perfiles
        <small>Listado</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-solid">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-body">
                <span class="btn btn-primary" data-toggle="modal" data-target="#nuevoPerfilModal"><i class="fa fa-plus-circle"></i> Nuevo Registro</span>
                <hr>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_perfiles">
                                <!-- Aquí se van a poblar los perfiles ingresados en la base de datos -->
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
<?php require_once "modalInsert.php" ?>
<?php require_once "modalUpdate.php" ?>
<script>
    $(document).ready(function() {
        pagination(1);
    });

    function pagination(partida) {
        $("#pagina_actual").val(partida);
        var url = "<?php echo RUTA_URL; ?>/perfiles/paginar";
        $.ajax({
            type: 'POST',
            url: url,
            data: 'partida=' + partida,
            success: function(data) {
                var array = eval(data);
                $("#tbody_perfiles").html(array[0]);
                $("#pagination").html(array[1]);
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    }

    function insertarPerfil() {
        let cont_errores = 0;
        let pe_nombre = $("#nombre").val().trim();

        var reg_nombre = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (pe_nombre == "") {
            $("#mensaje1").html("Debe ingresar el nombre del perfil...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else if (!reg_nombre.test(pe_nombre)) {
            $("#mensaje1").html("El nombre del perfil debe contener al menos tres caracteres alfabéticos.");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo RUTA_URL; ?>/perfiles/insert",
                data: {
                    pe_nombre: pe_nombre
                },
                dataType: "json",
                success: function(r) {
                    pagination(1);
                    swal(r.titulo, r.mensaje, r.estado);
                    $('#form_insert')[0].reset(); //limpiar formulario
                    $("#nuevoPerfilModal").modal('hide');
                }
            });
        }

        return false;
    }

    function obtenerDatos(id) {
        $.ajax({
            url: "<?php echo RUTA_URL; ?>/perfiles/obtener",
            type: "POST",
            data: "id=" + id,
            dataType: "json",
            success: function(r) {
                $("#id_perfil").val(r.id_perfil);
                $("#nombreu").val(r.pe_nombre);
            }
        });
    }

    function actualizarPerfil() {
        let cont_errores = 0;

        let id_perfil = $("#id_perfil").val().trim();
        let pe_nombre = $("#nombreu").val().trim();

        var reg_nombre = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;

        if (pe_nombre == "") {
            $("#mensaje2").html("Debe ingresar el nombre del perfil...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else if (!reg_nombre.test(pe_nombre)) {
            $("#mensaje2").html("El nombre del perfil debe contener al menos tres caracteres alfabéticos.");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo RUTA_URL; ?>/perfiles/update",
                data: {
                    id_perfil: id_perfil,
                    pe_nombre: pe_nombre
                },
                dataType: "json",
                success: function(r) {
                    pagination(1);
                    swal(r.titulo, r.mensaje, r.estado);
                    $('#form_update')[0].reset(); //limpiar formulario
                    $("#editarPerfilModal").modal('hide');
                }
            });
        }

        return false;
    }

    function eliminarPerfil(id) {
        swal({
                title: "¿Estás seguro de eliminar este registro?",
                text: "¡Una vez eliminado no podrá recuperarse!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo RUTA_URL; ?>/perfiles/delete",
                        data: "id=" + id,
                        success: function(r) {
                            if (r == 1) {
                                pagination(1);
                                swal("¡Eliminado con éxito!", ":D", "info");
                            } else {
                                swal("¡Error!", ":(", "error");
                            }
                        }
                    });
                }
            });
    }
</script>