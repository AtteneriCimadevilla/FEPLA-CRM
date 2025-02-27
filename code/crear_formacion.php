<?php
require 'conexion.php';

$tipo = $_GET['tipo'] ?? '';
$dni_nie = $_GET['dni'] ?? '';
$id_empresa = $_GET['id_empresa'] ?? '';
$is_editing = isset($_GET['edit']) && $_GET['edit'] == '1';

// Obtener información de la empresa si está seleccionada
$empresa_info = null;
if ($id_empresa) {
    $stmt = $mysqli->prepare("SELECT * FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $id_empresa);
    $stmt->execute();
    $empresa_info = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Obtener información del alumno si está seleccionado
$alumno_info = null;
if ($dni_nie) {
    $stmt = $mysqli->prepare("
        SELECT a.*, g.alias_grupo, g.curso as curso_actual, cc.nombre as nombre_ciclo, cc.id_ciclo
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

// Obtener lista de ciclos
$sql_ciclos = "SELECT id_ciclo, nombre FROM catalogo_ciclos ORDER BY nombre";
$result_ciclos = $mysqli->query($sql_ciclos);

// Obtener lista de empresas
$sql_empresas = "SELECT id, nombre_comercial, nombre_empresa FROM empresas ORDER BY nombre_comercial";
$result_empresas = $mysqli->query($sql_empresas);

// Definir cursos
$cursos = ['24/25', '25/26', '26/27'];
$curso_actual = '24/25';

// Si estamos editando, obtener la formación actual
$formacion_actual = null;
if ($is_editing) {
    $stmt = $mysqli->prepare("SELECT * FROM formaciones WHERE dni_nie_alumno = ? AND id_empresa = ?");
    $stmt->bind_param("si", $dni_nie, $id_empresa);
    $stmt->execute();
    $formacion_actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Editar' : 'Nueva' ?> Formación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4"><?= $is_editing ? 'Editar' : 'Nueva' ?> Formación</h1>
        
        <form action="guardar_formacion.php" method="POST" id="formacionForm">
            <input type="hidden" name="is_editing" value="<?= $is_editing ? '1' : '0' ?>">
            
            <!-- Selección de Empresa -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Selección de Empresa</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="id_empresa" class="form-label">Empresa</label>
                        <select class="form-select" name="id_empresa" id="id_empresa" required>
                            <option value="">Seleccione una empresa</option>
                            <?php while ($empresa = $result_empresas->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($empresa['id']) ?>" 
                                    <?= ($id_empresa == $empresa['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre_comercial']) ?> 
                                    <?php if (!empty($empresa['nombre_empresa'])): ?>
                                        (<?= htmlspecialchars($empresa['nombre_empresa']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Selección de Alumno -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Selección de Alumno</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ciclo" class="form-label">Ciclo</label>
                                <select class="form-select" id="ciclo" name="ciclo">
                                    <option value="">Seleccione un ciclo</option>
                                    <?php while ($row = $result_ciclos->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($row['id_ciclo']) ?>"
                                            <?= ($alumno_info && $alumno_info['id_ciclo'] == $row['id_ciclo']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nombre']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="curso_academico" class="form-label">Curso Académico</label>
                                <select class="form-select" id="curso_academico" name="curso_academico">
                                    <?php foreach ($cursos as $curso): ?>
                                        <option value="<?= htmlspecialchars($curso) ?>" 
                                            <?= ($alumno_info && $alumno_info['curso_actual'] == $curso) ? 'selected' : 
                                               (($curso === $curso_actual) ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($curso) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grupo" class="form-label">Grupo</label>
                                <select class="form-select" id="grupo" name="grupo">
                                    <option value="">Seleccione un grupo</option>
                                    <?php if ($alumno_info && $alumno_info['id_grupo']): ?>
                                        <option value="<?= htmlspecialchars($alumno_info['id_grupo']) ?>" selected>
                                            <?= htmlspecialchars($alumno_info['alias_grupo']) ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dni_nie_alumno" class="form-label">Alumno</label>
                                <select class="form-select" id="dni_nie_alumno" name="dni_nie_alumno" required>
                                    <option value="">Seleccione un alumno</option>
                                    <?php if ($alumno_info): ?>
                                        <option value="<?= htmlspecialchars($alumno_info['dni_nie']) ?>" selected>
                                            <?= htmlspecialchars($alumno_info['nombre'] . ' ' . $alumno_info['apellido1'] . ' ' . $alumno_info['apellido2']) ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Curso de la Formación -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de la Formación</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="curso" class="form-label">Curso de Formación</label>
                        <select class="form-select" name="curso" required>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso) ?>"
                                    <?= ($formacion_actual && $formacion_actual['curso'] == $curso) ? 'selected' : '' ?>>
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
            // Cargar grupos cuando cambia ciclo o curso académico
            $('#ciclo, #curso_academico').change(function() {
                var ciclo = $('#ciclo').val();
                var curso = $('#curso_academico').val();
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

            // Trigger initial load of grupos if ciclo and curso_academico are pre-selected
            if ($('#ciclo').val() && $('#curso_academico').val()) {
                $('#ciclo').trigger('change');
            }
        });

                function confirmarBorrado(dni, idEmpresa, curso) {
            if (confirm('¿Está seguro de que desea eliminar esta formación?')) {
                const formData = new FormData();
                formData.append('dni_nie', dni);
                formData.append('id_empresa', idEmpresa);
                formData.append('curso', curso);

                fetch('borrar_formacion.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al procesar la solicitud');
                    });
            }
        }
    </script>
</body>
</html>