<?php
require 'conexion.php';

$dni_nie = isset($_GET['dni']) ? $_GET['dni'] : '';

if (empty($dni_nie)) {
    die("No se ha proporcionado un DNI/NIE válido.");
}

// Fetch companies for the dropdown
$stmt = $mysqli->prepare("SELECT id, nombre_comercial FROM empresas");
$stmt->execute();
$result = $stmt->get_result();
$empresas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $tipo_actividad = $_POST['tipo_actividad'];
    $id_empresa = $_POST['id_empresa'];
    $texto_registro = $_POST['texto_registro'];

    // Validate inputs
    if (empty($fecha) || empty($tipo_actividad) || empty($id_empresa) || empty($texto_registro)) {
        $mensaje = '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
    } else {
        // Insert new activity record
        $stmt = $mysqli->prepare("INSERT INTO registro (dni_nie_alumno, fecha, tipo_actividad, id_empresa, texto_registro) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $dni_nie, $fecha, $tipo_actividad, $id_empresa, $texto_registro);

        if ($stmt->execute()) {
            $mensaje = '<div class="alert alert-success">Actividad agregada con éxito.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al agregar la actividad: ' . $mysqli->error . '</div>';
        }
        $stmt->close();
    }
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
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <div class="mb-3">
                <label for="tipo_actividad" class="form-label">Tipo de Actividad</label>
                <input type="text" class="form-control" id="tipo_actividad" name="tipo_actividad" required>
            </div>
            <div class="mb-3">
                <label for="id_empresa" class="form-label">Empresa</label>
                <select class="form-select" id="id_empresa" name="id_empresa" required>
                    <option value="">Seleccione una empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo htmlspecialchars($empresa['id']); ?>">
                            <?php echo htmlspecialchars($empresa['nombre_comercial']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="texto_registro" class="form-label">Detalles</label>
                <textarea class="form-control" id="texto_registro" name="texto_registro" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Actividad</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Close the window and refresh the parent page after successful submission
        <?php if (strpos($mensaje, 'alert-success') !== false): ?>
            setTimeout(function() {
                window.opener.location.reload();
                window.close();
            }, 2000);
        <?php endif; ?>
    </script>
</body>

</html>