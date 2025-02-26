<?php
include 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Obtener datos de la URL
$id_empresa = isset($_GET['id_empresa']) ? intval($_GET['id_empresa']) : null;
$dni_nie_alumno = isset($_GET['dni_nie']) ? $_GET['dni_nie'] : null;

$mensaje = '';
$today = date('Y-m-d');

// Obtener información de la empresa si está seleccionada
$empresa_seleccionada = null;
if ($id_empresa) {
    $stmt = $mysqli->prepare("SELECT nombre_comercial FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $id_empresa);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresa_seleccionada = $result->fetch_assoc();
    $stmt->close();
}

// Obtener información del alumno si está seleccionado
$alumno_seleccionado = null;
if ($dni_nie_alumno) {
    $stmt = $mysqli->prepare("SELECT CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) as nombre_completo FROM alumnos WHERE dni_nie = ?");
    $stmt->bind_param("s", $dni_nie_alumno);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno_seleccionado = $result->fetch_assoc();
    $stmt->close();
}

// Obtener lista de ciclos
$sql_ciclos = "SELECT id_ciclo, nombre FROM catalogo_ciclos ORDER BY nombre";
$result_ciclos = $mysqli->query($sql_ciclos);
if (!$result_ciclos) {
    die("Error al obtener los ciclos: " . $mysqli->error);
}

// Definir cursos y preseleccionar el actual
$cursos = ['24/25', '25/26', '26/27'];
$curso_actual = '24/25';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $tipo_actividad = $_POST['tipo_actividad'];
    $texto_registro = $_POST['texto_registro'];
    
    // Obtener valores del formulario o mantener los preseleccionados
    $id_empresa_final = isset($_POST['id_empresa']) && !empty($_POST['id_empresa']) ? 
        intval($_POST['id_empresa']) : $id_empresa;
    $dni_nie_alumno_final = isset($_POST['dni_nie_alumno']) && !empty($_POST['dni_nie_alumno']) ? 
        $_POST['dni_nie_alumno'] : $dni_nie_alumno;

    // Validación de campos obligatorios
    if (empty($fecha) || empty($tipo_actividad) || empty($texto_registro)) {
        $mensaje = '<div class="alert alert-danger">Rellene los campos obligatorios.</div>';
    } else {
        $query = "INSERT INTO registro (id_empresa, dni_nie_alumno, fecha, tipo_actividad, texto_registro) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);

        if ($stmt) {
            $stmt->bind_param("issss", 
                $id_empresa_final, 
                $dni_nie_alumno_final, 
                $fecha, 
                $tipo_actividad, 
                $texto_registro
            );

            if ($stmt->execute()) {
                $mensaje = '<div class="alert alert-success">Actividad agregada con éxito.</div>';
            } else {
                $mensaje = '<div class="alert alert-danger">Error al agregar la actividad: ' . $mysqli->error . '</div>';
            }
            $stmt->close();
        }
    }
}

