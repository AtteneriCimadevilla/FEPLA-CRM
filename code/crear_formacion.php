<?php
require 'conexion.php';

$dni_nie = $_GET['dni'] ?? '';
$is_editing = isset($_GET['edit']) && $_GET['edit'] == '1';

if (empty($dni_nie)) {
    die("No se ha proporcionado un DNI/NIE válido.");
}

// Obtener los datos del alumno
$stmt = $mysqli->prepare("SELECT * FROM alumnos WHERE dni_nie = ?");
$stmt->bind_param("s", $dni_nie);
$stmt->execute();
$resultado_alumno = $stmt->get_result();
$alumno = $resultado_alumno->fetch_assoc();
$stmt->close();

// Obtener la formación actual del alumno si existe
$formacion_actual = null;
if ($is_editing) {
    $stmt = $mysqli->prepare("SELECT * FROM formaciones WHERE dni_nie_alumno = ?");
    $stmt->bind_param("s", $dni_nie);
    $stmt->execute();
    $resultado_formacion = $stmt->get_result();
    $formacion_actual = $resultado_formacion->fetch_assoc();
    $stmt->close();
}

// Obtener las empresas
$empresas_resultado = $mysqli->query("SELECT * FROM empresas");
$empresas_options = '';
while ($empresa = $empresas_resultado->fetch_assoc()) {
    $selected = ($formacion_actual && $empresa['id'] == $formacion_actual['id_empresa']) ? 'selected' : '';
    $empresas_options .= "<option value='{$empresa['id']}' {$selected}>{$empresa['nombre_comercial']}</option>";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Editar' : 'Crear' ?> Formación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center"><?= $is_editing ? 'Editar' : 'Crear' ?> Formación</h1>

        <form action="guardar_formacion.php" method="POST">
            <input type="hidden" name="dni_nie_alumno" value="<?= htmlspecialchars($dni_nie) ?>">
            <input type="hidden" name="is_editing" value="<?= $is_editing ? '1' : '0' ?>">

            <!-- Información del Alumno -->
            <div class="mb-3">
                <label class="form-label">Alumno</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido1'] . ' ' . $alumno['apellido2']) ?>" disabled>
            </div>

            <!-- Desplegable de Empresas -->
            <div class="mb-3">
                <label for="empresa" class="form-label">Empresa</label>
                <select class="form-select" name="id_empresa" required>
                    <option value="">Seleccione una empresa</option>
                    <?= $empresas_options ?>
                </select>
            </div>

            <!-- Campo de Curso -->
            <div class="mb-3">
                <label for="curso" class="form-label">Curso</label>
                <select class="form-select" name="curso" required>
                    <?php
                    $cursos = ['24/25', '25/26', '26/27'];
                    foreach ($cursos as $curso) {
                        $selected = ($formacion_actual && $formacion_actual['curso'] == $curso) ? 'selected' : '';
                        echo "<option value='{$curso}' {$selected}>{$curso}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?= $is_editing ? 'Actualizar' : 'Crear' ?> Formación</button>
        </form>

        <!-- Botón para cerrar la ventana emergente -->
        <button onclick="window.close();" class="btn btn-secondary mt-3">Cerrar</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>