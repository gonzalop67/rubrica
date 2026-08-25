$(document).ready(function () {

    // Verificar si la institución permite copiar y pegar
    verificarConfiguracionCopiarPegar();

    // Cargar los paralelos al iniciar la página
    cargarParalelosDocente();

    // EVENTO REFACTORIZADO Y ASEGURADO: Al cambiar el paralelo
    $("#cboParalelos").on("change", function () {
        var $combo = $(this);
        var valorSeleccionado = $combo.val();

        // 1. VALIDACIÓN PREVENTIVA: Si el valor es nulo, vacío o es el texto por defecto ("0"), frenar
        if (!valorSeleccionado || valorSeleccionado.trim() === "" || valorSeleccionado === "0") {
            // Limpiar ambos paneles con mensajes coherentes
            $("#contenedor_asignaturas_docente").html(
                '<div class="text-muted text-center" style="padding: 40px 20px;">' +
                '<i class="fa fa-arrow-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>' +
                '<p style="margin: 0;">Seleccione un paralelo arriba para cargar...</p>' +
                '</div>'
            );
            $("#lista_estudiantes_paralelo").html(
                '<div class="text-center p-4 text-muted" style="padding: 30px;">' +
                '<i class="fa fa-hand-o-left fa-2x" style="margin-bottom: 10px; color: #ccc;"></i> <p style="margin: 0;">Seleccione una asignatura del panel izquierdo para cargar los alumnos...</p>' +
                '</div>'
            );
            $("#num_asignaturas").html("Número de Asignaturas: 0");
            $("#ver_reporte").hide();
            return; // Detiene la ejecución aquí de forma segura
        }

        // 2. Cargar las asignaturas asociadas en el panel derecho (Ya habiendo verificado el ID)
        cargarAsignaturasDocente();

        // 3. Limpiar el panel izquierdo indicando que elija una materia para ver su sábana
        $("#lista_estudiantes_paralelo").html(
            '<div class="text-center p-4 text-muted" style="padding: 30px;">' +
            '<i class="fa fa-hand-o-left fa-2x" style="margin-bottom: 10px; color: #ccc;"></i> <p style="margin: 0;">Seleccione una asignatura del panel izquierdo para cargar los alumnos...</p>' +
            '</div>'
        );

        // Ocultar botón de reporte hasta que elija una materia
        $("#ver_reporte").hide();
    });

    // Función auxiliar para truncar a dos decimales de forma exacta (Mismo comportamiento de PHP)
    function truncarDosDecimales(numero) {
        return Math.floor(numero * 100) / 100;
    }

    /* 
       ===================================================================
       1. MOTOR DE NAVEGACIÓN ESTILO EXCEL (FLECHAS, ENTER Y SALTO INTELIGENTE)
       ===================================================================
       Se ancla a 'document' para que no se rompa cuando AJAX recargue las tablas.
    */
    $(document).on('keydown', '.excel-cell', function (e) {
        var $inputActual = $(this);
        var $celdaActual = $inputActual.closest('td');
        var $filaActual = $celdaActual.closest('tr');
        var colIndex = $celdaActual.index();
        var $proximoInput = $();

        switch (e.key) {
            case "ArrowRight":
            case "Enter":
                // Buscar hacia la derecha en la misma fila (saltando celdas de promedio de texto simple)
                var $siguienteCelda = $celdaActual.next('td');
                while ($siguienteCelda.length) {
                    var $inputCandidato = $siguienteCelda.find('.excel-cell');
                    if ($inputCandidato.length) {
                        $proximoInput = $inputCandidato;
                        break;
                    }
                    $siguienteCelda = $siguienteCelda.next('td');
                }

                // Salto inteligente de fila si llegó al final del alumno actual
                if (!$proximoInput.length) {
                    var $proximaFila = $filaActual.next('tr.row-alumno-sabana');
                    if ($proximaFila.length) {
                        $proximoInput = $proximaFila.find('.excel-cell').first();
                    }
                }
                if (e.key === "Enter") e.preventDefault();
                break;

            case "ArrowLeft":
                // Buscar hacia la izquierda en la misma fila
                var $celdaAnterior = $celdaActual.prev('td');
                while ($celdaAnterior.length) {
                    var $inputCandidatoIzq = $celdaAnterior.find('.excel-cell');
                    if ($inputCandidatoIzq.length) {
                        $proximoInput = $inputCandidatoIzq;
                        break;
                    }
                    $celdaAnterior = $celdaAnterior.prev('td');
                }

                // Salto inteligente hacia la fila de arriba
                if (!$proximoInput.length) {
                    var $filaAnterior = $filaActual.prev('tr.row-alumno-sabana');
                    if ($filaAnterior.length) {
                        $proximoInput = $filaAnterior.find('.excel-cell').last();
                    }
                }
                break;

            case "ArrowDown":
                // Mover verticalmente abajo buscando el mismo índice de columna
                var $proximaFilaAbajo = $filaActual.next('tr.row-alumno-sabana');
                if ($proximaFilaAbajo.length) {
                    var $celdaObjetivoAbajo = $proximaFilaAbajo.children('td').eq(colIndex);
                    $proximoInput = $celdaObjetivoAbajo.find('.excel-cell');
                    // Si cayó en un promedio estático, busca el input editable anterior más cercano
                    if (!$proximoInput.length) {
                        $proximoInput = $celdaObjetivoAbajo.prevAll('td').find('.excel-cell').first();
                    }
                }
                break;

            case "ArrowUp":
                // Mover verticalmente arriba
                var $filaAnteriorArriba = $filaActual.prev('tr.row-alumno-sabana');
                if ($filaAnteriorArriba.length) {
                    var $celdaObjetivoArriba = $filaAnteriorArriba.children('td').eq(colIndex);
                    $proximoInput = $celdaObjetivoArriba.find('.excel-cell');
                    if (!$proximoInput.length) {
                        $proximoInput = $celdaObjetivoArriba.prevAll('td').find('.excel-cell').first();
                    }
                }
                break;

            default:
                return;
        }

        if ($proximoInput.length) {
            $proximoInput.focus().select();
        }
    });

    // =========================================================================
    // 🎯 CONTROL DE ENFOQUE AUTOMÁTICO EN SCROLL (FLEXBOX EXCEL)
    // =========================================================================
    // Escucha cuando el docente navega o escribe dentro de las celdas estilo Excel
    $(document).on('keyup focusin', '.excel-cell', function () {
        var $celdaActual = $(this);
        var $contenedorScroll = $("#tabla-sabana-scroll");

        // Si por alguna razón el contenedor hijo no existe, no hacemos nada
        if ($contenedorScroll.length === 0) return;

        // Calcular las posiciones físicas en la pantalla
        var posicionCeldaTop = $celdaActual.offset().top;
        var posicionContenedorTop = $contenedorScroll.offset().top;

        /* 
         * 1. 🛑 CONTROL DE SUBIDA (EVITAR QUE LA CABECERA TAPE AL ALUMNO 1 Y 2)
         * Sumamos 120 píxeles que es el espacio aproximado que ocupan tus 3 filas fijas.
         */
        var limiteSuperiorCabecera = posicionContenedorTop + 120;

        if (posicionCeldaTop < limiteSuperiorCabecera) {
            // Calculamos cuántos píxeles debemos devolver hacia arriba
            var scrollActual = $contenedorScroll.scrollTop();
            var diferenciaHaciaArriba = limiteSuperiorCabecera - posicionCeldaTop;

            // Movemos el scroll del contenedor para revelar al estudiante escondido
            $contenedorScroll.scrollTop(scrollActual - diferenciaHaciaArriba - 10);
        }

        /* 
         * 2. 🔽 CONTROL DE BAJADA (EVITAR QUE EL ALUMNO SE SALGA POR ABAJO DEL PANEL)
         */
        var alturaContenedor = $contenedorScroll.innerHeight();
        var limiteInferiorPanel = posicionContenedorTop + alturaContenedor;
        var altoCelda = $celdaActual.outerHeight();

        if ((posicionCeldaTop + altoCelda) > limiteInferiorPanel) {
            var scrollActual = $contenedorScroll.scrollTop();
            var diferenciaHaciaAbajo = (posicionCeldaTop + altoCelda) - limiteInferiorPanel;

            // Movemos el scroll hacia abajo para seguir la escritura del docente
            $contenedorScroll.scrollTop(scrollActual + diferenciaHaciaAbajo + 15);
        }
    });

    /**
     * Helper para truncar a dos decimales de forma exacta tal como lo hace tu PHP.
     */
    function truncarDosDecimales(numero) {
        return Math.floor(numero * 100) / 100;
    }

    /* ===================================================================
    2. MOTOR DE RECALCULO Y GUARDADO AUTOMÁTICO EN TIEMPO REAL
    ===================================================================
    Las notas ahora se calculan Y SE GUARDAN automáticamente en la 
    base de datos en el instante en que el docente sale de la celda.
    */
    $(document).on('blur', 'input.excel-cell', function () {
        var $inputModificado = $(this);

        // Control de seguridad por si la celda está bloqueada
        if ($inputModificado.is(':disabled')) {
            return false;
        }

        // 🎯 CAPTURA SEGURA: Leemos el valor y nos aseguramos de que no sea null ni undefined
        var valorCrudo = $inputModificado.val();
        var valorTexto = (valorCrudo !== null && valorCrudo !== undefined) ? valorCrudo.toString().trim() : "-";

        var esCualitativo = $inputModificado.is('select');
        var notaParaEnviar = null;

        if (esCualitativo) {
            // 🔤 CAMINO CUALITATIVO (Cívica): Actualiza el color del texto de la letra en vivo
            var coloresEscala = {
                'A+': '#2b542c', 'A-': '#3c763d', 'B+': '#245269', 'B-': '#31708f',
                'C+': '#66512c', 'C-': '#8a6d3b', 'D+': '#a94442', 'D-': '#ce8483',
                'E+': '#d9534f', 'E-': '#dd4b39', '-': '#333333'
            };
            $inputModificado.css('color', coloresEscala[valorTexto] || '#333');

            // Si eligen el guion, mandamos texto vacío para que el PHP ejecute el DELETE
            notaParaEnviar = (valorTexto === "-") ? "" : valorTexto;

            // 🚀 Disparamos directamente el envío AJAX a la base de datos (Ejecuta tu $.ajax de guardado)
            ejecutarGuardadoAjax($inputModificado, notaParaEnviar);
            return; // Detiene el flujo aquí para que no intente hacer matemáticas de números
        }

        // Si la celda está vacía o tiene un guion, se estandariza a "-" 
        if (valorTexto === "" || valorTexto === "-") {
            $inputModificado.val("-").css({ 'color': '', 'font-weight': '' });
            notaParaEnviar = ""; // En lugar de '0', mandamos vacío para indicarle al PHP que debe borrar
        } else {
            var valorFormatear = parseFloat(valorTexto) || 0;

            // 🔒 CONTROL DE SEGURIDAD: Validar rango escolar de 0.00 a 10.00
            if (valorFormatear < 0 || valorFormatear > 10) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nota fuera de rango',
                    text: 'Las calificaciones deben estar entre 0.00 y 10.00',
                    timer: 2000,
                    showConfirmButton: false
                });
                $inputModificado.val("-").css({ 'color': '', 'font-weight': '' });
                return; // Bloqueamos el flujo para que no guarde un dato erróneo
            } else {
                $inputModificado.val(valorFormatear.toFixed(2));
                notaParaEnviar = valorFormatear.toFixed(2);
            }
        }

        var $fila = $inputModificado.closest('tr.row-alumno-sabana');
        var idEstudiante = $inputModificado.data('estudiante');
        var idAporteActual = $inputModificado.data('aporte');
        var idPeriodoActual = $inputModificado.data('periodo');
        var idRubricaActual = $inputModificado.data('rubrica');

        // =========================================================================
        // TRIPLE CASCADA DE CÁLCULO (Se ejecuta primero para actualizar la pantalla)
        // =========================================================================

        // --- SUB-PROCESO 1: PROMEDIO DEL APORTE (PARCIAL) ---
        var sumaInsumos = 0;
        var $insumosDelParcial = $fila.find('.excel-cell[data-aporte="' + idAporteActual + '"]');
        var cantidadInsumosDefinidos = $insumosDelParcial.length;

        $insumosDelParcial.each(function () {
            var valor = parseFloat($(this).val());
            if (!isNaN(valor) && valor >= 0) {
                sumaInsumos += valor;
            }
        });

        var promedioAporte = 0;
        if (cantidadInsumosDefinidos > 0) {
            promedioAporte = truncarDosDecimales(sumaInsumos / cantidadInsumosDefinidos);
        }

        var $celdaPromAporte = $fila.find('.promedio-aporte-dinamico[data-aporte="' + idAporteActual + '"]');
        if ($celdaPromAporte.length) {
            $celdaPromAporte.html('<b>' + (promedioAporte === 0 ? '-' : promedioAporte.toFixed(2)) + '</b>');
            if (promedioAporte > 0 && promedioAporte < 7) {
                $celdaPromAporte.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
            } else if (promedioAporte >= 7) {
                $celdaPromAporte.css({ 'color': '#3c763d', 'background-color': '#dff0d8' }); // Verde pastel
            } else {
                $celdaPromAporte.css({ 'color': '#333333', 'background-color': '#f5f5f5' });
            }
        }

        // Estilo estático de la nota individual
        var valorInputActual = parseFloat($inputModificado.val());
        if (!isNaN(valorInputActual) && valorInputActual > 0 && valorInputActual < 7) {
            $inputModificado.css({ 'color': '#dd4b39', 'font-weight': 'bold' });
        } else if (!isNaN(valorInputActual) && valorInputActual >= 7) {
            $inputModificado.css({ 'color': '#3c763d', 'font-weight': 'normal' });
        }

        // --- SUB-PROCESO 2: PROMEDIO DEL PERIODO ---
        var sumaAportesPeriodo = 0;
        var conteoAportesPeriodo = 0;

        $fila.find('.promedio-aporte-dinamico[data-periodo="' + idPeriodoActual + '"]').each(function () {
            var valAporte = parseFloat($(this).text());
            if (!isNaN(valAporte) && valAporte > 0) {
                sumaAportesPeriodo += valAporte;
                conteoAportesPeriodo++;
            }
        });

        var promedioPeriodo = 0;
        if (conteoAportesPeriodo > 0) {
            promedioPeriodo = truncarDosDecimales(sumaAportesPeriodo / conteoAportesPeriodo);
        } else {
            promedioPeriodo = promedioAporte;
        }

        var $celdaPromPeriodo = $fila.find('.promedio-periodo-dinamico[data-periodo="' + idPeriodoActual + '"]');
        if ($celdaPromPeriodo.length) {
            $celdaPromPeriodo.html('<b>' + (promedioPeriodo === 0 ? '-' : promedioPeriodo.toFixed(2)) + '</b>');
            if (promedioPeriodo > 0 && promedioPeriodo < 7) {
                $celdaPromPeriodo.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
            } else if (promedioPeriodo >= 7) {
                $celdaPromPeriodo.css({ 'color': '#3c763d', 'background-color': '#dff0d8' }); // Verde pastel
            } else {
                $celdaPromPeriodo.css({ 'color': '', 'background-color': '#f5f5f5' });
            }
        }

        // --- SUB-PROCESO 3: PROMEDIO FINAL GENERAL ---
        var sumaPeriodosGeneral = 0;
        var conteoPeriodosGeneral = 0;

        $fila.find('.promedio-periodo-dinamico').each(function () {
            var valPeriodo = parseFloat($(this).text());
            if (!isNaN(valPeriodo) && valPeriodo > 0) {
                sumaPeriodosGeneral += valPeriodo;
                conteoPeriodosGeneral++;
            }
        });

        var promedioFinalGeneral = 0;
        if (conteoPeriodosGeneral > 0) {
            promedioFinalGeneral = truncarDosDecimales(sumaPeriodosGeneral / conteoPeriodosGeneral);
        } else {
            promedioFinalGeneral = promedioPeriodo;
        }

        var $celdaFinal = $fila.find('.promedio-final-dinamico');
        if ($celdaFinal.length) {
            $celdaFinal.html('<b>' + (promedioFinalGeneral === 0 ? '-' : promedioFinalGeneral.toFixed(2)) + '</b>');
            if (promedioFinalGeneral >= 0 && promedioFinalGeneral <= 4) {
                $celdaFinal.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
            } else if (promedioFinalGeneral > 4 && promedioFinalGeneral < 7) {
                $celdaFinal.css({ 'color': '#8a6d3b', 'background-color': '#fcf8e3' });
            } else if (promedioFinalGeneral >= 7) {
                $celdaFinal.css({ 'color': '#3c763d', 'background-color': '#dff0d8' }); // Verde pastel
            }
        }

        // =========================================================================
        // 🚀 ENVÍO Y GUARDADO AUTOMÁTICO VÍA AJAX (EFECTO AUTO-SAVE)
        // =========================================================================

        // Mostramos un sutil cambio visual de que está "guardando" (borde amarillo temporal)
        $inputModificado.css('border-bottom', '2px solid #f39c12');

        // ... al final absoluto de la lógica numérica ...
        ejecutarGuardadoAjax($inputModificado, notaParaEnviar); // 🎯 LLAMADA 2


        /*$.ajax({
            url: "calificaciones/guardar_nota_individual.php",
            type: "POST",
            dataType: "json",
            data: {
                id_estudiante: idEstudiante,
                id_rubrica: idRubricaActual,
                id_asignatura: $("#id_asignatura").val(),
                id_paralelo: $("#cboParalelos").val(),
                calificacion: notaParaEnviar
            },
            success: function (respuesta) {
                if (respuesta && respuesta.status === "success") {
                    // Éxito: Borde verde rápido que desaparece para indicar guardado transparente
                    $inputModificado.css('border-bottom', '2px solid #00a65a');
                    setTimeout(function () {
                        $inputModificado.css('border-bottom', 'none');
                    }, 1000);
                } else {
                    // Si el servidor reporta un error
                    $inputModificado.css('background-color', '#f2dede');
                    console.error("Error devuelto por el servidor:", respuesta.message);
                }
            },
            error: function (jqXHR) {
                // Error de conexión o caída de sesión
                $inputModificado.css('background-color', '#f2dede');
                console.error("Error crítico al guardar la nota:", jqXHR.responseText);

                // Notificación flotante discreta (Toast) para no interrumpir con un modal gigante
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'error',
                    title: 'Error de conexión: Nota no guardada.'
                });
            }
        });*/
    });

    /* ===================================================================
    3. DISPARADOR EXCLUSIVO PARA ASIGNATURAS CUALITATIVAS (SELECTS)
    ===================================================================
    Se ejecuta de forma INMEDIATA en el instante en que el docente cambia 
    la opción en el desplegable, sin esperar a que pierda el foco.
    */
    $(document).on('change', 'select.excel-cell', function () {
        var $selectModificado = $(this);

        // 🔒 Control de seguridad: Si está deshabilitado por candado de cierre, ignorar
        if ($selectModificado.is(':disabled')) {
            return false;
        }

        // Capturar el valor que el profesor acaba de elegir
        var valorSeleccionado = $selectModificado.val();

        // Convertir de forma segura a texto limpio
        var valorTexto = (valorSeleccionado !== null && valorSeleccionado !== undefined) ? valorSeleccionado.toString().trim() : "-";

        // 🎨 Cambiar el color de la letra en tiempo real según la escala oficial de Ecuador
        var coloresEscala = {
            'A+': '#2b542c', 'A-': '#3c763d',
            'B+': '#245269', 'B-': '#31708f',
            'C+': '#66512c', 'C-': '#8a6d3b',
            'D+': '#a94442', 'D-': '#ce8483',
            'E+': '#d9534f', 'E-': '#dd4b39',
            '-': '#333333'
        };
        $selectModificado.css('color', coloresEscala[valorTexto] || '#333');

        // 🎯 REGLA DE BORRADO: Si eligen el guion "-", mandamos texto vacío "" para que el PHP ejecute el DELETE
        var notaParaEnviar = (valorTexto === "-") ? "" : valorTexto;

        // 🚀 Lanzar el guardado automático de fondo hacia tu tabla 'sw_rubrica_cualitativa'
        ejecutarGuardadoAjax($selectModificado, notaParaEnviar);
    });

    /* ===================================================================
    4. DISPARADOR DEL EVENTO CLICK PARA GUARDAR CALIFICACIONES
    ===================================================================
    Escucha el clic en el botón "Guardar Todo" (#save_all) de forma 
    delegada para asegurar su funcionamiento tras cargas dinámicas.
    */
    $(document).on('click', '#save_all', function (e) {
        // 1. Detener cualquier comportamiento por defecto del botón
        e.preventDefault();

        // 2. Ejecutar la función principal que recolecta y envía las notas
        guardarTabla();
    });

    /* ===================================================================
    EFECTO FOCUS: Comportamiento Inteligente al Entrar a la Celda
    ===================================================================
    Distingue automáticamente si el docente entra a un cuadro numérico 
    o a un selector de letras para darle la guía visual correcta.
    */
    $(document).on('focus', '.excel-cell', function () {
        var $celda = $(this);

        // 🔒 Control de Seguridad: Si la celda está bloqueada por cierre, ignorar
        if ($celda.is(':disabled')) {
            return false;
        }

        // 🎯 ESCENARIO 1: Si es un INPUT de texto (Materias Numéricas)
        if ($celda.is('input')) {
            var valorActual = $celda.val();

            if (valorActual !== null && valorActual !== undefined) {
                var valorLimpio = valorActual.trim();

                // Si tiene un guion, lo borramos momentáneamente para dejar la celda limpia
                if (valorLimpio === "-") {
                    $celda.val("");
                } else {
                    // Si ya tiene una nota (ej: 8.50), la sombreamos toda para sobreescribir rápido
                    setTimeout(function () {
                        $celda.select();
                    }, 50);
                }
            }
        }

        // 🎯 ESCENARIO 2: Si es un SELECT desplegable (Materias Cualitativas como Cívica)
        if ($celda.is('select')) {
            // En Excel, al entrar a una celda con lista, el menú no se abre solo, 
            // pero resalta el borde para avisarte dónde estás parado.
            // No borramos nada porque el docente necesita ver la letra actual (ej: A+).
        }

        // 🎨 GUÍA VISUAL EXCEL: Le pintamos a ambos un borde inferior azul fuerte de AdminLTE
        $celda.css('border-bottom', '2px solid #3c8dbc');
    });

});

