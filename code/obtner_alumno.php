<?php
require_once "conexion.php";

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$sql_students = "SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) AS nombre_completo FROM alumnos";
$result_students = $mysqli->query($sql_students);

if (!$result_students) {
    die("Error en la consulta: " . $mysqli->error);
}

$students = [];
while($row = $result_students->fetch_assoc()) {
    $students[] = $row;
}

// Imprimir el número de estudiantes encontrados
echo "<!-- Número de estudiantes encontrados: " . count($students) . " -->\n";

header('Content-Type: application/json');
echo json_encode($students);

$mysqli->close();
?>