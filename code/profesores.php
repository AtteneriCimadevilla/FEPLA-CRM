<?php
require 'conexion.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.html");
    exit;
}

if ($_SESSION["tipo_usuario"] !== "admin") {
    header("location: home.php");
    exit;
}

// Función para obtener la lista de profesores
function obtenerProfesores($mysqli)
{
    $query = "SELECT dni_nie, nombre, apellido1, apellido2, telefono, email, tipo_usuario FROM profesores";
    $result = $mysqli->query($query);
    $profesores = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $profesores[] = $row;
        }
    }

    return $profesores;
}

// Obtener la acción desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $dni_nie = $_POST['dni_nie'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $apellido1 = $_POST['apellido1'] ?? null;
    $apellido2 = $_POST['apellido2'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $email = $_POST['email'] ?? null;
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'user';
    $contrasenya = $_POST['contrasenya'] ?? null;

    if ($accion === 'add') {
        // Hashear la contraseña
        $hash_contrasenya = password_hash($contrasenya, PASSWORD_DEFAULT);

        // Añadir profesor
        $query = "INSERT INTO profesores (dni_nie, contrasenya, nombre, apellido1, apellido2, telefono, email, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssssss", $dni_nie, $hash_contrasenya, $nombre, $apellido1, $apellido2, $telefono, $email, $tipo_usuario);
        $stmt->execute();
    } elseif ($accion === 'edit') {
        // Editar profesor
        $query = "UPDATE profesores SET contrasenya = ?, nombre = ?, apellido1 = ?, apellido2 = ?, telefono = ?, email = ?, tipo_usuario = ? WHERE dni_nie = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssssss", $contrasenya, $nombre, $apellido1, $apellido2, $telefono, $email, $tipo_usuario, $dni_nie);
        $stmt->execute();
    } elseif ($accion === 'delete') {
        // Eliminar profesor
        $query = "DELETE FROM profesores WHERE dni_nie = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $dni_nie);
        $stmt->execute();
    }

    // Redirigir para evitar reenvío de formularios
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Obtener la lista de profesores para mostrar
$profesores = obtenerProfesores($mysqli);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center mb-3">
        <!-- Flecha para volver al home -->
        <a href="home.php" class="btn btn-outline-secondary btn-sm" style="position: absolute; top: 10px; left: 10px;">
            ← Volver al Home
        </a>
    </header>
    <div class="container my-4">
        <h1 class="text-center">Gestión de Profesores</h1>

        <!-- Tabla de profesores -->
        <table class="table table-bordered mb-4">
            <thead class="thead-dark">
                <tr>
                    <th>DNI/NIE</th>
                    <th>Nombre y apellidos</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profesores as $profesor): ?>
                    <tr>
                        <td><?= htmlspecialchars($profesor['dni_nie']) ?></td>
                        <td><?= htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellido1'] . ' ' . $profesor['apellido2']) ?></td>
                        <td><?= htmlspecialchars($profesor['telefono']) ?></td>
                        <td><?= htmlspecialchars($profesor['email']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarProfesor('<?= htmlspecialchars(json_encode($profesor)) ?>')">Editar</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="delete">
                                <input type="hidden" name="dni_nie" value="<?= htmlspecialchars($profesor['dni_nie']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="text-center">Añadir un profesor</h3>

        <!-- Formulario para añadir o editar un profesor -->
        <form method="POST">
            <input type="hidden" name="accion" id="accion" value="add">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="dni_nie">DNI/NIE</label>
                    <input type="text" class="form-control" id="dni_nie" name="dni_nie" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="apellido1">Primer Apellido</label>
                    <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                </div>
                <div class="form-group col-md-2">
                    <label for="apellido2">Segundo Apellido</label>
                    <input type="text" class="form-control" id="apellido2" name="apellido2">
                </div>
                <div class="form-group col-md-2">
                    <label for="telefono">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono">
                </div>
                <div class="form-group col-md-2">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="contrasenya">Contraseña</label>
                    <input type="password" class="form-control" id="contrasenya" name="contrasenya" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="tipo_usuario">Tipo de Usuario</label>
                    <select class="form-control" id="tipo_usuario" name="tipo_usuario">
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                        <option value="root">Root</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-primary mt-4">Guardar</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function editarProfesor(profesor) {
            const data = JSON.parse(profesor);
            document.getElementById('accion').value = 'edit';
            document.getElementById('dni_nie').value = data.dni_nie;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('apellido1').value = data.apellido1;
            document.getElementById('apellido2').value = data.apellido2;
            document.getElementById('telefono').value = data.telefono;
            document.getElementById('email').value = data.email;
            document.getElementById('tipo_usuario').value = data.tipo_usuario;
        }
    </script>
</body>

</html>