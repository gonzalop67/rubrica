<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Asociar Cursos Superiores</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="box-body">
            <div id="alert-error" class="alert alert-danger alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_error']) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-ban"></i> <span id="mensaje_error"><?php echo isset($_SESSION['mensaje_error']) ? $_SESSION['mensaje_error'] : '' ?></span></p>
            </div>
            <?php if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']) ?>
            <div id="alert-success" class="alert alert-success alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje_exito']) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> <span id="mensaje_exito"><?php echo isset($_SESSION['mensaje_exito']) ? $_SESSION['mensaje_exito'] : '' ?></span></p>
            </div>
            <?php if (isset($_SESSION['mensaje_exito'])) unset($_SESSION['mensaje_exito']) ?>
            <form id="form_id" action="">
                <div class="form-group">
                    <label for="id_curso" class="control-label text-right">Curso Inferior:</label>

                    <select class="form-control fuente10" name="id_curso_inferior" id="id_curso_inferior" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($datos['cursos'] as $v) : ?>
                            <option value="<?= $v->id_curso; ?>"><?= "[" . $v->es_figura . "] - " . $v->cu_nombre; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_curso_superior" class="control-label text-right">Curso Superior:</label>

                    <select class="form-control fuente10" name="id_curso_superior" id="id_curso_superior" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($datos['cursos_superiores'] as $v) : ?>
                            <option value="<?= $v->id_curso_superior; ?>"><?= $v->cs_nombre; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row" id="botones_insercion">
                    <div class="col-sm-12" style="margin-top: 4px;">
                        <button id="btn-add-item" type="submit" class="btn btn-block btn-primary">
                            Asociar
                        </button>
                    </div>
                </div>
            </form>
            <!-- Línea de división -->
            <hr>
            <!-- message -->
            <div id="text_message" class="fuente10 text-center"></div>
            <!-- table -->
            <table class="table fuente10">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Curso Inferior</th>
                        <th>Curso Superior</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista_items">
                    <!-- Aqui desplegamos el contenido de la base de datos -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script>
    $(document).ready(function() {
        Biblioteca.validacionGeneral('form_id');
        cargar_cursos_asociados();

        $('#lista_items').on('click', '.item-delete', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            swal({
                    title: "¿Estás seguro de eliminar este registro?",
                    text: "¡Una vez eliminado no podrá recuperarse!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = url;
                    }
                });
        });

        $("#form_id").submit(function(e) {
            e.preventDefault();

            //Insertar Asociación de Curso Superior
            let id_curso_inferior = document.getElementById("id_curso_inferior").value;
            let id_curso_superior = document.getElementById("id_curso_superior").value;

            if (id_curso_inferior !== "" && id_curso_superior !== "") {
                $("#text_message").html("<img src='<?php echo RUTA_URL; ?>/public/img/ajax-loader-blue.GIF' alt='procesando...' />");

                $.ajax({
                    url: "<?= RUTA_URL ?>/cursos_superiores/insert",
                    method: "post",
                    data: {
                        id_curso_inferior: id_curso_inferior,
                        id_curso_superior: id_curso_superior
                    },
                    dataType: "json",
                    success: function(response) {
                        $("#text_message").html("");
                        swal({
                            title: response.titulo,
                            text: response.mensaje,
                            icon: response.tipo_mensaje,
                            confirmButtonText: 'Aceptar'
                        });
                        cargar_cursos_asociados();
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            } // fin if (id_curso_inferior !== "" && id_curso_superior !== "")
        });
    });

    function cargar_cursos_asociados() {
        var request = $.ajax({
            url: "<?= RUTA_URL ?>/cursos_superiores/listar",
            method: "get",
            dataType: "html"
        });

        request.done(function(response) {
            $("#lista_items").html(response);
        });

        request.fail(function(jqXHR, textStatus) {
            alert("Requerimiento fallido: " + jqXHR.responseText);
        });
    }
</script>