<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Login SIAE 2026</title>
    <meta content="Admin Dashboard" name="description" />
    <meta content="Mannatthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="<?= RUTA_URL ?>/public/img/favicon.ico">

    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/icons.css" rel="stylesheet" type="text/css">
    <link href="<?= RUTA_URL ?>/public/assets/Annex/css/style.css" rel="stylesheet" type="text/css">

</head>

<body class="fixed-left">

    <!-- Begin page -->
    <div class="accountbg"></div>
    <div class="wrapper-page">

        <div class="card">
            <div class="card-body">

                <h3 class="text-center mt-0 m-b-15">
                    S.I.A.E.
                </h3>

                <div class="p-3">
                    <form class="form-horizontal m-t-20" id="frmLogin" action="" method="POST">

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="text" name="usuario" id="usuario" required="" placeholder="Username">
                                <p id="error-usuario" class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="password" name="clave" id="clave" required="" placeholder="Password">
                                <p id="error-clave" class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <select class="form-control" name="perfil" id="perfil" required>
                                    <option value="" disabled selected>Selecciona tu rol</option>
                                    <?php foreach($perfiles as $perfil): ?>
                                    <option value="<?php echo htmlspecialchars((string)($perfil['id_perfil']), ENT_QUOTES, "UTF-8"); ?>"><?php echo htmlspecialchars((string)($perfil['pe_nombre']), ENT_QUOTES, "UTF-8"); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p id="error-perfil" class="invalid-feedback"></p>
                            </div>
                        </div>

                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <button class="btn btn-primary btn-block waves-effect waves-light"
                                    type="submit">Ingresar</button>
                            </div>
                        </div>

                        <div class="form-group text-center row">
                            <div id="img_loader" class="col-12" style="display: none;">
                                <img src="<?php echo htmlspecialchars((string)(RUTA_URL), ENT_QUOTES, "UTF-8"); ?>/public/img/ajax-loader-blue.GIF" alt="Procesando...">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div id="mensaje" class="col-12">
                                <!-- Aqui van los mensajes de error -->
                            </div>
                        </div>

                        <div class="form-group mb-0 row">
                            <div class="col-sm-7">
                                <a href="pages-recoverpw.html" class="text-muted"><i class="mdi mdi-lock"></i>
                                    <small>¿ Olvidaste tu contraseña ?</small></a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= RUTA_URL ?>/public/assets/js/pages/auth/login.js"></script>
</body>

</html>
