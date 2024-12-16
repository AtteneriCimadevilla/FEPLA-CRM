<?php
session_start();
require_once "conexion.php";

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

function logError($message)
{
    error_log(date('[Y-m-d H:i:s] ') . "Login error: " . $message . "\n", 3, "login_errors.log");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $mysqli->real_escape_string($_POST['login_user']);
    $password = $_POST['login_password'];

    logError("Attempting login for email: " . $email);

    $sql = "SELECT dni_nie, nombre, email, tipo_usuario, contrasenya FROM profesores WHERE email = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_email);
        $param_email = $email;

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['contrasenya'])) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["dni_nie"] = $row['dni_nie'];
                    $_SESSION["nombre"] = $row['nombre'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["tipo_usuario"] = $row['tipo_usuario'];
                    logError("Login successful for email: " . $email);
                    echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
                } else {
                    logError("Incorrect password for email: " . $email);
                    echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
                }
            } else {
                logError("Email not found: " . $email);
                echo json_encode(["status" => "error", "message" => "Email no encontrado."]);
            }
        } else {
            logError("Execute error: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Oops! Algo salió mal. Por favor, inténtalo de nuevo más tarde."]);
        }

        $stmt->close();
    } else {
        logError("Prepare error: " . $mysqli->error);
        echo json_encode(["status" => "error", "message" => "Error en la preparación de la consulta."]);
    }
} else {
    logError("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
    echo json_encode(["status" => "error", "message" => "Método de solicitud no válido."]);
}

$mysqli->close();
