<?php
require 'conexion.php';

session_start();
$mostrar_popup = false;
if (isset($_SESSION['alumno_actualizado']) && $_SESSION['alumno_actualizado']) {
    $mostrar_popup = true;
    unset($_SESSION['alumno_actualizado']);
}

// Consulta SQL para obtener los datos de la tabla "alumnos"
$query = "SELECT 
    a.dni_nie AS dni_nie, 
    a.nombre AS nombre, 
    CONCAT_WS(' ', a.apellido1, a.apellido2) AS apellidos,
    a.fecha_nacimiento AS fecha_nacimiento, 
    a.telefono AS telefono, 
    a.email AS email, 
    a.direccion AS direccion, 
    a.vehiculo AS vehiculo, 
    a.clase AS clase, 
    e.nombre_comercial AS empresa
FROM 
    alumnos a
LEFT JOIN 
    formaciones f ON a.dni_nie = f.dni_nie_alumno
LEFT JOIN 
    empresas e ON f.id_empresa = e.id;";
$result = $mysqli->query($query);

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['dni_nie'])) {
    $dni_nie_to_delete = $_POST['dni_nie'];
    $delete_query = "DELETE FROM alumnos WHERE dni_nie = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("s", $dni_nie_to_delete);
    $stmt->execute();
    $stmt->close();
    // Redirect to refresh the page after deletion
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
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
            <div class="busqueda">
                <input type="text" id="searchFilter" placeholder="🔍">
                <button id="searchButton">Filtrar</button>
            </div>
        </header>

        <h1 class="page-title text-center mb-4">Alumnos</h1>

        <!-- Add Student Button -->
        <div class="mb-3">
            <a href="gestionAlumno.php" class="btn btn-primary">Añadir Alumno</a>
        </div>

        <!-- Tabla responsive de alumnos -->
        <div class="table-responsive">
            <table class="table table-hover table-alumnos">
                <thead class="thead-dark">
                    <tr>
                        <th>DNI/NIE</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Fecha de nacimiento</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Vehículo</th>
                        <th>Clase</th>
                        <th>Empresa asignada</th>
                        <th>Acciones</th>
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
                                <td><?php echo htmlspecialchars($alumno['clase']); ?></td>
                                <td>
                                    <?php
                                    if ($alumno['empresa'] === null) {
                                        // Fetch empresas from the database
                                        $query_empresas = "SELECT id, nombre_comercial FROM empresas";
                                        $result_empresas = mysqli_query($mysqli, $query_empresas);

                                        // Generate the drop-down menu
                                        echo '<form method="POST" action="insert_empresa.php">';
                                        echo '<select name="empresa" id="empresa">';
                                        echo '<option value="">Select an Empresa</option>';
                                        while ($row = mysqli_fetch_assoc($result_empresas)) {  // Use $result_empresas here
                                            echo '<option value="' . $row['id'] . '">' . $row['nombre_comercial'] . '</option>';
                                        }
                                        echo '</select>';
                                        echo '<input type="hidden" name="dni_nie_alumno" value="' . $alumno['dni_nie'] . '">';  // Hidden field to pass the alumno's ID
                                        echo '<input type="submit" value="Save">';
                                        echo '</form>';
                                    } else {
                                        // Display the current empresa name if it's assigned
                                        echo htmlspecialchars($alumno['empresa']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="gestionAlumno.php?dni_nie=<?php echo urlencode($alumno['dni_nie']); ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar este alumno?');">
                                        <input type="hidden" name="dni_nie" value="<?php echo htmlspecialchars($alumno['dni_nie']); ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No hay alumnos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $mysqli->close(); ?>

    <?php if ($mostrar_popup): ?>
        <div id="popup" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Alumno actualizado con éxito</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var popupElement = document.getElementById('popup');
                var popup = new bootstrap.Modal(popupElement);
                popup.show();

                // Handle closing the modal
                popupElement.addEventListener('hidden.bs.modal', function() {
                    // Remove the modal from the DOM after it's hidden
                    popupElement.parentNode.removeChild(popupElement);
                });

                // Add event listeners to close buttons
                var closeButtons = popupElement.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        popup.hide();
                    });
                });
            });
        </script>
    <?php endif; ?>

    <script type="module" src="alumnos.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>