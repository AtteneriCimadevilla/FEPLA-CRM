<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni_nie = $_POST['dni_nie'];
    $empresa_id = $_POST['empresa'];
    $curso = $_POST['curso']; // Add this line to get the curso value

    // Update the formaciones table
    $stmt = $mysqli->prepare("UPDATE formaciones SET id_empresa = ?, curso = ? WHERE dni_nie_alumno = ?");
    $stmt->bind_param("iss", $empresa_id, $curso, $dni_nie);
    $result = $stmt->execute();

    if ($result) {
        // Get the new company name
        $stmt = $mysqli->prepare("SELECT nombre_comercial FROM empresas WHERE id = ?");
        $stmt->bind_param("i", $empresa_id);
        $stmt->execute();
        $stmt->bind_result($nombre_comercial);
        $stmt->fetch();

        echo json_encode(['success' => true, 'empresa' => $nombre_comercial]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $mysqli->close();
}