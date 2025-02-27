<?php
session_start();
require_once "conexion.php";

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.html");
    exit;
}

// Fetch ciclos
$sql_ciclos = "SELECT id_ciclo, nombre FROM catalogo_ciclos ORDER BY nombre";
$result_ciclos = $mysqli->query($sql_ciclos);

if (!$result_ciclos) {
    die("Error al obtener los ciclos: " . $mysqli->error);
}

// Definir cursos y preseleccionar el actual
$cursos = ['24/25', '25/26', '26/27'];
$curso_actual = '24/25'; // Cambia esto al curso actual

// Fetch activity types y preseleccionar uno
$activity_types = ['Reunión presencial', 'Reunión telefónica', 'Reunión online', 'Visita', 'Llamada', 'Email'];
$tipo_actividad_predeterminado = 'Reunión presencial'; // Cambia esto al tipo de actividad predeterminado que desees

// Set default date to today
$today = date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Actividad - CRM-FEPLA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estiloActividad.css">
</head>
<body>
    <header class="d-flex justify-content-between align-items-center mb-3">
        <a href="home.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
            ← Volver al Home
        </a>
    </header>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Módulo de Actividad</h1>
        <div class="form-container">
            <div id="message"></div>
            <form id="activityForm">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $today; ?>" required>
                </div>
                <div class="form-group">
                    <label for="tipo_actividad">Tipo de Actividad:</label>
                    <select class="form-control" id="tipo_actividad" name="tipo_actividad" required>
                        <?php foreach ($activity_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($type === $tipo_actividad_predeterminado) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="actividad_para">Actividad para:</label>
                    <select class="form-control" id="actividad_para" name="actividad_para" required>
                        <option value="">Seleccione una opción</option>
                        <option value="empresa">Empresa</option>
                        <option value="alumno">Alumno</option>
                        <option value="ambos">Empresa y Alumno</option>
                    </select>
                </div>
                <div id="empresa_section" style="display:none;">
                    <div class="form-group">
                        <label for="empresa">Empresa:</label>
                        <select class="form-control" id="empresa" name="empresa">
                            <option value="">Seleccione una empresa</option>
                        </select>
                    </div>
                </div>
                <div id="alumno_section" style="display:none;">
                    <div class="form-group">
                        <label for="ciclo">Ciclo:</label>
                        <select class="form-control" id="ciclo" name="ciclo">
                            <option value="">Seleccione un ciclo</option>
                            <?php while ($row = $result_ciclos->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['id_ciclo']); ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select class="form-control" id="curso" name="curso">
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo htmlspecialchars($curso); ?>" <?php echo ($curso === $curso_actual) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grupo">Grupo:</label>
                        <select class="form-control" id="grupo" name="grupo">
                            <option value="">Seleccione un grupo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alumno">Alumno:</label>
                        <select class="form-control" id="alumno" name="alumno">
                            <option value="">Seleccione un alumno</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="texto_registro">Nota:</label>
                    <textarea class="form-control" id="texto_registro" name="texto_registro" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Actividad</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#actividad_para').change(function() {
            var seleccion = $(this).val();
            if (seleccion === 'empresa') {
                $('#empresa_section').show();
                $('#alumno_section').hide();
            } else if (seleccion === 'alumno') {
                $('#empresa_section').hide();
                $('#alumno_section').show();
            } else if (seleccion === 'ambos') {
                $('#empresa_section').show();
                $('#alumno_section').show();
            } else {
                $('#empresa_section').hide();
                $('#alumno_section').hide();
            }
        });

        // Load grupos based on selected ciclo and curso
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
                        var err = textStatus + ", " + error;
                        console.log("Error al obtener grupos: " + err);
                        alert("Error al cargar los grupos. Por favor, inténtelo de nuevo.");
                    });
            }
        });

        // Load students based on selected grupo
        $('#grupo').change(function() {
            var grupo = $(this).val();
            if (grupo) {
                $.getJSON('obtener_alumnos.php', { grupo: grupo })
                    .done(function(data) {
                        $('#alumno').empty().append('<option value="">Seleccione un alumno</option>');
                        $.each(data, function(key, value) {
                            $('#alumno').append($('<option>').text(value.nombre_completo).attr('value', value.dni_nie));
                        });
                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ", " + error;
                        console.log("Error al obtener alumnos: " + err);
                        alert("Error al cargar los alumnos. Por favor, inténtelo de nuevo.");
                    });
            }
        });

        // Load empresas
        $.getJSON('obtener_empresas.php')
            .done(function(data) {
                $.each(data, function(key, value) {
                    $('#empresa').append($('<option>').text(value.nombre_comercial).attr('value', value.id));
                });
            })
            .fail(function(jqxhr, textStatus, error) {
                var err = textStatus + ", " + error;
                console.log("Error al obtener empresas: " + err);
                alert("Error al cargar las empresas. Por favor, recargue la página.");
            });
        // Handle form submission
        $('#activityForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'procesar_actividad.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#activityForm')[0].reset();
                        $('#fecha').val('<?php echo $today; ?>');  // Reset date to today
                        $('#tipo_actividad').val('<?php echo $tipo_actividad_predeterminado; ?>');  // Reset tipo_actividad to default
                        $('#curso').val('<?php echo $curso_actual; ?>');  // Reset curso to default
                        $('#empresa_section').hide();
                        $('#alumno_section').hide();
                    } else {
                        $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        console.log('Error details:', response);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#message').html('<div class="alert alert-danger">Error al procesar la solicitud. Por favor, inténtelo de nuevo.</div>');
                    console.log('AJAX error:', textStatus, errorThrown);
                    console.log('Response:', jqXHR.responseText);
                }
            });
        });
    });
    </script>
</body>
</html>

