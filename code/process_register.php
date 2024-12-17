<?php
// process_register.php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni_nie = $_POST['dni_nie'];
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT * FROM profesores WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        header("Location: register.php?error=email_exists");
        exit();
    }

    // Insertar nuevo profesor
    $stmt = $pdo->prepare("INSERT INTO profesores (dni_nie, contrasenya, nombre, apellido1, apellido2, telefono, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$dni_nie, $password, $nombre, $apellido1, $apellido2, $telefono, $email])) {
        header("Location: login.php?registered=1");
        exit();
    } else {
        header("Location: register.php?error=registration_failed");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>