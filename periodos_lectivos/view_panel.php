<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Encabezado de la página (Content Header) opcional si ya usas la estructura maestra -->
    <section class="content-header" style="padding: 15px;">
        <div class="row">
            <div class="col-sm-8 col-xs-12">
                <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #333;">
                    Estructura Académica Institucional
                    <small style="font-size: 13px; color: #777; display: block; margin-top: 5px;">Administración integral de los años lectivos, trimestres y parciales de evaluación.</small>
                </h1>
            </div>
        </div>
    </section>
    <!-- Contenido Principal -->
    <section class="content" style="padding: 15px;">
        <div class="row">
            <div class="col-xs-12">
                <!-- Contenedor Custom de Pestañas de AdminLTE 2 -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" id="academicTabs">
                        <li class="active">
                            <a href="#tab-lectivos" data-toggle="tab">
                                <i class="fa fa-calendar" style="margin-right: 5px;"></i> 1. Años Lectivos
                            </a>
                        </li>
                        <li>
                            <a href="#tab-academicos" data-toggle="tab">
                                <i class="fa fa-th-large" style="margin-right: 5px;"></i> 2. Trimestres / Bimestres
                            </a>
                        </li>
                        <li>
                            <a href="#tab-parciales" data-toggle="tab">
                                <i class="fa fa-cubes" style="margin-right: 5px;"></i> 3. Parciales y Bloques
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="academicTabsContent">
                        <!-- TAB 1: Años Lectivos -->
                        <div class="tab-pane active" id="tab-lectivos">
                            <!-- Filtros del Bloque -->
                            <div class="well well-sm" style="background-color: #fcfcfc; border: 1px solid #f4f4f4; margin-bottom: 15px; padding: 10px;">
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="form-group" style="margin-bottom: 0; display: inline-block; vertical-align: middle;">
                                            <label for="id_oferta_educativa" style="margin-right: 10px; color: #555;">Filtrar por Oferta Educativa:</label>
                                            <select class="form-control input-sm" id="id_oferta_educativa" style="display: inline-block; width: auto; background-color: #fff;">
                                                <option value="">-- Seleccione una Oferta Educativa --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 text-right">
                                        <button type="button" class="btn btn-primary btn-sm" id="btn-nuevo-lectivo" data-toggle="modal" data-target="#nuevoPeriodoLectivoModal">
                                            <i class="fa fa-plus" style="margin-right: 5px;"></i> Nuevo Año Lectivo
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" style="border: none; margin: 0;">
                                <table class="table table-hover table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f9f9f9;">
                                            <th style="width: 50px;">#</th> <!-- Nueva columna contador -->
                                            <th>Nombre del Ciclo</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-lectivos">
                                        <!-- Cargado dinámicamente con JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: Periodos Académicos -->
                        <div class="tab-pane" id="tab-academicos">
                            <!-- Filtros del Bloque -->
                            <div class="well well-sm" style="background-color: #fcfcfc; border: 1px solid #f4f4f4; margin-bottom: 15px; padding: 10px;">
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="form-group" style="margin-bottom: 0; display: inline-block; vertical-align: middle;">
                                            <label for="select-lectivos-filtro" style="margin-right: 10px; color: #555;">Filtrar por Año Lectivo:</label>
                                            <select class="form-control input-sm" id="select-lectivos-filtro" onchange="alCambiarLectivo(this.value)" style="display: inline-block; width: auto; background-color: #fff;">
                                                <option value="">-- Seleccione un Año Lectivo --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 text-right">
                                        <button type="button" class="btn btn-primary btn-sm" id="btn-nuevo-academico" data-toggle="modal" data-target="#modalAcademico" disabled>
                                            <i class="fa fa-plus" style="margin-right: 5px;"></i> Nuevo Bloque Académico
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" style="border: none; margin: 0;">
                                <table class="table table-hover table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f9f9f9;">
                                            <th style="width: 50px;">#</th> <!-- Nueva columna contador -->
                                            <th>Nombre del Bloque</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Orden Secuencial</th>
                                            <th>Rango de Fechas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-academicos">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted" style="padding: 20px; color: #999;">Selecciona un año lectivo en el filtro superior.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 3: Parciales de Evaluación -->
                        <div class="tab-pane" id="tab-parciales">
                            <!-- Filtros del Parcial -->
                            <div class="well well-sm" style="background-color: #fcfcfc; border: 1px solid #f4f4f4; margin-bottom: 15px; padding: 10px;">
                                <div class="row">
                                    <div class="col-sm-8 col-xs-12">
                                        <div class="form-group" style="margin-bottom: 0; display: inline-block; vertical-align: middle;">
                                            <label for="select-academicos-filtro" style="margin-right: 10px; color: #555;">Filtrar por Bloque Académico:</label>
                                            <select class="form-control input-sm" id="select-academicos-filtro" onchange="alCambiarAcademico(this.value)" style="display: inline-block; width: auto; background-color: #fff;">
                                                <option value="">-- Seleccione un Bloque Académico --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 text-right">
                                        <button type="button" class="btn btn-primary btn-sm" id="btn-nuevo-parcial" data-toggle="modal" data-target="#modalParcial" disabled>
                                            <i class="fa fa-plus" style="margin-right: 5px;"></i> Nuevo Parcial
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" style="border: none; margin: 0;">
                                <table class="table table-hover table-striped" style="margin-bottom: 0;">
                                    <thead>
                                        <tr style="background-color: #f9f9f9;">
                                            <th style="width: 50px;">#</th> <!-- Nueva columna contador -->
                                            <th>Descripción del Parcial</th>
                                            <th class="text-center">Peso Nota</th>
                                            <th>Rango Cronológico</th>
                                            <th>Cierre de Plataforma</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-parciales">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted" style="padding: 20px; color: #999;">Selecciona un bloque académico en el filtro superior.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include_once "modalInsert.php" ?>
