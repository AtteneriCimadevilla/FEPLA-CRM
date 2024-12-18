<?php
require 'conexion.php';

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
        a.clase AS clase, 
        e.nombre_comercial AS empresa
    FROM 
        alumnos a
    LEFT JOIN 
        formaciones f ON a.dni_nie = f.dni_nie_alumno
    LEFT JOIN 
        empresas e ON f.id_empresa = e.id
    WHERE 
        a.dni_nie = ?;
");

// Proporcionar el valor para el placeholder
$dni_nie = "12345678Q"; // Reemplaza con el DNI/NIE del alumno
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
    SELECT fecha, tipo_actividad, id_empresa, texto_registro
    FROM registro
    WHERE dni_nie_alumno = ?;
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
                            <p><strong>Vehículo:</strong> <?= $alumno['vehiculo'] ? 'Sí' : 'No' ?></p>
                            <p><strong>Clase:</strong> <?= htmlspecialchars($alumno['clase']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        No se encontró al alumno.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna de Registros de Actividades (en la parte derecha) -->
            <div class="col-md-6">
                <h2 class="mb-4">Registros de Actividades</h2>
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
                                    <td><?= htmlspecialchars($registro['id_empresa']) ?></td>
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

        <!-- Empresa Asignada -->
        <h2 class="mt-4">Empresa Asignada</h2>
        <table class="table table-bordered">
            <tr>
                <td>
                    <?php if ($alumno['empresa']): ?>
                        <span><?php echo htmlspecialchars($alumno['empresa']); ?></span>
                        <button class="btn btn-warning btn-sm" data-dni="<?= htmlspecialchars($alumno['dni_nie']) ?>" data-empresa="<?= htmlspecialchars($alumno['empresa']) ?>">Editar</button>
                    <?php else: ?>
                        <button class="btn btn-primary btn-sm" onclick="abrirVentanaEmergente('crear_formacion.php?dni=<?= htmlspecialchars($alumno['dni_nie']) ?>')">Crear formación</button>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

    </div>

    <!-- Vincula a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function abrirVentanaEmergente(url) {
            // Abrir una nueva ventana con la URL proporcionada
            window.open(url, 'VentanaEmergente', 'width=800,height=600,resizable=yes');
        }
    </script>

</body>

</html>