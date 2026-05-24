<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Ingreso de Calificaciones de <?php echo $_GET['nombre'] ?> - <?php echo $_GET['curso'] ?>
                </h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <input type="hidden" id="id_usuario" value="<?php echo $id_usuario; ?>">
                <input type="hidden" id="id_paralelo" value="<?php echo $_GET['id_paralelo']; ?>">
                <input type="hidden" id="id_asignatura" value="<?php echo $_GET['id_asignatura']; ?>">
                <input type="hidden" id="id_periodo_lectivo" value="<?php echo $_GET['id_periodo_lectivo']; ?>">
                <input type="hidden" id="id_periodo_evaluacion">
                <input type="hidden" id="id_aporte_evaluacion">
                <input type="hidden" id="nota_minima">
                <input type="hidden" id="nota_maxima">
                <input type="hidden" id="rows">
                <input type="hidden" id="cols">
                <div class="row">
                    <div class="col-sm-6 col-md-2 col-lg-1 control-label text-right">
                        <label for="">Aporte:</label>
                    </div>
                    <div class="col-sm-6 col-md-10 col-lg-10">
                        <select id="cboAportesEvaluacion" class="form-control">
                            <option value="0">Seleccione...</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div id="div_estado_rubrica" class="col-sm-12 col-md-12 col-lg-12 text-center">
                        ...
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#calificaciones" data-toggle="tab">Calificaciones</a></li>
                                <li><a href="#cuadro_estadistico" data-toggle="tab">Cuadro Estadístico</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="active tab-pane" id="calificaciones">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="tituloNomina" class="header2">NOMINA DE ESTUDIANTES</div>
                                            <div id="divTabla" class="table-responsive">
                                                <!-- Aqui se desplegaran las filas para el ingreso de calificaciones -->
                                                <table id="spreadsheet">
                                                    <thead>
                                                        <!-- Acá se generarán los titulares mediante AJAX -->
                                                    </thead>
                                                    <tbody>
                                                        <!-- Acá se generará el contenido mediante AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="img_loader_estudiantes" class="text-center"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="cuadro_estadistico">
                                    <!-- Default box -->
                                    <div class="box box-solid">
                                        <div class="box-body">
                                            <div class="login-box-body">
                                                <div class="row">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>

