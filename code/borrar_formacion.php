<?php
require 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    if (!isset($_POST['dni_nie']) || !isset($_POST['id_empresa']) || !isset($_POST['curso'])) {
        throw new Exception('Faltan parámetros requeridos');
    }

    $dni_nie = $_POST['dni_nie'];
    $id_empresa = $_POST['id_empresa'];
    $curso = $_POST['curso'];

    // Verificar si la formación existe
    $check_stmt = $mysqli->prepare("
        SELECT COUNT(*) 
        FROM formaciones 
        WHERE dni_nie_alumno = ? AND id_empresa = ? AND curso = ?
    ");
    
    $check_stmt->bind_param("sis", $dni_nie, $id_empresa, $curso);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count === 0) {
        throw new Exception('La formación no existe');
    }

    // Eliminar la formación
    $stmt = $mysqli->prepare("
        DELETE FROM formaciones 
        WHERE dni_nie_alumno = ? AND id_empresa = ? AND curso = ?
    ");
    
    $stmt->bind_param("sis", $dni_nie, $id_empresa, $curso);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al eliminar la formación: ' . $stmt->error);
    }

    $stmt->close();
    $mysqli->close();

    echo json_encode([
        'success' => true,
        'message' => 'Formación eliminada correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}