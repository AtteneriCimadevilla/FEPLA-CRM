<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Verificar si el parámetro 'dni' está en la URL
if (isset($_GET['dni_nie']) && !empty($_GET['dni_nie'])) {
    $dni_nie = $_GET['dni_nie']; // Obtener el DNI/NIE desde la URL
} else {
    die("No se ha proporcionado un DNI/NIE válido.");
}

// Consulta para obtener los detalles del alumno
$stmt = $mysqli->prepare("
    SELECT 
        a.dni_nie AS dni_nie, 
        a.nombre AS nombre, 
        CONCAT_WS(' ', a.apellido1, a.apellido2) AS apellidos,
        a.fecha_nacimiento AS fecha_nacimiento, 
        a.telefono AS telefono, 
        a.email AS email, 
        a.direccion AS direccion, 
        a.vehiculo AS vehiculo, 
        CONCAT(c.nombre, ' - ', g.alias_grupo) AS grupo,
        e.nombre_comercial AS empresa
    FROM 
        alumnos a
    LEFT JOIN 
        grupos g ON a.id_grupo = g.id_grupo
    LEFT JOIN 
        catalogo_ciclos c ON g.id_ciclo = c.id_ciclo
    LEFT JOIN 
        formaciones f ON a.dni_nie = f.dni_nie_alumno
    LEFT JOIN 
        empresas e ON f.id_empresa = e.id
    WHERE 
        a.dni_nie = ?;
");

// Proporcionar el valor para el placeholder
$stmt->bind_param("s", $dni_nie);

// Ejecuta la consulta
$stmt->execute();

// Obtiene los resultados
$resultado = $stmt->get_result();

// Procesa los datos
if ($fila = $resultado->fetch_assoc()) {
    $alumno = $fila;
} else {
    $alumno = null;
}

// Cierra la conexión de la primera consulta
$stmt->close();

// Consulta para obtener los registros de actividades del alumno
$stmt2 = $mysqli->prepare("
    SELECT r.fecha, r.tipo_actividad, e.nombre_comercial AS empresa, r.texto_registro
    FROM registro r
    LEFT JOIN empresas e ON r.id_empresa = e.id
    WHERE r.dni_nie_alumno = ?;
");
$stmt2->bind_param("s", $dni_nie);
$stmt2->execute();
$resultado2 = $stmt2->get_result();

// Procesa los datos de las actividades
$registros = [];
while ($registro = $resultado2->fetch_assoc()) {
    $registros[] = $registro;
}

// Cierra la conexión de la segunda consulta
$stmt2->close();

// Consulta para obtener las empresas disponibles para seleccionar en el modal
$stmt3 = $mysqli->prepare("SELECT id, nombre_comercial FROM empresas");
$stmt3->execute();
$resultado3 = $stmt3->get_result();
$empresas_options = '';

// Crear opciones del select de empresas
while ($empresa = $resultado3->fetch_assoc()) {
    $empresas_options .= "<option value='{$empresa['id']}'>{$empresa['nombre_comercial']}</option>";
}

// Cierra la conexión de la tercera consulta
$stmt3->close();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Alumno</title>
    <!-- Vincula a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center mb-3">
    <!-- Flecha para volver al home -->
    <a href="alumnos.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
        ← Volver
    </a>
    </header>
    <div class="container mt-5">

        <div class="row">
            <!-- Columna de Detalles del Alumno -->
            <div class="col-md-6">
                <h1 class="text-center mb-4">Detalles del Alumno</h1>

                <!-- Espacio para la foto -->
                <div class="text-center mb-4">
                    <img src="../img/flat-business-woman-user-profile-avatar-icon-vector-4334111-2809499332.jpg" alt="Foto del Alumno" class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
                </div>

                <?php if ($alumno): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos']) ?></h5>
                        </div>
                        <div class="card-body">
                            <p><strong>DNI/NIE:</strong> <?= htmlspecialchars($alumno['dni_nie']) ?></p>
                            <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($alumno['fecha_nacimiento']) ?></p>
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($alumno['telefono']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($alumno['email']) ?></p>
                            <p><strong>Dirección:</strong> <?= htmlspecialchars($alumno['direccion']) ?></p>
                            <p><strong>Vehículo:</strong> <?= $alumno['vehiculo'] ?></p>
                            <p><strong>Grupo:</strong> <?= htmlspecialchars($alumno['grupo']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        No se encontró al alumno.
                    </div>
                <?php endif; ?>
                <!-- Empresa Asignada -->
                <h2 class="mt-4">Empresa Asignada</h2>
                <table class="table table-bordered">
                    <tr>
                        <td>
                            <?php if ($alumno['empresa']): ?>
                                <span><?php echo htmlspecialchars($alumno['empresa']); ?></span>
                                <button class="btn btn-warning btn-sm" onclick="abrirVentanaEmergente('crear_formacion.php?dni=<?= urlencode($alumno['dni_nie']) ?>&edit=1')">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarBorrarFormacion('<?= urlencode($alumno['dni_nie']) ?>')">Borrar formación</button>
                            <?php else: ?>
                                <button class="btn btn-primary btn-sm" onclick="abrirVentanaEmergente('crear_formacion.php?dni=<?= urlencode($alumno['dni_nie']) ?>')">Crear formación</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Columna de Registros de Actividades (en la parte derecha) -->
            <div class="col-md-6">
                <h2 class="mb-4">Registros de Actividades</h2>
                <button class="btn btn-primary mb-3" onclick="abrirVentanaEmergente('agregar_actividad.php?dni_nie=<?php echo urlencode($alumno['dni_nie']); ?>')">Añadir Actividad</button>
                <?php if (count($registros) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Actividad</th>
                                <th>Empresa</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($registro['fecha']) ?></td>
                                    <td><?= htmlspecialchars($registro['tipo_actividad']) ?></td>
                                    <td><?= htmlspecialchars($registro['empresa']) ?></td>
                                    <td><?= htmlspecialchars($registro['texto_registro']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        No hay registros de actividades para este alumno.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Modal para editar empresa -->
    <div class="modal fade" id="editarEmpresaModal" tabindex="-1" aria-labelledby="editarEmpresaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarEmpresaModalLabel">Editar Empresa Asignada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarEmpresaForm">
                        <input type="hidden" id="dni_nie" name="dni_nie">
                        <div class="mb-3">
                            <label for="empresa" class="form-label">Empresa</label>
                            <select class="form-select" id="empresa" name="empresa" required>
                                <?php echo $empresas_options; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEmpresa()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vincula a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function abrirVentanaEmergente(url) {
            window.open(url, 'VentanaEmergente', 'width=800,height=600,resizable=yes');
        }

        function editarEmpresa(dni, empresa) {
            document.getElementById('dni_nie').value = dni;
            document.getElementById('empresa').value = empresa;
            var modal = new bootstrap.Modal(document.getElementById('editarEmpresaModal'));
            modal.show();
        }

        function guardarEmpresa() {
            var form = document.getElementById('editarEmpresaForm');
            var formData = new FormData(form);

            fetch('actualizar_empresa.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('empresaAsignada').textContent = data.empresa;
                        var modal = bootstrap.Modal.getInstance(document.getElementById('editarEmpresaModal'));
                        modal.hide();
                    } else {
                        alert('Error al actualizar la empresa');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar la empresa');
                });
        }

        function confirmarBorrarFormacion(dni) {
            if (confirm('¿Está seguro de que desea borrar esta formación?')) {
                // Realizar una solicitud AJAX para borrar la formación
                fetch('borrar_formacion.php?dni=' + dni, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Formación borrada con éxito');
                            location.reload(); // Recargar la página para reflejar los cambios
                        } else {
                            alert('Error al borrar la formación: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al borrar la formación');
                    });
            }
        }
    </script>

</body>

</html>