<script type="text/javascript">
    const id_asignatura = <?php echo $_GET['id_asignatura'] ?>;
    const id_paralelo = <?php echo $_GET['id_paralelo'] ?>;
    const id_curso = <?php echo $_GET['id_curso'] ?>;

    $(document).ready(function() {
        cargarAportesEvaluacion();

        const $table = $('#spreadsheet');

        $("#cboAportesEvaluacion").change(function(e) {
            $("#div_estado_rubrica").html("");

            if ($(this).val() !== "") {
                // Se determinan los valores de id_periodo_evaluacion e id_aporte_evaluacion
                document.getElementById("id_asignatura").value = id_asignatura;
                document.getElementById("id_paralelo").value = id_paralelo;

                var codigos = $("#cboAportesEvaluacion").val();
                var array_codigos = codigos.split("*");

                id_periodo_evaluacion = array_codigos[0];
                id_aporte_evaluacion = array_codigos[1];

                document.getElementById("id_periodo_evaluacion").value = id_periodo_evaluacion;
                document.getElementById("id_aporte_evaluacion").value = id_aporte_evaluacion;
                //

                mostrarEstadoRubrica();
                mostrarTitulosRubricas(id_periodo_evaluacion, id_aporte_evaluacion);
                cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura, id_periodo_evaluacion, id_aporte_evaluacion)
            } else {
                $("#div_estado_rubrica").html("Debe seleccionar un aporte de evaluación...");
            }

        });

        // Para navegación con flechas
        $('#spreadsheet').on('keydown', '.excel-input', function(e) {
            const id = $(this).attr('id');
            let col = id.replace(/[0-9]/g, ''); // Extraer letras
            let row = parseInt(id.replace(/\D/g, '')); // Extraer números

            const num_cols = parseInt($("#cols").val()) - 1;
            let last_col = String.fromCharCode(65 + num_cols);

            // Lógica de flechas
            if (e.key === 'ArrowUp' && row > 1) row--;
            else if (e.key === 'ArrowDown' && row < $("#rows").val()) row++;
            else if (e.key === 'ArrowLeft' && col > 'A') col = String.fromCharCode(col.charCodeAt(0) - 1);
            else if (e.key === 'ArrowRight' && col < last_col)
                col = String.fromCharCode(col.charCodeAt(0) + 1);
            else if (e.key === 'Enter') {
                e.preventDefault();
                row++;
            } else return;

            const nextId = col + row;

            // Al final usamos:
            $('#' + nextId).focus();
        });

        // Delegación de evento: escuchamos 'input' en cualquier '.formativa' dentro de la tabla
        $('#spreadsheet').on('input', '.formativa', function() {
            const nota_minima = $("#nota_minima").val();
            const nota_maxima = $("#nota_maxima").val();

            // Elimina cualquier cosa que no sea número o punto
            this.value = this.value.replace(/[^0-9.]/g, '');

            // Si hay más de un punto, elimina el último
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.replace(/\.$/, "");
            }

            if (parseFloat(this.value) < parseFloat(nota_minima)) {
                Swal.fire({
                    icon: "error",
                    title: "Error en el ingreso de calificaciones",
                    text: "La calificación no puede ser menor que la nota mínima (" + nota_minima + ")"
                });
                this.value = nota_minima;
            }

            if (parseFloat(this.value) > parseFloat(nota_maxima)) {
                Swal.fire({
                    icon: "error",
                    title: "Error en el ingreso de calificaciones",
                    text: "La calificación no puede ser mayor que la nota máxima (" + nota_maxima + ")"
                });
                this.value = nota_maxima;
            }

            // $(this) es el input que cambió. Buscamos su fila padre <tr>
            var fila = $(this).closest('.fila-datos');

            var suma = 0;
            var inputs = fila.find('.formativa');

            // Recorremos los inputs de esa fila específica
            inputs.each(function() {
                var valor = parseFloat($(this).val()) || 0; // Convertimos a número
                suma += valor;
            });

            var promedioReal = suma / inputs.length;
            var promedioTruncado = (promedioReal == 0) ? "" : trunc(promedioReal, 2);

            // console.log(promedioTruncado);

            // Para asegurar que siempre se vean dos dígitos (ej: 10.5 -> 10.50)
            fila.find('.resultado').text(promedioTruncado);
        });

        // Delegación de evento: escuchamos 'input' en cualquier '.comportamiento' dentro de la tabla
        $('#spreadsheet').on('input', '.formativa', function() {

        });
    });

    function trunc(x, posiciones = 0) {
        var s = x.toString()
        var l = s.length
        var decimalLength = s.indexOf('.') + 1
        var numStr = s.substr(0, decimalLength + posiciones)
        return Number(numStr)
    }

    function editarCalificacion(obj, calificacion_bd, id_estudiante, id_paralelo, id_asignatura, id_rubrica_evaluacion) {
        var valor = parseFloat(obj.value) || 0;

        if (valor > nota_maxima) {
            Swal.fire({
                icon: "error",
                title: "Error en el ingreso de calificaciones",
                text: "La calificación no puede ser mayor a la nota máxima (" + nota_maxima + ")"
            });
            obj.value = calificacion_bd;
            // Disparamos manualmente el evento 'input' para que el promedio se actualice
            $el.trigger('input');

            if (typeof calificacion_bd !== 'undefined' && calificacion_bd !== "")
                obj.value = calificacion_bd;
            else
                obj.value = "";

            return false;
        }

        // Insertar o actualizar la calificación
        $.ajax({
            type: "POST",
            url: "editar_calificacion.php",
            data: {
                id_estudiante: id_estudiante,
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_rubrica_personalizada: id_rubrica_evaluacion,
                re_calificacion: valor
            },
            dataType: "dataType",
            success: function(response) {
                console.log(response);
            }
        });

    }

    function editarComportamiento(obj, id_estudiante, id_paralelo, id_asignatura, id_aporte_evaluacion) {
        //
    }

    function cargarAportesEvaluacion() {
        var id_paralelo = document.getElementById("id_paralelo").value;
        var id_asignatura = document.getElementById("id_asignatura").value;
        var id_periodo_lectivo = document.getElementById("id_periodo_lectivo").value;

        $('#cboAportesEvaluacion option').remove();
        $('#cboAportesEvaluacion optgroup').remove();

        $.post("cargar_aportes_evaluacion_paralelo.php", {
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_periodo_lectivo: id_periodo_lectivo
            },
            function(resultado) {
                // console.log(resultado);
                $("#cboAportesEvaluacion").append(resultado);
            }
        );
    }

    function mostrarEstadoRubrica() {
        const id_aporte_evaluacion = $("#id_aporte_evaluacion").val();
        const id_paralelo = $("#id_paralelo").val();

        $.post("../calificaciones/mostrar_estado_rubrica.php", {
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_paralelo: id_paralelo
            },
            function(resultado) {
                if (resultado == false) {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "Error al obtener el estado del aporte de evaluación...",
                    });
                } else {
                    $("#div_estado_rubrica").html(resultado);

                    // Obtener la fecha de apertura del aporte de evaluación
                    $.ajax({
                        type: "POST",
                        url: "obtener_fecha_apertura_aporte.php",
                        data: {
                            id_aporte_evaluacion: id_aporte_evaluacion,
                            id_paralelo: id_paralelo
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.estado == "3") {
                                Swal.fire({
                                    icon: "info",
                                    title: "Mensaje",
                                    text: "No se encuentra definida la fecha de apertura...",
                                });
                            } else {
                                if (response.estado == "2") {
                                    Swal.fire({
                                        icon: "info",
                                        title: "Mensaje",
                                        text: "El Aporte de Evaluación no se encuentra Abierto todavía...",
                                    });
                                }

                                document.getElementById("div_estado_rubrica").innerHTML = document.getElementById("div_estado_rubrica").innerHTML + " - Fecha de apertura: " + response.fecha_apertura;
                            }
                        }
                    });

                    $.post("../calificaciones/obtener_fecha_cierre_aporte.php", {
                            id_aporte_evaluacion: id_aporte_evaluacion,
                            id_paralelo: id_paralelo
                        },
                        function(resultado) {
                            if (resultado == false) {
                                Swal.fire({
                                    icon: "error",
                                    title: "¡Error!",
                                    text: "Error al obtener la fecha de cierre del aporte de evaluación...",
                                });
                            } else {
                                document.getElementById("div_estado_rubrica").innerHTML = document.getElementById("div_estado_rubrica").innerHTML + " - " + resultado;
                            }
                        }
                    );
                }
            }
        );
    }

    function mostrarTitulosRubricas(id_periodo_evaluacion, id_aporte_evaluacion) {
        // Añadimos clases de Bootstrap a la tabla principal
        const $table = $('#spreadsheet').addClass('table table-bordered table-sm table-hover table-striped align-middle');
        const $thead = $table.find('thead').empty().addClass('cabeceraTabla'); // Fondo gris oscuro con letras en blanco para cabecera

        let filaCabecera = '<tr><th width="50px" class="bg-light">Nro.</th>'; // Esquina con clase de fondo
        filaCabecera += '<th scope="col" class="fw-bold">Nómina</th>';

        // --- CARGA TITULOS DE LAS RUBRICAS CON JQUERY ---
        $.ajax({
            type: "POST",
            url: "cargar_titulos_rubricas.php",
            data: {
                id_asignatura: id_asignatura,
                id_paralelo: id_paralelo,
                id_aporte_evaluacion: id_aporte_evaluacion
            },
            dataType: "json",
            success: function(response) {
                filaCabecera += response.titulos;
                $thead.append(filaCabecera + '</tr>');
            }
        });
    }

    function cargarEstudiantesParalelo(id_curso, id_paralelo, id_asignatura, id_periodo_evaluacion, id_aporte_evaluacion) {

        $("#img_loader_estudiantes").html("<img src='../imagenes/ajax-loader.gif' alt='Procesando...'>");

        const $table = $('#spreadsheet');
        const $tbody = $table.find('tbody').empty();

        $.ajax({
            type: "post",
            url: "listar_estudiantes_paralelo.php",
            data: {
                id_curso: id_curso,
                id_paralelo: id_paralelo,
                id_asignatura: id_asignatura,
                id_aporte_evaluacion: id_aporte_evaluacion,
                id_periodo_evaluacion: id_periodo_evaluacion
            },
            dataType: "json",
            success: function(response) {
                // console.log(response);
                $("#img_loader_estudiantes").html("");

                if (response.ok) {
                    $tbody.append(response.body);
                    $("#rows").val(response.num_filas);
                    $("#cols").val(response.num_columnas);
                    $("#nota_minima").val(response.nota_minima);
                    $("#nota_maxima").val(response.nota_maxima);
                } else {
                    Swal.fire({
                        icon: response.tipo_mensaje,
                        title: response.titulo,
                        text: response.mensaje
                    });
                }

                //adicionar el evento de escucha para "pegar" desde el portapapeles
                // var id_primer_input;
                // var frmFormulario = document.forms["formulario_rubrica"];
                // for (var iCont = 0; iCont < frmFormulario.length; iCont++) {
                //     var objElemento = frmFormulario.elements[iCont];
                //     if (objElemento.type == 'text') {
                //         var id_elem = objElemento.id;
                //         var fila_elem = id_elem.substr(id_elem.lastIndexOf("_") + 1);
                //         var campos = id_elem.split("_");
                //         if (fila_elem == 1 && (campos[0] == 'puntaje' || campos[0] == 'examenquimestral' || campos[0] == 'proyectofinal' || campos[0] == 'evalsubnivel' || campos[0] == 'faseproyecto' || campos[0] == 'cualitativa') || campos[0] == 'calificacionsupletorio') {
                //             id_primer_input = id_elem;
                //             break;
                //         }
                //     }
                // }
                // document.getElementById(id_primer_input).addEventListener('paste', handlePaste);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Otro manejador error
                console.log(jqXHR.responseText);
            }
        });
    }
</script>