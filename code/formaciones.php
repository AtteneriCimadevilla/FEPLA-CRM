<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Consulta para obtener todas las formaciones con información relacionada
$sql = "SELECT 
    f.dni_nie_alumno,
    f.id_empresa,
    f.curso,
    a.nombre AS nombre_alumno,
    a.apellido1,
    a.apellido2,
    e.nombre_comercial,
    g.alias_grupo,
    cc.nombre AS nombre_ciclo
FROM formaciones f
LEFT JOIN alumnos a ON f.dni_nie_alumno = a.dni_nie
LEFT JOIN empresas e ON f.id_empresa = e.id
LEFT JOIN grupos g ON a.id_grupo = g.id_grupo
LEFT JOIN catalogo_ciclos cc ON g.id_ciclo = cc.id_ciclo
ORDER BY cc.nombre, g.alias_grupo, a.apellido1, a.nombre";

$formaciones = [];
$error = null;

try {
    $resultado = $mysqli->query($sql);
    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $formaciones[] = $row;
        }
        $resultado->free();
    } else {
        throw new Exception("Error al obtener las formaciones: " . $mysqli->error);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Formaciones - CRM-FEPLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="home.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <h1 class="mb-0">Gestión de Formaciones</h1>
            <button class="btn btn-primary" onclick="crearNuevaFormacion()">
                <i class="bi bi-plus-circle"></i> Nueva Formación
            </button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Ciclo</th>
                                <th>Grupo</th>
                                <th>Alumno</th>
                                <th>Empresa</th>
                                <th>Curso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($formaciones) > 0): ?>
                                <?php foreach ($formaciones as $formacion): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($formacion['nombre_ciclo'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($formacion['alias_grupo'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($formacion['nombre_alumno'] . ' ' . $formacion['apellido1'] . ' ' . $formacion['apellido2']); ?></td>
                                        <td><?php echo htmlspecialchars($formacion['nombre_comercial']); ?></td>
                                        <td><?php echo htmlspecialchars($formacion['curso']); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editarFormacion('<?php echo htmlspecialchars($formacion['dni_nie_alumno']); ?>', '<?php echo htmlspecialchars($formacion['id_empresa']); ?>')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmarBorrado('<?php echo htmlspecialchars($formacion['dni_nie_alumno']); ?>', '<?php echo htmlspecialchars($formacion['id_empresa']); ?>', '<?php echo htmlspecialchars($formacion['curso']); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay formaciones registradas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function crearNuevaFormacion() {
            window.open('crear_formacion.php', 'CrearFormacion', 
                'width=800,height=600,resizable=yes,scrollbars=yes');
        }

        function editarFormacion(dni, idEmpresa) {
            window.open(`crear_formacion.php?dni_nie=${encodeURIComponent(dni)}&id_empresa=${encodeURIComponent(idEmpresa)}&edit=1`, 
                'EditarFormacion', 
                'width=800,height=600,resizable=yes,scrollbars=yes');
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