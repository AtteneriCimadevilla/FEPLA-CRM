<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni_nie = $mysqli->real_escape_string($_POST['register_dni']);
    $nombre = $mysqli->real_escape_string($_POST['register_nombre']);
    $apellido1 = $mysqli->real_escape_string($_POST['register_apellido1']);
    $apellido2 = $mysqli->real_escape_string($_POST['register_apellido2']);
    $email = $mysqli->real_escape_string($_POST['register_email']);
    $telefono = $mysqli->real_escape_string($_POST['register_telefono']);
    
    // Verificar si el email ya existe
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

    // Asumimos que todos los nuevos registros son de tipo 'user'
    $sql_tipo_usuario = "SELECT id_tipo_usuario FROM usuarios WHERE tipo = 'user'";
    $result = $mysqli->query($sql_tipo_usuario);
    $row = $result->fetch_assoc();
    $tipo_usuario = $row['id_tipo_usuario'];

    $sql = "INSERT INTO profesores (dni_nie, nombre, apellido1, apellido2, telefono, email, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssssssi", $dni_nie, $nombre, $apellido1, $apellido2, $telefono, $email, $tipo_usuario);
        
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
?>