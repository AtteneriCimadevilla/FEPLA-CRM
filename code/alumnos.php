<?php
require 'conexion.php';

// Consulta SQL para obtener los datos de la tabla "alumnos" con los apellidos combinados
$query = "SELECT alumnos.dni_nie, alumnos.nombre, 
    CONCAT(alumnos.apellido1, ' ', alumnos.apellido2) AS apellidos, 
    alumnos.fecha_nacimiento, alumnos.telefono, alumnos.email, alumnos.direccion,
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="alumno.css"> <!-- CSS personalizado -->
</head>

<body>
    <div class="container container-alumnos my-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
            <img src="logo.png" alt="logo" style="height: 50px;">
            <div class="busqueda">Filtros de búsqueda</div>
        </header>

        <h1 class="page-title text-center mb-4">Gestión de Alumnos</h1>

        <!-- Tabla responsive de alumnos -->
        <div class="table-responsive">
            <table class="table table-hover table-alumnos">
                <thead class="thead-dark">
                    <tr>
                        <th>DNI/NIE</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Vehículo</th>
                        <th>Curso</th>
                        <th>Empresa de Prácticas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($alumno = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno['dni_nie']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['apellidos']); ?></td>
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
                        <tr><td colspan="10" class="text-center">No hay alumnos registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php $mysqli->close(); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>