<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Consulta para obtener la información de formaciones
$sql = "SELECT f.dni_nie_alumno, f.id_empresa, f.curso,
               CONCAT_WS(' ', a.nombre, a.apellido1, a.apellido2) AS alumno, 
               e.nombre_comercial AS empresa,
               g.alias_grupo,
               cc.nombre AS ciclo
        FROM formaciones f
        JOIN alumnos a ON f.dni_nie_alumno = a.dni_nie
        JOIN empresas e ON f.id_empresa = e.id
        LEFT JOIN grupos g ON a.id_grupo = g.id_grupo
        LEFT JOIN catalogo_ciclos cc ON g.id_ciclo = cc.id_ciclo
        ORDER BY cc.nombre, g.alias_grupo, a.apellido1, a.nombre";

$result = $mysqli->query($sql);

$formaciones = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $formaciones[] = $row;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formaciones - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <header class="d-flex justify-content-between align-items-center p-3">
        <a href="home.php" class="btn btn-outline-secondary">← Volver a Home</a>
        <div>
            <button onclick="crearFormacionEmpresa()" class="btn btn-success me-2">
                <i class="bi bi-building"></i> Nueva Formación desde Empresa
            </button>
            <button onclick="crearFormacionAlumno()" class="btn btn-primary">
                <i class="bi bi-person"></i> Nueva Formación desde Alumno
            </button>
        </div>
    </header>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Formaciones en Centros de Trabajo</h1>

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
                    <?php foreach ($formaciones as $formacion): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($formacion['ciclo']); ?></td>
                            <td><?php echo htmlspecialchars($formacion['alias_grupo']); ?></td>
                            <td><?php echo htmlspecialchars($formacion['alumno']); ?></td>
                            <td><?php echo htmlspecialchars($formacion['empresa']); ?></td>
                            <td><?php echo htmlspecialchars($formacion['curso']); ?></td>
                            <td>
                                <button onclick="editarFormacion('<?php echo $formacion['dni_nie_alumno']; ?>', '<?php echo $formacion['id_empresa']; ?>')" 
                                        class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="borrarFormacion('<?php echo $formacion['dni_nie_alumno']; ?>')" 
                                        class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function crearFormacionEmpresa() {
            window.open('crear_formacion.php?tipo=empresa', 'CrearFormacion', 
                'width=800,height=600,resizable=yes,scrollbars=yes');
        }

        function crearFormacionAlumno() {
            window.open('crear_formacion.php?tipo=alumno', 'CrearFormacion', 
                'width=800,height=600,resizable=yes,scrollbars=yes');
        }

        function editarFormacion(dni, idEmpresa) {
            window.open(`crear_formacion.php?dni=${dni}&id_empresa=${idEmpresa}&edit=1`, 'EditarFormacion', 
                'width=800,height=600,resizable=yes,scrollbars=yes');
        }

        function borrarFormacion(dni) {
            if (confirm('¿Está seguro de que desea eliminar esta formación?')) {
                $.post('borrar_formacion.php?dni=' + dni, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar la formación');
                    }
                });
            }
        }
    </script>
</body>
</html>

