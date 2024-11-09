<?php
    require 'conexion.php';

    // <!-- // Consulta SQL para obtener los datos de la tabla "alumnos" -->
    $query = "SELECT alumnos.dni_nie, alumnos.nombre, alumnos.apellido1, alumnos.apellido2, alumnos.fecha_nacimiento, alumnos.telefono, alumnos.email, alumnos.direccion,
    alumnos.vehiculo, alumnos.curso, empresas.nombre_comercial AS empresa_nombre
          FROM alumnos
          LEFT JOIN empresas ON alumnos.id_empresa = empresas.id";
    $result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEPLA CRM Alumnos</title>
    <link rel="stylesheet" href="style-tablas.css">
</head>

<body>
    <header>
        <img src="logo.png" alt="logo">
        <div class="busqueda">filtros de búsqueda</div>
    </header>

    <table>
        <tr>
            <th>DNI/NIE</th>
            <th>Nombre</th>
            <th>1er apellido</th>
            <th>2o apellido</th>
            <th>Fecha de Nacimiento</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>Vehículo</th>
            <th>Curso</th>
            <th>Empresa de prácticas</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($alumno = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['dni_nie']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['apellido1']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['apellido2']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['email']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['direccion']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['vehiculo']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['curso']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['empresa_nombre']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10">No hay alumnos registrados.</td></tr>
        <?php endif; ?>
    </table>
    
    <?php $mysqli->close(); ?>

</body>

</html>


<!-- CON JS (AJAX) -->
 <!-- <?php /*
include 'config.php';

$query = "SELECT alumnos.dni_nie, alumnos.nombre, alumnos.apellido1, alumnos.apellido2, alumnos.fecha_nacimiento,
          alumnos.telefono, alumnos.email, alumnos.direccion, alumnos.vehiculo, profesores.nombre AS tutor_nombre,
          empresas.nombre_comercial AS empresa_nombre
          FROM alumnos
          LEFT JOIN profesores ON alumnos.dni_tutor = profesores.dni_nie
          LEFT JOIN empresas ON alumnos.id_empresa = empresas.id";
$result = $conn->query($query);

$alumnos = [];
if ($result->num_rows > 0) {
    while ($alumno = $result->fetch_assoc()) {
        $alumnos[] = $alumno;
    }
}

header('Content-Type: application/json');
echo json_encode($alumnos);

$conn->close(); */ ?> -->
