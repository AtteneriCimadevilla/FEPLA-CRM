<?php
require 'conexion.php';

$grupo = $_GET['grupo'] ?? '';

if ($grupo) {
    $stmt = $mysqli->prepare("SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) as nombre_completo FROM alumnos WHERE id_grupo = ?");
    $stmt->bind_param("i", $grupo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $alumnos = [];
    while ($row = $result->fetch_assoc()) {
        $alumnos[] = $row;
    }
    
    echo json_encode($alumnos);
} else {
    echo json_encode([]);
}

$mysqli->close();
?>