/**
 * Verifica si la institución permite copiar y pegar,
 * mostrando u ocultando los botones correspondientes de manera limpia.
 */
function verificarConfiguracionCopiarPegar() {
    $.ajax({
        url: "calificaciones/obtener_estado_copiar_y_pegar.php",
        type: "POST",
        dataType: "json"
    })
        .done(function (resp) {
            $("#in_copiar_y_pegar").val(resp.in_copiar_y_pegar);

            // Uso de clases utilitarias de Bootstrap para ocultar/mostrar elementos
            if (resp.in_copiar_y_pegar == 1) {
                $("#btn-guardar").removeClass("hidden");
            } else {
                $("#btn-guardar").addClass("hidden");
            }
        })
        .fail(function (jqXHR) {
            console.error("Error al obtener estado copiar/pegar:", jqXHR.responseText);
        });
}

/**
 * Carga los paralelos asignados al docente actual.
 */
function cargarParalelosDocente() {
    var $cboParalelos = $('#cboParalelos');
    var $listaEstudiantes = $("#lista_estudiantes_paralelo");

    $cboParalelos.empty();

    $.post("calificaciones/cargar_paralelos_docente.php", {
        _t: Date.now()
    })
        .done(function (resultado) {
            var datosLimpios = resultado.trim();

            if (datosLimpios === "" || datosLimpios === "false") {
                alert("Error al cargar los datos de los paralelos.");
            } else {
                $cboParalelos.html(datosLimpios);

                // Aplicar estado inicial de aviso al usuario usando clases de Bootstrap
                $listaEstudiantes
                    .removeClass("text-success")
                    .addClass("text-danger fw-bold")
                    .html('<div style="padding: 3px"><i class="fa fa-info-circle"></i> Debe elegir un paralelo...</div>');
            }
        })
        .fail(function () {
            alert("Error de conexión al cargar paralelos.");
        });
}

