<?php
// login.php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['login_user']);
    $password = $_POST['login_password'];
    
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        header("location: home.php");
                    } else {
                        $login_err = "Contraseña inválida.";
                    }
                }
            } else {
                $login_err = "Usuario no encontrado.";
            }
        } else {
            echo "Oops! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>