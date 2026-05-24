<div id="alert-message" class="alert alert-<?= isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'danger' ?> alert-dismissible" style="display:<?php echo isset($_SESSION['mensaje']) ? 'block' : 'none' ?>">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <p><i class="icon fa fa-<?= isset($_SESSION['icono']) ? $_SESSION['icono'] : 'ban' ?>"></i> <span id="mensaje"><?php echo isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '' ?></span></p>
</div>
<?php if (isset($_SESSION['mensaje'])) unset($_SESSION['mensaje']) ?>
<?php if (isset($_SESSION['tipo'])) unset($_SESSION['tipo']) ?>
<?php if (isset($_SESSION['icono'])) unset($_SESSION['icono']) ?>