/**
 * Carga los aportes de evaluación según el paralelo seleccionado.
 */
function cargarAportesEvaluacion() {
    var id_paralelo = $("#cboParalelos").val(); // Optimizado usando jQuery
    var $cboAportes = $('#cboAportesEvaluacion');
    var $listaEstudiantes = $("#lista_estudiantes_paralelo");

    $cboAportes.empty(); // Limpia options y optgroups de manera eficiente

    $.post("calificaciones/cargar_aportes_evaluacion_paralelo.php", {
        id_paralelo: id_paralelo
    })
        .done(function (resultado) {
            // Aseguramos consistencia si el backend devuelve un boolealno o un string "false"
            if (resultado === false || resultado === "false") {
                $listaEstudiantes
                    .removeClass("text-success")
                    .addClass("text-danger fw-bold")
                    .html('<div class="p-3"><i class="fa fa-warning"></i> No existen aportes de evaluación asociados a este paralelo...</div>');
            } else {
                $cboAportes.append(resultado);
                $listaEstudiantes
                    .removeClass("text-success")
                    .addClass("text-danger fw-bold")
                    .html('<div class="p-3"><i class="fa fa-info-circle"></i> Debe elegir un aporte de evaluación...</div>');
            }
        })
        .fail(function () {
            alert("Error de conexión al cargar aportes de evaluación.");
        });
}

