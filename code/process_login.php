<?php

//////////////////////////////////////////
// NO SÉ SI ESTE ARCHIVO SE ESTÁ USANDO //
//////////////////////////////////////////

// process_login.php
session_start();
require_once 'conexion.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT dni_nie, nombre, apellido1, tipo_usuario FROM profesores WHERE email = ? AND contrasenya = ?");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['dni_nie'];
            $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido1'];
            $_SESSION['user_type'] = $user['tipo_usuario'];
            header("Location: home.php");
            exit();
        } else {
            header("Location: login.php?error=1");
            exit();
        }
    } catch (PDOException $e) {
        // Log error and show a user-friendly message
        error_log("Error de base de datos: " . $e->getMessage());
        header("Location: login.php?error=2");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>