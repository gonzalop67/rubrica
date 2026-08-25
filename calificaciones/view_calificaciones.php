<div class="content-wrapper">
    <input type="hidden" id="in_copiar_y_pegar">

    <section class="content-header">
        <h1>Ingreso de Calificaciones <small>Panel de Docentes</small></h1>
    </section>

    <section class="content">

        <!-- PANEL DE FILTROS (Mantiene su estructura superior independiente) -->
        <div class="box box-solid box-primary" style="margin-bottom: 15px;">
            <div class="box-body" style="padding: 10px;">
                <div id="barra_principal" class="well well-sm clearfix" style="background-color: #f9f9f9; border-radius: 3px; margin-bottom: 0; padding: 15px;">
                    <div class="row clearfix">
                        <div class="col-xs-12">
                            <div class="form-group clearfix" style="margin-bottom: 0;">
                                <label for="cboParalelos" class="control-label" style="font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #333; display: block;">
                                    <i class="fa fa-users text-blue"></i> Seleccione el Paralelo para abrir la Sábana de Calificaciones:
                                </label>
                                <select id="cboParalelos" class="form-control" style="width: 100% !important; height: 34px;">
                                    <!-- Carga dinámica de paralelos -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <input id="id_estudiante" type="hidden" />
                    <input id="id_rubrica_personalizada" type="hidden" />
                    <input id="numero_pagina" type="hidden" />
                    <input id="id_asignatura" type="hidden" />
                </div>
            </div>
        </div>

        <!-- 🎯 CONTENEDOR PRINCIPAL FLEXBOX: Fuerza alturas idénticas y simétricas -->
        <div class="contenedor-flex-paneles">

            <!-- ⬅️ COLUMNA IZQUIERDA: Lista de Asignaturas (Ocupa el 32% del ancho flexible) -->
            <div class="columna-flex-materias">
                <div class="box box-solid box-default" style="margin-bottom: 0; height: 100%; display: flex; flex-direction: column;">
                    <div class="box-header with-border" style="background-color: #f4f4f4; flex-shrink: 0;">
                        <h3 class="box-title" style="font-size: 14px; font-weight: bold; color: #444;">
                            <i class="fa fa-book text-blue"></i> Asignaturas Asociadas
                        </h3>
                    </div>

                    <div class="box-body p-0 panel-materias-scroll">
                        <div class="p-2 bg-light text-center text-muted border-bottom" id="num_asignaturas" style="font-size: 12px; padding: 8px; border-bottom: 1px solid #eee;">
                            Número de Asignaturas: 0
                        </div>
                        <div id="pag_asignaturas" class="text-center" style="margin: 5px 0;"></div>

                        <div id="contenedor_asignaturas_docente" class="list-group" style="margin-bottom: 0; border-radius: 0;">
                            <div class="text-muted text-center" style="padding: 40px 20px; color: #999;">
                                <i class="fa fa-arrow-up fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                                <p style="margin: 0;">Seleccione un paralelo arriba para cargar...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ➡️ COLUMNA DERECHA: Estudiantes y Sábana de Notas (Ocupa el 68% del ancho flexible) -->
            <div class="columna-flex-estudiantes">
                <div class="box box-solid box-success" id="pag_nomina_estudiantes" style="margin-bottom: 0; height: 100%; display: flex; flex-direction: column;">

                    <div class="box-body panel-sabana-scroll">
                        <!-- Barra superior de resumen -->
                        <div id="total_registros_estudiantes" class="well well-sm" style="display: flex; justify-content: space-between; align-items: center; background-color: #f4f4f4; margin-bottom: 15px;">
                            <div id="num_estudiantes" style="font-weight: bold;">
                                Estudiantes: <span class="label label-primary" id="lbl_total_estudiantes">0</span>
                            </div>
                            <div id="btn-guardar">
                                <button type="button" id="save_all" class="btn btn-success btn-sm">
                                    <i class="fa fa-save"></i> Guardar Todo
                                </button>
                            </div>
                        </div>

                        <h3 class="box-title" style="font-size: 15px; font-weight: bold; border-bottom: 2px solid #00a65a; padding-bottom: 5px; margin-bottom: 15px;">
                            NÓMINA DE ESTUDIANTES
                        </h3>

                        <form id="formulario_rubrica" action="php_excel/reporte_por_parcial_docente.php" method="post" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <div id="img_loader_estudiantes" class="text-center"></div>

                            <!-- Contenedor interno de la tabla pura con scroll independiente -->
                            <div id="lista_estudiantes_paralelo" class="table-responsive" style="border: 1px solid #d2d6de; background-color: #fff; border-radius: 3px; flex-grow: 1; overflow: auto;">
                                <div class="text-muted text-center" style="padding: 50px 20px; color: #999;">
                                    <i class="fa fa-hand-o-left fa-2x" style="margin-bottom: 10px; color: #ccc;"></i>
                                    <p style="margin: 0;">Seleccione una asignatura del panel izquierdo para cargar los alumnos...</p>
                                </div>
                            </div>

                            <div id="ver_reporte" class="text-center" style="margin-top: 15px; display:none;">
                                <button type="submit" class="btn btn-primary btn-flat btn-sm">
                                    <i class="fa fa-file-excel-o"></i> Ver Reporte Excel
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div> <!-- /.contenedor-flex-paneles -->
    </section>
</div>

