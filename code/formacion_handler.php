<?php
require 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

try {
    // Validar datos requeridos
    $dni_nie = $_POST['dni_nie_alumno'] ?? null;
    $id_empresa = $_POST['id_empresa'] ?? null;
    $curso = $_POST['curso'] ?? null;
    $is_editing = isset($_POST['is_editing']) && $_POST['is_editing'] === '1';

    if (!$dni_nie || !$id_empresa || !$curso) {
        throw new Exception('Faltan datos requeridos');
    }

    // Iniciar transacción
    $mysqli->begin_transaction();

    if ($is_editing) {
        // Para edición, primero eliminamos la formación existente
        $delete_stmt = $mysqli->prepare("
            DELETE FROM formaciones 
            WHERE dni_nie_alumno = ? 
            AND curso = ?
        ");
        $delete_stmt->bind_param("ss", $dni_nie, $curso);
        $delete_stmt->execute();
        $delete_stmt->close();
    } else {
        // Para nueva formación, verificar que no exista una para este alumno en este curso
        $check_stmt = $mysqli->prepare("
            SELECT COUNT(*) 
            FROM formaciones 
            WHERE dni_nie_alumno = ? 
            AND curso = ?
        ");
        $check_stmt->bind_param("ss", $dni_nie, $curso);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            throw new Exception('El alumno ya tiene una formación asignada para este curso');
        }
    }

    // Insertar la nueva formación
    $insert_stmt = $mysqli->prepare("
        INSERT INTO formaciones (dni_nie_alumno, id_empresa, curso) 
        VALUES (?, ?, ?)
    ");
    $insert_stmt->bind_param("sis", $dni_nie, $id_empresa, $curso);
    
    if (!$insert_stmt->execute()) {
        throw new Exception('Error al guardar la formación: ' . $insert_stmt->error);
    }

    $insert_stmt->close();
    
    // Confirmar transacción
    $mysqli->commit();

    echo json_encode([
        'success' => true,
        'message' => $is_editing ? 'Formación actualizada correctamente' : 'Formación creada correctamente'
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($mysqli)) {
        $mysqli->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}