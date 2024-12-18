<?php
// obtener los datos del alumno (esto dependerá de tu código)
$dni_nie = $_GET['dni']; // O de otra forma, dependiendo de cómo pases el dni
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
            <input type="hidden" name="dni_nie" value="<?= htmlspecialchars($dni_nie) ?>">

            <div class="mb-3">
                <label for="empresa" class="form-label">Empresa</label>
                <select class="form-select" name="empresa" required>
                    <?php
                    // Aquí generas las opciones para las empresas, como lo haces en tu código original
                    echo $empresas_options;
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción de la Formación</label>
                <textarea class="form-control" name="descripcion" required></textarea>
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
