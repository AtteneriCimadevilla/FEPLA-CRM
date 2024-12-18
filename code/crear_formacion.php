<?php
require 'conexion.php';

// obtener los datos del alumno (esto dependerá de tu código)
$dni_nie = $_GET['dni']; // O de otra forma, dependiendo de cómo pases el dni

// Obtener los datos del alumno
$resultado_alumno = $mysqli->query("SELECT * FROM alumnos WHERE dni_nie = '$dni_nie'");
$alumno = $resultado_alumno->fetch_assoc();

// Obtener las empresas
$empresas_resultado = $mysqli->query("SELECT * FROM empresas");
$empresas_options = '';
while ($empresa = $empresas_resultado->fetch_assoc()) {
    $empresas_options .= "<option value='{$empresa['id']}' " . ($empresa['id'] == $alumno['id_empresa'] ? 'selected' : '') . ">{$empresa['nombre_comercial']}</option>";
}

// Obtener los alumnos para el desplegable
$alumnos_resultado = $mysqli->query("SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', apellido2) AS nombre_alumno FROM alumnos");
$alumnos_options = '';
while ($alumno_data = $alumnos_resultado->fetch_assoc()) {
    $alumnos_options .= "<option value='{$alumno_data['dni_nie']}' " . ($alumno_data['dni_nie'] == $dni_nie ? 'selected' : '') . ">{$alumno_data['nombre_alumno']}</option>";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Formación</title>
    <!-- Vincula a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Crear Formación</h1>

        <form action="guardar_formacion.php" method="POST">
            <input type="hidden" name="dni_nie_alumno" value="<?= htmlspecialchars($dni_nie) ?>">

            <!-- Desplegable de Alumnos -->
            <div class="mb-3">
                <label for="alumno" class="form-label">Alumno</label>
                <select class="form-select" name="dni_nie_alumno" required>
                    <?= $alumnos_options ?>
                </select>
            </div>

            <!-- Desplegable de Empresas -->
            <div class="mb-3">
                <label for="empresa" class="form-label">Empresa</label>
                <select class="form-select" name="id_empresa" required>
                    <?= $empresas_options ?>
                </select>
            </div>

            <!-- Campo de Curso -->
            <div class="mb-3">
                <label for="curso" class="form-label">Curso</label>
                <select class="form-select" name="curso" required>
                    <option value="24/25">24/25</option>
                    <option value="25/26">25/26</option>
                    <option value="26/27">26/27</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Crear Formación</button>
        </form>

        <!-- Botón para cerrar la ventana emergente -->
        <button onclick="window.close();" class="btn btn-secondary mt-3">Cerrar</button>
    </div>

    <!-- Vincula a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>