/**
 * Orquestador para refrescar el bloque de asignaturas del profesor.
 */
function cargarAsignaturasDocente() {
    contarAsignaturasDocente();
}

/**
 * Paso 1: Cuenta cuántas asignaturas hay en el paralelo.
 */
function contarAsignaturasDocente() {
    var id_paralelo = $("#cboParalelos").val();

    // Mostrar ventana de carga de SweetAlert2
    Swal.fire({
        title: 'Cargando asignaturas...',
        text: 'Por favor, espere un momento.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Petición original para contar registros
    $.post("calificaciones/contar_asignaturas_paralelo.php", {
        id_paralelo: id_paralelo
    })
        .done(function (resultado) {
            var datosLimpios = resultado ? resultado.trim() : "";

            if (datosLimpios === "false" || datosLimpios === "") {
                Swal.close();
                limpiarPanelesPorError("No se encontraron asignaturas asignadas.");
            } else {
                try {
                    var respuestaServer = JSON.parse(datosLimpios);
                    var total_registros = parseInt(respuestaServer.num_registros) || 0;

                    // Actualizar el número de registros en la interfaz gráfica
                    $("#num_asignaturas").html("Número de Asignaturas: " + total_registros);

                    if (total_registros > 0) {
                        // ¡Paso Clave! Como hay registros, llamamos a la función que descarga los nombres
                        obtenerDetalleAsignaturas(id_paralelo);
                    } else {
                        Swal.close();
                        limpiarPanelesPorError("No hay asignaturas disponibles para este paralelo.");
                    }
                } catch (error) {
                    Swal.close();
                    console.error("Error al procesar JSON de conteo:", error, datosLimpios);
                    limpiarPanelesPorError("Error en el formato de datos.");
                }
            }
        })
        .fail(function () {
            Swal.close();
            limpiarPanelesPorError("Error de conexión al contar asignaturas.");
        });
}

/**
 * Paso 2: Hace la segunda petición para traer el ID y Nombre de las materias.
 */
function obtenerDetalleAsignaturas(id_paralelo) {
    $.post("calificaciones/listar_asignaturas_docente.php", {
        id_paralelo: id_paralelo
    })
        .done(function (resultadoDetalle) {
            Swal.close(); // Cerramos definitivamente el SweetAlert de carga

            // Si jQuery ya parseó la respuesta como un objeto/array automáticamente
            if (typeof resultadoDetalle === "object" && resultadoDetalle !== null) {
                // Verificar si el servidor devolvió un nodo con mensaje de error
                if (resultadoDetalle.error) {
                    limpiarPanelesPorError(resultadoDetalle.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del Servidor',
                        text: resultadoDetalle.error
                    });
                } else {
                    // Pasar la lista directo a renderizar
                    listarAsignaturasDocente(resultadoDetalle);
                }
            } else {
                // Caso alternativo si por alguna razón llegó como String plano
                try {
                    var datosLimpios = resultadoDetalle ? resultadoDetalle.trim() : "";
                    if (datosLimpios === "" || datosLimpios === "false") {
                        limpiarPanelesPorError("No se pudieron cargar los detalles.");
                    } else {
                        var listaAsignaturas = JSON.parse(datosLimpios);
                        listarAsignaturasDocente(listaAsignaturas);
                    }
                } catch (e) {
                    console.error("Error al procesar el string de asignaturas:", e);
                    limpiarPanelesPorError("Error en el formato de datos devuelto.");
                }
            }
        })
        .fail(function (jqXHR) {
            Swal.close();
            console.error("Error en la petición de detalles:", jqXHR.responseText);
            limpiarPanelesPorError("Error al recuperar el listado de asignaturas.");
        });
}

/**
 * Paso 3: Renderiza las asignaturas en el panel derecho y maneja el evento de selección.
 */
function listarAsignaturasDocente(listaAsignaturasJSON) {
    // 🎯 CORRECCIÓN CLAVE: Apuntar al ID correcto de tu HTML para la lista de materias
    var $contenedor = $("#contenedor_asignaturas_docente");

    if (!Array.isArray(listaAsignaturasJSON) || listaAsignaturasJSON.length === 0) {
        $contenedor.html('<div class="list-group-item text-center text-muted" style="padding: 15px;">No hay asignaturas disponibles</div>');
        return;
    }

    $contenedor.empty(); // Limpiar el contenedor antes de rellenar

    listaAsignaturasJSON.forEach(function (item) {
        var id = item.id_asignatura || item.id || "0";
        var nombre = item.nombre_asignatura || item.nombre || item.asignatura || "Sin nombre";
        var tipoAsignatura = item.tipo_asignatura || item.id_tipo_asignatura || "1";

        // Estructura limpia usando componentes de AdminLTE
        var htmlItem =
            '<a href="#" class="list-group-item data-asignatura-btn" data-id="' + id + '" data-tipo="' + tipoAsignatura + '" style="padding: 12px 15px; border-left: 3px solid transparent; transition: all 0.2s; display: block; text-decoration: none;">' +
            '<span class="label label-primary pull-right" style="font-size: 10px; padding: 3px 6px;">ID: ' + id + '</span>' +
            '<h4 class="list-group-item-heading" style="font-size: 13px; margin-bottom: 0; font-weight: 600; color: #333;">' +
            '<i class="fa fa-book text-muted" style="margin-right: 5px;"></i> ' + nombre +
            '</h4>' +
            '</a>';

        $contenedor.append(htmlItem);
    });
}

// =========================================================================
// 🎯 MOTOR INTERACTIVO: LOGICA DE CLIC EN LA ASIGNATURA (PANEL DE DOCENTES)
// =========================================================================
$(document).on("click", ".data-asignatura-btn", function (e) {
    e.preventDefault();

    var $botonSeleccionado = $(this);

    // 1. Efecto visual: Quitar estado activo a todas y ponérselo a la seleccionada (Estilo AdminLTE)
    $(".data-asignatura-btn")
        .removeClass("active")
        .css({
            "border-left-color": "transparent",
            "background-color": "#fff"
        });

    $botonSeleccionado
        .addClass("active")
        .css({
            "border-left-color": "#3c8dbc",
            "background-color": "#f4f4f4"
        });

    // 2. Capturar el ID y el Tipo de Asignatura guardados en los atributos "data-" del botón
    var idAsignaturaSeleccionada = $botonSeleccionado.data("id");
    var tipoAsignaturaSeleccionada = $botonSeleccionado.data("tipo");

    // 3. Asignar el ID al campo oculto que procesa tu formulario de reportes
    $("#id_asignatura").val(idAsignaturaSeleccionada);

    // 4. Mostrar un spinner de carga en el contenedor de estudiantes (Panel Izquierdo)
    $("#lista_estudiantes_paralelo").html(
        '<div class="text-center text-muted" style="padding: 100px 20px;">' +
        '<i class="fa fa-refresh fa-spin fa-2x" style="color: #3c8dbc; margin-bottom: 10px;"></i>' +
        '<p class="small" style="margin-top: 10px;">Buscando nómina de estudiantes y calificaciones...</p>' +
        '</div>'
    );

    // 5. Mostrar el botón de reportes impresos ahora que hay una materia activa
    $("#ver_reporte").show();

    // 6. 🚀 ¡LLAMADA ENLAZADA A TU FUNCIÓN DE NÓMINA!
    // Enviamos el ID y el Tipo exactamente como lo requiere obtenerNominaEstudiantes()
    obtenerNominaEstudiantes(idAsignaturaSeleccionada, tipoAsignaturaSeleccionada);
});

/**
 * Función auxiliar para limpiar la pantalla de manera correcta según tu nuevo diseño
 */
function limpiarPanelesPorError(mensaje) {
    $("#contenedor_asignaturas_docente").html(
        '<div class="text-center text-muted" style="padding: 20px;">' +
        '<p class="small">' + mensaje + '</p>' +
        '</div>'
    );
    $("#lista_estudiantes_paralelo").html(
        '<div class="text-center text-muted" style="padding: 30px;">' +
        '<i class="fa fa-exclamation-circle text-warning"></i> No se pueden cargar alumnos.' +
        '</div>'
    );
    $("#num_asignaturas").html("Número de Asignaturas: 0");
    $("#ver_reporte").hide();
}

/**
 * Obtiene la nómina de estudiantes basada en el paralelo y la asignatura seleccionada.
 * Removidas todas las referencias antiguas al "Aporte de Evaluación".
 * @param {string|number} idAsignatura - ID de la asignatura seleccionada en el panel derecho.
 * @param {string|number} tipoAsignatura - Tipo de la asignatura seleccionada.
 */
function obtenerNominaEstudiantes(idAsignatura, tipoAsignatura) {
    var id_paralelo = $("#cboParalelos").val();

    // 1. Mostrar SweetAlert2 de carga (Bloquea la pantalla para evitar interrupciones)
    Swal.fire({
        title: 'Cargando estudiantes...',
        text: 'Buscando registros en la base de datos.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Sincronizar el campo oculto de la asignatura en el formulario de reportes
    $("#id_paralelo").val(id_paralelo);
    $("#id_asignatura").val(idAsignatura);

    // 🚀 ENVIAMOS LAS TRES VARIABLES AL SERVIDOR
    $.post("calificaciones/cargar_estudiantes.php", {
        id_paralelo: id_paralelo,
        id_asignatura: idAsignatura,
        id_tipo_asignatura: tipoAsignatura // Pasamos el parámetro al backend de la sábana
    })
        .done(function (resultadoAlumnos) {
            Swal.close(); // Cerrar el cargando de SweetAlert2 

            var datosLimpios = resultadoAlumnos ? resultadoAlumnos.trim() : "";

            // Validar si el backend no devolvió registros o dio error 
            if (datosLimpios === "false" || datosLimpios === "") {
                $("#lbl_total_estudiantes").html("0");
                $("#lista_estudiantes_paralelo").html(
                    '<div class="text-center p-4 text-muted" style="padding: 20px;">' +
                    '<i class="fa fa-users"></i> No se encontraron estudiantes matriculados en este paralelo.' +
                    '</div>'
                );
                $("#ver_reporte").hide();
            } else {
                // 🎯 ¡TU IDEA EN ACCIÓN! Envolvemos la tabla pura de PHP dentro de un div limpio sin clases de Bootstrap
                var tablaEnvoltorioConScroll = '<div id="tabla-sabana-scroll">' + resultadoAlumnos + '</div>';

                // Inyectar el nuevo bloque envuelto en el panel principal izquierdo
                $("#lista_estudiantes_paralelo")
                    .removeClass("text-danger fw-bold")
                    .html(tablaEnvoltorioConScroll);

                // Hacer visible el botón inferior para descargar el Reporte en Excel 
                $("#ver_reporte").fadeIn();

                // 🎯 CORRECCIÓN DE CONTEO: Apuntamos al nuevo contenedor hijo para contar las filas reales de alumnos
                var totalFilas = $("#tabla-sabana-scroll").find("tr.row-alumno-sabana").length;
                if (totalFilas > 0) {
                    $("#lbl_total_estudiantes").html(totalFilas);
                }
            }
        })
        .fail(function (jqXHR) {
            Swal.close();
            console.error("Error al cargar estudiantes:", jqXHR.responseText);
            $("#lista_estudiantes_paralelo").html(
                '<div class="text-center p-4 text-danger">' +
                '<i class="fa fa-ban"></i> Error al conectar con el servidor.' +
                '</div>'
            );
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Hubo un problema al recuperar la nómina. Inténtelo de nuevo.'
            });
        });
}

/**
 * Recolecta todas las calificaciones ingresadas o modificadas en la sábana 
 * y las envía en un solo bloque masivo (JSON) al servidor.
 */
function guardarTabla() {
    // 1. Crear un arreglo vacío para guardar las notas válidas
    var loteCalificaciones = [];

    // 2. Recorrer todos los inputs de notas en la pantalla
    $("#lista_estudiantes_paralelo").find(".excel-cell").each(function () {
        var $input = $(this);
        var valorTexto = $input.val().trim();

        // Solo guardamos si el docente escribió una nota real (ignoramos celdas vacías o con guion)
        if (valorTexto !== "" && valorTexto !== "-") {
            var notaNum = parseFloat(valorTexto);

            // Validar que la nota sea un número real y esté en el rango correcto
            if (!isNaN(notaNum) && notaNum >= 0 && notaNum <= 10) {
                loteCalificaciones.push({
                    id_estudiante: $input.data("estudiante"),
                    id_rubrica: $input.data("rubrica"),
                    id_aporte: $input.data("aporte"),
                    id_periodo: $input.data("periodo"),
                    calificacion: notaNum.toFixed(2) // Guardar con sus 2 decimales limpios
                });
            }
        }
    });

    // 🔒 CONTROL DE SEGURIDAD: Validar si hay notas para procesar
    if (loteCalificaciones.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin datos',
            text: 'No se encontraron calificaciones ingresadas para almacenar.',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    // 3. Mostrar pantalla de bloqueo con SweetAlert2 mientras se procesa en el backend
    Swal.fire({
        title: 'Guardando calificaciones...',
        text: 'Almacenando notas de forma masiva en el sistema. Espere un momento.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Capturar variables del entorno para auditoría o amarre del servidor
    var id_paralelo = $("#cboParalelos").val();
    var id_asignatura = $("#id_asignatura").val();

    // 4. Enviar el paquete de notas mediante una petición POST
    $.ajax({
        url: "calificaciones/guardar_notas_masivo.php", // Reemplaza por la ruta exacta de tu archivo PHP de guardado
        type: "POST",
        dataType: "json",
        data: {
            id_paralelo: id_paralelo,
            id_asignatura: id_asignatura,
            notas: JSON.stringify(loteCalificaciones) // Convertimos el arreglo a una cadena JSON fuerte
        },
        success: function (respuesta) {
            Swal.close(); // Cerrar el spinner de carga

            if (respuesta && respuesta.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: respuesta.message || 'Todas las calificaciones se guardaron correctamente.',
                    timer: 2500,
                    showConfirmButton: false
                });

                // Opcional: Podrías refrescar la nómina para confirmar datos de base de datos
                // obtenerNominaEstudiantes(id_asignatura);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al guardar',
                    text: respuesta.message || 'El servidor rechazó el lote de notas. Intente nuevamente.'
                });
            }
        },
        error: function (jqXHR) {
            Swal.close();
            console.error("Error crítico de guardado:", jqXHR.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error del Servidor',
                text: 'Hubo un problema crítico de comunicación. Compruebe los registros del sistema.'
            });
        }
    });
}

