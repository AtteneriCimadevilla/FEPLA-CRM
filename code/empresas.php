<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

$mostrar_popup = false;
if (isset($_SESSION['empresa_actualizada']) && $_SESSION['empresa_actualizada']) {
    $mostrar_popup = true;
    unset($_SESSION['empresa_actualizada']);
}

// Consulta SQL para obtener los datos de la tabla "empresas"
$query = "SELECT * FROM empresas";
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
    <link rel="stylesheet" href="empresa.css"> <!-- CSS personalizado -->
    <style>
        .table-empresas th:first-child,
        .table-empresas td:first-child {
            width: 30%;
        }

        .table-empresas th:nth-child(3),
        .table-empresas td:nth-child(3) {
            width: 30%;
        }

        .table-empresas th:nth-child(4),
        .table-empresas td:nth-child(4) {
            width: 10%;
        }
    </style>
</head>

<body>
    <div class="container container-empresas my-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
            <a href="home.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
                ← Volver al Home </a>
        </header>

        <h1 class="page-title text-center mb-4">Empresas</h1>

        <!-- Add Company Button -->
        <div class="mb-3">
            <a href="gestionEmpresa.php" class="btn btn-primary">Añadir Empresa</a>
        </div>

        <!-- Botones para exportar e importar empresas -->
        <div class="text-end mb-3">
            <a href="exportar_empresas.php" class="btn btn-success">
                Exportar a CSV
            </a>
            <button type="button" class="btn btn-primary ml-2" data-bs-toggle="modal" data-bs-target="#importModal">

                Importar CSV
            </button>
        </div>

        <!-- Tabla responsive de empresas -->
        <div class="table-responsive">
            <table class="table table-hover table-empresas">
                <thead class="thead-dark">
                    <tr>
                        <th>Empresa</th>
                        <th>Contacto</th>
                        <th>Interés en formaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($empresa = $result->fetch_assoc()): ?>
                            <tr>
                                <!-- Columna Empresa -->
                                <td>
                                    <strong>NIF:</strong> <?php echo htmlspecialchars($empresa['nif']); ?><br>
                                    <strong>Comercial:</strong> <a href="empresa.php?id=<?php echo urlencode($empresa['id']); ?>"><?php echo htmlspecialchars($empresa['nombre_comercial']); ?></a><br>
                                    <strong>Empresa:</strong> <?php echo htmlspecialchars($empresa['nombre_empresa']); ?><br>
                                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($empresa['telefono_empresa']); ?><br>
                                    <strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?>
                                </td>

                                <!-- Columna Contacto -->
                                <td>
                                    <strong>Nombre:</strong> <?php echo htmlspecialchars($empresa['nombre_contacto']); ?><br>
                                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($empresa['telefono_contacto']); ?><br>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($empresa['email_contacto']); ?>
                                </td>

                                <!-- Columna Interés en formaciones -->
                                <td>
                                    <strong>Interesado:</strong> <?php echo $empresa['interesado'] ? 'Sí' : 'No'; ?><br>
                                    <strong>Alumnos:</strong> <?php echo htmlspecialchars($empresa['cantidad_alumnos']); ?><br>
                                    <strong>Descripción:</strong> <?php echo htmlspecialchars($empresa['descripcion']); ?>
                                </td>

                                <!-- Columna Acciones -->
                                <td>
                                    <a href="gestionEmpresa.php?id=<?php echo urlencode($empresa['id']); ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar esta empresa?');">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($empresa['id']); ?>">
                                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No hay empresas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para importar CSV -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Importar Empresas desde CSV</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="importar_empresas.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">Seleccionar archivo CSV</label>
                            <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </form>
                </div>
            </div>
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

                popupElement.addEventListener('hidden.bs.modal', function() {
                    popupElement.parentNode.removeChild(popupElement);
                });
            });
        </script>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['import_success'])) {
        $importCount = $_SESSION['import_count'];
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var importSuccessModal = new bootstrap.Modal(document.getElementById('importSuccessModal'));
            importSuccessModal.show();
        });
    </script>";
        unset($_SESSION['import_success']);
        unset($_SESSION['import_count']);
    }

    if (isset($_SESSION['import_error'])) {
        $importError = $_SESSION['import_error'];
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var importErrorModal = new bootstrap.Modal(document.getElementById('importErrorModal'));
            importErrorModal.show();
        });
    </script>";
        unset($_SESSION['import_error']);
    }
    ?>

    <!-- Modal para éxito de importación -->
    <div class="modal fade" id="importSuccessModal" tabindex="-1" aria-labelledby="importSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSuccessModalLabel">Importación Exitosa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Se han importado o actualizado <?php echo $importCount; ?> registros correctamente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para error de importación -->
    <div class="modal fade" id="importErrorModal" tabindex="-1" aria-labelledby="importErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importErrorModalLabel">Error de Importación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo $importError; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>