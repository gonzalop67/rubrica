<?php if (isset($_SESSION['mensaje'])) { ?>
    <div class="alert alert-<?= isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'danger' ?> d-flex align-items-center" role="alert">
        <i class="fa-solid fa-<?= isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'success' ? 'circle-check' : 'ban' ?> me-2 fs-4"></i>
        <div class="ml-2">
            <?php echo isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '' ?>
        </div>
    </div>
<?php } ?>

<?php if (isset($_SESSION['mensaje'])) unset($_SESSION['mensaje']) ?>
<?php if (isset($_SESSION['tipo'])) unset($_SESSION['tipo']) ?>
<?php if (isset($_SESSION['icono'])) unset($_SESSION['icono']) ?>