<?php
require 'conexion.php';

$tipo = $_GET['tipo'] ?? '';
$dni_nie = $_GET['dni'] ?? '';
$id_empresa = $_GET['id_empresa'] ?? '';
$is_editing = isset($_GET['edit']) && $_GET['edit'] == '1';

// Obtener lista de ciclos
$sql_ciclos = "SELECT id_ciclo, nombre FROM catalogo_ciclos ORDER BY nombre";
$result_ciclos = $mysqli->query($sql_ciclos);

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

// Obtener empresas
$empresas = [];
$stmt = $mysqli->prepare("SELECT id, nombre_comercial FROM empresas ORDER BY nombre_comercial");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $empresas[] = $row;
}
$stmt->close();

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
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4"><?= $is_editing ? 'Editar' : 'Nueva' ?> Formación</h1>
        
        <form action="guardar_formacion.php" method="POST" id="formacionForm">
            <input type="hidden" name="is_editing" value="<?= $is_editing ? '1' : '0' ?>">
            
            <?php if ($tipo === 'empresa' || $is_editing): ?>
                <!-- Selector de Empresa -->
                <div class="mb-3">
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <?php if ($is_editing): ?>
                        <input type="hidden" name="id_empresa" value="<?= htmlspecialchars($id_empresa) ?>">
                        <?php 
                        $empresa_nombre = '';
                        foreach ($empresas as $emp) {
                            if ($emp['id'] == $id_empresa) {
                                $empresa_nombre = $emp['nombre_comercial'];
                                break;
                            }
                        }
                        ?>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($empresa_nombre) ?>" disabled>
                    <?php else: ?>
                        <select class="form-select" name="id_empresa" required>
                            <option value="">Seleccione una empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= htmlspecialchars($empresa['id']) ?>">
                                    <?= htmlspecialchars($empresa['nombre_comercial']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($tipo === 'alumno' || $is_editing): ?>
                <!-- Filtros de Alumno -->
                <?php if (!$is_editing): ?>
                    <div class="mb-3">
                        <label for="ciclo" class="form-label">Ciclo</label>
                        <select class="form-select" id="ciclo" name="ciclo">
                            <option value="">Seleccione un ciclo</option>
                            <?php while ($row = $result_ciclos->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($row['id_ciclo']) ?>">
                                    <?= htmlspecialchars($row['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="curso" class="form-label">Curso Académico</label>
                        <select class="form-select" id="curso_academico" name="curso_academico">
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso) ?>" 
                                    <?= ($curso === $curso_actual) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($curso) ?>
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
                        <label for="dni_nie_alumno" class="form-label">Alumno</label>
                        <select class="form-select" id="dni_nie_alumno" name="dni_nie_alumno" required>
                            <option value="">Seleccione un alumno</option>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="dni_nie_alumno" value="<?= htmlspecialchars($dni_nie) ?>">
                    <?php 
                    $stmt = $mysqli->prepare("SELECT CONCAT(nombre, ' ', apellido1, ' ', COALESCE(apellido2, '')) as nombre_completo FROM alumnos WHERE dni_nie = ?");
                    $stmt->bind_param("s", $dni_nie);
                    $stmt->execute();
                    $alumno = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Alumno</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($alumno['nombre_completo']) ?>" disabled>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Curso de la Formación -->
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
        });
    </script>
</body>
</html>

