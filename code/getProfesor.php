<?php
require 'conexion.php';

if (isset($_GET['dni_nie'])) {
    $dni_nie = $_GET['dni_nie'];
    $query = "SELECT dni_nie, nombre, apellido1, apellido2, telefono, email, tipo_usuario FROM profesores WHERE dni_nie = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $dni_nie);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "No se encontró el profesor."]);
    }

    $stmt->close();
}
$mysqli->close();
