<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="text-center">Consulta de Asistencia</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelos">
                                <option value="">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_paralelo"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Horario:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboHorarios">
                                <option value="">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="error_horario"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Fecha:</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group date">
                                <input type="text" name="fecha" id="fecha" class="form-control">
                                <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div id="tituloNomina" class="text-center"> NOMINA DE ESTUDIANTES </div>
                    </div>
                </div>
                <div class="row">
                    <div id="tabla_asistencia" class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <!-- Acá se va a poblar el registro de asistencias -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        cargarParalelos();
        cargarHorarios();

        $("#cboParalelos").change(function(e) {
            $("#tabla_asistencia").html("");
            if ($("#fecha").val() !== "") {
                obtenerAsistencia($("#fecha").val());
            }
        });

        $("#cboHorarios").change(function(e) {
            $("#tabla_asistencia").html("");
            if ($("#fecha").val() !== "") {
                obtenerAsistencia($("#fecha").val());
            }
        });

        $("#fecha").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            onClose: function(selectDate) {
                obtenerAsistencia(selectDate);
            }
        });
    });

    function cargarParalelos() {
        $.get("scripts/cargar_paralelos_especialidad.php", {},
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#cboParalelos").append(resultado);
                    // $("#error_paralelo").html("Debe seleccionar un paralelo...");
                }
            }
        );
    }

    function cargarHorarios() {
        var request = $.ajax({
            url: "dias_semana/cargar_titulos_horarios.php",
            method: "get",
            dataType: "html"
        });

        request.done(function(data) {
            $("#cboHorarios").append(data);
        });
    }

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

    function obtenerAsistencia(fecha) {
        //Consultar el dia de la semana
        var ds_ordinal = dia_semana(fecha);
        var id_horario_def = $("#cboHorarios").val();
        //Gif animado para preloader
        $("#tabla_asistencia").html("<div class='text-center'><img src='imagenes/ajax-loader.gif'></div>");
        $.ajax({
            type: "post",
            url: "horarios/consultar_id_dia_semana.php",
            data: {
                ds_ordinal: ds_ordinal,
                id_horario_def: id_horario_def
            },
            dataType: "json",
            success: function(resultado) {
                if (resultado == false) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops! Ocurrió un error inesperado",
                        text: "No se han definido Días de la Semana...",
                    });
                } else {
                    var id_dia_semana = resultado.id_dia_semana;
                    var id_paralelo = $("#cboParalelos").val();
                    $.ajax({
                        type: "post",
                        url: "autoridad/consultar_asistencia_dia_semana.php",
                        data: {
                            id_dia_semana: id_dia_semana,
                            id_horario_def: id_horario_def,
                            id_paralelo: id_paralelo,
                            ae_fecha: fecha
                        },
                        dataType: "json",
                        success: function(resultado) {
                            console.log(resultado);
                            $("#tabla_asistencia").html(resultado.cadena);
                            $("#tituloNomina").html('ASISTENTES: ' + resultado.asistentes + ' - AUSENTES: ' + resultado.ausentes);
                        }
                    });
                }
            }
        });
    }
</script>