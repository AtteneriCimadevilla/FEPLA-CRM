<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

$sql = "SELECT r.id, r.fecha, r.tipo_actividad, r.texto_registro, e.nombre_comercial AS empresa, 
            a.nombre AS alumno, a.apellido1 AS apellido
        FROM registro r
        LEFT JOIN empresas e ON r.id_empresa = e.id
        LEFT JOIN alumnos a ON r.dni_nie_alumno = a.dni_nie";

$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estiloActividad.css">
</head>
<body>
    <header class="d-flex justify-content-between align-items-center mb-3">
        <a href="home.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
        ← Volver al Home </a>
    </header>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Actividades Registradas</h1>
        
        <!-- Actividades -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Actividad</th>
                    <th>Empresa</th>
                    <th>Alumno</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($activity['tipo_actividad']); ?></td>
                        <td><?php echo htmlspecialchars($activity['empresa']); ?></td>
                        <td><?php echo htmlspecialchars($activity['alumno'] . ' ' . $activity['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($activity['texto_registro']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>