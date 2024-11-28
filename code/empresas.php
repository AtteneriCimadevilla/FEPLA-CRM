<?php
require 'conexion.php';

session_start();
$mostrar_popup = false;
if (isset($_SESSION['empresa_actualizada']) && $_SESSION['empresa_actualizada']) {
    $mostrar_popup = true;
    unset($_SESSION['empresa_actualizada']);
}

// Consulta SQL para obtener los datos de la tabla "empresas"
$query = "SELECT id, cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, email_contacto, interesado, cantidad_alumnos
          FROM empresas";
$result = $mysqli->query($query);

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id_to_delete = $_POST['id'];
    $delete_query = "DELETE FROM empresas WHERE id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $id_to_delete);
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
    <title>FEPLA CRM Empresas</title>
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

        <h1 class="page-title text-center mb-4">Empresas</h1>

        <!-- Add Company Button -->
        <div class="mb-3">
            <a href="gestionEmpresa.php" class="btn btn-primary">Añadir Empresa</a>
        </div>

        <!-- Tabla responsive de empresas -->
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($empresa = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($empresa['cif']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['nombre_comercial']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['nombre_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['telefono_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['nombre_contacto']); ?></td>
                                <td><?php echo htmlspecialchars($empresa['email_contacto']); ?></td>
                                <td><?php echo $empresa['interesado'] ? 'Sí' : 'No'; ?></td>
                                <td><?php echo htmlspecialchars($empresa['cantidad_alumnos']); ?></td>
                                <td>
                                    <a href="gestionEmpresa.php?id=<?php echo urlencode($empresa['id']); ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($empresa['id']); ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta empresa?');">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay empresas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $mysqli->close(); ?>

    <script type="module" src="empresas.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php if ($mostrar_popup): ?>
        <div id="popup" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Empresa actualizada con éxito</p>
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

</body>

</html>