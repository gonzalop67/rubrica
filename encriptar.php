<?php
require_once "scripts/clases/class.encrypter.php";
$clave_generada = "";
if ($_POST) {
    $clave_generada = encrypter::encrypt($_POST["contrasenia"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encriptar</title>
</head>
<body>
    <form action="" method="post">
        <label>
            Clave:
            <input type="text" name="contrasenia" autofocus>
        </label>
        <br><br>
        <button type="submit">Enviar</button>
    </form>
    <br>
    <label>
        Clave generada:
        <input type="text" id="generated_key" value="<?php echo $clave_generada ?>" disabled>
    </label>
</body>
</html>