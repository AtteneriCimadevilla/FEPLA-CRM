<?php
require_once "conexion.php";

$sql_students = "SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) AS nombre_completo FROM alumnos";
$result_students = $mysqli->query($sql_students);

$students = [];
while($row = $result_students->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$mysqli->close();
?>