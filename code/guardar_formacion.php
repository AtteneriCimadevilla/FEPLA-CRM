<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni_nie_alumno = $_POST['dni_nie_alumno'];
    $id_empresa = $_POST['id_empresa'];
    $curso = $_POST['curso'];
    $is_editing = $_POST['is_editing'] === '1';

    try {
        if ($is_editing) {
            // Actualizar formación existente
            $stmt = $mysqli->prepare("UPDATE formaciones SET curso = ? WHERE dni_nie_alumno = ? AND id_empresa = ?");
            $stmt->bind_param("ssi", $curso, $dni_nie_alumno, $id_empresa);
        } else {
            // Verificar si ya existe una formación para este alumno en este curso
            $stmt = $mysqli->prepare("SELECT id_empresa FROM formaciones WHERE dni_nie_alumno = ? AND curso = ?");
            $stmt->bind_param("ss", $dni_nie_alumno, $curso);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception("El alumno ya tiene una formación asignada para este curso");
            }

            // Crear nueva formación
            $stmt = $mysqli->prepare("INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $dni_nie_alumno, $id_empresa, $curso);
        }

        if ($stmt->execute()) {
            echo "<script>
                    alert('Formación " . ($is_editing ? "actualizada" : "creada") . " con éxito');
                    window.opener.location.reload();
                    window.close();
                  </script>";
        } else {
            throw new Exception($mysqli->error);
        }

    } catch (Exception $e) {
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                history.back();
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('Método no permitido');
            window.close();
          </script>";
}

$mysqli->close();

