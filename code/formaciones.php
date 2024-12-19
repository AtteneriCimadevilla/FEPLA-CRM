<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Consulta para obtener la información de alumnos y empresas
$sql = "SELECT CONCAT_WS(' ', a.nombre, a.apellido1, a.apellido2) AS alumno, 
               e.nombre_comercial AS empresa
        FROM alumnos a
        JOIN formaciones f ON a.dni_nie = f.dni_nie_alumno
        JOIN empresas e ON f.id_empresa = e.id";

$result = $mysqli->query($sql);

$alumnos_empresas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alumnos_empresas[] = $row;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relación Alumnos-Empresas - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estiloActividad.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Listado de Formaciones en Centros de Trabajo</h1>

        <!-- Tabla de Alumnos y Empresas -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Empresa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos_empresas as $entry): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['alumno']); ?></td>
                        <td><?php echo htmlspecialchars($entry['empresa']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>