/**
 * 🚀 FUNCIÓN COMPACTA DE GUARDADO AUTOMÁTICO VÍA AJAX (AUTO-SAVE)
 * Envía la calificación (número o letra) de forma transparente al servidor.
 * 
 * @param {jQuery} $element - El elemento HTML (<input> o <select>) modificado.
 * @param {string} nota - El valor limpio a almacenar (un decimal o escala como 'A+').
 */
function ejecutarGuardadoAjax($element, nota) {
    // 1. Capturar los identificadores del mapeo asignados en el HTML/PHP
    var idEstudiante = $element.data('estudiante');
    var idRubrica = $element.data('rubrica');
    var idAporte = $element.data('aporte');
    var idPeriodo = $element.data('periodo');

    // 2. Feedback Visual: Mostramos que el sistema está procesando (Borde inferior amarillo)
    $element.css('border-bottom', '2px solid #f39c12');

    // 3. Lanzar la petición asíncrona hacia el backend de producción
    $.ajax({
        url: "calificaciones/guardar_nota_individual.php", // Tu archivo PHP adaptado
        type: "POST",
        dataType: "json",
        data: {
            id_estudiante: idEstudiante,
            id_rubrica: idRubrica,
            id_aporte: idAporte,
            id_periodo: idPeriodo,
            id_asignatura: $("#id_asignatura").val(),
            id_paralelo: $("#cboParalelos").val(),
            id_tipo_asignatura: $(".data-asignatura-btn.active").data("tipo") || "1", // 🎯 NUEVO: Enviamos el tipo activo del panel derecho
            calificacion: nota
        },
        success: function (respuesta) {
            if (respuesta && respuesta.status === "success") {
                // Éxito: Borde verde rápido que desaparece tras un segundo para indicar éxito
                $element.css('border-bottom', '2px solid #00a65a');
                setTimeout(function () {
                    $element.css('border-bottom', 'none');
                }, 1000);
            } else {
                // Si el servidor reporta un error lógico de base de datos
                $element.css({
                    'background-color': '#f2dede',
                    'border-bottom': '2px solid #dd4b39'
                });
                console.error("Error devuelto por el servidor:", respuesta.message);
            }
        },
        error: function (jqXHR) {
            // Error crítico de red, caída de internet o sesión expirada
            $element.css({
                'background-color': '#f2dede',
                'border-bottom': '2px solid #dd4b39'
            });
            console.error("Error crítico de comunicación con el servidor:", jqXHR.responseText);

            // Notificación discreta flotante (Toast) en la esquina superior para no interrumpir al docente
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'error',
                title: 'Error de conexión: Calificación no guardada.'
            });
        }
    });
}
