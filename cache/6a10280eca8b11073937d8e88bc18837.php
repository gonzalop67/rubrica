<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title><?php echo htmlspecialchars((string)($title ? $title . ' | SIAE 2026' : 'SIAE 2026'), ENT_QUOTES, "UTF-8"); ?></title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="<?= RUTA_URL ?>/public/img/favicon.ico">

    <!-- sweetalert 2 -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/public/assets/js/sweetalert2/sweetalert2.min.css">
    <script src="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/public/assets/js/sweetalert2/sweetalert2.min.js"></script>

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <?php echo $this->sections['styles'] ?? ""; ?>

    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/icons.css" rel="stylesheet" type="text/css">
    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/style.css" rel="stylesheet" type="text/css">

</head>

<body class="fixed-left">

    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner"></div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- ========== Left Sidebar Start ========== -->
        <?php echo $this->renderView('layout.sidebar', get_defined_vars()); ?>
        <!-- Left Sidebar End -->

        <script>
            const base_url = "<?php echo RUTA_URL; ?>";
        </script>

        <!-- Start right Content here -->

        <div class="content-page">
            <!-- Start content -->
            <div class="content">

                <!-- Top Bar Start -->
                <?php echo $this->renderView('layout.topbar', get_defined_vars()); ?>
                <!-- Top Bar End -->

                <div class="page-content-wrapper ">

                    <div class="container-fluid">

                        <?php echo $this->sections['content'] ?? ""; ?>

                    </div><!-- container -->

                </div> <!-- Page content Wrapper -->

            </div> <!-- content -->

            <footer class="footer">
                © 2018 Annex by Mannatthemes.
            </footer>

        </div>
        <!-- End Right content here -->

    </div>
    <!-- END wrapper -->


    <!-- jQuery  -->
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/jquery.min.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/popper.min.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/bootstrap.min.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/modernizr.min.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/detect.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/fastclick.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/jquery.slimscroll.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/jquery.blockUI.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/waves.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/jquery.nicescroll.js"></script>
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/jquery.scrollTo.min.js"></script>

    <!-- App js -->
    <script src="<?= RUTA_URL ?>/public/assets/Annex/js/app.js"></script>

    <?php echo $this->sections['scripts'] ?? ""; ?>

</body>

</html>
