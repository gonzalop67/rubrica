<?php
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

// Variables POST
$emisor_id = $_POST["emisor_id"];
$receptor_id = $_POST["receptor_id"];

$query = "SELECT * 
            FROM sw_mensajes 
	       WHERE emisor_id = $emisor_id  
	         AND receptor_id = $receptor_id  
	       ORDER BY fecha DESC";

echo $query;
