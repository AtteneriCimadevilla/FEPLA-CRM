<?php
require 'conexion.php';

// Consulta SQL para obtener los datos de la tabla "empresas" con los apellidos combinados
$query = "SELECT cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, email_contacto, interesado, cantidad_alumnos
          FROM empresas ";
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

        <h1 class="page-title text-center mb-4">Empresas</h1>

        <!-- Tabla responsive de alumnos -->
        <div class="table-responsive">
            <table class="table table-hover table-alumnos">
                <thead class="thead-dark">
                    <tr>
                        <th>CIF</th>
                        <th>Nombre Comercial</th>
                        <th>Nombre Empresa</th>
                        <th>Telefono</th>
                        <th>Nombre Contacto</th>
                        <th>Email Contacto</th>
                        <th>Interesado</th>
                        <th>Cantidad Alumnos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($alumno = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno['cif']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre_comercial']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['telefono_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre_contacto']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['email_contacto']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['interesado']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['cantidad_alumnos']); ?></td>
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