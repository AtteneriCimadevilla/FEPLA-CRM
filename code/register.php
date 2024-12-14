<?php
require_once "conexion.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni_nie = $mysqli->real_escape_string($_POST['register_dni']);
    $nombre = $mysqli->real_escape_string($_POST['register_nombre']);
    $apellido1 = $mysqli->real_escape_string($_POST['register_apellido1']);
    $apellido2 = $mysqli->real_escape_string($_POST['register_apellido2']);
    $telefono = $mysqli->real_escape_string($_POST['register_telefono']);
    $email = $mysqli->real_escape_string($_POST['register_email']);
    $password = $_POST['register_password'];

    // Server-side validation
    $errors = [];

    // DNI/NIE validation
    if (!preg_match('/^[0-9XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/i', $dni_nie)) {
        $errors['dni_nie'] = 'DNI/NIE inválido';
    }

    // Name validation
    if (strlen(trim($nombre)) < 2) {
        $errors['nombre'] = 'El nombre debe tener al menos 2 caracteres';
    }

    // First surname validation
    if (strlen(trim($apellido1)) < 2) {
        $errors['apellido1'] = 'El primer apellido debe tener al menos 2 caracteres';
    }

    // Phone validation
    if (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errors['telefono'] = 'Número de teléfono inválido';
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Correo electrónico inválido';
    }

    // Password validation
    if (strlen($password) < 8) {
        $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "message" => "Errores de validación", "errors" => $errors]);
        exit();
    }

    // Check if email already exists
    $check_email = "SELECT * FROM profesores WHERE email = ?";
    if ($stmt = $mysqli->prepare($check_email)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Este email ya está registrado."]);
            exit();
        }
        $stmt->close();
    }

    // Hash the password
    $hashed_password = password_hash($_POST['register_password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO profesores (dni_nie, nombre, apellido1, apellido2, telefono, email, contrasenya, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("sssssss", $dni_nie, $nombre, $apellido1, $apellido2, $telefono, $email, $hashed_password);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Registro exitoso. Ahora puedes iniciar sesión."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error en el registro: " . $mysqli->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta: " . $mysqli->error]);
    }
}

$mysqli->close();
