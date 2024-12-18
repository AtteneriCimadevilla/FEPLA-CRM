<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Obtener las actividades filtradas
$filter_query = "";
$filter_value = "";
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    $filter_value = $_GET['filter_value'];
    if ($filter == 'empresa') {
        $filter_query = " WHERE r.id_empresa = ?";
    } elseif ($filter == 'alumno') {
        $filter_query = " WHERE r.dni_nie_alumno = ?";
    }
}

$sql = "SELECT r.id, r.fecha, r.tipo_actividad, r.texto_registro, e.nombre_comercial AS empresa, 
            a.nombre AS alumno, a.apellido1 AS apellido
        FROM registro r
        JOIN empresas e ON r.id_empresa = e.id
        LEFT JOIN alumnos a ON r.dni_nie_alumno = a.dni_nie" . $filter_query;

$stmt = $mysqli->prepare($sql);

// Vincular parámetro si se aplica filtro
if ($filter_query) {
    $stmt->bind_param("s", $filter_value);
}

$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

// Obtener empresas y alumnos para el filtro
$sql_empresas = "SELECT id, nombre_comercial FROM empresas";
$result_empresas = $mysqli->query($sql_empresas);
$empresas = [];
while ($row_empresa = $result_empresas->fetch_assoc()) {
    $empresas[] = $row_empresa;
}

$sql_alumnos = "SELECT dni_nie, CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) AS nombre_completo FROM alumnos";
$result_alumnos = $mysqli->query($sql_alumnos);
$alumnos = [];
while ($row_alumno = $result_alumnos->fetch_assoc()) {
    $alumnos[] = $row_alumno;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estiloActividad.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Actividades Registradas</h1>
        
        <!-- Filtro -->
        <h3 class="mt-4">Filtrar</h3>
        <div class="form-group">
            <label for="filtro">Seleccione empresa o alumno:</label>
            <select class="form-control" id="filtro">
                <option value="">Seleccione una opción</option>
                <optgroup label="Empresas" id="empresas-filtro">
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="empresa-<?php echo $empresa['id']; ?>"><?php echo $empresa['nombre_comercial']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Alumnos" id="alumnos-filtro">
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="alumno-<?php echo $alumno['dni_nie']; ?>"><?php echo $alumno['nombre_completo']; ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        <button id="filtrarBtn" class="btn btn-secondary">Filtrar</button>
        
        <!-- Actividades -->
        <h3 class="mt-4">Lista de Actividades</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Actividad</th>
                    <th>Empresa</th>
                    <th>Alumno</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo $activity['fecha']; ?></td>
                        <td><?php echo $activity['tipo_actividad']; ?></td>
                        <td><?php echo $activity['empresa']; ?></td>
                        <td><?php echo $activity['alumno'] . ' ' . $activity['apellido']; ?></td>
                        <td><?php echo $activity['texto_registro']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filtrar actividades
            $('#filtrarBtn').click(function() {
                var filterValue = $('#filtro').val();
                if (filterValue) {
                    var filterType = filterValue.split('-')[0];
                    var filterId = filterValue.split('-')[1];
                    window.location.href = 'actividades.php?filter=' + filterType + '&filter_value=' + filterId;
                } else {
                    window.location.href = 'actividades.php';  // No filtrar
                }
            });
        });
    </script>
</body>
</html>
