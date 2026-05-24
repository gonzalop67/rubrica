
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Comentarios
        <small id="subtitulo">Listado</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div id="cuadro2" class="col-sm-12 col-md-12 col-lg-12">
                        <form id="comment_form" class="form-horizontal" action="" method="POST">
                            <div class="form-group">
                                <h3 id="titulo_formulario" class="col-sm-offset-2 col-sm-8 text-center">					
                                Ingrese aqu&iacute; su comentario</h3>
                            </div>
                            <input type="hidden" id="id_estudiante" name="id_estudiante" value="<?php echo $id_estudiante; ?>">
                            <input type="hidden" name="comment_id" id="comment_id" value="0" />
                            <div class="form-group">
                                <label for="comment_content" class="col-sm-2 control-label">Mensaje:</label>
                                <div class="col-sm-8"><textarea id="comment_content" name="comment_content" class="form-control text-uppercase" maxlength="250"></textarea></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input id="" type="submit" class="btn btn-success" value="Enviar">
                                    <!--<input id="btn_cancelar" type="button" class="btn btn-primary" value="Cancelar">-->
                                </div>
                            </div>
                        </form>
                        <div class="col-sm-offset-2 col-sm-8">
                            <p id="mensaje" class="mensaje">Estimado(a) estudiante: Se pide un m&iacute;nimo de educaci&oacute;n en el lenguaje. Todos los comentarios tienen su autor.</p>
                        </div>
                    </div>
                </div>
                <span id="comment_message" class="message"></span>
                <br />
                <div id="display_comment"><!-- Aquí van los comentarios... --></div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    $(document).ready(function(){
        // JQuery Listo para utilizar
        //listar(); //función que llama al plugin DataTables
        load_comment();
        $("#comment_form").on("submit", function(e){
            e.preventDefault();
            var mensaje = $("#comment_content").val();
            if(mensaje.trim()==""){
                $(".message").html("Debe ingresar el texto de su comentario.").css({"color": "#C9302C" });
                $(".message").fadeOut(5000, function(){
                    $(this).html("");
                    $(this).fadeIn(3000);
                });
                return false;
            }
            var form_data = $(this).serialize();
            $.ajax({
                url:"add_comment.php",
                method:"POST",
                data:form_data,
                dataType:"JSON",
                success:function(data)
                {
                    if(data.error != '')
                    {
                        $('#comment_form')[0].reset();
                        $('#comment_message').html(data.error);
                        $('#comment_id').val('0');
                        load_comment();
                        $('#titulo_formulario').html("Ingrese aqu&iacute; su comentario");
                    }
                }
            });
        });
        $(document).on('click', '.reply', function(){
            var comment_id = $(this).attr("id");
            $('#comment_id').val(comment_id);
            $('#titulo_formulario').html("Ingrese aqu&iacute; su respuesta");
            $('#comment_content').focus();
        });
    });
    var mostrar_mensaje = function( informacion ){
        var texto = "", color = "";
        if( informacion.respuesta == "BIEN" ){
                texto = "<strong>Bien!</strong> " + informacion.mensaje;
                color = "#379911";
        }else if( informacion.respuesta == "ERROR"){
                texto = "<strong>Error</strong>, " + informacion.mensaje;
                color = "#C9302C";
        }else if( informacion.respuesta == "EXISTE" ){
                texto = "<strong>Información!</strong> el usuario ya existe.";
                color = "#5b94c5";
        }else if( informacion.respuesta == "VACIO" ){
                texto = "<strong>Advertencia!</strong> debe llenar todos los campos solicitados.";
                color = "#ddb11d";
        }else if( informacion.respuesta == "OPCION_VACIA" ){
                texto = "<strong>Advertencia!</strong> la opción no existe o esta vacia, recargar la página.";
                color = "#ddb11d";
        }
        $(".mensaje").html( texto ).css({"color": color });
        $(".mensaje").fadeOut(5000, function(){
            $(this).html("");
            $(this).fadeIn(3000);
        });	
    }
    /*var listar = function(){
        $('#tbl_comentarios').DataTable({
            "destroy": true,
            "ajax":{
                    "method":"POST",
                    "url":"comentarios/cargar_comentarios.php"
                },
            "columns":[
                {"data":"usuario"},
                {"data":"co_texto"},
                {"data":"fecha"}
            ],
            "order": [],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por pagina",
                "zeroRecords": "No se encontraron resultados en su busqueda",
                "searchPlaceholder": "Buscar registros",
                "info": "Mostrando registros de _START_ al _END_ de un total de  _TOTAL_ registros",
                "infoEmpty": "No existen registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
            }
        });
    }*/
    var agregar_nuevo_mensaje = function(){
        limpiar_datos();

    }
    var limpiar_datos = function(){
        $("#txt_comentario").html("").focus();
        $("#titulo_formulario").html("Ingrese aqu&iacute; su comentario");
    }
    function load_comment()
	{
		$.ajax({
			url:"fetch_comment.php",
			method:"POST",
			success:function(data)
			{
				$('#display_comment').html(data);
			}
		})
	}
</script>
