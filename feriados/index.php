<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Feriados
        <small id="subtitulo">Definición</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 table-responsive">
                        <hr>
                        <table id="tbl_feriados" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nro.</th>
                                    <th>Id</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_feriados">
                                <!-- Aqui vamos a poblar los feriados mediante AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div id="titulo" class="panel-heading">Nuevo Feriado</div>
                        </div>
                        <div class="panel-body">
                            <form id="frm-feriado" action="" method="post">
                                <input type="hidden" name="id_feriado" id="id_feriado" value="0">
                                <div class="form-group">
                                    <label for="fe_fecha">Fecha:</label>
                                    <input type="date" name="fe_fecha" id="fe_fecha" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <button id="btn-save" type="submit" class="btn btn-success">Guardar</button>
                                    <button id="btn-cancel" type="button" class="btn btn-info">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>

<script>
$(document).ready(function(){
    //JQuery Listo para ser utilizado
    cargar_feriados();
    $('#tbody_feriados').on('click', '.item-edit', function(){
        var id_feriado = $(this).attr('data');
        $("#titulo").html("Editar Feriado");
        $("#btn-save").html("Actualizar");
        $.ajax({
            url: "feriados/getFeriado.php",
            method: "post",
            data: {
                id_feriado: id_feriado
            },
            dataType: "json",
            success: function(data){
                $("#id_feriado").val(id_feriado);
                $("#fe_fecha").val(data.fe_fecha);
            },
            error: function(jqXHR, textStatus){
                alert(jqXHR.responseText);
            }
        });
    });
    $('#tbody_feriados').on('click', '.item-delete', function(){
        var id_feriado = $(this).attr('data');
        swal({
            title: "¿Está seguro que quiere eliminar el registro?",
            text: "No podrá recuperar el registro que va a ser eliminado!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Sí, elimínelo!"
        },
        function(){
            $.ajax({
                url: "feriados/deleteFeriado.php",
                method: "post",
                data: {
                    id_feriado: id_feriado
                },
                dataType: "json",
                success: function(data){
                    swal({
                        title: data.titulo,
                        text: data.mensaje,
                        type: data.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });
                    cargar_feriados();
                    $("#frm-feriado")[0].reset();
                    $("#titulo").html("Nuevo Feriado");
                    $("#btn-save").html("Guardar");
                },
                error: function(jqXHR, textStatus){
                    alert(jqXHR.responseText);
                }
            });
        });
    });
    $("#frm-feriado").submit(function(e){
        e.preventDefault();
        var url;
        var id_feriado = $("#id_feriado").val();
        var fe_fecha = $.trim($("#fe_fecha").val());

        if(fe_fecha==""){
            swal("Ocurrió un error inesperado!", "Debe ingresar la fecha del dia feriado.", "error");
        }else{
            
            if($("#btn-save").html()=="Guardar")
                url = "feriados/storeFeriado.php";
            else if($("#btn-save").html()=="Actualizar")
                url = "feriados/updateFeriado.php";
                
            $.ajax({
                url: url,
                method: "post",
                data: {
                    id_feriado: id_feriado,
                    fe_fecha: fe_fecha
                },
                dataType: "json",
                success: function(response){

                    swal({
                        title: response.titulo,
                        text: response.mensaje,
                        type: response.tipo_mensaje,
                        confirmButtonText: 'Aceptar'
                    });

                    cargar_feriados();
                    
                    $("#frm-feriado")[0].reset();

                    if($("#btn-save").html()=="Actualizar"){
                        $("#btn-save").html("Guardar");
                        $("#titulo").html("Nuevo Feriado");
                    }

                },
                error: function(jqXHR, textStatus){
                    alert(jqXHR.responseText);
                }
            });
        }
    });
});
function cargar_feriados()
{
    //Aqui vamos a poblar los feriados que se hayan ingresado en la base de datos
    $.ajax({
        url: "feriados/cargar_feriados.php",
        method: "POST",
        success: function(response){
            $("#tbody_feriados").html(response);
        },
        error: function(xhr, status, error) {
            alert(xhr.responseText);
        }
    });
}
</script>