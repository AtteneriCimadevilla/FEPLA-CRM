<?php
require_once "conexion.php";

$sql_students = "SELECT 
    a.dni_nie, 
    CONCAT(a.nombre, ' ', a.apellido1, ' ', COALESCE(a.apellido2, '')) AS nombre_completo,
    CONCAT(c.nombre, ' - ', g.alias_grupo) AS grupo
FROM 
    alumnos a
LEFT JOIN 
    grupos g ON a.id_grupo = g.id_grupo
LEFT JOIN 
    catalogo_ciclos c ON g.id_ciclo = c.id_ciclo";

$result_students = $mysqli->query($sql_students);

$students = [];
while($row = $result_students->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$mysqli->close();
?>

