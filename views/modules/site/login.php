<?php
require_once("../../../app/Controllers/UsuariosController.php");
require_once("../../partials/routes.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $_ENV['TITLE_SITE'] ?> | Iniciar Sesión</title>
    <?php require("../../partials/head_imports.php"); ?>
</head>
<body  class="hold-transition login-page" >


<div class="login-box">
    <div class="login-logo">
        <a href="login.php"><b>PLOT</b>TER </a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Ingrese sus datos para iniciar sesión</p>
            <form action="../../../app/Controllers/UsuariosController.php?action=login" method="post">
                <div class="input-group mb-3">
                    <input type="text" id="user" name="user" class="form-control" placeholder="Usuario">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-success btn-block">Ingresar</button>
                    </div>
                    <!-- /.col -->
                </div>
                <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p>
                <br>
                <?php if (!empty($_GET['respuesta'])) { ?>
                    <?php if ( !empty($_GET['respuesta']) && $_GET['respuesta'] != "correcto" ) { ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error al Ingresar: </h5> <?= $_GET['mensaje'] ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </form>

        </div>

    </div>
</div>

<?php require('../../partials/scripts.php'); ?>

</body>
</html>