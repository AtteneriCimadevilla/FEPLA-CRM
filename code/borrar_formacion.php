<?php
require 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['dni'])) {
    $dni_nie = $_GET['dni'];

    $stmt = $mysqli->prepare("DELETE FROM formaciones WHERE dni_nie_alumno = ?");
    $stmt->bind_param("s", $dni_nie);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido o DNI no proporcionado']);
}

$mysqli->close();