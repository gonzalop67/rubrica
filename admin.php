<?php
session_start();
// Conexión con la base de datos
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.periodos_lectivos.php");
require_once("scripts/clases/class.encrypter.php");

function humanizarFecha($fecha)
{
  $fecha_dada = new DateTime($fecha);
  $ahora = new DateTime();
  $diferencia = $ahora->diff($fecha_dada);

  if ($diferencia->days == 0) {
    if ($diferencia->h == 0) {
      return "hace " . $diferencia->i . " minutos";
    }
    return "hace " . $diferencia->h . " horas";
  } elseif ($diferencia->days == 1) {
    return "ayer";
  } else {
    return $diferencia->days . " días atrás";
  }
}

if (!isset($_SESSION['usuario_logueado']))
  header("Location: index.php");
else {
  // Recepción de las variables GET
  $id_usuario = encrypter::decrypt($_GET['id_usuario']);
  $id_perfil = $_GET["id_perfil"];

  $db = new MySQL();

  //Obtengo el perfil del usuario logueado
  $consulta = $db->consulta("SELECT pe_nombre 
                               FROM sw_perfil
                              WHERE id_perfil = $id_perfil");
  $perfil = $db->fetch_assoc($consulta);
  $nombrePerfil = strtoupper($perfil["pe_nombre"]);
  $id_periodo_lectivo = $_SESSION["id_periodo_lectivo"];

  // Si el perfil es TUTOR se obtiene el id_paralelo correspondiente
  if ($nombrePerfil == "TUTOR") {
    $qryString = "SELECT p.id_paralelo, 
                         pa_nombre, 
                         cu_shortname  
                    FROM sw_paralelo_tutor pt, 
                         sw_paralelo p, 
                         sw_curso c
                   WHERE p.id_paralelo = pt.id_paralelo 
                     AND c.id_curso = p.id_curso
                     AND id_usuario = $id_usuario 
                     AND pt.id_periodo_lectivo = $id_periodo_lectivo";
    $consulta = $db->consulta($qryString);
    $num_registros = $db->num_rows($consulta);
    $paralelo_tutor = $db->fetch_object($consulta);
    $id_paralelo_tutor = $paralelo_tutor->id_paralelo;
  }

  //Obtengo los nombres del usuario
  $consulta = $db->consulta("SELECT SUBSTRING_INDEX(us_apellidos, ' ', 1) AS primer_apellido, 
									                  SUBSTRING_INDEX(us_nombres, ' ', 1) AS primer_nombre,
									                  us_foto 
								               FROM sw_usuario 
								              WHERE id_usuario = $id_usuario");
  $usuario = $db->fetch_assoc($consulta);
  $nombreUsuario = $usuario["primer_nombre"] . " " . $usuario["primer_apellido"];
  $userImage = "public/uploads/" . $usuario["us_foto"];

  if (isset($_GET["enlace"])) {
    $enlace = $_GET["enlace"];
    $titulo = $nombrePerfil;
  } else {
    if (isset($_GET["id_menu"])) {
      $consulta = $db->consulta("SELECT mnu_texto, 
											  mnu_enlace, 
											  mnu_nivel 
										 FROM sw_menu 
										WHERE mnu_publicado = 1 
										  AND id_menu = " . $_GET['id_menu']);
      $pagina = $db->fetch_assoc($consulta);
      $titulo = $pagina["mnu_texto"];
      $enlace = $pagina["mnu_enlace"];
      $nivel = $pagina["mnu_nivel"];
      $_SESSION['titulo_pagina'] = $titulo;
    } else {
      $titulo = $nombrePerfil;
      $enlace = "dashboard.php";
      $nivel = 0;
      $_SESSION['titulo_pagina'] = $titulo;
    }
  }

  $titulo = "SIAE Web | " . $titulo;
}

$periodos_lectivos = new periodos_lectivos();
$periodo_lectivo = $periodos_lectivos->obtenerPeriodoLectivo($id_periodo_lectivo);
$meses_abrev = array(0, "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
$fecha_inicial = explode("-", $periodo_lectivo->pe_fecha_inicio);
$fecha_final = explode("-", $periodo_lectivo->pe_fecha_fin);
$nombrePeriodoLectivo = $meses_abrev[(int)$fecha_inicial[1]] . " " . $fecha_inicial[0] . " - " . $meses_abrev[(int)$fecha_final[1]] . " " . $fecha_final[0];
$aniosPeriodoLectivo = $periodo_lectivo->pe_anio_inicio . " - " . $periodo_lectivo->pe_anio_fin;

//Obtengo el nombre de la oferta educativa asociada
$consulta = $db->consulta("SELECT nombre FROM sw_ofertas_educativas oe, sw_periodo_lectivo p WHERE oe.id = p.oferta_educativa_id AND p.id_periodo_lectivo = $id_periodo_lectivo");
$oferta_educativa = $db->fetch_object($consulta);
$nombreOfertaEducativa = $oferta_educativa->nombre;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $titulo ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- jquery-ui -->
  <link rel="stylesheet" href="assets/template/jquery-ui/jquery-ui.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">

  <link href="estilos.css" rel="stylesheet" type="text/css" />

  <!-- jquery-ui -->
  <script src="assets/template/jquery-ui/jquery-ui.js"></script>
  <!-- jquery-ui-validation -->
  <script src="assets/template/jquery-validation/jquery.validate.min.js"></script>
  <script src="assets/template/jquery-validation/localization/messages_es.min.js"></script>
  <!-- sweetalert 2 -->
  <link rel="stylesheet" href="assets/plugins/node_modules/sweetalert2/dist/sweetalert2.min.css">
  <script src="assets/plugins/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
  <!-- Chart JS -->
  <script src="js/chart.min.js"></script>
  <!-- plotly -->
  <script src="js/plotly-latest.min.js"></script>
  <!-- Select2 -->
  <script src="assets/template/select2/select2.min.js"></script>
  <link rel="stylesheet" href="assets/template/select2/select2.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="assets/template/toastr/toastr.min.css">
  <script src="assets/template/toastr/toastr.min.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="assets/datatables/datatables.css">
  <script src="assets/datatables/datatables.js"></script>

  <script src="js/keypress.js"></script>
  <script src="js/funciones.js"></script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

<body class="hold-transition skin-blue sidebar-mini">
  <input type="hidden" id="id_periodo_lectivo" value="<?php echo $id_periodo_lectivo ?>">
  <input type="hidden" id="id_usuario" value="<?php echo $id_usuario ?>">
  <?php
  if ($nombrePerfil == "TUTOR") {
  ?>
    <input type="hidden" id="id_paralelo_tutor" value="<?php echo $id_paralelo_tutor; ?>">
  <?php } ?>
  <div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

      <!-- Logo -->
      <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=dashboard.php&nivel=0" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>SIAE</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>SIAE <?php echo $aniosPeriodoLectivo ?></b></span>
      </a>

      <!-- Header Navbar -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- Messages: style can be found in dropdown.less-->
            <?php
            $mensajes = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_mensajes WHERE receptor_id = $id_usuario");
            $num_mensajes = $db->fetch_object($mensajes)->num_rows;
            $terminacion = $num_mensajes == 1 ? '' : 's';
            ?>
            <li class="dropdown messages-menu">
              <!-- Menu toggle button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo $num_mensajes . ' mensaje' . $terminacion; ?>">
                <i class="fa fa-comments-o"></i>
                <span class="label label-danger"><?php echo $num_mensajes ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="header"><?php echo "Tienes " . $num_mensajes . " mensaje" . $terminacion ?></li>
                <li>
                  <?php
                  $sql = "SELECT us_foto, perfil_emisor, fecha, mensaje FROM sw_usuario u, sw_mensajes m WHERE u.id_usuario = m.emisor_id AND receptor_id = $id_usuario";
                  $mensajes = $db->consulta($sql);
                  ?>
                  <!-- inner menu: contains the messages -->
                  <ul class="menu">
                    <?php
                    while ($row = $db->fetch_object($mensajes)) {
                    ?>
                      <li><!-- start message -->
                        <a href="#">
                          <div class="pull-left">
                            <!-- User Image -->
                            <img src="<?php echo "public/uploads/" . $row->us_foto ?>" class="img-circle" alt="User Image">
                          </div>
                          <!-- Message title and timestamp -->
                          <h4>
                            <?php echo $row->perfil_emisor ?>
                            <small><i class="fa fa-clock-o"></i> <?php echo humanizarFecha($row->fecha) ?></small>
                          </h4>
                          <!-- The message -->
                          <p>
                            <?php
                            if (strlen($row->mensaje) > 32) {
                              echo mb_substr($texto, 0, 32) . "...";
                            } else {
                              echo $row->mensaje;
                            }
                            ?>
                          </p>
                        </a>
                      </li>
                      <!-- end message -->
                    <?php } ?>
                  </ul>
                  <!-- /.menu -->
                </li>
                <li class="footer"><a href="#">Ver todos los mensajes</a></li>
              </ul>
            </li>
            <!-- /.messages-menu -->

            <!-- Notifications Menu -->
            <li class="dropdown notifications-menu">
              <!-- Menu toggle button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <span class="label label-warning">10</span>
              </a>
              <ul class="dropdown-menu">
                <li class="header">You have 10 notifications</li>
                <li>
                  <!-- Inner Menu: contains the notifications -->
                  <ul class="menu">
                    <li><!-- start notification -->
                      <a href="#">
                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                      </a>
                    </li>
                    <!-- end notification -->
                  </ul>
                </li>
                <li class="footer"><a href="#">View all</a></li>
              </ul>
            </li>
            <?php
            if ($nombrePerfil == "ADMINISTRADOR") {
            ?>
              <?php
              $tareas = $db->consulta("SELECT COUNT(*) AS num_rows FROM sw_tarea WHERE hecho = 0");
              $num_tareas = $db->fetch_object($tareas)->num_rows;
              $terminacion = $num_tareas == 1 ? '' : 's';
              ?>
              <!-- Tasks Menu -->
              <li class="dropdown tasks-menu">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-danger"><?php echo $num_tareas ?></span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header"><?php echo "Tienes " . $num_tareas . " tarea" . $terminacion . " pendientes" ?></li>
                  <li>
                    <!-- Inner menu: contains the tasks -->
                    <ul class="menu">
                      <li><!-- Task item -->
                        <a href="#">
                          <!-- Task title and progress text -->
                          <h3>
                            Design some buttons
                            <small class="pull-right">20%</small>
                          </h3>
                          <!-- The progress bar -->
                          <div class="progress xs">
                            <!-- Change the css width attribute to simulate progress -->
                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                              aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">20% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li>
                      <!-- end task item -->
                    </ul>
                  </li>
                  <li class="footer">
                    <a href="#">View all tasks</a>
                  </li>
                </ul>
              </li>
            <?php } ?>
            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="<?php echo $userImage ?>" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs"><?php echo $nombreUsuario ?></span>
                <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">
                <li><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=editar_perfil_usuario.php&nivel=0"><i class="fa fa-cogs" aria-hidden="true"></i> Editar Perfil de Usuario</a></li>
                <li><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=comentarios.php&nivel=0"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
                <li>
                  <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=calificaciones/view_cambiar_periodo_lectivo.php&nivel=0"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Cambiar Periodo</a>
                </li>
                <li role="separator" class="divider"></li>
                <li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
              </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            <!-- <li>
              <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li> -->
          </ul>
        </div>
      </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="<?php echo $userImage ?>" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?php echo $nombrePerfil ?></p>
            <!-- Status -->
            <!-- <a href="#"><i class="fa fa-circle text-success"></i> Online</a> -->
            <!-- Modalidad -->
            <small><?php echo $nombreOfertaEducativa ?></small>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
          <li class="header" style="font-size: 1.2rem; text-align: center; color: white; padding-top: 4px"><?= $nombrePeriodoLectivo ?></li>
          <!-- Optionally, you can add icons to the links -->
          <?php $active = !isset($_GET['id_menu']) ? 'active' : '' ?>
          <li class="<?php echo $active ?>"><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=dashboard.php&nivel=0"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
          <?php
          $menus = $db->consulta("SELECT m.* FROM sw_menu m, sw_menu_perfil mp WHERE m.id_menu = mp.id_menu AND mp.id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");
          while ($menu = $db->fetch_assoc($menus)) {
            $submenus = $db->consulta("SELECT * FROM sw_menu WHERE mnu_publicado = 1 AND mnu_padre = " . $menu['id_menu'] . " ORDER BY mnu_orden");
            $num_submenus = $db->num_rows($submenus);
            if ($num_submenus > 0) {
          ?>
              <li class="treeview">
                <?php
                $menu_icono = 'fa fa-link';
                if ($menu["mnu_icono"] != '') {
                  $menu_icono = $menu["mnu_icono"];
                }
                ?>
                <a href="#"><i class="<?php echo $menu_icono ?>"></i> <span><?php echo $menu["mnu_texto"] ?></span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
                  <?php
                  while ($submenu = $db->fetch_assoc($submenus)) {
                  ?>
                    <?php
                    if (isset($_GET['id_menu']) && $_GET['id_menu'] == $submenu['id_menu']) {
                      $active = 'active';
                    } else {
                      $active = '';
                    }
                    ?>
                    <li class="<?php echo $active ?>">
                      <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $submenu["id_menu"] ?>&nivel=<?php echo $submenu["mnu_nivel"] ?>"><i class="fa fa-circle-o"></i> <?php echo $submenu["mnu_texto"] ?></a>
                    </li>
                  <?php
                  }
                  ?>
                </ul>
              </li>
            <?php
            } else {
            ?>
              <?php
              if (isset($_GET['id_menu']) && $_GET['id_menu'] == $menu['id_menu']) {
                $active = 'active';
              } else {
                $active = '';
              }
              ?>
              <li class="<?php echo $active ?>"><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $menu["id_menu"] ?>&nivel=<?php echo $menu["mnu_nivel"] ?>"><i class="fa fa-link"></i> <span><?php echo $menu["mnu_texto"] ?></span></a></li>
          <?php
            }
          }
          ?>
        </ul>
        <!-- /.sidebar-menu -->
      </section>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <?php include($enlace); ?>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <?php
      $consulta = $db->consulta("SELECT version FROM sw_versiones ORDER BY id DESC LIMIT 1");
      $version = $db->fetch_object($consulta);
      ?>
      <div class="pull-right hidden-xs">
        <b>Version</b> <span id="version"><?= $version->version ?></span>
      </div>
      <?php
      $consulta = $db->consulta("SELECT in_nombre,
											                  in_url
										               FROM sw_institucion
										              WHERE id_institucion = 1");
      $result = $db->fetch_object($consulta);
      $nom_institucion = $result->in_nombre;
      $url = $result->in_url;
      ?>
      <strong><?php echo date("Y"); ?> &copy; <a href="<?php echo $url; ?>" target="_blank"><?php echo $nom_institucion; ?></a>.</strong> Todos los derechos reservados.
    </footer>

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 3 -->
  <!-- <script src="bower_components/jquery/dist/jquery.min.js"></script> -->
  <!-- Bootstrap 3.3.7 -->
  <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <!-- <script src="dist/js/scripts.js"></script> -->

  <!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>

</html>