// Obtener lista de empresas
$empresas = [];
if (!$id_empresa) {
    $stmt = $mysqli->prepare("SELECT id, nombre_comercial FROM empresas ORDER BY nombre_comercial");
    $stmt->execute();
    $result = $stmt->get_result();
    $empresas = $result->fetch_all(MYSQLI_ASSOC);
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
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $today; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="tipo_actividad" class="form-label">* Tipo de actividad</label>
                <select class="form-select" id="tipo_actividad" name="tipo_actividad" required>
                    <option value="Reunión presencial">Reunión presencial</option>
                    <option value="Reunión telefónica">Reunión telefónica</option>
                    <option value="Reunión online">Reunión online</option>
                    <option value="Visita">Visita</option>
                    <option value="Llamada">Llamada</option>
                    <option value="Email">Email</option>
                </select>
            </div>

            <!-- Sección de Empresa -->
            <?php if ($id_empresa): ?>
                <div class="mb-3">
                    <label class="form-label">Empresa seleccionada</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($empresa_seleccionada['nombre_comercial']); ?>" disabled>
                    <input type="hidden" name="id_empresa" value="<?php echo htmlspecialchars($id_empresa); ?>">
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label for="id_empresa" class="form-label">Empresa (opcional)</label>
                    <select class="form-select" id="id_empresa" name="id_empresa">
                        <option value="">Seleccione una empresa</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?php echo htmlspecialchars($empresa['id']); ?>">
                                <?php echo htmlspecialchars($empresa['nombre_comercial']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Sección de Alumno -->
            <?php if ($dni_nie_alumno): ?>
                <div class="mb-3">
                    <label class="form-label">Alumno seleccionado</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($alumno_seleccionado['nombre_completo']); ?>" disabled>
                    <input type="hidden" name="dni_nie_alumno" value="<?php echo htmlspecialchars($dni_nie_alumno); ?>">
                </div>
            <?php else: ?>
                <div id="filtros_alumno" class="mb-3">
                    <div class="mb-3">
                        <label for="ciclo" class="form-label">Ciclo</label>
                        <select class="form-select" id="ciclo" name="ciclo">
                            <option value="">Seleccione un ciclo</option>
                            <?php while ($row = $result_ciclos->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['id_ciclo']); ?>">
                                    <?php echo htmlspecialchars($row['nombre']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="curso" class="form-label">Curso</label>
                        <select class="form-select" id="curso" name="curso">
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo htmlspecialchars($curso); ?>" 
                                    <?php echo ($curso === $curso_actual) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="grupo" class="form-label">Grupo</label>
                        <select class="form-select" id="grupo" name="grupo">
                            <option value="">Seleccione un grupo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="dni_nie_alumno" class="form-label">Alumno (opcional)</label>
                        <select class="form-select" id="dni_nie_alumno" name="dni_nie_alumno">
                            <option value="">Seleccione un alumno</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="texto_registro" class="form-label">* Detalles</label>
                <textarea class="form-control" id="texto_registro" name="texto_registro" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Agregar Actividad</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar grupos cuando cambia ciclo o curso
            $('#ciclo, #curso').change(function() {
                var ciclo = $('#ciclo').val();
                var curso = $('#curso').val();
                if (ciclo && curso) {
                    $.getJSON('obtener_grupos.php', { ciclo: ciclo, curso: curso })
                        .done(function(data) {
                            $('#grupo').empty().append('<option value="">Seleccione un grupo</option>');
                            $.each(data, function(key, value) {
                                $('#grupo').append($('<option>').text(value.alias_grupo).attr('value', value.id_grupo));
                            });
                        })
                        .fail(function(jqxhr, textStatus, error) {
                            console.log("Error al obtener grupos: " + error);
                            alert("Error al cargar los grupos. Por favor, inténtelo de nuevo.");
                        });
                }
            });

            // Cargar alumnos cuando cambia el grupo
            $('#grupo').change(function() {
                var grupo = $(this).val();
                if (grupo) {
                    $.getJSON('obtener_alumnos.php', { grupo: grupo })
                        .done(function(data) {
                            $('#dni_nie_alumno').empty().append('<option value="">Seleccione un alumno</option>');
                            $.each(data, function(key, value) {
                                $('#dni_nie_alumno').append($('<option>').text(value.nombre_completo).attr('value', value.dni_nie));
                            });
                        })
                        .fail(function(jqxhr, textStatus, error) {
                            console.log("Error al obtener alumnos: " + error);
                            alert("Error al cargar los alumnos. Por favor, inténtelo de nuevo.");
                        });
                }
            });

            <?php if (strpos($mensaje, 'alert-success') !== false): ?>
                setTimeout(function() {
                    window.opener.location.reload();
                    window.close();
                }, 2000);
            <?php endif; ?>
        });
    </script>
</body>
</html>

