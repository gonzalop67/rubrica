<div class="content-wrapper">
    <input type="hidden" id="in_copiar_y_pegar">

    <section class="content-header">
        <h1>Ingreso de Calificaciones <small>Panel de Docentes</small></h1>
    </section>

    <section class="content">
        <!-- Fila Principal de Bootstrap 3 -->
        <div class="row">

            <!-- COLUMNA IZQUIERDA: Filtros y Estudiantes (8 de 12 espacios) -->
            <div class="col-xs-12 col-md-8">

                <!-- PANEL DE FILTROS: Corregido (Se eliminó la caja duplicada) -->
                <div class="box box-solid box-primary">
                    <div class="box-body" style="padding: 10px;">
                        <div id="barra_principal" class="well well-sm clearfix" style="background-color: #f9f9f9; border-radius: 3px; margin-bottom: 0; padding: 15px;">
                            <div class="row clearfix">
                                <!-- Selector de Paralelo al 100% de ancho -->
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

                            <!-- Campos ocultos de navegación internos -->
                            <input id="id_estudiante" type="hidden" />
                            <input id="id_rubrica_personalizada" type="hidden" />
                            <input id="numero_pagina" type="hidden" />
                            <input id="id_asignatura" type="hidden" />
                        </div>
                    </div>
                </div>

                <!-- NÓMINA DE ESTUDIANTES -->
                <div class="box box-solid box-success" id="pag_nomina_estudiantes">
                    <div class="box-body">

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

                        <form id="formulario_rubrica" action="php_excel/reporte_por_parcial_docente.php" method="post">
                            <div id="img_loader_estudiantes" class="text-center"></div>

                            <!-- 
                                CONTENEDOR GENERAL DE LA SÁBANA: El ID ahora tiene la propiedad 'table-responsive'. 
                                Asegúrate de que tu respuesta PHP traiga ÚNICAMENTE la etiqueta <table> pura 
                                para evitar barras de desplazamiento duplicadas.
                            -->
                            <div id="lista_estudiantes_paralelo" class="table-responsive" style="max-height: 540px; overflow: auto; border: 1px solid #d2d6de; background-color: #fff; border-radius: 3px;">
                                <div class="text-muted text-center p-3" style="padding: 30px; color: #999;">
                                    <i class="fa fa-arrow-right"></i> Seleccione una asignatura del panel derecho para cargar los alumnos...
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

            <!-- COLUMNA DERECHA: Lista de Asignaturas Compacta (4 de 12 espacios) -->
            <div class="col-xs-12 col-md-4">
                <div class="box box-solid box-default">
                    <div class="box-header with-border" style="background-color: #f4f4f4;">
                        <h3 class="box-title" style="font-size: 14px; font-weight: bold; color: #444;">
                            <i class="fa fa-book text-blue"></i> Asignaturas Asociadas
                        </h3>
                    </div>
                    <div class="box-body p-0">
                        <div class="p-2 bg-light text-center text-muted border-bottom" id="num_asignaturas" style="font-size: 12px; padding: 8px; border-bottom: 1px solid #eee;">
                            Número de Asignaturas: 0
                        </div>
                        <div id="pag_asignaturas" class="text-center" style="margin: 5px 0;"></div>

                        <div id="lista_asignaturas" class="list-group" style="max-height: 520px; overflow-y: auto; margin-bottom: 0; border-radius: 0;">
                            <div class="text-muted text-center p-3" style="padding: 20px; color: #999;">Seleccione un paralelo para cargar...</div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.row -->
    </section>
</div>

<style>
    /* 1. Forzar al contenedor gris a liberar cualquier alto fijo antiguo */
    #barra_principal {
        height: auto !important;
        /* Rompe cualquier height: 30px antiguo */
        max-height: none !important;
        /* Rompe límites de altura máximos */
        min-height: 50px !important;
        /* Le da una altura base saludable */
        display: block !important;
        /* Asegura comportamiento de bloque */
        clear: both !important;
        /* Refuerza el truco del clearfix */
    }

    /* 2. Asegurar que los selectores nativos ocupen todo el ancho de su columna */
    #barra_principal select.form-control {
        width: 100% !important;
        max-width: 100% !important;
        height: 34px !important;
        display: block !important;
        box-sizing: border-box !important;
    }

    /* 3. Estilos exclusivos para tus celdas de Calificaciones Estilo Excel */
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

    /* Clases utilitarias comunes */
    .text-center {
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .fw-bold {
        font-weight: bold;
    }

    .p-3 {
        padding: 3px;
    }
</style>

<script src="calificaciones/index.js"></script>