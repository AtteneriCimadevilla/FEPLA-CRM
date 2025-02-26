<?php
require_once "conexion.php";

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

header('Content-Type: application/json');

$grupo = isset($_GET['grupo']) ? $mysqli->real_escape_string($_GET['grupo']) : '';

if (empty($grupo)) {
    echo json_encode(["error" => "Grupo es requerido"]);
    exit;
}

$sql = "SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) AS nombre_completo 
        FROM alumnos WHERE id_grupo = ?";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en la preparación de la consulta: " . $mysqli->error]);
    exit;
}

$stmt->bind_param("i", $grupo);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Error al ejecutar la consulta: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

$alumnos = [];
while ($row = $result->fetch_assoc()) {
    $alumnos[] = $row;
}

echo json_encode($alumnos);

$stmt->close();
$mysqli->close();
?>