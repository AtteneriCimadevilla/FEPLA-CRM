<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

// Variables para manejar errores y mensajes de éxito
$errores = [];
$exito = "";

// Check if we're editing an existing student
$editing = false;
$alumno = null;
if (isset($_GET['dni_nie'])) {
    $editing = true;
    $dni_nie = $_GET['dni_nie'];
    $query = "SELECT * FROM alumnos WHERE dni_nie = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $dni_nie);
    $stmt->execute();
    $result = $stmt->get_result();
    $alumno = $result->fetch_assoc();
    $stmt->close();
}

// Verificar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $dni_nie = trim($_POST['dni_nie']);
    $nombre = trim($_POST['nombre']);
    $apellido1 = trim($_POST['apellido1']);
    $apellido2 = trim($_POST['apellido2']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);
    $vehiculo = $_POST['vehiculo'];
    $id_grupo = trim($_POST['id_grupo']);

    // Validaciones básicas
    if (empty($dni_nie) || strlen($dni_nie) != 9) {
        $errores[] = "El DNI/NIE debe tener 9 caracteres.";
    }
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }
    if (empty($apellido1)) {
        $errores[] = "El primer apellido es obligatorio.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    // Si no hay errores, insertar o actualizar en la base de datos
    if (empty($errores)) {
        if ($editing) {
            // Check if DNI/NIE has changed
            if ($dni_nie != $_GET['dni_nie']) {
                // DNI/NIE has changed, we need to update it
                $stmt = $mysqli->prepare("UPDATE alumnos SET dni_nie=?, nombre=?, apellido1=?, apellido2=?, fecha_nacimiento=?, telefono=?, email=?, direccion=?, vehiculo=?, id_grupo=? WHERE dni_nie=?");
                $stmt->bind_param("sssssssssss", $dni_nie, $nombre, $apellido1, $apellido2, $fecha_nacimiento, $telefono, $email, $direccion, $vehiculo, $id_grupo, $_GET['dni_nie']);
            } else {
                // DNI/NIE hasn't changed, update other fields
                $stmt = $mysqli->prepare("UPDATE alumnos SET nombre=?, apellido1=?, apellido2=?, fecha_nacimiento=?, telefono=?, email=?, direccion=?, vehiculo=?, id_grupo=? WHERE dni_nie=?");
                $stmt->bind_param("ssssssssss", $nombre, $apellido1, $apellido2, $fecha_nacimiento, $telefono, $email, $direccion, $vehiculo, $id_grupo, $dni_nie);
            }
        } else {
            $stmt = $mysqli->prepare("INSERT INTO alumnos (dni_nie, nombre, apellido1, apellido2, fecha_nacimiento, telefono, email, direccion, vehiculo, id_grupo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $dni_nie, $nombre, $apellido1, $apellido2, $fecha_nacimiento, $telefono, $email, $direccion, $vehiculo, $id_grupo);
        }

        if ($stmt->execute()) {
            $exito = $editing ? "Alumno actualizado con éxito." : "Alumno creado con éxito.";
            // Set a session variable to indicate success
            session_start();
            $_SESSION['alumno_actualizado'] = true;
            // Redirect to the list of students
            header("Location: alumnos.php");
            exit();
        } else {
            $errores[] = "Error al " . ($editing ? "actualizar" : "insertar") . " en la base de datos: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch groups for the dropdown
$grupos_query = "SELECT id_grupo, CONCAT(c.nombre, ' - ', g.alias_grupo) AS nombre_grupo 
                 FROM grupos g 
                 JOIN catalogo_ciclos c ON g.id_ciclo = c.id_ciclo";
$grupos_result = $mysqli->query($grupos_query);
$grupos = $grupos_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Editar' : 'Crear'; ?> Alumno</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center mb-3">
        <a href="alumnos.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
            ← Volver
        </a>
    </header>
    <div class="container mt-5">
        <h1 class="text-center"><?php echo $editing ? 'Editar' : 'Crear'; ?> Alumno</h1>

        <!-- Mostrar errores -->
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="dni_nie" class="form-label">DNI/NIE</label>
                <input type="text" class="form-control" id="dni_nie" name="dni_nie" maxlength="9" value="<?php echo $editing ? htmlspecialchars($alumno['dni_nie']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $editing ? htmlspecialchars($alumno['nombre']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido1" class="form-label">Primer Apellido</label>
                <input type="text" class="form-control" id="apellido1" name="apellido1" value="<?php echo $editing ? htmlspecialchars($alumno['apellido1']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido2" class="form-label">Segundo Apellido</label>
                <input type="text" class="form-control" id="apellido2" name="apellido2" value="<?php echo $editing ? htmlspecialchars($alumno['apellido2']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $editing ? htmlspecialchars($alumno['fecha_nacimiento']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $editing ? htmlspecialchars($alumno['telefono']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $editing ? htmlspecialchars($alumno['email']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $editing ? htmlspecialchars($alumno['direccion']) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="vehiculo" class="form-label">Vehículo</label>
                <select class="form-select" id="vehiculo" name="vehiculo">
                    <option value="Si" <?php echo ($editing && $alumno['vehiculo'] == 'Si') ? 'selected' : ''; ?>>Sí</option>
                    <option value="No" <?php echo ($editing && $alumno['vehiculo'] == 'No') ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_grupo" class="form-label">Grupo</label>
                <select class="form-select" id="id_grupo" name="id_grupo" required>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo $grupo['id_grupo']; ?>" <?php echo ($editing && $alumno['id_grupo'] == $grupo['id_grupo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Actualizar' : 'Crear'; ?> Alumno</button>
            <a href="alumnos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>

</html>