<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni_nie = $_POST['dni_nie'];
    $empresa_id = $_POST['empresa'];
    $curso = $_POST['curso'];

    if ($_POST['action'] == 'save') {
        // Insertar o actualizar la formación
        $stmt = $mysqli->prepare("
            INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE id_empresa = VALUES(id_empresa), curso = VALUES(curso)
        ");
        $stmt->bind_param("sis", $dni_nie, $empresa_id, $curso);
    } elseif ($_POST['action'] == 'delete') {
        // Eliminar la formación
        $stmt = $mysqli->prepare("DELETE FROM formaciones WHERE dni_nie_alumno = ?");
        $stmt->bind_param("s", $dni_nie);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Formación actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la formación.']);
    }

    $stmt->close();
    $mysqli->close();
}
?>