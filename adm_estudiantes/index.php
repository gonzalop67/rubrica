<style>
    #table_div {
        padding: 5px;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 id="titulo_principal">
            Estudiantes
            <small>Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-primary">
            <div id="table_div">
                <table id="t_estudiantes" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Id</th>
                            <th>Apellidos</th>
                            <th>Nombres</th>
                            <th>DNI</th>
                            <th>Fec.Nacim.</th>
                            <th>Edad</th>
                            <th>Género</th>
                            <th>Nacionalidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- View Student Modal -->
<div class="modal fade" id="viewStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel1">Editar Estudiante</h4>
            </div>
            <form id="form_view" action="" method="post" autocomplete="off">
                <div class="modal-body fuente9">
                    <div class="form-group row">
                        <label for="ver_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="ver_id_tipo_documento" readonly>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <label for="ver_dni" class="col-sm-1 col-form-label">DNI:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="ver_dni" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-uppercase" id="ver_apellidos" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-uppercase" id="ver_nombres" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="ver_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10" readonly>
                        </div>

                        <label for="ver_edad" class="col-sm-1 col-form-label">Edad:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="ver_edad" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_direccion" class="col-sm-2 col-form-label">Dirección:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-uppercase" id="ver_direccion" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_sector" class="col-sm-2 col-form-label">Sector:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control text-uppercase" id="ver_sector" value="" readonly>
                        </div>
                        <label for="ver_telefono" class="col-sm-1 col-form-label">Celular:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="ver_telefono" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_email" class="col-sm-2 col-form-label">E-mail:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="ver_email" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ver_genero" class="col-sm-2 col-form-label">Género:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="ver_genero" readonly>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <label for="ver_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="ver_nacionalidad" readonly>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-center" id="myModalLabel2">Editar Estudiante</h4>
            </div>
            <form id="form_edit" action="" method="post" autocomplete="off">
                <input type="hidden" name="id_estudiante" id="id_estudiante">
                <div class="modal-body fuente9">
                    <div class="form-group row">
                        <label for="edit_id_tipo_documento" class="col-sm-2 col-form-label">Tipo de Documento:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_id_tipo_documento" name="edit_id_tipo_documento" required>
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="error-edit_id_tipo_documento" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_dni" class="col-sm-1 col-form-label">DNI:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_dni" name="edit_dni" value="">
                            <span id="error-edit_dni" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_apellidos" class="col-sm-2 col-form-label">Apellidos:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_apellidos" name="edit_apellidos" value="">
                            <span id="error-edit_apellidos" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_nombres" class="col-sm-2 col-form-label">Nombres:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_nombres" name="edit_nombres" value="">
                            <span id="error-edit_nombres" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_fec_nac" class="col-sm-2 col-form-label">Fecha de nacimiento:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="edit_fec_nac" name="edit_fec_nac" value="" placeholder="aaaa-mm-dd" maxlength="10">
                            <span id="error-edit_fec_nac" style="color: #e73d4a"></span>
                        </div>

                        <label for="edit_edad" class="col-sm-1 col-form-label">Edad:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_edad" name="edit_edad" value="" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_direccion" class="col-sm-2 col-form-label">Dirección:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mayusculas" id="edit_direccion" name="edit_direccion" value="">
                            <span id="error-edit_direccion" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_sector" class="col-sm-2 col-form-label">Sector:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control mayusculas" id="edit_sector" name="edit_sector" value="">
                            <span id="error-edit_sector" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_telefono" class="col-sm-1 col-form-label">Celular:</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="edit_telefono" name="edit_telefono" value="">
                            <span id="error-edit_telefono" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_email" class="col-sm-2 col-form-label">E-mail:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_email" name="edit_email" value="">
                            <span id="error-edit_email" style="color: #e73d4a"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_genero" class="col-sm-2 col-form-label">Género:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_genero" name="edit_genero">
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="error-edit_genero" style="color: #e73d4a"></span>
                        </div>
                        <label for="edit_nacionalidad" class="col-sm-2 col-form-label">Nacionalidad:</label>
                        <div class="col-sm-4">
                            <select class="form-control fuente9" id="edit_nacionalidad" name="edit_nacionalidad">
                                <option value="">Seleccione...</option>
                            </select>
                            <span id="error-edit_nacionalidad" style="color: #e73d4a"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="actualizarEstudiante()"><span class="glyphicon glyphicon-save"></span> Actualizar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let table;

    $(document).ready(function() {
        cargar_def_generos();
        cargar_tipos_documento();
        cargar_def_nacionalidades();
        cargar_estudiantes();
    });

    function setearIndice(nombreCombo, indice) {
        for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
            if (document.getElementById(nombreCombo).options[i].value == indice) {
                document.getElementById(nombreCombo).options[i].selected = indice;
            }
    }

    function cargar_estudiantes() {
        table = $('#t_estudiantes').DataTable({
            "processing": true, // Habilita el mensaje de carga
            "serverSide": true, // Procesamiento en el servidor
            "language": {
                "processing": "Consultando servidor, por favor espere...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "search": "Buscar:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "ajax": function(data, callback, settings) {
                // Usamos Fetch para realizar la petición
                fetch('adm_estudiantes/obtener_estudiantes.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(json => {
                        callback(json); // Retornamos los datos a DataTables
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            },
            "columns": [{
                    // Columna secuencial
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Retorna el índice de la fila + 1
                    }
                },
                {
                    "data": "id_estudiante"
                },
                {
                    "data": "es_apellidos"
                },
                {
                    "data": "es_nombres"
                },
                {
                    "data": "es_cedula"
                },
                {
                    "data": "es_fec_nacim"
                },
                {
                    "data": "edad"
                },
                {
                    "data": "dg_nombre"
                },
                {
                    "data": "dn_nombre"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class='btn btn-info btn-sm' onclick="verEstudiante(${row.id_estudiante})"><i class="fa fa-fw fa-eye"></i> Ver</button>
                            <button class='btn btn-success btn-sm' onclick="editarEstudiante(${row.id_estudiante})"><i class="fa fa-fw fa-pencil"></i> Editar</button>
                        `;
                    }
                }
            ]
        });
    }

    function cargar_def_generos() {
        $.get("scripts/cargar_definicion_generos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#ver_genero').append(resultado);
                $('#edit_genero').append(resultado);
            }
        });
    }

    function cargar_tipos_documento() {
        $.get("scripts/cargar_tipos_documento.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#ver_id_tipo_documento').append(resultado);
                $('#edit_id_tipo_documento').append(resultado);
            }
        });
    }

    function cargar_def_nacionalidades() {
        $.get("scripts/cargar_definicion_nacionalidades.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#ver_nacionalidad').append(resultado);
                $('#edit_nacionalidad').append(resultado);
            }
        });
    }

    function cedulaValida(cedula) {
        var total = 0;
        var longitud = cedula.length;
        var longcheck = longitud - 1;

        if (longitud != 10) {
            return false;
        }

        if (cedula !== "" && longitud === 10) {
            for (i = 0; i < longcheck; i++) {
                if (i % 2 === 0) {
                    var aux = cedula.charAt(i) * 2;
                    if (aux > 9) aux -= 9;
                    total += aux;
                } else {
                    total += parseInt(cedula.charAt(i)); // parseInt o concatenará en lugar de sumar
                }
            }

            total = total % 10 ? 10 - total % 10 : 0;

            return cedula.charAt(longitud - 1) == total;
        }
    }

    function calcularEdad(es_fec_nacim) {
        //Aqui se va a calcular la edad a partir de la fecha de nacimiento
        var hoy = new Date();
        var fec_nac = new Date(es_fec_nacim);
        var edad = hoy.getFullYear() - fec_nac.getFullYear();
        var m = hoy.getMonth() - fec_nac.getMonth();

        if (m < 0 || (m == 0 && hoy.getDate() < fec_nac.getDate())) {
            edad--;
        }

        return edad;
    }

    function verEstudiante(id_estudiante) {
        $.ajax({
            url: "matriculacion/obtener_estudiante.php",
            method: "post",
            data: {
                id_estudiante: id_estudiante
            },
            dataType: "json",
            success: function(data) {
                $("#form_view")[0].reset();

                setearIndice("ver_id_tipo_documento", data.id_tipo_documento);
                $("#ver_dni").val(data.es_cedula);
                $("#ver_apellidos").val(data.es_apellidos);
                $("#ver_nombres").val(data.es_nombres);
                $("#ver_fec_nac").val(data.es_fec_nacim);
                $("#ver_edad").val(calcularEdad(data.es_fec_nacim));
                $("#ver_direccion").val(data.es_direccion);
                $("#ver_sector").val(data.es_sector);
                $("#ver_telefono").val(data.es_telefono);
                $("#ver_email").val(data.es_email);
                setearIndice("ver_genero", data.id_def_genero);
                setearIndice("ver_nacionalidad", data.id_def_nacionalidad);

                $('#viewStudentModal').modal('show');
            },
            error: function(jqXHR, textStatus) {
                console.log(jqXHR.responseText);
            }
        });
    }

    function editarEstudiante(id_estudiante) {
        $.ajax({
            url: "matriculacion/obtener_estudiante.php",
            method: "post",
            data: {
                id_estudiante: id_estudiante
            },
            dataType: "json",
            success: function(data) {
                $("#form_edit")[0].reset();

                $("#error-edit_id_tipo_documento").html("");
                $("#error-edit_dni").html("");
                $("#error-edit_apellidos").html("");
                $("#error-edit_nombres").html("");
                $("#error-edit_fec_nac").html("");
                $("#error-edit_direccion").html("");
                $("#error-edit_sector").html("");
                $("#error-edit_telefono").html("");
                $("#error-edit_email").html("");
                $("#error-edit_genero").html("");
                $("#id_def_nacionalidad").html("");

                $("#id_estudiante").val(id_estudiante);
                setearIndice("edit_id_tipo_documento", data.id_tipo_documento);
                $("#edit_dni").val(data.es_cedula);
                $("#edit_apellidos").val(data.es_apellidos);
                $("#edit_nombres").val(data.es_nombres);
                $("#edit_fec_nac").val(data.es_fec_nacim);
                $("#edit_edad").val(calcularEdad(data.es_fec_nacim));
                $("#edit_direccion").val(data.es_direccion);
                $("#edit_sector").val(data.es_sector);
                $("#edit_telefono").val(data.es_telefono);
                $("#edit_email").val(data.es_email);
                setearIndice("edit_genero", data.id_def_genero);
                setearIndice("edit_nacionalidad", data.id_def_nacionalidad);

                $('#editStudentModal').modal('show');
            },
            error: function(jqXHR, textStatus) {
                console.log(jqXHR.responseText);
            }
        });
    }

    function actualizarEstudiante() {
        let cont_errores = 0;
        let id_estudiante = $("#id_estudiante").val();
        let es_cedula = $("#edit_dni").val().trim();
        let es_email = $("#edit_email").val().trim();
        let es_sector = $("#edit_sector").val().trim();
        let es_nombres = $("#edit_nombres").val().trim();
        let id_def_genero = $("#edit_genero").val().trim();
        let es_telefono = $("#edit_telefono").val().trim();
        let es_fec_nacim = $("#edit_fec_nac").val().trim();
        let es_apellidos = $("#edit_apellidos").val().trim();
        let es_direccion = $("#edit_direccion").val().trim();
        let id_def_nacionalidad = $("#edit_nacionalidad").val().trim();
        let id_tipo_documento = $("#edit_id_tipo_documento").val().trim();

        var reg_texto = /^([a-zA-Z0-9 ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_nombres = /^([a-zA-Z ñáéíóúÑÁÉÍÓÚ]{3,64})$/i;
        var reg_cedula = /^([A-Z0-9.]{4,10})$/i;
        var reg_email = /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/i;
        var reg_fecnac = /^([12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01]))$/i;

        if (id_tipo_documento == "") {
            $("#error-edit_id_tipo_documento").html("Debe seleccionar un tipo de documento...");
            $("#error-edit_id_tipo_documento").fadeIn("slow");
            cont_errores++;
        }

        if (es_cedula == "") {
            $("#error-edit_dni").html("Debe ingresar el DNI...");
            $("#error-edit_dni").fadeIn("slow");
            cont_errores++;
        } else if (es_cedula.length != 0 && id_tipo_documento == 1 && !reg_cedula.test(es_cedula)) {
            $("#error-edit_dni").html("El DNI del estudiante no tiene un formato válido.");
            $("#error-edit_dni").fadeIn("slow");
            cont_errores++;
        } else if (id_tipo_documento == 1 && !cedulaValida(es_cedula)) {
            $("#error-edit_dni").html("La cédula ingresada no es válida...");
            $("#error-edit_dni").fadeIn("slow");
            cont_errores++;
        }

        if (es_apellidos == "") {
            $("#error-edit_apellidos").html("Debe ingresar los apellidos...");
            $("#error-edit_apellidos").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombres.test(es_apellidos)) {
            $("#error-edit_apellidos").html("Los apellidos del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#error-edit_apellidos").fadeIn("slow");
            cont_errores++;
        }

        if (es_nombres == "") {
            $("#error-edit_nombres").html("Debe ingresar los nombres...");
            $("#error-edit_nombres").fadeIn("slow");
            cont_errores++;
        } else if (!reg_nombres.test(es_nombres)) {
            $("#error-edit_nombres").html("Los nombres del estudiante deben contener al menos tres caracteres alfabéticos.");
            $("#error-edit_nombres").fadeIn("slow");
            cont_errores++;
        }

        if (es_fec_nacim == "") {
            $("#error-edit_fec_nac").html("Debe ingresar la fecha de nacimiento...");
            $("#error-edit_fec_nac").fadeIn("slow");
            cont_errores++;
        } else if (!reg_fecnac.test(es_fec_nacim)) {
            $("#error-edit_fec_nac").html("La fecha de nacimiento debe tener el formato aaaa-mm-dd");
            $("#error-edit_fec_nac").fadeIn("slow");
            cont_errores++;
        }

        if (es_direccion == "") {
            $("#error-edit_direccion").html("Debe ingresar la dirección de su domicilio...");
            $("#error-edit_direccion").fadeIn("slow");
            cont_errores++;
        }

        if (es_sector == "") {
            $("#error-edit_sector").html("Debe ingresar el sector de su domicilio...");
            $("#error-edit_sector").fadeIn("slow");
            cont_errores++;
        }

        if (es_telefono == "") {
            $("#error-edit_telefono").html("Debe ingresar el número de celular...");
            $("#error-edit_telefono").fadeIn("slow");
            cont_errores++;
        }

        if (es_email.length != 0 && !reg_email.test(es_email)) {
            $("#error-edit_email").html("Dirección de correo electrónico no válida.");
            $("#error-edit_email").fadeIn("slow");
            cont_errores++;
        }

        if (id_def_genero == "") {
            $("#error-edit_genero").html("Debe seleccionar el género...");
            $("#error-edit_genero").fadeIn("slow");
            cont_errores++;
        }

        if (id_def_nacionalidad == "") {
            $("#id_def_nacionalidad").html("Debe seleccionar la nacionalidad...");
            $("#id_def_nacionalidad").fadeIn("slow");
            cont_errores++;
        }

        if (cont_errores == 0) {
            // Aquí vamos a actualizar el estudiante mediante AJAX
            $.ajax({
                url: "adm_estudiantes/actualizar_estudiante.php",
                method: "POST",
                data: {
                    id_estudiante: id_estudiante,
                    id_tipo_documento: id_tipo_documento,
                    id_def_genero: id_def_genero,
                    id_def_nacionalidad: id_def_nacionalidad,
                    es_apellidos: es_apellidos.toUpperCase(),
                    es_nombres: es_nombres.toUpperCase(),
                    es_cedula: es_cedula.toUpperCase(),
                    es_email: es_email,
                    es_sector: es_sector.toUpperCase(),
                    es_direccion: es_direccion.toUpperCase(),
                    es_telefono: es_telefono,
                    es_fec_nacim: es_fec_nacim
                },
                success: function(resultado) {
                    table.destroy();
                    cargar_estudiantes();
                    Swal.fire({
                        title: "Operación exitosa",
                        text: resultado,
                        icon: "success"
                    });
                    $("#form_edit")[0].reset();
                    $('#editStudentModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }
</script>