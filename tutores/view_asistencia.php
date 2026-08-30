<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="text-center">Registro de Asistencia</h1>
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
                                <label for="fecha" class="col-md-2 col-sm-2 col-xs-4 col-form-label text-right">Horario:</label>
                                <div class="col-md-4 col-sm-4 col-xs-8">
                                    <select class="form-control fuente9" id="cboHorarios" name="cboHorarios" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                                <label for="fecha" class="col-md-2 col-sm-2 col-xs-4 col-form-label text-right">Fecha:</label>
                                <div class="controls col-md-4 col-sm-4 col-xs-8">
                                    <div class="input-group date">
                                        <input type="text" name="fecha" id="fecha" class="form-control">
                                        <label class="input-group-addon generic-btn" style="cursor: pointer;" onclick="$('#fecha').focus();"><i class="fa fa-calendar" aria-hidden="true"></i></label>
                                    </div>
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
                    <div id="tabla_asistencia" class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <!-- Acá se va a poblar el registro de asistencias -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        cargarHorarios();

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

    async function obtenerAsistencia(fecha) {
        // 1. Obtener los datos directamente del navegador
        const ds_ordinal = dia_semana(fecha); // Este valor ya es tu "id_dia_semana"
        const id_horario_def = $("#cboHorarios").val();
        const id_paralelo_tutor = $("#id_paralelo_tutor").val();

        // 2. Mostrar la animación de carga (Preloader)
        $("#tabla_asistencia").html("<div class='text-center'><img src='imagenes/ajax-loader.gif'></div>");

        try {
            // 3. Única petición Ajax: Vamos directo a consultar la asistencia
            const resultado = await $.ajax({
                type: "post",
                url: "tutores/consultar_asistencia_dia_semana.php",
                data: {
                    id_dia_semana: ds_ordinal, // Usamos el valor local directamente aquí
                    id_horario_def: id_horario_def,
                    id_paralelo_tutor: id_paralelo_tutor,
                    ae_fecha: fecha
                },
                dataType: "json"
            });

            // 4. Dibujar los resultados en la pantalla
            console.log(resultado);
            $("#tabla_asistencia").html(resultado.cadena);
            $("#tituloNomina").html(`ASISTENTES: ${resultado.asistentes} - AUSENTES: ${resultado.ausentes}`);

        } catch (error) {
            // DETECTAR EL ERROR REAL:
            // Imprime el código de estado (ej: 404, 500) y la respuesta del servidor
            console.error("Código de estado HTTP:", error.status);
            console.error("Texto del error:", error.statusText);
            console.error("Respuesta cruda del servidor:", error.responseText);

            Swal.fire({
                icon: "error",
                title: "Error de conexión",
                text: `No se pudo obtener la lista. (Código: ${error.status})`,
            });
            $("#tabla_asistencia").html("");
        }
    }

    function actualizar_asistencia_tutor(obj, id_estudiante, id_paralelo, ae_fecha) {
        if (obj.checked) id_inasistencia = 1;
        else id_inasistencia = 2;
        $.ajax({
            type: "post",
            url: "tutores/actualizar_asistencia_tutor.php",
            data: {
                id_estudiante: id_estudiante,
                id_paralelo: id_paralelo,
                ae_fecha: ae_fecha,
                id_inasistencia: id_inasistencia
            },
            dataType: "json",
            success: function(resultado) {
                // console.log(resultado);
                $("#observacion_" + id_estudiante).html(resultado.observacion);
                $("#tituloNomina").html('ASISTENTES: ' + resultado.asistentes + ' - AUSENTES: ' + resultado.ausentes);
            }
        });
    }
</script>