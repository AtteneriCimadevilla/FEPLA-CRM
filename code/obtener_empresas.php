<?php
require_once "conexion.php";

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

header('Content-Type: application/json');

$sql = "SELECT id, nombre_comercial FROM empresas ORDER BY nombre_comercial";
$result = $mysqli->query($sql);

if (!$result) {
    echo json_encode(["error" => "Error al obtener las empresas: " . $mysqli->error]);
    exit;
}

$empresas = [];
while ($row = $result->fetch_assoc()) {
    $empresas[] = $row;
}

echo json_encode($empresas);

$mysqli->close();
?>