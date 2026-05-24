<?php
function incrementarVersion($tipo = 'parche', $descripcion = '')
{
    $conn = new mysqli("localhost", "colegion_1", "AQSWDE123", "colegion_1");

    $res = $conn->query("SELECT version FROM versiones ORDER BY id DESC LIMIT 1");
    $row = $res->fetch_assoc();
    list($mayor, $menor, $parche) = explode('.', $row['version']);

    switch ($tipo) {
        case 'mayor':
            $mayor++;
            $menor = 0;
            $parche = 0;
            break;
        case 'menor':
            $menor++;
            $parche = 0;
            break;
        default:
            $parche++;
            break;
    }

    $nuevaVersion = "$mayor.$menor.$parche";
    $stmt = $conn->prepare("INSERT INTO versiones (version, descripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $nuevaVersion, $tipo . ": " . $descripcion == '' ? 'update' : $descripcion);
    $stmt->execute();

    return $nuevaVersion;
}

$descripcion = $_GET['descripcion'];
$tipo = $_GET['tipo'];

incrementarVersion($tipo, $descripcion);