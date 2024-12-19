<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni_nie = $_POST['dni_nie'];
    $empresa_id = $_POST['empresa'];

    // Update the formaciones table
    $stmt = $mysqli->prepare("UPDATE formaciones SET id_empresa = ? WHERE dni_nie_alumno = ?");
    $stmt->bind_param("is", $empresa_id, $dni_nie);
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
