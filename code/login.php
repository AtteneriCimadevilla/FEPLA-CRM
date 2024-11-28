<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $mysqli->real_escape_string($_POST['login_user']);
    $password = $_POST['login_password'];
    
    $sql = "SELECT p.dni_nie, p.nombre, p.email, u.tipo FROM profesores p JOIN usuarios u ON p.tipo_usuario = u.id_tipo_usuario WHERE p.email = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $param_email);
        $param_email = $email;
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                // Aquí deberías verificar la contraseña, pero como no está en la base de datos,
                // vamos a permitir cualquier contraseña por ahora. En un sistema real, 
                // necesitarías almacenar y verificar contraseñas de forma segura.
                $_SESSION["loggedin"] = true;
                $_SESSION["dni_nie"] = $row['dni_nie'];
                $_SESSION["nombre"] = $row['nombre'];
                $_SESSION["email"] = $row['email'];
                $_SESSION["tipo_usuario"] = $row['tipo'];
                echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso"]);
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
?>