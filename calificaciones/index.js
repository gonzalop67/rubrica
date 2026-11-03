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
            // Limpiar ambos paneles con mensajes coherentes orientados al flujo horizontal
            $("#contenedor_asignaturas_docente").html(
                '<div class="text-muted text-center" style="width: 100%; padding: 15px 0; color: #999; font-size: 13px;">' +
                '<i class="fa fa-arrow-up" style="margin-right: 5px; color: #ccc;"></i>' +
                'Seleccione un paralelo arriba para desplegar las materias...' +
                '</div>'
            );
            $("#lista_estudiantes_paralelo").html(
                '<div class="text-muted text-center" style="padding: 60px 20px; color: #999;">' +
                '<i class="fa fa-hand-o-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>' +
                '<p style="margin: 0; font-size: 13px;">Seleccione una asignatura del panel superior para cargar la nómina de alumnos...</p>' +
                '</div>'
            );
            $("#num_asignaturas").html("Número de Asignaturas: 0");
            $("#ver_reporte").hide();
            return; // Detiene la ejecución aquí de forma segura
        }

        // 2. Cargar las asignaturas asociadas en el panel superior (Ya habiendo verificado el ID)
        cargarAsignaturasDocente();

        // 3. Limpiar la sábana indicando que elija una materia de arriba para ver su sábana
        $("#lista_estudiantes_paralelo").html(
            '<div class="text-muted text-center" style="padding: 60px 20px; color: #999;">' +
            '<i class="fa fa-hand-o-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>' +
            '<p style="margin: 0; font-size: 13px;">Seleccione una asignatura del panel superior para cargar la nómina de alumnos...</p>' +
            '</div>'
        );

        // Ocultar botón de reporte hasta que elija una materia
        $("#ver_reporte").hide();
    });

    // Función auxiliar para redondear a dos decimales de forma exacta (Mismo comportamiento de PHP)
    function redondearDosDecimales(valor) {
        let numero = parseFloat(valor);
        if (isNaN(numero) || numero === 0) return 0;

        // Redondeo matemático seguro que simula a la perfección el PHP_ROUND_HALF_UP
        return Number(Math.round(numero + 'e2') + 'e-2');
    }

    /* 
       ===================================================================
       1. MOTOR DE NAVEGACIÓN Y SCROLL FLUIDO ESTILO EXCEL (REPARADO Y SUAVE)
       ===================================================================
    */
    $(document).on('keydown', '.excel-cell', function (e) {
        var $inputActual = $(this);
        var $celdaActual = $inputActual.closest('td');
        var $filaActual = $celdaActual.closest('tr');
        var colIndex = $celdaActual.index();
        var $proximoInput = $();

        // Si la tecla presionada no es de dirección o Enter, ignorar
        if (["ArrowRight", "ArrowLeft", "ArrowDown", "ArrowUp", "Enter"].indexOf(e.key) === -1) {
            return;
        }

        // Prevenir el scroll tosco y salto por defecto que hace el navegador
        e.preventDefault();

        switch (e.key) {
            case "ArrowRight":
            case "Enter":
                var $siguienteCelda = $celdaActual.next('td');
                while ($siguienteCelda.length) {
                    var $inputCandidato = $siguienteCelda.find('.excel-cell');
                    if ($inputCandidato.length && !$inputCandidato.is(':disabled')) {
                        $proximoInput = $inputCandidato;
                        break;
                    }
                    $siguienteCelda = $siguienteCelda.next('td');
                }
                if (!$proximoInput.length) {
                    var $proximaFila = $filaActual.next('tr.row-alumno-sabana');
                    if ($proximaFila.length) {
                        $proximoInput = $proximaFila.find('.excel-cell').first();
                    }
                }
                break;

            case "ArrowLeft":
                var $celdaAnterior = $celdaActual.prev('td');
                while ($celdaAnterior.length) {
                    var $inputCandidatoIzq = $celdaAnterior.find('.excel-cell');
                    if ($inputCandidatoIzq.length && !$inputCandidatoIzq.is(':disabled')) {
                        $proximoInput = $inputCandidatoIzq;
                        break;
                    }
                    $celdaAnterior = $celdaAnterior.prev('td');
                }
                if (!$proximoInput.length) {
                    var $filaAnterior = $filaActual.prev('tr.row-alumno-sabana');
                    if ($filaAnterior.length) {
                        $proximoInput = $filaAnterior.find('.excel-cell').last();
                    }
                }
                break;

            case "ArrowDown":
                var $proximaFilaAbajo = $filaActual.next('tr.row-alumno-sabana');
                if ($proximaFilaAbajo.length) {
                    var $celdaObjetivoAbajo = $proximaFilaAbajo.children('td').eq(colIndex);
                    $proximoInput = $celdaObjetivoAbajo.find('.excel-cell:not(:disabled)');
                    if (!$proximoInput.length) {
                        $proximoInput = $celdaObjetivoAbajo.prevAll('td').find('.excel-cell:not(:disabled)').first();
                    }
                }
                break;

            case "ArrowUp":
                var $filaAnteriorArriba = $filaActual.prev('tr.row-alumno-sabana');
                if ($filaAnteriorArriba.length) {
                    var $celdaObjetivoArriba = $filaAnteriorArriba.children('td').eq(colIndex);
                    $proximoInput = $celdaObjetivoArriba.find('.excel-cell:not(:disabled)');
                    if (!$proximoInput.length) {
                        $proximoInput = $celdaObjetivoArriba.prevAll('td').find('.excel-cell:not(:disabled)').first();
                    }
                }
                break;
        }

        // ===================================================================
        // 🎯 CONTROL DE ENFOQUE Y SCROLL SIN EFECTO ELÁSTICO (REPARADO)
        // ===================================================================
        if ($proximoInput.length) {
            // 1. Activar el candado de movimiento para el guardado automático AJAX
            $inputActual.addClass('moving-focus');

            // 2. Enfocar la celda destino
            if ($proximoInput) {
                $proximoInput.focus();
                $proximoInput.select();
            }

            // Retirar el candado una vez que el foco cambió de forma segura
            $inputActual.removeClass('moving-focus');

            var $contenedorScroll = $("#tabla-sabana-scroll");
            if ($contenedorScroll.length === 0) return;

            // Extraemos los elementos nativos puros usando el índice de arreglo
            var contenedorNativo = $contenedorScroll[0];
            var filaNativa = $proximoInput.closest('tr')[0];
            var celdaNativa = $proximoInput.closest('td')[0];

            // 3. 🔒 CONTROL VERTICAL MAESTRO NATIVO BLINDADO
            if (filaNativa && typeof filaNativa.scrollIntoView === "function") {
                filaNativa.scrollIntoView({
                    behavior: 'auto',
                    block: 'nearest',
                    inline: 'nearest'
                });
            } else if (filaNativa && $contenedorScroll.length) {
                // Failsafe por si el navegador bloquea la función nativa: scroll manual con jQuery
                var altoCabecerasFijas = 114;
                var topFila = $proximoInput.closest('tr').position().top + $contenedorScroll.scrollTop();
                if (topFila < ($contenedorScroll.scrollTop() + altoCabecerasFijas)) {
                    $contenedorScroll.scrollTop(topFila - altoCabecerasFijas - 5);
                }
            }

            // Compensación milimétrica por si la cabecera triple (114px) tapa al alumno de arriba al subir
            var altoCabecerasFijas = 114;
            if (contenedorNativo && filaNativa && filaNativa.offsetTop < (contenedorNativo.scrollTop + altoCabecerasFijas)) {
                contenedorNativo.scrollTop = filaNativa.offsetTop - altoCabecerasFijas - 5;
            }

            // 4. CONTROL HORIZONTAL FLUIDO (MANTIENE LA ESTABILIDAD DE LADOS)
            if (colIndex > 1) {
                var celdaIzquierdaAbsoluta = celdaNativa.offsetLeft;
                var celdaDerechaAbsoluta = celdaIzquierdaAbsoluta + celdaNativa.offsetWidth;
                var scrollIzquierdoActual = contenedorNativo.scrollLeft;
                var anchoVisibleContenedor = contenedorNativo.clientWidth;
                var anchoColumnasFijas = 250; // Margen para el bloque fijo de nombres

                if (celdaIzquierdaAbsoluta < (scrollIzquierdoActual + anchoColumnasFijas)) {
                    contenedorNativo.scrollLeft = celdaIzquierdaAbsoluta - anchoColumnasFijas - 15;
                } else if (celdaDerechaAbsoluta > (scrollIzquierdoActual + anchoVisibleContenedor)) {
                    contenedorNativo.scrollLeft = celdaDerechaAbsoluta - anchoVisibleContenedor + 15;
                }
            }
        }

    });

    /* ===================================================================
    2. MOTOR DE RECALCULO Y GUARDADO AUTOMÁTICO EN TIEMPO REAL
    ===================================================================
    */
    $(document).on('blur', 'input.excel-cell', function () {
        var $inputModificado = $(this);

        // 🛡️ ESCUDO DE SEGURIDAD INTERNO: Si es un input de comportamiento, sal de aquí inmediatamente
        if ($inputModificado.data('tipo-input') === 'comportamiento') {
            return;
        }

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
            var coloresEscala = {
                'A+': '#2b542c', 'A-': '#3c763d', 'B+': '#245269', 'B-': '#31708f',
                'C+': '#66512c', 'C-': '#8a6d3b', 'D+': '#a94442', 'D-': '#ce8483',
                'E+': '#d9534f', 'E-': '#dd4b39', '-': '#333333'
            };
            $inputModificado.css('color', coloresEscala[valorTexto] || '#333');
            notaParaEnviar = (valorTexto === "-") ? "" : valorTexto;
            ejecutarGuardadoAjax($inputModificado, notaParaEnviar);
            return; 
        }

        // Si la celda está vacía o tiene un guion, se estandariza a "-" 
        if (valorTexto === "" || valorTexto === "-") {
            $inputModificado.val("-").css({ 'color': '', 'font-weight': '' });
            notaParaEnviar = ""; 
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
                return; 
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
        // TRIPLE CASCADA DE CÁLCULO TRADUCIDA AL MODELO POLINÓMICO PONDERADO
        // =========================================================================

        // --- SUB-PROCESO 1: PROMEDIO DEL APORTE (PARCIAL O EXAMEN INDIVIDUAL) ---
        var sumaInsumos = 0;
        var $insumosDelAporte = $fila.find('.excel-cell[data-aporte="' + idAporteActual + '"]');
        var cantidadInsumosDefinidos = $insumosDelAporte.length; 

        $insumosDelAporte.each(function () {
            var textoInsumo = $(this).val().trim();
            if (textoInsumo !== "" && textoInsumo !== "-") {
                var valor = parseFloat(textoInsumo);
                if (!isNaN(valor) && valor >= 0) {
                    sumaInsumos += valor;
                }
            }
        });

        var promedioAporte = 0;
        if (cantidadInsumosDefinidos > 0) {
            var promedioCrudo = sumaInsumos / cantidadInsumosDefinidos;
            promedioAporte = Number(Math.floor((promedioCrudo + 0.000001) * 100) / 100);
        }

        // Actualizar la celda visual del parcial en pantalla
        var $celdaPromAporte = $fila.find('.promedio-aporte-dinamico[data-aporte="' + idAporteActual + '"]');
        if ($celdaPromAporte.length) {
            $celdaPromAporte.html('<b>' + (sumaInsumos === 0 ? '-' : promedioAporte.toFixed(2)) + '</b>');
            if (sumaInsumos > 0 && promedioAporte < 7) {
                $celdaPromAporte.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
            } else if (sumaInsumos > 0 && promedioAporte >= 7) {
                $celdaPromAporte.css({ 'color': '#3c763d', 'background-color': '#dff0d8' });
            } else {
                $celdaPromAporte.css({ 'color': '#333333', 'background-color': '#f5f5f5' });
            }
        }

        // Estilo estático en vivo de la celda que se está digitando
        var valorInputActual = parseFloat($inputModificado.val());
        if (!isNaN(valorInputActual) && valorInputActual > 0 && valorInputActual < 7) {
            $inputModificado.css({ 'color': '#dd4b39', 'font-weight': 'bold' });
        } else if (!isNaN(valorInputActual) && valorInputActual >= 7) {
            $inputModificado.css({ 'color': '#3c763d', 'font-weight': 'normal' });
        }

        // --- SUB-PROCESO 2: PROMEDIO DEL PERIODO (BIMESTRAL/TRIMESTRAL CON DIVISOR FIJO AL 100%) ---
        var promedioPonderadoBimestral = 0;
        
        // 🔒 FIX: Forzamos el divisor a 1.00 (100% de la nota) para que no sea un promedio móvil
        var sumaPonderacionesEfectivas = 1.00; 

        $fila.find('.promedio-aporte-dinamico[data-periodo="' + idPeriodoActual + '"]').each(function () {
            var $celdaParcial = $(this);
            var idAporte = $celdaParcial.data('aporte');
            
            var $inputPatron = $fila.find('.excel-cell[data-aporte="' + idAporte + '"]').first();
            var ponderacion = parseFloat($inputPatron.data('ponderacion')) || 0;

            var textoPromAporte = $celdaParcial.text().trim();

            if (textoPromAporte !== "" && textoPromAporte !== "-") {
                var valPromAporte = parseFloat(textoPromAporte) || 0;
                promedioPonderadoBimestral += (valPromAporte * ponderacion);
            }
        });

        // 🎯 FIX: También debemos escanear si el Examen Individual u otros insumos directos sumaron puntos
        // buscando inputs directos del periodo que no formen parte de los parciales tradicionales agrupados
        $fila.find('.excel-cell[data-periodo="' + idPeriodoActual + '"]').each(function () {
            var $inputDirecto = $(this);
            // Si el input tiene tipo de aporte diferente al parcial acumulado (ej. Examen) y no ha sido procesado arriba
            if ($inputDirecto.data('tipo-aporte') != '1') { 
                var textoDirecto = $inputDirecto.val().trim();
                var ponderacionDirecta = parseFloat($inputDirecto.data('ponderacion')) || 0;
                
                if (textoDirecto !== "" && textoDirecto !== "-") {
                    var valDirecto = parseFloat(textoDirecto) || 0;
                    // Evitamos duplicar sumando solo si el elemento padre no pertenece a un promedio dinámico ya calculado
                    if (!$inputDirecto.closest('td').hasClass('promedio-aporte-dinamico')) {
                        promedioPonderadoBimestral += (valDirecto * ponderacionDirecta);
                    }
                }
            }
        });

        var promedioPeriodo = 0;
        if (sumaPonderacionesEfectivas > 0) {
            // El resultado se divide directamente para 1.00, manteniendo la penalización por notas faltantes
            promedioPeriodo = Number(Math.round(promedioPonderadoBimestral + 'e2') + 'e-2');
        }

        // ACTUALIZACIÓN DINÁMICA DE LA COLUMNA DE PROMEDIO TRIMESTRAL
        var $celdaPromPeriodo = $fila.find('.promedio-periodo-dinamico[data-periodo="' + idPeriodoActual + '"]');

        if ($celdaPromPeriodo.length) {
            if (promedioPeriodo === 0) {
                $celdaPromPeriodo.html('<b>-</b>').css({ 'color': '#999', 'background-color': '#f5f5f5' });
            } else {
                $celdaPromPeriodo.html('<b>' + promedioPeriodo.toFixed(2) + '</b>');
                if (promedioPeriodo < 7) {
                    $celdaPromPeriodo.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
                } else {
                    $celdaPromPeriodo.css({ 'color': '#3c763d', 'background-color': '#dff0d8' });
                }
            }
        }

        // --- SUB-PROCESO 3: PROMEDIO FINAL GENERAL ANUAL ---
        var sumaPeriodosGeneral = 0;
        var conteoPeriodosGeneral = 0;

        $fila.find('.promedio-periodo-dinamico').each(function () {
            var textoPeriodoLimpio = $(this).text().replace(/[^0-9.]/g, '').trim();
            var valPeriodo = parseFloat(textoPeriodoLimpio);
            if (!isNaN(valPeriodo) && valPeriodo > 0) {
                sumaPeriodosGeneral += valPeriodo;
                conteoPeriodosGeneral++;
            }
        });

        var promedioFinalGeneral = 0;
        if (conteoPeriodosGeneral > 0) {
            var divisionAnual = sumaPeriodosGeneral / conteoPeriodosGeneral;
            promedioFinalGeneral = Number(Math.round(divisionAnual + 'e2') + 'e-2');
        }

        var $celdaFinal = $fila.find('.promedio-final-dinamico');
        if ($celdaFinal.length) {
            if (promedioFinalGeneral === 0) {
                $celdaFinal.html('<b>-</b>').css({ 'color': '#999', 'background-color': '#f5f5f5' });
            } else {
                $celdaFinal.html('<b>' + promedioFinalGeneral.toFixed(2) + '</b>');
                if (promedioFinalGeneral > 0 && promedioFinalGeneral <= 4) {
                    $celdaFinal.css({ 'color': '#dd4b39', 'background-color': '#f2dede' });
                } else if (promedioFinalGeneral > 4 && promedioFinalGeneral < 7) {
                    $celdaFinal.css({ 'color': '#8a6d3b', 'background-color': '#fcf8e3' });
                } else if (promedioFinalGeneral >= 7) {
                    $celdaFinal.css({ 'color': '#3c763d', 'background-color': '#dff0d8' });
                }
            }
        }

        // =========================================================================
        // 🚀 ENVÍO Y GUARDADO AUTOMÁTICO VÍA AJAX (EFECTO AUTO-SAVE)
        // =========================================================================
        $inputModificado.css('border-bottom', '2px solid #f39c12');
        ejecutarGuardadoAjax($inputModificado, notaParaEnviar);
    });

    /* ===================================================================
    3. DISPARADOR EXCLUSIVO PARA ASIGNATURAS CUALITATIVAS (SELECTS)
    ===================================================================
    */
    $(document).on('change', 'select.excel-cell', function () {
        var $selectModificado = $(this);

        if ($selectModificado.is(':disabled')) {
            return false;
        }

        var valorSeleccionado = $selectModificado.val();
        var valorTexto = (valorSeleccionado !== null && valorSeleccionado !== undefined) ? valorSeleccionado.toString().trim() : "-";

        var coloresEscala = {
            'A+': '#2b542c', 'A-': '#3c763d',
            'B+': '#245269', 'B-': '#31708f',
            'C+': '#66512c', 'C-': '#8a6d3b',
            'D+': '#a94442', 'D-': '#ce8483',
            'E+': '#d9534f', 'E-': '#dd4b39',
            '-': '#333333'
        };
        $selectModificado.css('color', coloresEscala[valorTexto] || '#333');

        var notaParaEnviar = (valorTexto === "-") ? "" : valorTexto;
        ejecutarGuardadoAjax($selectModificado, notaParaEnviar);
    });

    /* ===================================================================
    4. DISPARADOR DEL EVENTO CLICK PARA GUARDAR CALIFICACIONES
    ===================================================================
    */
    $(document).on('click', '#save_all', function (e) {
        e.preventDefault();
        guardarTabla();
    });

    /* ===================================================================
   🎯 OPTIMIZADOR EXCEL: DETECTOR DE CELDAS MODIFICADAS
   =================================================================== */
    $(document).on("input change", "#tabla-sabana-scroll .excel-cell", function () {
        $(this).addClass("dirty-cell");
        $("#save_all").addClass("btn-save-pulse");
    });

    /* ===================================================================
    EFECTO FOCUS: Comportamiento Inteligente al Entrar a la Celda
    ===================================================================
    */
    $(document).on('focus', '.excel-cell', function () {
        var $celda = $(this);

        if ($celda.is(':disabled')) {
            return false;
        }

        if ($celda.is('input')) {
            var valorActual = $celda.val();

            if (valorActual !== null && valorActual !== undefined) {
                var valorLimpio = valorActual.trim();

                if (valorLimpio === "-") {
                    $celda.val("");
                } else {
                    setTimeout(function () {
                        $celda.select();
                    }, 50);
                }
            }
        }

        $celda.css('border-bottom', '2px solid #3c8dbc');
    });

    // Escuchar el envío del formulario para validar datos
    $("#formulario_rubrica").on("submit", function (e) {
        var idParalelo = $("#id_paralelo_excel").val();
        var idAsignatura = $("#id_asignatura_excel").val();

        if (!idAsignatura || idAsignatura === "" || !idParalelo || idParalelo === "") {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor, seleccione un paralelo y una asignatura antes de generar el reporte.'
            });
            return false;
        }

        Swal.fire({
            icon: 'success',
            title: 'Generando Excel',
            text: 'Tu archivo se descargará en unos segundos...',
            timer: 3000,
            showConfirmButton: false
        });
    });

    // 🎯 VALIDACIÓN ESTRICTA PARA LA NOTA DE COMPORTAMIENTO (S, F, O, N)
    $('#lista_estudiantes_paralelo').on('input keyup paste', 'input[data-tipo-input="comportamiento"]', function (e) {
        var elemento = $(this);
        var valorOriginal = elemento.val().toUpperCase();
        var valorValidado = valorOriginal.replace(/[^SFON]/g, '');

        if (valorValidado.length > 1) {
            valorValidado = valorValidado.charAt(0);
        }

        elemento.val(valorValidado);

        switch (valorValidado) {
            case 'S': elemento.css('color', '#3c763d'); break; 
            case 'F': elemento.css('color', '#31708f'); break; 
            case 'O': elemento.css('color', '#8a6d3b'); break; 
            case 'N': elemento.css('color', '#a94442'); break; 
            default: elemento.css('color', '#0d47a1');        
        }
    });

    // =========================================================================
    // 🎯 EVENTO AUTOMÁTICO PARA GUARDAR COMPORTAMIENTO (INPUTS TEXT)
    // =========================================================================
    $(document).on('blur', 'input[data-tipo-input="comportamiento"]', function () {
        var $inputModificado = $(this);

        if ($inputModificado.is(':disabled')) {
            return false;
        }

        var valorCrudo = $inputModificado.val();
        var valorTexto = (valorCrudo !== null && valorCrudo !== undefined) ? valorCrudo.toString().trim().toUpperCase() : "-";
        var notaParaEnviar = "";

        if (valorTexto === "" || valorTexto === "-") {
            $inputModificado.val("-").css('color', '#333');
            notaParaEnviar = ""; 
        } else {
            var letrasValidas = ['S', 'F', 'O', 'N'];

            if (!letrasValidas.includes(valorTexto)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Letra no válida',
                    text: 'El comportamiento debe evaluarse con las letras: S, F, O, N.',
                    timer: 2500,
                    showConfirmButton: false
                });
                $inputModificado.val("-").css('color', '#333');
                return; 
            }

            $inputModificado.val(valorTexto);
            notaParaEnviar = valorTexto;

            var coloresComportamiento = {
                'S': '#2b542c', 'F': '#3c763d', 'O': '#245269', 'N': '#dd4b39'
            };
            $inputModificado.css('color', coloresComportamiento[valorTexto] || '#0d47a1');
        }

        $inputModificado.data('estudiante', $inputModificado.data('id-estudiante'));
        $inputModificado.data('aporte', $inputModificado.data('id-aporte'));
        $inputModificado.data('rubrica', 0); 
        $inputModificado.data('periodo', $inputModificado.closest('td').prev().find('input').data('periodo') || 0);

        ejecutarGuardadoAjax($inputModificado, notaParaEnviar);
    });

});

