        <header class="main-header">
            <!-- Logo -->
            <a href="<?php echo RUTA_URL ?>/auth/dashboard" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>SIAE</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>SIAE <?php echo $datos['nombrePeriodoLectivo'] ?></b></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-comments-o"></i>
                                <span class="label label-warning">14</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"><?php echo "Tiene 14 comentarios" ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li>
                                            <!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="<?php echo RUTA_URL ?>/assets/images/student-male.png" class="img-circle" alt="User Image">
                                                </div>
                                                <h4>
                                                    <?php echo "VINUEZA MANOBANDA JENNY MARGARITA" ?>
                                                </h4>
                                                <p>
                                                    <?php
                                                    /* if (strlen($co_texto) <= 25) {
                                                    echo strtoupper($co_texto);
                                                } else {
                                                    echo substr(strtoupper($co_texto), 0, 24) . "...";
                                                } */
                                                    echo "LICEN CHARITO BUENAS NOC..."
                                                    ?>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- end message -->
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">Ver todos los comentarios</a></li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?php echo RUTA_URL ?>/assets/images/teacher-male-avatar.png" class="user-image" alt="User Image">
                                <span class="hidden-xs"><?php echo $datos['nombreUsuario'] ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><i class="fa fa-check-square-o" aria-hidden="true"></i> Cambiar Clave</a></li>
                                <li><a href="#"><i class="fa fa-comments-o" aria-hidden="true"></i> Comentarios</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?php echo RUTA_URL ?>/auth/logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Salir</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>