<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nota = $mysqli->real_escape_string($_POST['nota']);
    $fecha = $mysqli->real_escape_string($_POST['fecha']);
    $empresa_id = $mysqli->real_escape_string($_POST['empresa']);
    $alumno_dni = $mysqli->real_escape_string($_POST['alumno']);
    $profesor_dni = $_SESSION['dni_nie']; // Assuming you store the professor's DNI in the session

    $sql_insert = "INSERT INTO registro (fecha, id_empresa, dni_nie_alumno, texto_registro) VALUES (?, ?, ?, ?)";
    if ($stmt = $mysqli->prepare($sql_insert)) {
        $stmt->bind_param("siss", $fecha, $empresa_id, $alumno_dni, $nota);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Actividad registrada con éxito."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar la actividad: " . $mysqli->error]);
        }
        $stmt->close();
    }
}
$mysqli->close();
?>