<!-- End Content Wrapper -->

<script>
    $(document).ready(function() {
        // Inicialización del módulo 
        cargarOfertasEducativas();

        // CORREGIDO: Se envía vacío explícito para activar la limpieza inicial en cascada
        cargarAñosLectivosPrincipal('');

        // Evento para recargar la tabla de años lectivos al cambiar el filtro de Oferta Educativa 
        $("#id_oferta_educativa").change(function() {
            var ofertaId = $(this).val();
            cargarAñosLectivosPrincipal(ofertaId);
        });

        // Intercepta el evento de envío de manera correcta con jQuery
        $("#form_insert_periodo").on("submit", function(e) {
            e.preventDefault(); // Detiene la recarga automática de la página

            // 1. Capturar los valores de los inputs usando tus IDs reales del HTML
            var idOferta = $("#id_oferta_educativa").val(); // Viene de la interfaz general
            var nombre = $("#nombre").val();
            var anioInicio = $("#anio_inicio").val();
            var anioFin = $("#anio_fin").val();
            var fechaInicio = $("#fecha_inicio").val();
            var fechaFin = $("#fecha_fin").val();
            var notaMinima = $("#nota_minima").val();
            var notaAprobacion = $("#nota_aprobacion").val();
            var quienInsertaComp = $("#quien_inserta_comp_id").val();

            // 2. Validaciones básicas ajustadas a tu HTML (Removido 'estado' que no existe)
            if (!idOferta) {
                alert("Por favor, asegúrate de tener seleccionada una Oferta Educativa en la pestaña principal.");
                return false;
            }

            // El atributo 'required' de tu HTML ya ayuda, pero esto asegura la consistencia de textos
            if (!nombre || !nombre.trim() || !fechaInicio || !fechaFin || !anioInicio || !anioFin) {
                alert("Todos los campos obligatorios deben estar completos.");
                return false;
            }

            // 3. Petición AJAX por POST
            $.ajax({
                url: "periodos_lectivos/insertar_ano_lectivo.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_oferta: idOferta,
                    nombre: nombre,
                    anio_inicio: anioInicio,
                    anio_fin: anioFin,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    nota_minima: notaMinima,
                    nota_aprobacion: notaAprobacion,
                    quienInsertaComp: quienInsertaComp
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message || "Año lectivo registrado correctamente.");

                        // Cierra el modal de Bootstrap de forma limpia
                        $("#nuevoPeriodoLectivoModal").modal("hide");

                        // Limpia TODO el formulario de forma masiva y segura
                        $("#form_insert_periodo")[0].reset();

                        // REACTIVIDAD: Recarga los datos en la interfaz principal
                        if (typeof cargarAñosLectivosPrincipal === "function") {
                            cargarAñosLectivosPrincipal(idOferta);
                        }
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Te permite ver en la consola de desarrollador (F12) si el PHP falló o arrojó un error 500
                    console.error("Detalles del fallo en el servidor:", xhr.responseText, status, error);
                    alert("Ocurrió un error crítico en el servidor al intentar guardar.");
                }
            });
        });
    });

    // Carga las ofertas en el select del filtro
    function cargarOfertasEducativas() {
        $.ajax({
            url: "periodos_lectivos/cargar_ofertas_educativas.php",
            type: "GET",
            dataType: "html",
            success: function(data) {
                // Mantiene la opción por defecto y concatena los resultados
                $("#id_oferta_educativa").html('<option value="">-- Seleccione una Oferta Educativa --</option>' + data);
            },
            error: function(jqXHR) {
                console.error("Error al cargar ofertas:", jqXHR.responseText);
            }
        });
    }

    // Carga la tabla principal de años lectivos y llena en paralelo el filtro de la Pestaña 2
    function cargarAñosLectivosPrincipal(ofertaId = '') {
        // CORREGIDO: Asegúrate de usar el ID real del select en el HTML (aquí usamos el que tenías arriba)
        var selectLectivo = $("#select-lectivos-filtro");

        // Si NO se ha seleccionado ninguna oferta educativa (Valor vacío)
        if (ofertaId === '') {
            // Mensaje inicial en la tabla de la pestaña 1 (6 columnas)
            $("#tbody-lectivos").html('<tr><td colspan="6" class="text-center text-muted" style="padding: 20px;">Selecciona una oferta educativa en el filtro superior.</td></tr>');

            // Resetea y limpia el filtro select de la pestaña 2
            selectLectivo.html('<option value="">-- Seleccione un Año Lectivo --</option>').val("");

            // Lanza la cascada de limpieza hacia las pestañas 2 y 3
            alCambiarLectivo('');
            return;
        }

        // SI HAY UNA OFERTA SELECCIONADA: Procedemos con el AJAX
        $("#tbody-lectivos").html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Cargando...</td></tr>');

        $.ajax({
            url: "periodos_lectivos/listar_anos_lectivos.php",
            type: "GET",
            data: {
                id_oferta: ofertaId
            },
            dataType: "json",
            success: function(response) {
                var htmlTabla = "";
                var htmlSelectFiltro = '<option value="">-- Seleccione un Año Lectivo --</option>';

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        // Renderiza filas de la tabla de la Pestaña 1
                        htmlTabla += `<tr>
                            <td class="text-muted"><b>${index + 1}</b></td>
                            <td><b>${item.nombre}</b></td>
                            <td>${item.fecha_inicio}</td>
                            <td>${item.fecha_fin}</td>
                            <td><span class="label ${item.estado == 'Activo' ? 'label-success' : 'label-danger'}">${item.estado}</span></td>
                            <td>
                                <button class="btn btn-warning btn-xs" onclick="editarLectivo(${item.id})"><i class="fa fa-pencil"></i></button>
                            </td>
                        </tr>`;

                        // Renderiza opciones para el select de la Pestaña 2
                        htmlSelectFiltro += `<option value="${item.id}">${item.nombre}</option>`;
                    });
                } else {
                    // CORREGIDO: Se cambia colspan de 5 a 6 para evitar saltos en el diseño
                    htmlTabla = '<tr><td colspan="6" class="text-center text-muted">No se encontraron registros.</td></tr>';
                }

                $("#tbody-lectivos").html(htmlTabla);
                // Actualiza el filtro de la pestaña 2 de forma reactiva y asegura que esté limpio
                selectLectivo.html(htmlSelectFiltro).val("");

                // Forzamos la limpieza inicial de las pestañas de abajo hasta que el usuario elija un año lectivo
                alCambiarLectivo('');
            },
            error: function(jqXHR) {
                // CORREGIDO: Se cambia colspan de 5 a 6
                $("#tbody-lectivos").html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos.</td></tr>');
                alCambiarLectivo('');
            }
        });
    }

    // ==========================================
    // PESTAÑA 2: TRIMESTRES / BIMESTRES (BLOQUES)
    // ==========================================

    // Se ejecuta automáticamente al cambiar el select de la pestaña 2 o por limpieza de la pestaña 1
    function alCambiarLectivo(lectivoId) {
        var btnNuevo = $("#btn-nuevo-academico");
        var tbody = $("#tbody-academicos");
        var selectBloquesP3 = $("#select-academicos-filtro");

        // Si NO hay un año lectivo seleccionado (Valor vacío o por cascada)
        if (!lectivoId) {
            if (btnNuevo.length) btnNuevo.prop("disabled", true);

            // Mensaje inicial en la tabla de la pestaña 2 (6 columnas)
            tbody.html('<tr><td colspan="6" class="text-center text-muted" style="padding: 20px;">Selecciona un año lectivo en el filtro superior.</td></tr>');

            // Resetea y limpia el filtro select de la pestaña 3
            if (selectBloquesP3.length) selectBloquesP3.html('<option value="">-- Seleccione un Bloque Académico --</option>').val("");

            // Continúa el flujo de limpieza hacia la pestaña 3 si la función existe
            if (typeof alCambiarAcademico === "function") {
                alCambiarAcademico('');
            }
            return;
        }

        // SI HAY UN AÑO LECTIVO SELECCIONADO: Procedemos con el AJAX
        if (btnNuevo.length) btnNuevo.prop("disabled", false);
        tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Cargando bloques...</td></tr>');

        $.ajax({
            url: "periodos_lectivos/listar_bloques.php",
            type: "GET",
            data: {
                id_lectivo: lectivoId
            },
            dataType: "json",
            success: function(response) {
                var htmlTabla = "";
                var htmlSelectFiltroP3 = '<option value="">-- Seleccione un Bloque Académico --</option>';

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        htmlTabla += `<tr> 
                            <td class="text-muted"><b>${index + 1}</b></td> 
                            <td><b>${item.nombre}</b></td> 
                            <td><span class="badge bg-purple">${item.tipo}</span></td> 
                            <td class="text-center">${item.orden}</td> 
                            <td><i class="fa fa-calendar-o text-muted"></i> ${item.fecha_inicio} al ${item.fecha_fin}</td> 
                            <td> 
                                <button class="btn btn-warning btn-xs" onclick="editarBloque(${item.id})"><i class="fa fa-pencil"></i></button> 
                            </td> 
                        </tr>`;
                        htmlSelectFiltroP3 += `<option value="${item.id}">${item.nombre}</option>`;
                    });
                } else {
                    htmlTabla = '<tr><td colspan="6" class="text-center text-muted">No hay bloques configurados para este año lectivo.</td></tr>';
                }

                tbody.html(htmlTabla);
                // Actualiza el filtro de la pestaña 3 de forma reactiva y asegura que esté limpio
                if (selectBloquesP3.length) selectBloquesP3.html(htmlSelectFiltroP3).val("");

                if (typeof alCambiarAcademico === "function") {
                    alCambiarAcademico('');
                }
            },
            error: function() {
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Error al procesar la solicitud.</td></tr>');
                if (typeof alCambiarAcademico === "function") {
                    alCambiarAcademico('');
                }
            }
        });
    }

    // ==========================================
    // PESTAÑA 3: PARCIALES Y BLOQUES
    // ==========================================

    // Se ejecuta automáticamente al cambiar el select de la pestaña 3
    function alCambiarAcademico(bloqueId) {
        var btnNuevo = $("#btn-nuevo-parcial");
        var tbody = $("#tbody-parciales");

        if (!bloqueId) {
            btnNuevo.prop("disabled", true);
            tbody.html('<tr><td colspan="6" class="text-center text-muted" style="padding: 20px;">Selecciona un bloque académico en el filtro superior.</td></tr>');
            return;
        }

        btnNuevo.prop("disabled", false);
        tbody.html('<tr><td colspan="6" class="text-center"><i class="fa fa-refresh fa-spin"></i> Cargando parciales...</td></tr>');

        $.ajax({
            url: "periodos_lectivos/listar_parciales.php",
            type: "GET",
            data: {
                id_bloque: bloqueId
            },
            dataType: "json",
            success: function(response) {
                var htmlTabla = "";

                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        htmlTabla += `<tr>
                            <td class="text-muted"><b>${index + 1}</b></td>
                            <td><b>${item.descripcion}</b></td>
                            <td class="text-center"><span class="label label-info">${item.peso_nota}%</span></td>
                            <td>${item.fecha_inicio} al ${item.fecha_fin}</td>
                            <td><span class="text-danger"><i class="fa fa-clock-o"></i> ${item.fecha_cierre}</span></td>
                            <td>
                                <button class="btn btn-warning btn-xs" onclick="editarParcial(${item.id})"><i class="fa fa-pencil"></i></button>
                            </td>
                        </tr>`;
                    });
                } else {
                    // CORREGIDO: Se cambia colspan de 5 a 6
                    htmlTabla = '<tr><td colspan="6" class="text-center text-muted">No hay parciales configurados en este bloque.</td></tr>';
                }
                tbody.html(htmlTabla);
            },
            error: function() {
                // CORREGIDO: Se cambia colspan de 5 a 6
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Error al procesar la solicitud.</td></tr>');
            }
        });
    }

    // Funciones de apoyo para acciones (STUBS)

    function editarLectivo(id) {
        console.log("Editar Lectivo ID:", id);
    }

    function editarBloque(id) {
        console.log("Editar Bloque ID:", id);
    }

    function editarParcial(id) {
        console.log("Editar Parcial ID:", id);
    }
</script>