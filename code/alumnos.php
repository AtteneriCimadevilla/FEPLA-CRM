<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

$mostrar_popup = false;
if (isset($_SESSION['alumno_actualizado']) && $_SESSION['alumno_actualizado']) {
    $mostrar_popup = true;
    unset($_SESSION['alumno_actualizado']);
}

// Consulta SQL para obtener los datos de la tabla "alumnos"
$query = "SELECT 
    a.dni_nie AS dni_nie, 
    CONCAT_WS(' ', a.nombre, a.apellido1, a.apellido2) AS nombre_completo,
    a.fecha_nacimiento AS fecha_nacimiento, 
    a.telefono AS telefono, 
    a.email AS email, 
    a.direccion AS direccion, 
    a.vehiculo AS vehiculo, 
    CONCAT(c.nombre, ' - ', g.alias_grupo) AS grupo,
    e.id AS id_empresa,
    e.nombre_comercial AS empresa,
    f.curso AS curso
FROM 
    alumnos a
LEFT JOIN 
    grupos g ON a.id_grupo = g.id_grupo
LEFT JOIN 
    catalogo_ciclos c ON g.id_ciclo = c.id_ciclo
LEFT JOIN 
    formaciones f ON a.dni_nie = f.dni_nie_alumno
LEFT JOIN 
    empresas e ON f.id_empresa = e.id";

// Si se selecciona un grupo, agregar el filtro
if (isset($_GET['grupo']) && !empty($_GET['grupo'])) {
    $grupo_id = intval($_GET['grupo']);
    $query .= " WHERE g.id_grupo = $grupo_id";
}

$result = $mysqli->query($query);

// Obtener los grupos para el filtro
$grupos_filtro_query = "SELECT id_grupo, CONCAT(c.nombre, ' - ', g.alias_grupo) AS nombre_grupo 
                        FROM grupos g 
                        JOIN catalogo_ciclos c ON g.id_ciclo = c.id_ciclo";
$grupos_filtro_result = $mysqli->query($grupos_filtro_query);
$grupos_filtro = $grupos_filtro_result->fetch_all(MYSQLI_ASSOC);

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
    <style>
        .table-alumnos th:first-child,
        .table-alumnos td:first-child {
            width: 30%;
        }

        .table-alumnos th:nth-child(3),
        .table-alumnos td:nth-child(3) {
            width: 30%;
        }

        .table-alumnos th:nth-child(4),
        .table-alumnos td:nth-child(4) {
            width: 10%;
        }
    </style>
</head>

<body>
    <div class="container container-alumnos my-4">
        <header class="d-flex justify-content-between align-items-center mb-3">
            <a href="home.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
                ← Volver al Home
            </a>
        </header>

        <h1 class="page-title text-center mb-4">Alumnos</h1>

        <!-- Add Student Button -->
        <div class="mb-3">
            <a href="gestionAlumno.php" class="btn btn-primary">Añadir Alumno</a>
        </div>

        <!-- Filtro por grupo -->
        <div class="mb-3">
            <form method="GET" action="">
                <label for="grupo" class="form-label">Filtrar por grupo:</label>
                <select class="form-select" id="grupo" name="grupo" onchange="this.form.submit()">
                    <option value="">Todos los grupos</option>
                    <?php foreach ($grupos_filtro as $grupo): ?>
                        <option value="<?= $grupo['id_grupo'] ?>" <?= (isset($_GET['grupo']) && $_GET['grupo'] == $grupo['id_grupo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- Tabla responsive de alumnos -->
        <div class="table-responsive">
            <table class="table table-hover table-alumnos">
                <thead class="thead-dark">
                    <tr>
                        <th>Alumno</th>
                        <th>Contacto</th>
                        <th>Formación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($alumno = $result->fetch_assoc()): ?>
                            <tr>
                                <!-- Columna Alumno -->
                                <td>
                                    <strong>DNI/NIE:</strong> <?php echo htmlspecialchars($alumno['dni_nie']); ?><br>
                                    <strong>Nombre:</strong> <a href="alumno.php?dni_nie=<?php echo urlencode($alumno['dni_nie']); ?>"><?php echo htmlspecialchars($alumno['nombre_completo']); ?></a><br>
                                    <strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($alumno['fecha_nacimiento']); ?><br>
                                    <strong>Dirección:</strong> <?php echo htmlspecialchars($alumno['direccion']); ?><br>
                                    <strong>Vehículo:</strong> <?php echo htmlspecialchars($alumno['vehiculo']); ?>
                                </td>

                                <!-- Columna Contacto -->
                                <td>
                                    <strong>Teléfono:</strong> <?php echo htmlspecialchars($alumno['telefono']); ?><br>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($alumno['email']); ?>
                                </td>

                                <!-- Columna Formación -->
                                <td>
                                    <strong>Grupo:</strong> <?php echo htmlspecialchars($alumno['grupo']); ?><br>
                                    <strong>Empresa:</strong> <?php echo htmlspecialchars($alumno['empresa']); ?><br>
                                    <strong>Curso:</strong> <?php echo htmlspecialchars($alumno['curso']); ?><br>
                                    <?php if ($alumno['empresa']): ?>
                                        <button onclick="abrirVentanaEmergente('crear_formacion.php?tipo=alumno&dni=<?php echo urlencode($alumno['dni_nie']); ?>&edit=1')" class="btn btn-warning btn-sm">
                                            Editar Formación
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="confirmarBorrado('<?php echo htmlspecialchars($alumno['dni_nie']); ?>', '<?php echo htmlspecialchars($alumno['id_empresa']); ?>', '<?php echo htmlspecialchars($alumno['curso']); ?>')">
                                                Eliminar Formación
                                        </button>
                                    <?php else: ?>
                                        <button onclick="abrirVentanaEmergente('crear_formacion.php?tipo=alumno&dni=<?php echo urlencode($alumno['dni_nie']); ?>')" class="btn btn-primary btn-sm">
                                            Crear Formación
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <!-- Columna Acciones -->
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
                            <td colspan="4" class="text-center">No hay alumnos registrados.</td>
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
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirVentanaEmergente(url) {
            window.open(url, 'VentanaEmergente', 'width=800,height=800,resizable=yes,scrollbars=yes');
        }

        function confirmarBorrado(dni, idEmpresa, curso) {
            if (confirm('¿Está seguro de que desea eliminar esta formación?')) {
                const formData = new FormData();
                formData.append('dni_nie', dni);
                formData.append('id_empresa', idEmpresa);
                formData.append('curso', curso);

                fetch('borrar_formacion.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            }
        }
    </script>
</body>

</html>

