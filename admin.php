<?php
session_start();
// Conexión con la base de datos
require_once("scripts/clases/class.mysql.php");
require_once("scripts/clases/class.periodos_lectivos.php");
require_once("scripts/clases/class.encrypter.php");

// Requerimiento del archivo de constantes
// require_once("App/config/config.php");

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
    // echo $qryString;
    $consulta = $db->consulta($qryString);
    $num_registros = $db->num_rows($consulta);
    $paralelo_tutor = $db->fetch_object($consulta);
    $id_paralelo_tutor = $paralelo_tutor->id_paralelo;
  }

  //Obtengo los nombres del usuario
  $consulta = $db->consulta("SELECT primer_apellido, 
									                  primer_nombre,
									                  us_foto 
								               FROM sw_usuario u,
                                    sw_persona p 
								              WHERE p.id_persona = u.persona_id  
                                AND u.id_usuario = $id_usuario");
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
$consulta = $db->consulta("SELECT oe.nombre FROM sw_ofertas_educativas oe, sw_periodo_lectivo p WHERE oe.id = p.oferta_educativa_id AND p.id_periodo_lectivo = $id_periodo_lectivo");
$oferta_educativa = $db->fetch_object($consulta);
$nombreOfertaEducativa = $oferta_educativa->nombre;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $titulo ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- 1. jQuery cargado al inicio de todo -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    
    <!-- 2. Hojas de Estilo Base -->
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/template/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="estilos.css" type="text/css" />
    
    <!-- 3. Componentes Adicionales (CSS) -->
    <link rel="stylesheet" href="assets/plugins/node_modules/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/template/select2/select2.min.css">
    <link rel="stylesheet" href="assets/template/toastr/toastr.min.css">
    <link rel="stylesheet" href="assets/datatables/datatables.css">

    <!-- 4. Plugins e Inicializadores Core (JS) -->
    <script src="assets/template/jquery-ui/jquery-ui.js"></script>
    <script src="assets/template/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/template/jquery-validation/localization/messages_es.min.js"></script>
    <script src="assets/plugins/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="assets/template/select2/select2.min.js"></script>
    <script src="assets/template/toastr/toastr.min.js"></script>
    <script src="assets/datatables/datatables.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/plotly-latest.min.js"></script>
    <script src="js/keypress.js"></script>
    
    <!-- NOTA: Quitamos js/funciones.js de aquí para cargarlo abajo en el flujo correcto -->

    <!-- HTML5 Shim and Respond.js IE8 support -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans Pro:300,400,600,700,300italic,400italic,600italic">
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
  <input type="hidden" id="id_perfil" value="<?php echo $id_perfil ?>">
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
            <!-- Busca esta línea en tu barra superior y déjala estructurada así: -->
            <li class="dropdown messages-menu" id="navbar-messages-container">
              <!-- El contenido PHP se cargará e inyectará de forma automática aquí -->
            </li>
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
                <li><a href="logout.php" class="btn-logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
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

          <!-- Dashboard -->
          <?php $dashboard_active = !isset($_GET['id_menu']) ? 'active' : ''; ?>
          <li class="<?php echo $dashboard_active ?>"><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&enlace=dashboard.php&nivel=0"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>

          <?php
          $menus = $db->consulta("SELECT m.* FROM sw_menu m, sw_menu_perfil mp WHERE m.id_menu = mp.id_menu AND mp.id_perfil = $id_perfil AND mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden");

          while ($menu = $db->fetch_assoc($menus)) {
            $submenus = $db->consulta("SELECT * FROM sw_menu WHERE mnu_publicado = 1 AND mnu_padre = " . $menu['id_menu'] . " ORDER BY mnu_orden");
            $num_submenus = $db->num_rows($submenus);

            if ($num_submenus > 0) {
              // 1. Almacenar submenús y verificar si el padre debe estar activo
              $submenu_list = [];
              $padre_active = '';

              while ($submenu = $db->fetch_assoc($submenus)) {
                if (isset($_GET['id_menu']) && $_GET['id_menu'] == $submenu['id_menu']) {
                  $submenu['is_active'] = 'active';
                  $padre_active = 'active'; // Si un hijo está activo, el padre también
                } else {
                  $submenu['is_active'] = '';
                }
                $submenu_list[] = $submenu;
              }

              $menu_icono = ($menu["mnu_icono"] != '') ? $menu["mnu_icono"] : 'fa fa-link';
          ?>
              <!-- Menú Padre con Submenús -->
              <li class="treeview <?php echo $padre_active ?>">
                <a href="#">
                  <i class="<?php echo $menu_icono ?>"></i> <span><?php echo $menu["mnu_texto"] ?></span>
                  <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
                </a>
                <ul class="treeview-menu">
                  <?php foreach ($submenu_list as $sub) { ?>
                    <li class="<?php echo $sub['is_active'] ?>">
                      <a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $sub["id_menu"] ?>&nivel=<?php echo $sub["mnu_nivel"] ?>"><i class="fa fa-circle-o"></i> <?php echo $sub["mnu_texto"] ?></a>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            <?php } else { ?>
              <!-- Menú Padre Simple Sin Submenús -->
              <?php $menu_active = (isset($_GET['id_menu']) && $_GET['id_menu'] == $menu['id_menu']) ? 'active' : ''; ?>
              <li class="<?php echo $menu_active ?>"><a href="admin.php?id_usuario=<?php echo encrypter::encrypt($id_usuario) ?>&id_perfil=<?php echo $id_perfil ?>&id_menu=<?php echo $menu["id_menu"] ?>&nivel=<?php echo $menu["mnu_nivel"] ?>"><i class="fa fa-link"></i> <span><?php echo $menu["mnu_texto"] ?></span></a></li>
          <?php }
          } ?>
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
      //$consulta = $db->consulta("SELECT version FROM sw_versiones ORDER BY id DESC LIMIT 1");
      //$version = $db->fetch_object($consulta);
      ?>
      <div class="pull-right hidden-xs">
        <b>Version</b> <span id="version"><?= isset($version->version) ? $version->version : "2.4.0" ?></span>
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

  <script>
    $(document).ready(function() {
      // 1. Carga inmediata apenas se abre el sistema
      load_navbar_messages();

      // 2. CORREGIDO: Ejecutar de forma automática cada 3 segundos (3000ms)
      /*setInterval(function() {
        load_navbar_messages();
      }, 3000);*/

      // 1. Guardar la URL del enlace clicado en localStorage
      $('.sidebar-menu').on('click', 'a', function() {
        var href = $(this).attr('href');

        // Evitar guardar enlaces vacíos o contenedores de submenús
        if (href && href !== '#') {
          localStorage.setItem('activeMenuUrl', href);
        }
      });

      // 2. Recuperar y aplicar el estado activo al cargar la página
      var savedUrl = localStorage.getItem('activeMenuUrl');

      if (savedUrl) {
        // Buscar el enlace exacto guardado
        var $activeLink = $('.sidebar-menu a[href="' + savedUrl + '"]');

        if ($activeLink.length) {
          // Remover estados activos previos para evitar duplicados
          $('.sidebar-menu .active').removeClass('active menu-open');
          $('.sidebar-menu .treeview-menu').hide();

          // Activar el li del submenú actual
          $activeLink.closest('li').addClass('active');

          // Activar el contenedor padre treeview, abrirlo y mostrar el submenú con slide o display
          var $parentTree = $activeLink.closest('.treeview');
          if ($parentTree.length) {
            $parentTree.addClass('active menu-open');
            $parentTree.children('.treeview-menu').show(); // AdminLTE requiere show() o slideDown()
          }
        }
      }

      // 3. NUEVO: Limpiar el localStorage al hacer clic en Cerrar Sesión
      // Asegúrate de poner la clase 'btn-logout' en tu enlace de salir
      $(document).on('click', '.btn-logout', function() {
        localStorage.removeItem('activeMenuUrl');
      });
    });

    // Refresca el menú desplegable de notificaciones superiores
    function load_navbar_messages() {
      var emisor_id = $("#id_usuario").val();
      var id_perfil = $("#id_perfil").val();

      if (!emisor_id) return;

      $.ajax({
        // RECOMENDACIÓN: Si tu proyecto está en una carpeta raíz, añade una barra diagonal inicial (ej: "/comentarios/...") 
        // o asegúrate de que la ruta relativa coincida con la ubicación de admin.php
        url: "comentarios/fetch_navbar_messages.php",
        method: "POST",
        data: {
          emisor_id: emisor_id,
          id_perfil: id_perfil
        },
        success: function(data) {
          // Reemplaza todo el HTML interno del elemento de la barra superior
          $('#navbar-messages-container').html(data);
        },
        error: function(xhr, status, error) {
          console.error("Error cargando notificaciones de la barra superior:", error);
        }
      });
    }
  </script>

  <!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>

</html>