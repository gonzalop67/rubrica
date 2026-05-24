<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <h2>Cambiar Paralelo</h2>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Seleccione el paralelo</p>
            <form id="form-cambiar-paralelo" action="" method="post">
                <div class="form-group has-feedback">
                    <select class="form-control" id="cboParalelosTutor" name="cboParalelosTutor">
                        <option value="">Seleccione...</option>
                        <?php
                            $paralelos = $db->consulta("
                            SELECT p.id_paralelo, 
                                   pa_nombre, 
                                   cu_shortname  
                              FROM sw_paralelo_tutor pt, 
                                   sw_paralelo p, 
                                   sw_curso c
                             WHERE p.id_paralelo = pt.id_paralelo 
                               AND c.id_curso = p.id_curso
                               AND id_usuario = $id_usuario 
                               AND pt.id_periodo_lectivo = $id_periodo_lectivo");
                            while($paralelo=$db->fetch_assoc($paralelos)) {
                        ?>
                            <option value="<?php echo $paralelo['id_paralelo']; ?>">
                                <?php echo $paralelo['cu_shortname'] . ' "' . $paralelo['pa_nombre'] . '"'; ?>
                            </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-xs-7"></div>
                    <!-- /.col -->
                    <div class="col-xs-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Seleccionar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <!-- /.login-box-body -->
    </div>
</body>

<script>
    $(document).ready(function(){
        $("#form-cambiar-paralelo").submit(function(event){
            event.preventDefault();
            var id_paralelo_tutor = $("#cboParalelosTutor").val();
            if (id_paralelo_tutor == 0) {
                alert("Debe seleccionar un paralelo...");
            } else {
                // Aqui consultamos el nuevo nombre de paralelo, cambiamos la variable de sesion 
                // $_SESSION['cambio_paralelo'] = 1; y seteamos $_SESSION['nombre_paralelo']
                $.post("tutores/obtener_nombre_paralelo.php",
					{
						id_paralelo: id_paralelo_tutor
					},
					function(resultado)
					{
						if(resultado == false)
						{
							alert("Error");
						}
						else
						{
							$("#nombre_paralelo").html(resultado);
                            $("#id_paralelo_tutor").val(id_paralelo_tutor);
                            alert("Paralelo cambiado exitosamente...");
						}
					}
				);
            }
        });
    });
</script>