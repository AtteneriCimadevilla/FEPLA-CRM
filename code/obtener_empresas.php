<?php
require_once "conexion.php";

$sql_companies = "SELECT id, nombre_comercial FROM empresas";
$result_companies = $mysqli->query($sql_companies);

$companies = [];
while($row = $result_companies->fetch_assoc()) {
    $companies[] = $row;
}

echo json_encode($companies);

$mysqli->close();
?>