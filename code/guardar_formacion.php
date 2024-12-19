<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni_nie_alumno = $_POST['dni_nie_alumno'];
    $id_empresa = $_POST['id_empresa'];
    $curso = $_POST['curso'];
    $is_editing = $_POST['is_editing'] === '1';

    // Comprobar si el alumno ya tiene una formación asignada
    $stmt = $mysqli->prepare("SELECT id_empresa FROM formaciones WHERE dni_nie_alumno = ?");
    $stmt->bind_param("s", $dni_nie_alumno);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_formacion = $result->fetch_assoc();
    $stmt->close();

    if ($is_editing || $existing_formacion) {
        // Actualizar la formación existente
        $stmt = $mysqli->prepare("UPDATE formaciones SET id_empresa = ?, curso = ? WHERE dni_nie_alumno = ?");
        $stmt->bind_param("iss", $id_empresa, $curso, $dni_nie_alumno);
    } else {
        // Crear una nueva formación
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
        echo "<script>
                alert('Error al " . ($is_editing ? "actualizar" : "crear") . " la formación: " . $mysqli->error . "');
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
