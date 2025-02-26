<?php
require 'conexion.php';

$tipo = $_GET['tipo'] ?? '';
$dni_nie = $_GET['dni_nie'] ?? '';
$id_empresa = $_GET['id_empresa'] ?? '';
$is_editing = isset($_GET['edit']) && $_GET['edit'] == '1';

// Establecer curso actual predefinido
$curso_actual = '23/24'; // Ajusta según el curso actual
$cursos = ['23/24', '24/25', '25/26'];

// Obtener información de la formación si estamos editando
$formacion_actual = null;
if ($is_editing) {
    $stmt = $mysqli->prepare("
        SELECT f.*, 
               a.nombre AS nombre_alumno, 
               a.apellido1, 
               a.apellido2,
               e.nombre_comercial,
               g.id_grupo,
               cc.id_ciclo
        FROM formaciones f
        LEFT JOIN alumnos a ON f.dni_nie_alumno = a.dni_nie
        LEFT JOIN empresas e ON f.id_empresa = e.id
        LEFT JOIN grupos g ON a.id_grupo = g.id_grupo
        LEFT JOIN catalogo_ciclos cc ON g.id_ciclo = cc.id_ciclo
        WHERE f.dni_nie_alumno = ? AND f.id_empresa = ?
    ");
    $stmt->bind_param("si", $dni_nie, $id_empresa);
    $stmt->execute();
    $formacion_actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Obtener lista de ciclos
$sql_ciclos = "SELECT id_ciclo, nombre FROM catalogo_ciclos ORDER BY nombre";
$result_ciclos = $mysqli->query($sql_ciclos);

// Obtener lista de empresas
$sql_empresas = "SELECT id, nombre_comercial FROM empresas ORDER BY nombre_comercial";
$result_empresas = $mysqli->query($sql_empresas);

// Si tenemos un DNI, obtener información del alumno
$alumno_info = null;
if ($dni_nie) {
    $stmt = $mysqli->prepare("
        SELECT a.*, 
               g.alias_grupo, 
               g.id_grupo,
               cc.nombre AS nombre_ciclo, 
               cc.id_ciclo
        FROM alumnos a
        LEFT JOIN grupos g ON a.id_grupo = g.id_grupo
        LEFT JOIN catalogo_ciclos cc ON g.id_ciclo = cc.id_ciclo
        WHERE a.dni_nie = ?
    ");
    $stmt->bind_param("s", $dni_nie);
    $stmt->execute();
    $alumno_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Si tenemos un ID de empresa, obtener información de la empresa
$empresa_info = null;
if ($id_empresa) {
    $stmt = $mysqli->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $id_empresa);
    $stmt->execute();
    $empresa_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Editar' : 'Nueva' ?> Formación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center mb-4"><?= $is_editing ? 'Editar' : 'Nueva' ?> Formación</h2>

        <form id="formacionForm" class="needs-validation" novalidate>
            <input type="hidden" name="is_editing" value="<?= $is_editing ? '1' : '0' ?>">
            
            <?php if (!$dni_nie || $is_editing): ?>
            <!-- Sección de Selección de Alumno -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Selección de Alumno</h5>
                </div>
                <div class="card-body">
                    <?php if (!$dni_nie): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ciclo" class="form-label">Ciclo</label>
                            <select class="form-select" id="ciclo" name="ciclo" required>
                                <option value="">Seleccione un ciclo</option>
                                <?php while ($ciclo = $result_ciclos->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($ciclo['id_ciclo']) ?>"
                                        <?= ($formacion_actual && $formacion_actual['id_ciclo'] == $ciclo['id_ciclo']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ciclo['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="grupo" class="form-label">Grupo</label>
                            <select class="form-select" id="grupo" name="grupo" required>
                                <option value="">Primero seleccione un ciclo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="dni_nie_alumno" class="form-label">Alumno</label>
                        <select class="form-select" id="dni_nie_alumno" name="dni_nie_alumno" required>
                            <option value="">Primero seleccione un grupo</option>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Alumno Seleccionado</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($alumno_info['nombre'] . ' ' . $alumno_info['apellido1'] . ' ' . $alumno_info['apellido2']) ?>" disabled>
                        <input type="hidden" name="dni_nie_alumno" value="<?= htmlspecialchars($dni_nie) ?>">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sección de Selección de Empresa -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Selección de Empresa</h5>
                </div>
                <div class="card-body">
                    <?php if (!$id_empresa || $is_editing): ?>
                    <div class="mb-3">
                        <label for="id_empresa" class="form-label">Empresa</label>
                        <select class="form-select" id="id_empresa" name="id_empresa" required>
                            <option value="">Seleccione una empresa</option>
                            <?php while ($empresa = $result_empresas->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($empresa['id']) ?>"
                                    <?= ($id_empresa == $empresa['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre_comercial']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label">Empresa Seleccionada</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($empresa_info['nombre_comercial']) ?>" disabled>
                        <input type="hidden" name="id_empresa" value="<?= htmlspecialchars($id_empresa) ?>">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sección de Curso -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Curso Académico</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="curso" class="form-label">Curso</label>
                        <select class="form-select" id="curso" name="curso" required>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso) ?>"
                                    <?= ($curso === $curso_actual) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($curso) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <?= $is_editing ? 'Actualizar' : 'Crear' ?> Formación
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.close();">Cancelar</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Función para cargar grupos
            function cargarGrupos(cicloId) {
                $.getJSON('obtener_grupos.php', { ciclo: cicloId })
                    .done(function(data) {
                        $('#grupo').empty().append('<option value="">Seleccione un grupo</option>');
                        data.forEach(function(grupo) {
                            $('#grupo').append(
                                $('<option></option>')
                                    .val(grupo.id_grupo)
                                    .text(grupo.alias_grupo)
                            );
                        });
                    })
                    .fail(function(jqxhr, textStatus, error) {
                        console.error("Error cargando grupos:", error);
                        alert("Error al cargar los grupos");
                    });
            }

            // Función para cargar alumnos
            function cargarAlumnos(grupoId) {
                $.getJSON('obtener_alumnos.php', { grupo: grupoId })
                    .done(function(data) {
                        $('#dni_nie_alumno').empty().append('<option value="">Seleccione un alumno</option>');
                        data.forEach(function(alumno) {
                            $('#dni_nie_alumno').append(
                                $('<option></option>')
                                    .val(alumno.dni_nie)
                                    .text(alumno.nombre_completo)
                            );
                        });
                    })
                    .fail(function(jqxhr, textStatus, error) {
                        console.error("Error cargando alumnos:", error);
                        alert("Error al cargar los alumnos");
                    });
            }

            // Event listeners
            $('#ciclo').change(function() {
                const cicloId = $(this).val();
                if (cicloId) {
                    cargarGrupos(cicloId);
                } else {
                    $('#grupo').empty().append('<option value="">Primero seleccione un ciclo</option>');
                    $('#dni_nie_alumno').empty().append('<option value="">Primero seleccione un grupo</option>');
                }
            });

            $('#grupo').change(function() {
                const grupoId = $(this).val();
                if (grupoId) {
                    cargarAlumnos(grupoId);
                } else {
                    $('#dni_nie_alumno').empty().append('<option value="">Primero seleccione un grupo</option>');
                }
            });

            // Manejar envío del formulario
            $('#formacionForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('formacion_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.opener.location.reload();
                        window.close();
                    } else {
                        alert('Error: ' + (data.error || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
            });

            // Si estamos editando, cargar los valores iniciales
            <?php if ($is_editing && $formacion_actual): ?>
            const cicloId = '<?= $formacion_actual['id_ciclo'] ?>';
            const grupoId = '<?= $formacion_actual['id_grupo'] ?>';
            if (cicloId) {
                $('#ciclo').val(cicloId).trigger('change');
                setTimeout(() => {
                    if (grupoId) {
                        $('#grupo').val(grupoId).trigger('change');
                    }
                }, 500);
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>