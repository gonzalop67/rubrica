<div class="content-wrapper">
    <div id="asociarAsignaturaCursoApp" class="col-sm-12">
        <br>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Asociar Paralelos para Promoción Automática</h4>
            </div>
            <div class="panel-body">
                <form id="form_id" action="" class="app-form">
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo Actual:</label>
                        </div>
                        <div class="col-sm-10">
                            <select class="form-control fuente9" id="cboParalelosActuales">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje1"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 text-right">
                            <label class="control-label" style="position:relative; top:7px;">Paralelo Anterior:</label>
                        </div>
                        <div class="col-sm-10" style="margin-top: 2px;">
                            <select class="form-control fuente9" id="cboParalelosAnteriores">
                                <option value="0">Seleccione...</option>
                            </select>
                            <span class="help-desk error" id="mensaje2"></span>
                        </div>
                    </div>
                    <div class="row" id="botones_insercion">
                        <div class="col-sm-12" style="margin-top: 4px;">
                            <button id="btn-add-item" type="button" class="btn btn-block btn-primary" onclick="insertarAsociacion()">
                                Asociar
                            </button>
                        </div>
                    </div>
                </form>
                <!-- Línea de división -->
                <hr>
                <!-- message -->
                <div id="text_message" class="fuente9 text-center"></div>
                <!-- table -->
                <table class="table fuente9">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Paralelo Actual</th>
                            <th>Paralelo Anterior</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista_items">
                        <!-- Aqui desplegamos el contenido de la base de datos -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        cargar_paralelos_actuales();
        cargar_paralelos_anteriores();
        cargar_paralelos_promocion();
        $('#lista_items').on('click', '.item-delete', function() {
            var id = $(this).attr('data');
            
            Swal.fire({
                title: "¿Está seguro que quiere eliminar el registro?",
                text: "No podrá recuperar el registro que va a ser eliminado!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, elimínelo!",
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "post",
                        url: "promocion_paralelos/eliminar_asociacion.php",
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.tipo_mensaje == "success") {
                                Swal.fire({
                                    icon: response.tipo_mensaje,
                                    title: response.titulo,
                                    text: response.mensaje
                                });
                                $("#cboParalelosActuales").val("0");
                                $("#cboParalelosAnteriores").val("0");
                                cargar_paralelos_promocion();
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    });
                }
            });
        });
    });

    function cargar_paralelos_actuales() {
        $.get("scripts/cargar_paralelos.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboParalelosActuales').append(resultado);
            }
        });
    }

    function cargar_paralelos_anteriores() {
        $.get("scripts/cargar_paralelos_anteriores.php", function(resultado) {
            if (resultado == false) {
                alert("Error");
            } else {
                $('#cboParalelosAnteriores').append(resultado);
            }
        });
    }

    function cargar_paralelos_promocion() {
        $.get("promocion_paralelos/listar_paralelos_promocion.php",
            function(resultado) {
                if (resultado == false) {
                    alert("Error");
                } else {
                    $("#mensaje").html("");
                    $("#lista_items").html(resultado);
                }
            }
        );
    }

    function insertarAsociacion() {
        var id_paralelo_actual = document.getElementById("cboParalelosActuales").value;
        var id_paralelo_anterior = document.getElementById("cboParalelosAnteriores").value;
        var cont_errores = 0;

        if (id_paralelo_actual == 0) {
            $("#mensaje1").html("Debe seleccionar un paralelo...");
            $("#mensaje1").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje1").fadeOut();
        }

        if (id_paralelo_anterior == 0) {
            $("#mensaje2").html("Debe elegir un paralelo...");
            $("#mensaje2").fadeIn();
            cont_errores++;
        } else {
            $("#mensaje2").fadeOut();
        }

        if (cont_errores == 0) {
            $("#text_message").html("<img src='imagenes/ajax-loader.gif' alt='procesando...' />");
            $.ajax({
                type: "POST",
                url: "promocion_paralelos/insertar_asociacion.php",
                data: "id_paralelo_actual=" + id_paralelo_actual + "&id_paralelo_anterior=" + id_paralelo_anterior,
                dataType: "json",
                success: function(response) {
                    $("#text_message").html("");
                    Swal.fire({
                        title: response.titulo,
                        text: response.mensaje,
                        icon: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    cargar_paralelos_promocion();
                }
            });
        }
    }
</script>