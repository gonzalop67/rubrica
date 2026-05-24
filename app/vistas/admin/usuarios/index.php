<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box box-primary">
        <!-- Default box -->
        <div class="box-header with-border">
            <h2 class="text-center">
                Usuarios
                <small>Listado</small>
            </h2>

            <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/usuarios/create"><i class="fa fa-user-plus"></i> Nuevo Usuario</a>

            <!-- <div class="box-tools">
                <form id="form-search" action="<?php echo RUTA_URL; ?>/usuarios/search" method="POST">
                    <div class="input-group input-group-md" style="width: 250px;">
                        <input type="text" name="search_pattern" id="search_pattern" class="form-control pull-right text-uppercase" placeholder="Buscar Usuario..." value="<?= isset($datos['search_pattern']) ? $datos['search_pattern'] : '' ?>">

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div> -->
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
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
                    <table id="t_usuarios" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="centered">#</th>
                                <th class="centered">Id</th>
                                <th class="centered">Avatar</th>
                                <th class="centered">Nombre</th>
                                <th class="centered">Usuario</th>
                                <th class="centered">Activo</th>
                                <th class="centered">Perfiles</th>
                                <th class="centered">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_usuarios">
                            <?php
                            if (!empty($datos['usuarios'])) {
                                $contador = 0;
                                foreach ($datos['usuarios'] as $usuario) {
                                    $contador++;
                            ?>
                                    <tr>
                                        <td><?php echo $contador ?></td>
                                        <td><?php echo $usuario->id_usuario ?></td>
                                        <?php $us_foto = ($usuario->us_foto == '') ? RUTA_URL . '/public/uploads/no-disponible.png' : RUTA_URL . '/public/uploads/' . $usuario->us_foto ?>
                                        <td><img class="img-thumbnail" width="50" src="<?php echo $us_foto ?>" alt="Avatar del Usuario"></td>
                                        <td><?php echo $usuario->us_fullname ?></td>
                                        <td><?php echo $usuario->us_login ?></td>
                                        <?php $us_activo = ($usuario->us_activo == 1) ? 'Sí' : 'No' ?>
                                        <td><?php echo $us_activo ?></td>
                                        <td>
                                            <?php
                                            $cadena = "";
                                            foreach ($datos['perfilesUsuarios'] as $perfil) {
                                                if ($usuario->id_usuario == $perfil->id_usuario) {
                                                    $cadena .= $perfil->pe_nombre . ", ";
                                                }
                                            }
                                            $cadena = rtrim($cadena, ", ");
                                            echo $cadena;
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo RUTA_URL; ?>/usuarios/edit/<?php echo $usuario->id_usuario ?>" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>
                                                <a href="javascript:;" class="btn btn-danger item-delete" data="<?php echo $usuario->id_usuario ?>" title="Eliminar"><span class="fa fa-trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr class="text-center">
                                    <td colspan="100%">Aún no se han registrado usuarios...</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</section>

<script>
    $(document).ready(function() {
        // initialize datatables
        let table = new DataTable("#t_usuarios", {
            lengthMenu: [5, 10, 15, 20, 25, 50],
            columnDefs: [{
                    className: "centered",
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                {
                    orderable: false,
                    targets: [6, 7]
                },
                // { width: "50%", targets: [0]}
            ],
            pageLength: 5,
            language: {
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                loadingRecords: "Cargando...",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior",
                },
            },
        });

        $.ajaxSetup({
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 0) {
                    alert('Not connect: Verify Network.');
                } else if (jqXHR.status == 404) {
                    alert('Requested page not found [404]');
                } else if (jqXHR.status == 500) {
                    alert('Internal Server Error [500].');
                } else if (textStatus === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (textStatus === 'timeout') {
                    alert('Time out error.');
                } else if (textStatus === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error: ' + jqXHR.responseText);
                }
            }
        });

        $('table tbody').on('click', '.item-delete', function() {
            // e.preventDefault();
            const id_usuario = $(this).attr('data');
            const url = "<?php echo RUTA_URL; ?>/usuarios/delete/" + id_usuario;
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

        $("#search_pattern").keyup(function() {
            var request = $.ajax({
                url: "<?php echo RUTA_URL; ?>/usuarios/buscar",
                type: "POST",
                data: {
                    patron: $(this).val().trim()
                },
                dataType: "json"
            });
            request.done(function(data) {
                console.log(data);
                var array = eval(data);
                if (array[2] == 1) {
                    swal("¡Información!", "No se han encontrado coincidencias...", "info");
                } else {
                    console.log(eval(array[1]));
                    let usuarios = eval(array[0]);
                    let perfiles = eval(array[1]);

                    listarUsuariosEncontrados(usuarios, perfiles)
                }
            });
        });
    });

    function listarUsuariosEncontrados(usuarios, perfiles) {
        let html = "";
        const RUTA_URL = "<?php echo RUTA_URL ?>";
        for (let i = 0; i < usuarios.length; i++) {
            us_foto = (usuarios[i].us_foto == '') ? RUTA_URL + '/public/uploads/no-disponible.png' : RUTA_URL +
                '/public/uploads/' + usuarios[i].us_foto;
            us_activo = (usuarios[i].us_activo == 1) ? 'Sí' : 'No';
            perfilesAsociados = "";
            for (let j = 0; j < perfiles.length; j++) {
                if (usuarios[i].id_usuario == perfiles[j].id_usuario) {
                    perfilesAsociados += perfiles[j].pe_nombre + ", ";
                }
            }
            perfilesAsociados = perfilesAsociados.substr(0, perfilesAsociados.length - 2);
            html += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + usuarios[i].id_usuario + '</td>' +
                '<td><img class="img-thumbnail" width="50" src="' + us_foto + '" alt="Avatar del Usuario"></td>' +
                '<td>' + usuarios[i].us_fullname + '</td>' +
                '<td>' + usuarios[i].us_login + '</td>' +
                '<td>' + us_activo + '</td>' +
                '<td>' + perfilesAsociados + '</td>' +
                '<td>' +
                '<div class="btn-group">' +
                '<a href="' + RUTA_URL + '/usuarios/edit/' + usuarios[i].id_usuario + '" class="btn btn-warning" title="Editar"><span class="fa fa-pencil"></span></a>' +
                '<a href="javascript:;" class="btn btn-danger item-delete" data="' + usuarios[i].id_usuario + '" title="Editar"><span class="fa fa-trash"></span></a>' +
                '</div>' +
                '</td>' +
                '</tr>';
        }
        $("#tbody_usuarios").html(html);
    }
</script>