<?php
require 'conexion.php';

// Consulta SQL para obtener los datos de la tabla "empresas" con los apellidos combinados
$query = "SELECT cif, nombre_comercial, nombre_empresa, telefono_empresa, nombre_contacto, email_contacto, interesado, cantidad_alumnos
          FROM empresas ";
$result = $mysqli->query($query);

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['cif'])) {
    $cif_to_delete = $_POST['cif'];
    $delete_query = "DELETE FROM empresas WHERE cif = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("s", $cif_to_delete);
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

        <h1 class="page-title text-center mb-4">Empresas</h1>

        <!-- Add Company Button -->
        <div class="mb-3">
            <a href="gestionEmpresa.php" class="btn btn-primary">Añadir Empresa</a>
        </div>

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
                                    <a href="gestionEmpresa.php?cif=<?php echo urlencode($empresa['cif']); ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="cif" value="<?php echo htmlspecialchars($empresa['cif']); ?>">
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
</body>

</html>