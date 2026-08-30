<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="text-center">Justificar Faltas</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <form id="frm-fecha" action="" method="post" autocomplete="off">
                            <input type="hidden" id="id_curso" name="id_curso" value="<?php echo $_GET['id_curso'] ?>">
                            <input type="hidden" id="id_paralelo_tutor" name="id_paralelo_tutor" value="<?php echo $_GET['id_paralelo'] ?>">
                            <input type="hidden" id="id_asignatura" name="id_asignatura" value="<?php echo $_GET['id_asignatura'] ?>">
                            <input type="hidden" id="id_dia_semana" name="id_dia_semana" />
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
                        <!-- <table id="t_asistencia" class="table">
                            <thead>
                                <tr>
                                    <th>Nro.</th>
                                    <th>Id</th>
                                    <th>Nómina</th>
                                    <th>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody id="lista_estudiantes_paralelo">
                                
                            </tbody>
                        </table> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<?php require_once "modalActualizarJustificacion.php" ?>

<script src="assets/template/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        cargarHorarios();

        $("#cboHorarios").change(function(e) {
            $("#tabla_asistencia").html("");
            const fechaActual = $("#fecha").val();
            if (fechaActual !== "") {
                obtenerAsistencia(fechaActual);
            }
        });

        $("#fecha").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            onClose: function(selectDate) {
                if (selectDate !== "") {
                    obtenerAsistencia(selectDate);
                }
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

    /**
     * Carga los títulos de los horarios en el combobox desplegable
     */
    async function cargarHorarios() {
        try {
            const data = await $.ajax({
                url: "dias_semana/cargar_titulos_horarios.php",
                method: "get",
                dataType: "html"
            });
            $("#cboHorarios").append(data);
        } catch (error) {
            console.error("Error al cargar horarios:", error);
        }
    }

    /**
     * Consulta y genera el listado de asistencia para justificar faltas
     */
    async function obtenerAsistencia(fecha) {
        const id_horario_def = $("#cboHorarios").val();

        if (id_horario_def === "") {
            Swal.fire({
                icon: "warning",
                title: "Horario requerido",
                text: "Por favor, seleccione un horario antes de elegir la fecha."
            });
            $("#fecha").val("");
            return;
        }

        const ds_ordinal = dia_semana(fecha);

        // Capturar los valores de los inputs corregidos
        const id_curso = $("#id_curso").val();
        const id_paralelo_tutor = $("#id_paralelo_tutor").val();
        const id_asignatura = $("#id_asignatura").val();

        // Mostrar precargador animado (Preloader)
        $("#tabla_asistencia").html("<div class='text-center'><img src='imagenes/ajax-loader.gif'></div>");

        try {
            const resultado = await $.ajax({
                type: "post",
                url: "tutores/consultar_asistencia_justificar.php",
                data: {
                    id_dia_semana: ds_ordinal,
                    id_horario_def: id_horario_def,
                    id_curso: id_curso,
                    id_paralelo_tutor: id_paralelo_tutor, // Ahora viajará con el ID correcto
                    id_asignatura: id_asignatura,
                    ae_fecha: fecha
                },
                dataType: "json"
            });

            if (resultado.ok) {
                $("#tabla_asistencia").html(resultado.cadena);
            } else {
                Swal.fire({
                    title: "Atención",
                    text: resultado.mensaje,
                    icon: 'warning'
                });
                $("#tabla_asistencia").html("");
            }

        } catch (error) {
            console.error("Error al obtener asistencia para justificar:", error);
            Swal.fire({
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo conectar con el servidor. Inténtalo de nuevo."
            });
            $("#tabla_asistencia").html("");
        }
    }

    /**
     * Abre el modal y carga los datos de la justificación actual del alumno
     */
    async function justificar_asistencia_tutor(obj, id_estudiante, id_paralelo, ae_fecha) {
        try {
            const response = await $.ajax({
                type: "POST",
                url: "tutores/obtener_justificacion.php",
                data: {
                    id_estudiante,
                    id_paralelo,
                    ae_fecha
                },
                dataType: "json"
            });

            $("#form_update")[0].reset();
            $("#error-justificacion").html("").hide();
            $("#justificacion").val(response.justificacion);
            $("#id_asistencia_tutor").val(response.id_asistencia_tutor);
            $("#editarJustificacionModal").modal("show");

        } catch (error) {
            console.error("Error al obtener justificación:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudieron recuperar los datos de la justificación."
            });
        }
    }

    /**
     * Remueve la justificación guardada de una falta
     */
    async function deshacer_asistencia_tutor(id_asistencia_tutor) {
        try {
            const response = await $.ajax({
                type: "POST",
                url: "tutores/deshacer_justificacion.php",
                data: {
                    id_asistencia_tutor
                },
                dataType: "html"
            });
            console.log(response);
            obtenerAsistencia($("#fecha").val());
        } catch (error) {
            console.error("Error al deshacer justificación:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo deshacer la justificación."
            });
        }
    }

    /**
     * Envío del formulario del Modal para guardar/actualizar la justificación
     */
    $("#form_update").on("submit", async function(e) {
        e.preventDefault();

        const id_asistencia_tutor = $("#id_asistencia_tutor").val();
        const justificacion = $("#justificacion").val().trim();

        if (justificacion === "") {
            $("#error-justificacion").html("Debe ingresar la justificación...").fadeIn();
            return;
        }

        try {
            await $.ajax({
                type: "POST",
                url: "tutores/actualizar_justificacion.php",
                data: {
                    id_asistencia_tutor,
                    justificacion
                },
                dataType: "json"
            });

            obtenerAsistencia($("#fecha").val());
            $("#form_update")[0].reset();
            $("#editarJustificacionModal").modal("hide");

            Swal.fire({
                icon: "success",
                title: "Guardado",
                text: "La justificación se registró con éxito.",
                timer: 1500,
                showConfirmButton: false
            });

        } catch (error) {
            console.error("Error al actualizar justificación:", error);
            Swal.fire({
                icon: "error",
                title: "Error al guardar",
                text: "Hubo un fallo en la red y no se pudo guardar. Inténtalo de nuevo."
            });
        }
    });
</script>