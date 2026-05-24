<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_SESSION['titulo_pagina'] ?></title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
</head>

<body>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Periodos Lectivos
                <small>Listado</small>
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box box-solid">
                <div class="box-body">
                    <hr>
                    <div class="row">
                        <div class="col-md-8 table-responsive">
                            <div class="form-group">
                                <label for="id_modalidad">Modalidad:</label>
                                <select name="id_modalidad" id="id_modalidad" class="form-control">
                                    <option value="0">Seleccione...</option>
                                </select>
                            </div>
                            <table id="example1" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Id</th>
                                        <th>Año Lectivo</th>
                                        <th>Fecha Inicial</th>
                                        <th>Fecha Final</th>
                                        <th>Estado</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_periodos_lectivos">
                                    <!-- Aqui vamos a poblar los periodos lectivos ingresados en la BDD mediante AJAX  -->
                                </tbody>
                            </table>
                            <div class="text-center">
                                <ul class="pagination" id="pagination"></ul>
                            </div>
                            <input type="hidden" id="pagina_actual">
                        </div>
                        <div class="col-md-4">
                            <div class="panel panel-success">
                                <div id="titulo" class="panel-heading">Nuevo Periodo Lectivo</div>
                            </div>
                            <div class="panel-body">
                                <form id="frm-periodo-lectivo" action="" method="post">
                                    <input type="hidden" name="id_periodo_lectivo" id="id_periodo_lectivo" value="0">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_anio_inicio">Año Inicial:</label>
                                                <input type="text" name="pe_anio_inicio" id="pe_anio_inicio" class="form-control" autofocus>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_anio_fin">Año Final:</label>
                                                <input type="text" name="pe_anio_fin" id="pe_anio_fin" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_fecha_inicio">Fecha de inicio:</label>
                                                <div class="controls">
                                                    <div class="input-group date">
                                                        <input type="text" name="pe_fecha_inicio" id="pe_fecha_inicio" class="form-control">
                                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_inicio').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_fecha_fin">Fecha de fin:</label>
                                                <div class="controls">
                                                    <div class="input-group date">
                                                        <input type="text" name="pe_fecha_fin" id="pe_fecha_fin" class="form-control">
                                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#pe_fecha_fin').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_nota_minima">Nota mínima:</label>
                                                <input type="number" min="0.01" step="0.01" class="form-control fuente9" id="pe_nota_minima" value="0.01" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pe_nota_aprobacion">Nota aprobación:</label>
                                                <input type="number" min="7" max="10" step="0.01" class="form-control fuente9" id="pe_nota_aprobacion" value="7" onfocus="sel_texto(this)" onkeypress="return permite(event,'num')">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="">¿Quién inserta el comportamiento?:</label>
                                                <select class="form-control fuente9" name="quien_inserta_comp" id="quien_inserta_comp">
                                                    <!-- <option value="1">Tutor</option>
                                                    <option value="2">Docente</option> -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                        <button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
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
    </div>
    <!-- /.content-wrapper -->
    <!-- <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
    <script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            cargarModalidades();
            cargarQuienInsertaComportamiento();
            //pagination(1);
            $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");

            $("#id_modalidad").change(function() {
                let id_modalidad = $(this).val();
                if (id_modalidad == 0) {
                    $("#tbody_periodos_lectivos").html("<tr><td colspan='6' align='center'>Debe seleccionar una modalidad...</td></tr>");
                    $("#pagination").hide();
                } else {
                    pagination(1, id_modalidad);
                    $("#pagination").show();
                }
            });

            $("#pe_fecha_inicio").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            $("#pe_fecha_fin").datepicker({
                dateFormat: 'yy-mm-dd',
                firstDay: 1
            });

            $("#btn-cancel").click(function() {
                $("#frm-periodo-lectivo")[0].reset();
                $("#titulo").html("Nuevo Periodo Lectivo");
                $("#btn-save").html("Guardar");
                $("#btn-save").prop("disabled", false);
                $("#pe_anio_inicio").focus();
            });

            $('#tbody_periodos_lectivos').on('click', '.item-edit', function() {
                var id_periodo_lectivo = $(this).attr('data');
                $("#titulo").html("Editar Periodo Lectivo");
                $("#btn-save").html("Actualizar");
                $.ajax({
                    url: "periodos_lectivos/obtener_periodo_lectivo.php",
                    data: {
                        id: id_periodo_lectivo
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(data) {
                        $("#id_periodo_lectivo").val(id_periodo_lectivo);
                        $("#pe_anio_inicio").val(data.pe_anio_inicio);
                        $("#pe_anio_fin").val(data.pe_anio_fin);
                        $("#pe_fecha_inicio").val(data.pe_fecha_inicio);
                        $("#pe_fecha_fin").val(data.pe_fecha_fin);
                        $("#pe_nota_minima").val(data.pe_nota_minima);
                        $("#pe_nota_aprobacion").val(data.pe_nota_aprobacion);

                        setearIndice("id_modalidad", data.id_modalidad);
                        setearIndice("quien_inserta_comp", data.quien_inserta_comp_id);

                        if (data.id_periodo_estado == 1)
                            $("#btn-save").prop("disabled", false);
                        else
                            $("#btn-save").prop("disabled", true);
                    },
                    error: function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    }
                });
            });

            $("#frm-periodo-lectivo").submit(function(e) {
                e.preventDefault();
                var url;
                var id_periodo_lectivo = $("#id_periodo_lectivo").val();
                var pe_anio_inicio = $.trim($("#pe_anio_inicio").val());
                var pe_anio_fin = $.trim($("#pe_anio_fin").val());
                var pe_fecha_inicio = $.trim($("#pe_fecha_inicio").val());
                var pe_fecha_fin = $.trim($("#pe_fecha_fin").val());
                var pe_nota_minima = $.trim($("#pe_nota_minima").val());
                var pe_nota_aprobacion = $.trim($("#pe_nota_aprobacion").val());
                var id_modalidad = $("#id_modalidad").val();
                var quien_inserta_comp = $("#quien_inserta_comp").val();

                if (pe_anio_inicio == "") {
                    Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año inicial.", "error");
                } else if (pe_anio_fin == "") {
                    Swal.fire("Ocurrió un error inesperado!", "Debe ingresar el año final.", "error");
                } else if (pe_fecha_inicio == "") {
                    Swal.fire("Ocurrió un error inesperado!", "Debe ingresar la fecha de inicio.", "error");
                } else if (pe_anio_inicio.length != 4) {
                    Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año inicial.", "error");
                } else if (pe_anio_fin.length != 4) {
                    Swal.fire("Ocurrió un error inesperado!", "Debe ingresar los 4 dígitos para el año final.", "error");
                } else if (parseInt(pe_anio_inicio) > parseInt(pe_anio_fin)) {
                    Swal.fire("Ocurrió un error inesperado!", "El año inicial no puede ser mayor que el año final.", "error");
                } else if (id_modalidad == 0) {
                    Swal.fire("Ocurrió un error inesperado!", "Debe seleccionar una modalidad.", "error");
                } else {

                    if ($("#btn-save").html() == "Guardar")
                        url = "periodos_lectivos/insertar_periodo_lectivo.php";
                    else if ($("#btn-save").html() == "Actualizar")
                        url = "periodos_lectivos/actualizar_periodo_lectivo.php";

                    $.ajax({
                        url: url,
                        method: "post",
                        data: {
                            id_periodo_lectivo: id_periodo_lectivo,
                            id_modalidad: id_modalidad,
                            anio_inicial: pe_anio_inicio,
                            anio_final: pe_anio_fin,
                            fec_ini: pe_fecha_inicio,
                            fec_fin: pe_fecha_fin,
                            nota_minima: pe_nota_minima,
                            nota_aprobacion: pe_nota_aprobacion,
                            quien_inserta_comp: quien_inserta_comp
                        },
                        dataType: "json",
                        success: function(response) {

                            console.log(response);

                            Swal.fire({
                                title: response.titulo,
                                text: response.mensaje,
                                icon: response.tipo_mensaje,
                                confirmButtonText: 'Aceptar'
                            });

                            var pagina_actual = $("#pagina_actual").val();
                            pagination(pagina_actual, id_modalidad);

                            $("#frm-periodo-lectivo")[0].reset();

                            if ($("#btn-save").html() == "Actualizar") {
                                $("#btn-save").html("Guardar");
                                $("#titulo").html("Nuevo Periodo Lectivo");
                            }

                        },
                        error: function(jqXHR, textStatus) {
                            console.log(jqXHR.responseText);
                        }
                    });
                }

            });
        });

        function cargarModalidades() {
            $.ajax({
                url: "periodos_lectivos/cargar_modalidades.php",
                dataType: "html",
                success: function(data) {
                    // console.log(data);
                    $("#id_modalidad").append(data);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        }

        function cargarQuienInsertaComportamiento() {
            $.ajax({
                url: "periodos_lectivos/cargar_quien_inserta_comportamiento.php",
                dataType: "html",
                success: function(data) {
                    // console.log(data);
                    $("#quien_inserta_comp").append(data);
                },
                error: function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                }
            });
        }

        function pagination(partida, id_modalidad) {
            $("#pagina_actual").val(partida);
            var url = "periodos_lectivos/paginar_periodos_lectivos.php";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    partida: partida,
                    id_modalidad: id_modalidad
                },
                success: function(data) {
                    var array = eval(data);
                    $("#tbody_periodos_lectivos").html(array[0]);
                    $("#pagination").html(array[1]);
                }
            });
            return false;
        }

        function setearIndice(nombreCombo, indice) {
            for (var i = 0; i < document.getElementById(nombreCombo).options.length; i++)
                if (document.getElementById(nombreCombo).options[i].value == indice) {
                    document.getElementById(nombreCombo).options[i].selected = indice;
                }
        }
    </script>
</body>

</html>