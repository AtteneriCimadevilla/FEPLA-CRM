<?php
include 'conexion.php';

// Obtener datos de la URL
$id_empresa = isset($_GET['id_empresa']) ? intval($_GET['id_empresa']) : 0;
$dni_nie_alumno = isset($_GET['dni_nie']) ? $_GET['dni_nie'] : '';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $tipo_actividad = $_POST['tipo_actividad'];
    $texto_registro = $_POST['texto_registro'];
    $id_empresa_seleccionada = isset($_POST['id_empresa']) ? intval($_POST['id_empresa']) : null;
    $dni_nie_alumno_seleccionado = $_POST['dni_nie_alumno'] ?? '';

    // Validación de campos obligatorios
    if (empty($fecha) || empty($tipo_actividad) || empty($texto_registro)) {
        $mensaje = '<div class="alert alert-danger">Rellene los campos obligatorios.</div>';
    } else {
        // Preparar consulta con parámetros opcionales
        $query = "INSERT INTO registro (id_empresa, dni_nie_alumno, fecha, tipo_actividad, texto_registro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);

        // Insertar datos, permitiendo valores nulos
        $stmt->bind_param(
            "issss",
            $id_empresa_seleccionada,
            $dni_nie_alumno_seleccionado,
            $fecha,
            $tipo_actividad,
            $texto_registro
        );

        // Ejecutar consulta
        $mensaje = ($stmt->execute())
            ? '<div class="alert alert-success">Actividad agregada con éxito.</div>'
            : '<div class="alert alert-danger">Error al agregar la actividad: ' . $mysqli->error . '</div>';
        $stmt->close();
    }
}

// Obtener lista de empresas si no hay una seleccionada
$empresas = [];
if (empty($id_empresa)) {
    $stmt = $mysqli->prepare("SELECT id, nombre_comercial FROM empresas");
    $stmt->execute();
    $result = $stmt->get_result();
    $empresas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Obtener lista de alumnos si se selecciona una empresa
$alumnos = [];
if ($id_empresa > 0) {
    $stmt = $mysqli->prepare("SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', apellido2) AS nombre_completo FROM alumnos");
    $stmt->execute();
    $result = $stmt->get_result();
    $alumnos = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Agregar Actividad</h1>
        <?php echo $mensaje; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="fecha" class="form-label">* Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <div class="mb-3">
                <label for="tipo_actividad" class="form-label">* Tipo de actividad</label>
                <select class="form-select" id="tipo_actividad" name="tipo_actividad" required>
                    <option value="Llamada">Llamada</option>
                    <option value="Email">Email</option>
                    <option value="Visita">Visita</option>
                </select>
            </div>
            <?php if ($id_empresa > 0): ?>
                <div class="mb-3">
                    <label for="dni_nie_alumno" class="form-label">Alumno (opcional)</label>
                    <select class="form-select" id="dni_nie_alumno" name="dni_nie_alumno">
                        <option value="">Seleccione un alumno</option>
                        <?php foreach ($alumnos as $alumno): ?>
                            <option value="<?php echo htmlspecialchars($alumno['dni_nie']); ?>">
                                <?php echo htmlspecialchars($alumno['nombre_completo']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="id_empresa" value="<?php echo $id_empresa; ?>">
            <?php else: ?>
                <div class="mb-3">
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <select class="form-select" id="id_empresa" name="id_empresa">
                        <option value="">Seleccione una empresa</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?php echo htmlspecialchars($empresa['id']); ?>">
                                <?php echo htmlspecialchars($empresa['nombre_comercial']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="dni_nie_alumno" value="<?php echo htmlspecialchars($dni_nie_alumno); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="texto_registro" class="form-label">* Detalles</label>
                <textarea class="form-control" id="texto_registro" name="texto_registro" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Actividad</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (strpos($mensaje, 'alert-success') !== false): ?>
            setTimeout(function() {
                window.opener.location.reload();
                window.close();
            }, 2000);
        <?php endif; ?>
    </script>
</body>

</html>
