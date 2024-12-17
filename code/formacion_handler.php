<?php
require 'conexion.php'; // Asegúrate de tener aquí tu archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos datos del formulario
    $dni_nie = $_POST['dni_nie'] ?? null;
    $id_empresa = $_POST['empresa'] ?? null;
    $curso = $_POST['curso'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$dni_nie) {
        echo "Error: DNI/NIE del alumno no proporcionado.";
        exit;
    }

    if ($action === 'save') {
        if (!$id_empresa) {
            echo "Error: Debes seleccionar una empresa para guardar la formación.";
            exit;
        }

        // Verificar si ya existe una formación para el alumno
        $queryCheck = "SELECT * FROM formaciones WHERE dni_nie_alumno = ?";
        $stmtCheck = $mysqli->prepare($queryCheck);
        $stmtCheck->bind_param('s', $dni_nie);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Actualizar asociación existente
            $queryUpdate = "UPDATE formaciones SET id_empresa = ?, curso = ? WHERE dni_nie_alumno = ?";
            $stmtUpdate = $mysqli->prepare($queryUpdate);
            $stmtUpdate->bind_param('iss', $id_empresa, $curso, $dni_nie);
            if ($stmtUpdate->execute()) {
                echo "Asociación actualizada con éxito.";
            } else {
                echo "Error al actualizar la asociación: " . $stmtUpdate->error;
            }
        } else {
            // Crear nueva asociación
            $queryInsert = "INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) VALUES (?, ?, ?)";
            $stmtInsert = $mysqli->prepare($queryInsert);
            $stmtInsert->bind_param('sis', $dni_nie, $id_empresa, $curso);
            if ($stmtInsert->execute()) {
                echo "Asociación creada con éxito.";
            } else {
                echo "Error al crear la asociación: " . $stmtInsert->error;
            }
        }
    } elseif ($action === 'delete') {
        // Eliminar asociación
        $queryDelete = "DELETE FROM formaciones WHERE dni_nie_alumno = ?";
        $stmtDelete = $mysqli->prepare($queryDelete);
        $stmtDelete->bind_param('s', $dni_nie);
        if ($stmtDelete->execute()) {
            echo "Asociación eliminada con éxito.";
        } else {
            echo "Error al eliminar la asociación: " . $stmtDelete->error;
        }
        $stmtDelete->close();
    } else {
        echo "Acción no válida.";
    }
    $mysqli->close();
    // Redirigir después de guardar la formación
    header("Location: alumnos.php");
    exit;
} else {
    echo "Método no permitido.";
}
