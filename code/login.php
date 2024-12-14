<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $mysqli->real_escape_string($_POST['login_user']);
    $password = $_POST['login_password'];

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
                    echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Contraseña incorrecta."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Email no encontrado."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Oops! Algo salió mal. Por favor, inténtalo de nuevo más tarde."]);
        }

        $stmt->close();
    }
}

$mysqli->close();
