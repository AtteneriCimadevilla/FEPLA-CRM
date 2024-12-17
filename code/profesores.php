<?php
require 'conexion.php';

session_start();
$mostrar_popup = false;
if (isset($_SESSION['profesor_actualizado']) && $_SESSION['profesor_actualizado']) {
    $mostrar_popup = true;
    unset($_SESSION['profesor_actualizado']);
}

// Consulta SQL para obtener los datos de la tabla "profesores"
$query = "SELECT dni_nie, nombre, apellido1, apellido2, telefono, email, tipo_usuario FROM profesores";
$result = $mysqli->query($query);

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['dni_nie'])) {
    $dni_nie_to_delete = $_POST['dni_nie'];
    $delete_query = "DELETE FROM profesores WHERE dni_nie = ?";
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
    <title>FEPLA CRM Profesores</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="profesores.css"> <!-- CSS personalizado -->
</head>

<body>
    <div class="container container-profesores my-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
            <img src="logo.png" alt="logo" style="height: 50px;">
            <div class="busqueda">
                <input type="text" id="searchFilter" placeholder="🔍">
                <button id="searchButton">Filtrar</button>
            </div>
        </header>

        <h1 class="page-title text-center mb-4">Profesores</h1>

        <!-- Add Teacher Button -->
        <div class="mb-3">
            <a href="gestionProfesor.php" class="btn btn-primary">Añadir Profesor</a>
        </div>

        <!-- Tabla responsive de profesores -->
        <div class="table-responsive">
            <table class="table table-hover table-profesores">
                <thead class="thead-dark">
                    <tr>
                        <th>DNI/NIE</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Tipo de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($profesor = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($profesor['dni_nie']); ?></td>
                                <td><?php echo htmlspecialchars($profesor['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($profesor['apellido1'] . ' ' . $profesor['apellido2']); ?></td>
                                <td><?php echo htmlspecialchars($profesor['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($profesor['email']); ?></td>
                                <td><?php echo htmlspecialchars($profesor['tipo_usuario']); ?></td>
                                <td>
                                    <a href="gestionProfesor.php?dni_nie=<?php echo urlencode($profesor['dni_nie']); ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar este profesor?');">
                                        <input type="hidden" name="dni_nie" value="<?php echo htmlspecialchars($profesor['dni_nie']); ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay profesores registrados.</td>
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
                        <p>Profesor actualizado con éxito</p>
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
                    popupElement.parentNode.removeChild(popupElement);
                });

                var closeButtons = popupElement.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        popup.hide();
                    });
                });
            });
        </script>
    <?php endif; ?>

    <script type="module" src="profesores.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>