/**
 * Verifica si la institución permite copiar y pegar
 */
function verificarConfiguracionCopiarPegar() {
    $.ajax({
        url: "calificaciones/obtener_estado_copiar_y_pegar.php",
        type: "POST",
        dataType: "json"
    })
        .done(function (resp) {
            $("#in_copiar_y_pegar").val(resp.in_copiar_y_pegar);
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

            if (datosLimpios === "false") {
                alert("Error al cargar los datos de los paralelos.");
            }
            else if (datosLimpios.endsWith("SIN_REGISTROS")) {
                $cboParalelos.html('<option value="">Sin paralelos asignados</option>').prop('disabled', true);
                $listaEstudiantes
                    .removeClass("text-success")
                    .addClass("text-warning")
                    .html(
                        '<div class="alert alert-warning" style="margin: 10px 0; font-weight: normal;">' +
                        '<i class="fa fa-exclamation-triangle"></i> Usted no tiene paralelos ni asignaturas asignadas en este periodo lectivo.' +
                        '</div>'
                    );
            }
            else {
                $cboParalelos.html(datosLimpios).prop('disabled', false);
                $listaEstudiantes
                    .removeClass("text-success text-warning")
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
    var id_paralelo = $("#cboParalelos").val(); 
    var $cboAportes = $('#cboAportesEvaluacion');
    var $listaEstudiantes = $("#lista_estudiantes_paralelo");

    $cboAportes.empty(); 

    $.post("calificaciones/cargar_aportes_evaluacion_paralelo.php", {
        id_paralelo: id_paralelo
    })
        .done(function (resultado) {
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

function cargarAsignaturasDocente() {
    contarAsignaturasDocente();
}

function contarAsignaturasDocente() {
    var id_paralelo = $("#cboParalelos").val();

    Swal.fire({
        title: 'Cargando asignaturas...',
        text: 'Por favor, espere un momento.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

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

                    $("#num_asignaturas").html("Número de Asignaturas: " + total_registros);

                    if (total_registros > 0) {
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

function obtenerDetalleAsignaturas(id_paralelo) {
    $.post("calificaciones/listar_asignaturas_docente.php", {
        id_paralelo: id_paralelo
    })
        .done(function (resultadoDetalle) {
            Swal.close(); 

            if (typeof resultadoDetalle === "object" && resultadoDetalle !== null) {
                if (resultadoDetalle.error) {
                    limpiarPanelesPorError(resultadoDetalle.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del Servidor',
                        text: resultadoDetalle.error
                    });
                } else {
                    listarAsignaturasDocente(resultadoDetalle);
                }
            } else {
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

function listarAsignaturasDocente(listaAsignaturasJSON) {
    var $contenedor = $("#contenedor_asignaturas_docente");

    if (!Array.isArray(listaAsignaturasJSON) || listaAsignaturasJSON.length === 0) {
        $contenedor.html('<div class="text-center text-muted" style="width: 100%; padding: 10px;">No hay asignaturas disponibles</div>');
        return;
    }

    $contenedor.empty(); 

    listaAsignaturasJSON.forEach(function (item) {
        var id = item.id_asignatura || item.id || "0";
        var nombre = item.nombre_asignatura || item.nombre || item.asignatura || "Sin nombre";
        var tipoAsignatura = item.tipo_asignatura || item.id_tipo_asignatura || "1";

        // Renderizado adaptado en formato de tarjeta/botón horizontal alineado con tu CSS
        var htmlItem =
            '<div class="materia-btn-item" data-id="' + id + '" data-tipo="' + tipoAsignatura + '">' +
            '<i class="fa fa-book text-muted" style="margin-right: 6px;"></i> ' + nombre +
            ' <span class="badge" style="font-size: 9px; background-color: rgba(0,0,0,0.1); color: #666; margin-left: 5px;">ID: ' + id + '</span>' +
            '</div>';

        $contenedor.append(htmlItem);
    });
}

// =========================================================================
// 🎯 INTERCEPTOR REFACTORIZADO PARA EL NUEVO ENTORNO DE BOTONES HORIZONTALES
// =========================================================================
$(document).on("click", ".materia-btn-item", function (e) {
    e.preventDefault();

    $("#ver_reporte").hide();
    $("#save_all").hide(); 

    var $botonSeleccionado = $(this);

    // Reiniciar los estados de todos los botones horizontales superiores
    $(".materia-btn-item")
        .removeClass("active")
        .css({
            "background-color": "#f4f4f4",
            "color": "#444",
            "border-color": "#ddd"
        });

    // Destacar únicamente el botón seleccionado con el estilo primario de AdminLTE
    $botonSeleccionado
        .addClass("active")
        .css({
            "background-color": "#3c8dbc",
            "color": "#fff",
            "border-color": "#357ca5"
        });

    var idAsignaturaSeleccionada = $botonSeleccionado.data("id");
    var tipoAsignaturaSeleccionada = $botonSeleccionado.data("tipo");

    $("#id_asignatura").val(idAsignaturaSeleccionada);
    $("#ver_reporte").hide();

    // Mensaje de carga limpio adaptado a todo el ancho de la sábana extendida
    $("#lista_estudiantes_paralelo").html(
        '<div class="text-center text-muted" style="padding: 100px 20px;">' +
        '<i class="fa fa-refresh fa-spin fa-2x" style="color: #3c8dbc; margin-bottom: 10px;"></i>' +
        '<p class="small" style="margin-top: 10px;">Buscando nómina de estudiantes y calificaciones...</p>' +
        '</div>'
    );

    obtenerNominaEstudiantes(idAsignaturaSeleccionada, tipoAsignaturaSeleccionada);
});

function limpiarPanelesPorError(mensaje) {
    $("#contenedor_asignaturas_docente").html(
        '<div class="text-center text-muted" style="width: 100%; padding: 15px 0;">' +
        '<p class="small text-danger"><i class="fa fa-exclamation-triangle"></i> ' + mensaje + '</p>' +
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

function obtenerNominaEstudiantes(idAsignatura, tipoAsignatura) {
    var id_paralelo = $("#cboParalelos").val();

    Swal.fire({
        title: 'Cargando estudiantes...',
        text: 'Buscando registros en la base de datos.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $("#id_paralelo").val(id_paralelo);
    $("#id_asignatura").val(idAsignatura);

    $.post("calificaciones/cargar_estudiantes.php", {
        id_paralelo: id_paralelo,
        id_asignatura: idAsignatura,
        id_tipo_asignatura: tipoAsignatura 
    })
        .done(function (resultadoAlumnos) {
            Swal.close();

            var datosLimpios = resultadoAlumnos ? resultadoAlumnos.trim() : "";

            if (datosLimpios === "false" || datosLimpios === "") {
                $("#lbl_total_estudiantes").html("0");
                $("#lista_estudiantes_paralelo").html(
                    '<div class="text-center p-4 text-muted" style="padding: 20px;">' +
                    '<i class="fa fa-users"></i> No se encontraron estudiantes matriculados.' +
                    '</div>'
                );
                $("#ver_reporte").hide();
                $("#save_all").hide();
            } else {
                var tablaEnvoltorioConScroll = '<div id="tabla-sabana-scroll">' + resultadoAlumnos + '</div>';

                $("#lista_estudiantes_paralelo")
                    .removeClass("text-danger fw-bold")
                    .html(tablaEnvoltorioConScroll);

                $("#id_paralelo_excel").val(id_paralelo);
                $("#id_asignatura_excel").val(idAsignatura);

                $("#ver_reporte").show();

                var permiteCopiarPegar = $("#in_copiar_y_pegar").val();
                if (permiteCopiarPegar == "1" || permiteCopiarPegar === "1") {
                    $("#save_all").show();
                } else {
                    $("#save_all").hide();
                }

                ajustarColumnasSticky();

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

function ajustarColumnasSticky() {
    var primeraCelda = document.querySelector('#tabla-sabana-scroll table tbody tr td:nth-child(1)');
    if (!primeraCelda) return;

    var anchoReal = primeraCelda.getBoundingClientRect().width;
    var elementosColumnaDos = document.querySelectorAll(
        '#tabla-sabana-scroll table thead tr th:nth-child(2), #tabla-sabana-scroll table tbody tr td:nth-child(2)'
    );

    elementosColumnaDos.forEach(function (celda) {
        celda.style.setProperty('left', anchoReal + 'px', 'important');
    });
}

window.addEventListener('resize', function () {
    ajustarColumnasSticky();
});

function guardarTabla() {
    var loteCalificaciones = [];
    var $celdasModificadas = $("#tabla-sabana-scroll").find(".excel-cell.dirty-cell");

    $celdasModificadas.each(function () {
        var $input = $(this);
        var valorTexto = $input.val().trim();

        if (valorTexto === "" || valorTexto === "-") {
            loteCalificaciones.push({
                id_estudiante: $input.data("estudiante"),
                id_rubrica: $input.data("rubrica"),
                id_aporte: $input.data("aporte"),
                id_periodo: $input.data("periodo"),
                calificacion: ""
            });
        } else {
            var textoFiltrado = valorTexto.replace(/[^0-9.]/g, '');
            var notaNum = parseFloat(textoFiltrado);

            if (!isNaN(notaNum) && notaNum >= 0 && notaNum <= 10) {
                loteCalificaciones.push({
                    id_estudiante: $input.data("estudiante"),
                    id_rubrica: $input.data("rubrica"),
                    id_aporte: $input.data("aporte"),
                    id_periodo: $input.data("periodo"),
                    calificacion: notaNum.toFixed(2)
                });
            }
        }
    });

    if (loteCalificaciones.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin cambios',
            text: 'No se detectaron nuevas modificaciones en las calificaciones para almacenar.',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }

    Swal.fire({
        title: 'Guardando calificaciones...',
        text: 'Almacenando notas de forma masiva en el sistema. Espere un momento.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    var id_paralelo = $("#cboParalelos").val();
    var id_asignatura = $("#id_asignatura").val();

    $.ajax({
        url: "calificaciones/guardar_notas_masivo.php",
        type: "POST",
        dataType: "json",
        data: {
            id_paralelo: id_paralelo,
            id_asignatura: id_asignatura,
            notas: JSON.stringify(loteCalificaciones)
        },
        success: function (respuesta) {
            Swal.close();

            if (respuesta && respuesta.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: respuesta.message || 'Todas las calificaciones se guardaron correctamente.',
                    timer: 2500,
                    showConfirmButton: false
                });

                $celdasModificadas.removeClass("dirty-cell");
                $("#save_all").removeClass("btn-save-pulse");
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

function ejecutarGuardadoAjax($element, nota) {
    var idEstudiante = parseInt($element.data('estudiante') || $element.attr('data-id-estudiante') || $element.data('id-id-estudiante'), 10) || 0;
    var idRubrica = $element.data('rubrica') !== undefined ? parseInt($element.data('rubrica'), 10) : (parseInt($element.attr('data-rubrica'), 10) || 0);
    var idAporte = parseInt($element.data('aporte') || $element.attr('data-id-aporte') || $element.data('id-id-aporte'), 10) || 0;
    var idPeriodo = parseInt($element.data('periodo') || $element.attr('data-id-periodo') || $element.data('id-id-periodo'), 10) || 0;

    var tipoNota = $element.attr('data-tipo-input') === 'comportamiento' || $element.data('tipo-input') === 'comportamiento' ? 'comportamiento' : 'academica';
    if (tipoNota === 'comportamiento') { idRubrica = 0; }

    var $contenedorScroll = $("#tabla-sabana-scroll");

    if ($element.hasClass('moving-focus')) {
        $element.css('border-bottom', '2px solid #f39c12');
        $.ajax({
            url: "calificaciones/guardar_nota_individual.php",
            type: "POST",
            dataType: "json",
            data: { id_estudiante: idEstudiante, id_rubrica: idRubrica, id_aporte: idAporte, id_periodo: idPeriodo, id_asignatura: parseInt($("#id_asignatura").val(), 10) || 0, id_paralelo: $("#cboParalelos").val(), id_tipo_asignatura: $(".materia-btn-item.active").data("tipo") || "1", tipo_nota: tipoNota, calificacion: nota },
            success: function (respuesta) {
                if (respuesta && respuesta.status === "success") {
                    $element.css('border-bottom', '2px solid #00a65a');
                    setTimeout(function () { $element.css('border-bottom', 'none'); }, 1000);
                } else {
                    $element.css({ 'background-color': '#f2dede', 'border-bottom': '2px solid #dd4b39' });
                }
            },
            error: function () {
                $element.css({ 'background-color': '#f2dede', 'border-bottom': '2px solid #dd4b39' });
            }
        });
        return;
    }

    var posicionScrollTop = $contenedorScroll.length ? $contenedorScroll.scrollTop() : 0;
    var posicionScrollLeft = $contenedorScroll.length ? $contenedorScroll.scrollLeft() : 0;

    $element.css('border-bottom', '2px solid #f39c12');

    $.ajax({
        url: "calificaciones/guardar_nota_individual.php",
        type: "POST",
        dataType: "json",
        data: { id_estudiante: idEstudiante, id_rubrica: idRubrica, id_aporte: idAporte, id_periodo: idPeriodo, id_asignatura: parseInt($("#id_asignatura").val(), 10) || 0, id_paralelo: $("#cboParalelos").val(), id_tipo_asignatura: $(".materia-btn-item.active").data("tipo") || "1", tipo_nota: tipoNota, calificacion: nota },
        success: function (respuesta) {
            if (respuesta && respuesta.status === "success") {
                $element.css('border-bottom', '2px solid #00a65a');
                setTimeout(function () { $element.css('border-bottom', 'none'); }, 1000);

                if ($contenedorScroll.length) {
                    $contenedorScroll.scrollTop(posicionScrollTop);
                    $contenedorScroll.scrollLeft(posicionScrollLeft);
                }
            } else {
                $element.css({ 'background-color': '#f2dede', 'border-bottom': '2px solid #dd4b39' });
            }
        },
        error: function (jqXHR) {
            $element.css({ 'background-color': '#f2dede', 'border-bottom': '2px solid #dd4b39' });
            if ($contenedorScroll.length) { $contenedorScroll.scrollTop(posicionScrollTop); $contenedorScroll.scrollLeft(posicionScrollLeft); }
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            Toast.fire({ icon: 'error', title: 'Error de conexión: Calificación no guardada.' });
        }
    });
}
