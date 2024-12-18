<?php
require 'conexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dni_nie = $_POST['dni_nie'];
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$tipo_usuario = $_POST['tipo_usuario'];

// Validar campos obligatorios
if (empty($dni_nie) || empty($nombre) || empty($apellido1) || empty($telefono) || empty($email) || empty($tipo_usuario)) {
    echo "Error: Todos los campos son obligatorios.";
    exit();
}

// Comprobar si el profesor ya existe
$query = "SELECT dni_nie FROM profesores WHERE dni_nie = ?";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    echo "Error en la preparación de la consulta: " . $mysqli->error;
    exit();
}
$stmt->bind_param("s", $dni_nie);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Actualizar profesor existente
    $update_query = "UPDATE profesores SET nombre = ?, apellido1 = ?, apellido2 = ?, telefono = ?, email = ?, tipo_usuario = ? WHERE dni_nie = ?";
    $stmt = $mysqli->prepare($update_query);
    if (!$stmt) {
        echo "Error en la preparación de la consulta de actualización: " . $mysqli->error;
        exit();
    }
    $stmt->bind_param("sssssss", $nombre, $apellido1, $apellido2, $telefono, $email, $tipo_usuario, $dni_nie);
} else {
    // Insertar nuevo profesor
    $insert_query = "INSERT INTO profesores (dni_nie, nombre, apellido1, apellido2, telefono, email, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($insert_query);
    if (!$stmt) {
        echo "Error en la preparación de la consulta de inserción: " . $mysqli->error;
        exit();
    }
    $stmt->bind_param("sssssss", $dni_nie, $nombre, $apellido1, $apellido2, $telefono, $email, $tipo_usuario);
}

// Ejecutar la consulta
if (!$stmt->execute()) {
    echo "Error al guardar: " . $stmt->error;
} else {
    echo "Profesor guardado correctamente.";
}

$stmt->close();
$mysqli->close();
