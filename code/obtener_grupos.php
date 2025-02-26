<?php
require_once "conexion.php";

header('Content-Type: application/json');

$ciclo = isset($_GET['ciclo']) ? $mysqli->real_escape_string($_GET['ciclo']) : '';
$curso = isset($_GET['curso']) ? $mysqli->real_escape_string($_GET['curso']) : '';

if (empty($ciclo) || empty($curso)) {
    echo json_encode(["error" => "Ciclo y curso son requeridos"]);
    exit;
}

$sql = "SELECT id_grupo, alias_grupo FROM grupos WHERE id_ciclo = ? AND curso = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en la preparación de la consulta: " . $mysqli->error]);
    exit;
}

$stmt->bind_param("is", $ciclo, $curso);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Error al ejecutar la consulta: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$grupos = [];
while ($row = $result->fetch_assoc()) {
    $grupos[] = $row;
}

echo json_encode($grupos);

$stmt->close();
$mysqli->close();
?>