<style>
    /* =========================================================================
       🎯 REGLAS DE DISEÑO FLEXBOX ADAPTADAS (UNIFICADO)
       ========================================================================= */

    /* Contenedor que agrupa ambas columnas obligándolas a medir lo mismo */
    .contenedor-flex-paneles {
        display: flex !important;
        gap: 20px;
        align-items: stretch;
        /* Estira ambos bloques al mismo alto exacto */
        width: 100%;
    }

    /* Columna del Menú de Materias (Izquierda) */
    .columna-flex-materias {
        width: 32%;
        display: flex;
        flex-direction: column;
    }

    /* Columna de la Sábana de Notas (Derecha) */
    .columna-flex-estudiantes {
        width: 68%;
        display: flex;
        flex-direction: column;
    }

    /* 💻 CONTROL DE ALTURA CON MAX-HEIGHT EN BASE A LA PANTALLA (vh) */
    .panel-sabana-scroll {
        max-height: calc(100vh - 240px) !important;
        min-height: 450px !important;
        overflow: hidden !important;
        /* 🎯 CAMBIO CLAVE: Bloquea el scroll aquí para fijar el botón Guardar Todo */
        display: flex !important;
        flex-direction: column !important;
        flex-grow: 1 !important;
    }

    .panel-materias-scroll {
        max-height: calc(100vh - 240px) !important;
        min-height: 450px !important;
        overflow-y: auto !important;
        flex-grow: 1;
    }

    /* Reutilización del scroll moderno que tienes en tus horarios */
    .panel-materias-scroll::-webkit-scrollbar,
    #tabla-sabana-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .panel-materias-scroll::-webkit-scrollbar-track,
    #tabla-sabana-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .panel-materias-scroll::-webkit-scrollbar-thumb,
    #tabla-sabana-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    /* Reglas nativas para liberar filtros superiores */
    #barra_principal {
        height: auto !important;
        max-height: none !important;
        display: block !important;
        clear: both !important;
    }

    /* Estilos para tus celdas de Calificaciones Estilo Excel */
    .excel-cell {
        width: 100%;
        height: 34px;
        border: none;
        background: transparent;
        padding: 5px;
        outline: none;
        transition: all 0.15s ease-in-out;
    }

    .excel-cell:focus {
        background-color: #ffffff !important;
        box-shadow: inset 0 0 0 2px #217346 !important;
        color: #000000 !important;
    }

    .row-alumno-sabana:hover td {
        background-color: #f5f5f5 !important;
    }

    /* =========================================================================
       📌 ENCAPSULACIÓN DEL SCROLL ÚNICAMENTE EN LA SÁBANA DE LA TABLA
       ========================================================================= */

    /* 1. Liberamos el contenedor viejo de Bootstrap */
    #lista_estudiantes_paralelo {
        overflow: visible !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: column !important;
        flex-grow: 1 !important;
    }

    /* 2. 🎯 EL CONTENEDOR HIJO (ÚNICO DUEÑO DEL SCROLL): 
          Aquí se concentra el desplazamiento vertical y horizontal de los alumnos */
    #tabla-sabana-scroll {
        position: relative !important;
        overflow: auto !important;
        /* Único contenedor autorizado para tener barras de scroll */
        border: 1px solid #d2d6de;
        background-color: #fff;
        border-radius: 3px;
        flex-grow: 1 !important;
        /* Se estira de forma inteligente */
        margin-bottom: 5px;
        max-height: calc(100vh - 350px) !important;
        /* Altura calibrada al monitor */
        min-height: 320px !important;
    }

    /* 3. Forzar el acople de bordes sin bugs en navegadores */
    #tabla-sabana-scroll table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }

    /* 🎯 ALTURA FIJA ESTRICTA PARA LAS TRES FILAS DE LA CABECERA */
    #tabla-sabana-scroll table thead tr {
        height: 38px !important;
    }

    #tabla-sabana-scroll table thead th {
        height: 38px !important;
        vertical-align: middle !important;
        padding: 5px !important;
        box-sizing: border-box !important;
        background-clip: padding-box !important;
    }

    /* --- FILA 1: Se congela en el techo absoluto (0px) --- */
    #tabla-sabana-scroll table thead tr:nth-child(1) th {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 0px !important;
        z-index: 30 !important;
        background-color: #3c8dbc !important;
        color: #ffffff !important;
    }

    /* --- FILA 2: Se congela exactamente debajo de la primera (38px) --- */
    #tabla-sabana-scroll table thead tr:nth-child(2) th {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 38px !important;
        z-index: 29 !important;
        background-color: #f4f4f4 !important;
        color: #333333 !important;
        border-top: 1px solid #d2d6de !important;
    }

    /* --- FILA 3: Se congela exactamente debajo de la segunda (38px + 38px = 76px) --- */
    #tabla-sabana-scroll table thead tr:nth-child(3) th {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 76px !important;
        z-index: 28 !important;
        background-color: #fafafa !important;
        color: #666666 !important;
        border-top: 1px solid #d2d6de !important;
        border-bottom: 2px solid #2b669a !important;
    }

    /* 🎯 TRUCO DE REFORZAMIENTO PARA LAS COLUMNAS QUE ANTES TENÍAN ROWSPAN:
       Ocultamos los bordes intermedios para mantener la estética limpia */
    #tabla-sabana-scroll table thead tr:nth-child(2) th:nth-child(1),
    #tabla-sabana-scroll table thead tr:nth-child(2) th:nth-child(2),
    #tabla-sabana-scroll table thead tr:nth-child(3) th:nth-child(1),
    #tabla-sabana-scroll table thead tr:nth-child(3) th:nth-child(2) {
        border-top: none !important;
        border-bottom: none !important;
    }
</style>

<script src="calificaciones/index.js"></script>