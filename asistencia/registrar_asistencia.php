<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Asistencia
            <small>Registro de asistencia</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <form id="frm-fecha" action="" method="post" autocomplete="off">
                            <input type="hidden" id="id_curso" value="<?php echo $_GET['id_curso'] ?>">
                            <input type="hidden" id="id_paralelo" value="<?php echo $_GET['id_paralelo'] ?>">
                            <input type="hidden" id="id_asignatura" value="<?php echo $_GET['id_asignatura'] ?>">
                            <input id="id_dia_semana" name="id_dia_semana" type="hidden" />
                            <div class="form-group row">
                                <label for="fecha" class="col-md-2 col-sm-2 col-xs-4 col-form-label text-right">Fecha:</label>
                                <div class="controls col-md-4 col-sm-4 col-xs-8">
                                    <div class="input-group date">
                                        <input type="text" name="fecha" id="fecha" class="form-control">
                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                    </div>
                                </div>
                                <label for="cboHoraClase" class="col-md-2 col-sm-2 col-xs-4 col-form-label text-right">Hora Clase:</label>
                                <div class="col-md-4 col-sm-4 col-xs-8">
                                    <select class="form-control fuente9" id="cboHoraClase" name="cboHoraClase" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div id="tituloNomina" class="text-center"> NOMINA DE ESTUDIANTES </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <table id="t_asistencia" class="table">
                            <thead>
                                <tr>
                                    <th>Nro.</th>
                                    <th>Id</th>
                                    <th>Nómina</th>
                                    <th>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody id="lista_estudiantes_paralelo">
                                <!-- Aquí van los estudiantes con sus asistencias recuperados
                                    desde la Base de Datos mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<script src="../assets/template/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        document.getElementById("tituloNomina").innerHTML = "NOMINA DE ESTUDIANTES (<?php echo $_GET['nombre'] ?> - <?php echo $_GET['curso'] ?>)";

        $("#cboHoraClase").change(function() {
            cargarInasistencias();
        });

        $("#fecha").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            onClose: function(selectDate) {
                document.getElementById("cboHoraClase").disabled = true;
                //Consultar el dia de la semana
                var ds_ordinal = dia_semana(selectDate);
                var id_periodo_lectivo = <?php echo $_GET['id_periodo_lectivo'] ?>;
                $.ajax({
                    type: "post",
                    url: "../horarios/consultar_id_dia_semana.php",
                    data: {
                        ds_ordinal: ds_ordinal,
                        id_periodo_lectivo: id_periodo_lectivo
                    },
                    dataType: "html",
                    success: function(resultado) {
                        if (resultado == false) {
                            // swal("Oops! Ocurrió un error inesperado", "No se han definido D&iacute;as de la Semana...", "error");
                            Swal.fire({
                                icon: "error",
                                title: "Oops! Ocurrió un error inesperado",
                                text: "No se han definido Días de la Semana...",
                            });
                        } else {
                            const JSONIdDiaSemana = eval('(' + resultado + ')');
                            const id_dia_semana = JSONIdDiaSemana.id_dia_semana;
                            const id_asignatura = $("#id_asignatura").val();
                            const id_paralelo = $("#id_paralelo").val();
                            $("#lista_estudiantes_paralelo").html("<tr><td colspan='4' align='center'><img src='../imagenes/ajax-loader-blue.GIF' alt='Procesando...'></td></tr>");
                            $.ajax({
                                type: "post",
                                url: "../horarios/consultar_horas_clase.php",
                                data: {
                                    id_asignatura: id_asignatura,
                                    id_paralelo: id_paralelo,
                                    id_dia_semana: id_dia_semana
                                },
                                dataType: "html",
                                success: function(resultado) {
                                    const datos = JSON.parse(resultado);
                                    document.getElementById("cboHoraClase").length = 1;
                                    if (parseInt(datos.num_registros) == 0) {
                                        // swal("Oops! Ocurrió un error inesperado", "No se han definido Horas Clase para la Asignatura seleccionada...", "error");
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops! Ocurrió un error inesperado",
                                            text: "No se han definido Horas Clase para la Asignatura seleccionada...",
                                        });
                                    } else {
                                        $("#cboHoraClase").append(datos.cadena);
                                    }
                                    $("#lista_estudiantes_paralelo").html("");
                                }
                            });
                        }
                    }
                });
                document.getElementById("cboHoraClase").disabled = false;
            }
        });

    });

    function dia_semana(fecha) {
        fecha = fecha.split('-');
        if (fecha.length != 3) {
            return null;
        }
        //Vector para calcular día de la semana de un año regular.
        var regular = [0, 3, 3, 6, 1, 4, 6, 2, 5, 0, 3, 5];
        //Vector para calcular día de la semana de un año bisiesto.
        var bisiesto = [0, 3, 4, 0, 2, 5, 0, 3, 6, 1, 4, 6];
        //Vector para hacer la traducción de resultado en día de la semana.
        //var semana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        var semana = [7, 1, 2, 3, 4, 5, 6];
        //Día especificado en la fecha recibida por parametro.
        var dia = fecha[2];
        //Módulo acumulado del mes especificado en la fecha recibida por parametro.
        var mes = fecha[1] - 1;
        //Año especificado por la fecha recibida por parametros.
        var anno = fecha[0];
        //Comparación para saber si el año recibido es bisiesto.
        if ((anno % 4 == 0) && !(anno % 100 == 0 && anno % 400 != 0))
            mes = bisiesto[mes];
        else
            mes = regular[mes];
        //Se retorna el resultado del calculo del día de la semana.
        return semana[Math.ceil(Math.ceil(Math.ceil((anno - 1) % 7) + Math.ceil((Math.floor((anno - 1) / 4) - Math.floor((3 * (Math.floor((anno - 1) / 100) + 1)) / 4)) % 7) + mes + dia % 7) % 7)];
    }

    const cargarHorasClase = () => {
        var fecha = document.getElementById("fecha").value;
        document.getElementById("cboHoraClase").disabled = true;
        //Consultar el dia de la semana
        var ds_ordinal = dia_semana(fecha);
        var id_periodo_lectivo = <?php echo $_GET['id_periodo_lectivo'] ?>;
        $.post("../horarios/consultar_id_dia_semana.php", {
                ds_ordinal: ds_ordinal,
                id_periodo_lectivo: id_periodo_lectivo
            },
            function(resultado) {
                if (resultado == false) {
                    swal("Oops! Ocurrió un error inesperado", "No se han definido D&iacute;as de la Semana...", "error");
                } else {
                    var JSONIdDiaSemana = eval('(' + resultado + ')');
                    var id_dia_semana = JSONIdDiaSemana.id_dia_semana;
                    // document.getElementById("id_dia_semana").value = id_dia_semana;
                    if (id_dia_semana == null) {
                        // swal("Oops! Ocurrió un error inesperado", "No se ha definido el D&iacute;a de la Semana...", "error");
                        Swal.fire({
                            icon: "error",
                            title: "Oops! Ocurrió un error inesperado",
                            text: "No se ha definido el Día de la Semana...",
                        });
                    } else {
                        $.post("../horarios/consultar_horas_clase.php", {
                                id_asignatura: id_asignatura,
                                id_paralelo: id_paralelo,
                                id_dia_semana: id_dia_semana
                            },
                            function(resultado) {
                                console.log(resultado);
                                datos = JSON.parse(resultado);
                                document.getElementById("cboHoraClase").length = 1;
                                if (parseInt(datos.num_registros) == 0) {
                                    // swal("Oops! Ocurrió un error inesperado", "No se han definido Horas Clase para la Asignatura seleccionada...", "error");
                                    Swal.fire({
                                        icon: "error",
                                        title: "Oops! Ocurrió un error inesperado",
                                        text: "No se han definido Horas Clase para la Asignatura seleccionada...",
                                    });
                                } else {
                                    $("#cboHoraClase").append(datos.cadena);
                                }
                                document.getElementById("cboHoraClase").disabled = false;
                                $("#lista_estudiantes_paralelo").html("");
                            }
                        );
                    }
                }
            }
        );
    }

    function cargarInasistencias() {
        // Procedimiento para cargar las inasistencia de los estudiantes
        const id_asignatura = document.getElementById("id_asignatura").value;
        const id_paralelo = document.getElementById("id_paralelo").value;
        const id_hora_clase = document.getElementById("cboHoraClase").value;
        const ae_fecha = document.getElementById("fecha").value;

        $("#lista_estudiantes_paralelo").html("<tr><td colspan='4' align='center'><img src='../imagenes/ajax-loader-blue.GIF' alt='Procesando...'></td></tr>");

        $.ajax({
            type: "post",
            url: "../horarios/listar_inasistencia_paralelo.php",
            data: {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_hora_clase: id_hora_clase,
                ae_fecha: ae_fecha
            },
            dataType: "html",
            success: function(resultado) {
                //anadir el resultado al DOM
                $("#lista_estudiantes_paralelo").html(resultado);
            }
        });
    }

    function actualizar_asistencia(obj, id_asistencia_estudiante) {
        if (obj.checked) abreviatura = "A";
        else abreviatura = "I";
        $.ajax({
            type: "POST",
            url: "../horarios/actualizar_inasistencia_estudiante.php",
            data: {
                id_asistencia_estudiante: id_asistencia_estudiante,
                in_abreviatura: abreviatura
            },
            success: function(resultado) {
                // No desplega nada... esto es solo para ejecutar el codigo php
                console.log(resultado);
            }
        });
    }
</script>