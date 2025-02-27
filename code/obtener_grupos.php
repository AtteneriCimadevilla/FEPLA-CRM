<?php
require 'conexion.php';

$ciclo = $_GET['ciclo'] ?? '';
$curso = $_GET['curso'] ?? '';

if ($ciclo && $curso) {
    $stmt = $mysqli->prepare("SELECT id_grupo, alias_grupo FROM grupos WHERE id_ciclo = ? AND curso = ?");
    $stmt->bind_param("is", $ciclo, $curso);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $grupos = [];
    while ($row = $result->fetch_assoc()) {
        $grupos[] = $row;
    }
    
    echo json_encode($grupos);
} else {
    echo json_encode([]);
}

$mysqli->close();
?>

