<?php
include 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM empresas WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $empresa = $resultado->fetch_assoc();
        } else {
            echo "No se encontró la empresa.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error en la consulta.";
        exit();
    }

    $sql_actividades = "SELECT fecha, tipo_actividad, texto_registro FROM registro WHERE id_empresa = ? ORDER BY fecha DESC";
    $stmt_actividades = $mysqli->prepare($sql_actividades);

    if ($stmt_actividades) {
        $stmt_actividades->bind_param("i", $id);
        $stmt_actividades->execute();
        $resultado_actividades = $stmt_actividades->get_result();
        $actividades = $resultado_actividades->fetch_all(MYSQLI_ASSOC);
        $stmt_actividades->close();
    } else {
        echo "Error en la consulta de actividades.";
        exit();
    }

    // Fetch assigned students
    $sql_alumnos = "SELECT a.dni_nie, CONCAT(a.nombre, ' ', a.apellido1, ' ', a.apellido2) AS nombre_completo, f.curso
                    FROM alumnos a
                    JOIN formaciones f ON a.dni_nie = f.dni_nie_alumno
                    WHERE f.id_empresa = ?
                    ORDER BY f.curso DESC, a.apellido1, a.nombre";
    $stmt_alumnos = $mysqli->prepare($sql_alumnos);

    if ($stmt_alumnos) {
        $stmt_alumnos->bind_param("i", $id);
        $stmt_alumnos->execute();
        $resultado_alumnos = $stmt_alumnos->get_result();
        $alumnos = $resultado_alumnos->fetch_all(MYSQLI_ASSOC);
        $stmt_alumnos->close();
    } else {
        echo "Error en la consulta de alumnos.";
        exit();
    }
} else {
    echo "ID de empresa no proporcionado.";
    exit();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Empresa</title>
    <link rel="stylesheet" href="styleEmpresa.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <header>
            <a href="empresas.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
                ← Volver </a>
            <h1 class="text-center mb-4">Detalles de Empresa</h1>
        </header>

        <div class="row">
            <!-- Columna izquierda: Información General y de Contacto -->
            <div class="col-md-6">
                <section class="perfil card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Información General</h2>
                        <p><strong>NIF:</strong> <?php echo htmlspecialchars($empresa['nif']); ?></p>
                        <p><strong>Nombre Comercial:</strong> <?php echo htmlspecialchars($empresa['nombre_comercial']); ?></p>
                        <p><strong>Nombre de la Empresa:</strong> <?php echo htmlspecialchars($empresa['nombre_empresa']); ?></p>
                        <p><strong>Teléfono de Empresa:</strong> <?php echo htmlspecialchars($empresa['telefono_empresa']); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?></p>
                        <p><strong>CP:</strong> <?php echo htmlspecialchars($empresa['cp']); ?></p>
                        <p><strong>Web:</strong> <?php echo htmlspecialchars($empresa['web']); ?></p>
                        <p><strong>Email de Empresa:</strong> <?php echo htmlspecialchars($empresa['email_empresa']); ?></p>
                        <p><strong>Interesado:</strong> <?php echo $empresa['interesado'] ? 'Sí' : 'No'; ?></p>
                        <p><strong>Cantidad de Alumnos:</strong> <?php echo htmlspecialchars($empresa['cantidad_alumnos']); ?></p>
                        <div class="notas">
                            <strong>Descripción:</strong>
                            <p><?php echo nl2br(htmlspecialchars($empresa['descripcion'])); ?></p>
                        </div>
                        <p><strong>Actividad Principal:</strong> <?php echo htmlspecialchars($empresa['actividad_principal']); ?></p>
                        <div class="notas">
                            <strong>Otras Actividades:</strong>
                            <p><?php echo nl2br(htmlspecialchars($empresa['otras_actividades'])); ?></p>
                        </div>
                        <p><strong>DNI Profesor:</strong> <?php echo htmlspecialchars($empresa['dni_profesor']); ?></p>
                    </div>
                </section>

                <section class="contacto card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Información de Contacto</h2>
                        <p><strong>Nombre de Contacto:</strong> <?php echo htmlspecialchars($empresa['nombre_contacto']); ?></p>
                        <p><strong>Teléfono de Contacto:</strong> <?php echo htmlspecialchars($empresa['telefono_contacto']); ?></p>
                        <p><strong>Email de Contacto:</strong> <?php echo htmlspecialchars($empresa['email_contacto']); ?></p>
                    </div>
                </section>
            </div>

            <!-- Columna derecha: Registro de Actividades y Alumnos Asignados -->
            <div class="col-md-6">
                <section class="registro card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Registro de Actividades</h2>
                        <button class="btn btn-primary mb-3" onclick="abrirVentanaEmergente('agregar_actividad.php?id_empresa=<?php echo $empresa['id']; ?>')">Añadir Actividad</button>
                        <?php if (!empty($actividades)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Actividad</th>
                                        <th>Detalles</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actividades as $actividad): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($actividad['fecha']); ?></td>
                                            <td><?php echo htmlspecialchars($actividad['tipo_actividad']); ?></td>
                                            <td><?php echo htmlspecialchars($actividad['texto_registro']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                No hay actividades registradas actualmente.
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="alumnos card mb-4">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Alumnos Asignados</h2>
                        <?php if (!empty($alumnos)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>DNI/NIE</th>
                                        <th>Nombre</th>
                                        <th>Curso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alumnos as $alumno): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($alumno['dni_nie']); ?></td>
                                            <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                                            <td><?php echo htmlspecialchars($alumno['curso']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                No hay alumnos asignados actualmente.
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>

        <!-- Botón Crear Formación -->
        <div class="acciones text-center">
            <button onclick="abrirVentanaEmergente('crear_formacion.php?tipo=empresa&id_empresa=<?php echo $empresa['id']; ?>')" class="btn btn-success">
                Crear Formación
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function abrirVentanaEmergente(url) {
            window.open(url, 'VentanaEmergente', 'width=800,height=800,resizable=yes,scrollbars=yes');
        }
    </script>
</body>

</html>